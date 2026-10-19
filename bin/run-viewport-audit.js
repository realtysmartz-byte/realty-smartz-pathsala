const { spawn } = require('child_process');
const fs = require('fs');
const http = require('http');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../tests/screenshots');
const USER_DATA = path.resolve(__dirname, '../tests/chrome_user_data_audit');

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

function fetchJson(url) {
    return new Promise((resolve, reject) => {
        http.get(url, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => {
                try { resolve(JSON.parse(data)); } catch (e) { reject(e); }
            });
        }).on('error', reject);
    });
}

function httpLogin(loginUrl, email, password) {
    return new Promise((resolve, reject) => {
        http.get(loginUrl, (res) => {
            let html = '';
            const setCookie = res.headers['set-cookie'] || [];
            let phpSessId = '';
            for (let c of setCookie) {
                const m = c.match(/PHPSESSID=([^;]+)/);
                if (m) phpSessId = m[1];
            }

            res.on('data', chunk => html += chunk);
            res.on('end', () => {
                const tokenMatch = html.match(/name="_csrf_token"\s+value="([^"]+)"/);
                const token = tokenMatch ? tokenMatch[1] : '';

                const postData = new URLSearchParams({
                    _csrf_token: token,
                    email: email,
                    password: password
                }).toString();

                const u = new URL(loginUrl);
                const req = http.request({
                    hostname: u.hostname,
                    port: u.port,
                    path: u.pathname,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Content-Length': Buffer.byteLength(postData),
                        'Cookie': `PHPSESSID=${phpSessId}`
                    }
                }, (postRes) => {
                    const postCookies = postRes.headers['set-cookie'] || [];
                    for (let c of postCookies) {
                        const m = c.match(/PHPSESSID=([^;]+)/);
                        if (m) phpSessId = m[1];
                    }
                    resolve(phpSessId);
                });
                req.on('error', reject);
                req.write(postData);
                req.end();
            });
        }).on('error', reject);
    });
}

class CDP {
    constructor(wsUrl) {
        this.wsUrl = wsUrl;
        this.id = 1;
        this.callbacks = new Map();
    }
    connect() {
        return new Promise((resolve, reject) => {
            this.ws = new WebSocket(this.wsUrl);
            this.ws.onopen = resolve;
            this.ws.onerror = reject;
            this.ws.onmessage = (msg) => {
                const res = JSON.parse(msg.data);
                if (res.id && this.callbacks.has(res.id)) {
                    const cb = this.callbacks.get(res.id);
                    this.callbacks.delete(res.id);
                    if (res.error) cb.reject(res.error);
                    else cb.resolve(res.result);
                }
            };
        });
    }
    send(method, params = {}) {
        const id = this.id++;
        return new Promise((resolve, reject) => {
            this.callbacks.set(id, { resolve, reject });
            this.ws.send(JSON.stringify({ id, method, params }));
        });
    }
    close() { if (this.ws) this.ws.close(); }
}

const VIEWPORTS = [
    { name: '320x640', width: 320, height: 640, mobile: true },
    { name: '360x740', width: 360, height: 740, mobile: true },
    { name: '390x844', width: 390, height: 844, mobile: true },
    { name: '768x1024', width: 768, height: 1024, mobile: false },
    { name: '1024x768', width: 1024, height: 768, mobile: false },
    { name: '1280x800', width: 1280, height: 800, mobile: false },
    { name: '1440x900', width: 1440, height: 900, mobile: false },
    { name: '1920x1080', width: 1920, height: 1080, mobile: false },
];

const PAGES = [
    { name: 'home', url: 'http://127.0.0.1:8000/', authType: null },
    { name: 'projects', url: 'http://127.0.0.1:8000/projects', authType: null },
    { name: 'about', url: 'http://127.0.0.1:8000/about', authType: null },
    { name: 'contact', url: 'http://127.0.0.1:8000/contact', authType: null },
    { name: 'login', url: 'http://127.0.0.1:8000/login', authType: null },
    { name: 'admin_login', url: 'http://127.0.0.1:8000/admin/login', authType: null },
    { name: 'candidate_dash', url: 'http://127.0.0.1:8000/dashboard', authType: 'candidate' },
    { name: 'admin_dash', url: 'http://127.0.0.1:8000/admin/dashboard', authType: 'admin' },
];

async function runAudit() {
    console.log('=== MULTI-VIEWPORT RESPONSIVENESS & BRAND COLOR AUDIT ===\n');

    // 1. Pre-authenticate sessions
    console.log('Authenticating candidate and admin sessions for portal verification...');
    const candSessId = await httpLogin('http://127.0.0.1:8000/login', 'testcandidate@realtysmartzpathshala.in', 'Candidate@2026');
    const adminSessId = await httpLogin('http://127.0.0.1:8000/admin/login', 'admin@realtysmartzpathshala.in', 'Admin@2026');
    console.log(`Candidate Session: ${candSessId ? 'Authenticated ✓' : 'Failed ✗'}`);
    console.log(`Admin Session: ${adminSessId ? 'Authenticated ✓' : 'Failed ✗'}\n`);

    const chrome = spawn(CHROME_PATH, [
        '--headless=new',
        '--remote-debugging-port=9227',
        `--user-data-dir=${USER_DATA}`,
        '--disable-gpu',
        '--no-sandbox'
    ]);

    await sleep(1500);

    const auditResults = [];

    try {
        const versionInfo = await fetchJson('http://127.0.0.1:9227/json/version');
        const cdp = new CDP(versionInfo.webSocketDebuggerUrl);
        await cdp.connect();

        const { targetId } = await cdp.send('Target.createTarget', { url: 'about:blank' });
        const targets = await fetchJson('http://127.0.0.1:9227/json');
        const target = targets.find(t => t.id === targetId);

        const pageCdp = new CDP(target.webSocketDebuggerUrl);
        await pageCdp.connect();
        await pageCdp.send('Network.enable');
        await pageCdp.send('Page.enable');
        await pageCdp.send('Runtime.enable');

        for (const p of PAGES) {
            console.log(`\nTesting Page: ${p.name} (${p.url})`);

            // Apply cookie if needed
            if (p.authType === 'candidate' && candSessId) {
                await pageCdp.send('Network.setCookie', {
                    name: 'PHPSESSID',
                    value: candSessId,
                    domain: '127.0.0.1',
                    path: '/'
                });
            } else if (p.authType === 'admin' && adminSessId) {
                await pageCdp.send('Network.setCookie', {
                    name: 'PHPSESSID',
                    value: adminSessId,
                    domain: '127.0.0.1',
                    path: '/'
                });
            } else {
                await pageCdp.send('Network.clearBrowserCookies');
            }

            for (const vp of VIEWPORTS) {
                // Set device metrics
                await pageCdp.send('Emulation.setDeviceMetricsOverride', {
                    width: vp.width,
                    height: vp.height,
                    deviceScaleFactor: 1,
                    mobile: vp.mobile
                });

                await pageCdp.send('Page.navigate', { url: p.url });
                await sleep(800);

                // Run client-side assertions
                const evalRes = await pageCdp.send('Runtime.evaluate', {
                    expression: `
                    (() => {
                        const winW = window.innerWidth;
                        const docW = document.documentElement.scrollWidth;
                        const bodyW = document.body.scrollWidth;
                        const maxW = Math.max(docW, bodyW);
                        const hasOverflow = maxW > (winW + 1);

                        // Check for blue color in computed styles of elements
                        let blueCount = 0;
                        const allEls = document.querySelectorAll('*');
                        for (let el of allEls) {
                            const cs = window.getComputedStyle(el);
                            const checkProp = (prop) => {
                                const val = cs.getPropertyValue(prop);
                                const match = val.match(/rgb\\((\\d+),\\s*(\\d+),\\s*(\\d+)\\)/);
                                if (match) {
                                    const r = parseInt(match[1]);
                                    const g = parseInt(match[2]);
                                    const b = parseInt(match[3]);
                                    // Blue dominant check
                                    if (b > 180 && b > r * 1.3 && b > g * 1.3) {
                                        return true;
                                    }
                                }
                                return false;
                            };
                            if (checkProp('color') || checkProp('background-color') || checkProp('border-color')) {
                                blueCount++;
                            }
                        }

                        return {
                            winW,
                            maxW,
                            hasOverflow,
                            overflowDiff: maxW - winW,
                            blueCount
                        };
                    })()
                    `,
                    returnByValue: true
                });

                const metrics = evalRes.result ? evalRes.result.value : {};

                // Capture screenshot
                const shot = await pageCdp.send('Page.captureScreenshot', { format: 'png' });
                const shotPath = path.join(SCREENSHOT_DIR, `${p.name}_${vp.name}.png`);
                fs.writeFileSync(shotPath, Buffer.from(shot.data, 'base64'));

                const passed = !metrics.hasOverflow && metrics.blueCount === 0;

                auditResults.push({
                    page: p.name,
                    viewport: vp.name,
                    width: vp.width,
                    scrollWidth: metrics.maxW,
                    overflow: metrics.hasOverflow ? `+${metrics.overflowDiff}px OVERFLOW` : '0px (clean)',
                    blueDetected: metrics.blueCount,
                    status: passed ? 'PASS' : 'FAIL',
                    screenshot: `${p.name}_${vp.name}.png`
                });

                process.stdout.write(`  [${vp.name}] ${passed ? '✓ PASS' : '✗ FAIL'} (Overflow: ${metrics.overflowDiff || 0}px, Blue: ${metrics.blueCount})\n`);
            }
        }

        pageCdp.close();
        cdp.close();
    } finally {
        chrome.kill();
    }

    console.log('\n========================================================================');
    console.log('AUDIT SUMMARY TABLE (64 VIEWPORT PERMUTATIONS)');
    console.log('========================================================================');
    console.table(auditResults);

    const failures = auditResults.filter(r => r.status === 'FAIL');
    if (failures.length === 0) {
        console.log('\nSUCCESS: All 64 page/viewport permutations PASSED with zero horizontal overflow and zero blue.');
        process.exit(0);
    } else {
        console.log(`\nFAILURE: ${failures.length} permutation(s) failed audit.`);
        process.exit(1);
    }
}

runAudit().catch(err => {
    console.error(err);
    process.exit(1);
});
