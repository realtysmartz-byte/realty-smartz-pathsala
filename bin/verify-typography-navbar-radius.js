const { spawn } = require('child_process');
const fs = require('fs');
const http = require('http');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../tests/verification_screenshots');
const USER_DATA = path.resolve(__dirname, '../tests/chrome_user_data_verify');

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
        this.eventListeners = new Map();
    }
    connect() {
        return new Promise((resolve, reject) => {
            this.ws = new WebSocket(this.wsUrl);
            this.ws.onopen = resolve;
            this.ws.onerror = reject;
            this.ws.onmessage = (msg) => {
                const res = JSON.parse(msg.data);
                if (res.method && this.eventListeners.has(res.method)) {
                    this.eventListeners.get(res.method)(res.params);
                }
                if (res.id && this.callbacks.has(res.id)) {
                    const cb = this.callbacks.get(res.id);
                    this.callbacks.delete(res.id);
                    if (res.error) cb.reject(res.error);
                    else cb.resolve(res.result);
                }
            };
        });
    }
    on(event, handler) {
        this.eventListeners.set(event, handler);
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

const REQUIRED_VIEWPORTS = [
    { name: '360x740', width: 360, height: 740, mobile: true },
    { name: '390x844', width: 390, height: 844, mobile: true },
    { name: '768x1024', width: 768, height: 1024, mobile: false },
    { name: '1280x800', width: 1280, height: 800, mobile: false },
    { name: '1440x900', width: 1440, height: 900, mobile: false }
];

async function main() {
    console.log('========================================================================');
    console.log('VERIFICATION SUITE: TYPOGRAPHY, COLOR, NAVBAR & RADIUS SYSTEM');
    console.log('========================================================================\n');

    const candSessId = await httpLogin('http://127.0.0.1:8000/login', 'testcandidate@realtysmartzpathshala.in', 'Candidate@2026');
    const adminSessId = await httpLogin('http://127.0.0.1:8000/admin/login', 'admin@realtysmartzpathshala.in', 'Admin@2026');
    console.log(`[AUTH] Candidate session: ${candSessId ? 'PASS' : 'FAIL'}`);
    console.log(`[AUTH] Admin session: ${adminSessId ? 'PASS' : 'FAIL'}\n`);

    const chrome = spawn(CHROME_PATH, [
        '--headless=new',
        '--remote-debugging-port=9228',
        `--user-data-dir=${USER_DATA}`,
        '--disable-gpu',
        '--no-sandbox'
    ]);

    await sleep(1500);

    const results = {
        navbarScrollTests: [],
        typographyTests: [],
        colorTests: [],
        radiusTests: [],
        consoleErrors: []
    };

    try {
        const versionInfo = await fetchJson('http://127.0.0.1:9228/json/version');
        const rootCdp = new CDP(versionInfo.webSocketDebuggerUrl);
        await rootCdp.connect();

        const { targetId } = await rootCdp.send('Target.createTarget', { url: 'about:blank' });
        const targets = await fetchJson('http://127.0.0.1:9228/json');
        const target = targets.find(t => t.id === targetId);

        const pageCdp = new CDP(target.webSocketDebuggerUrl);
        await pageCdp.connect();
        await pageCdp.send('Network.enable');
        await pageCdp.send('Page.enable');
        await pageCdp.send('Runtime.enable');

        pageCdp.on('Runtime.consoleAPICalled', (params) => {
            if (params.type === 'error') {
                const text = params.args.map(a => a.value || a.description || '').join(' ');
                results.consoleErrors.push({ url: target.url, error: text });
                console.error(`[CONSOLE ERROR]: ${text}`);
            }
        });

        // ================================================================
        // 1. NAVBAR SCROLL TEST AT 5 VIEWPORTS (scrollY 0, 300, and back to 0)
        // ================================================================
        console.log('--- TEST 1: Navbar Scroll & Hysteresis Across 5 Viewports ---');

        for (const vp of REQUIRED_VIEWPORTS) {
            console.log(`\nTesting Viewport: ${vp.name} (${vp.width}x${vp.height})`);

            await pageCdp.send('Emulation.setDeviceMetricsOverride', {
                width: vp.width,
                height: vp.height,
                deviceScaleFactor: 1,
                mobile: vp.mobile
            });

            await pageCdp.send('Page.navigate', { url: 'http://127.0.0.1:8000/' });
            await sleep(600);

            // Step A: scrollY = 0
            const state0 = await pageCdp.send('Runtime.evaluate', {
                expression: `(() => {
                    const header = document.getElementById('siteHeader');
                    const nav = header ? header.querySelector('.site-nav') : null;
                    if (!header || !nav) return { error: 'header or nav not found' };
                    const csH = window.getComputedStyle(header);
                    const csN = window.getComputedStyle(nav);
                    return {
                        isScrolled: header.classList.contains('is-scrolled'),
                        bg: csN.backgroundColor,
                        border: csN.borderColor,
                        shadow: csN.boxShadow,
                        maxWidth: csN.maxWidth,
                        height: csN.height,
                        scrollY: window.scrollY
                    };
                })()`,
                returnByValue: true
            });

            // Capture screenshot at scrollY 0
            const shot0 = await pageCdp.send('Page.captureScreenshot', { format: 'png' });
            fs.writeFileSync(path.join(SCREENSHOT_DIR, `home_scrollY_0_${vp.name}.png`), Buffer.from(shot0.data, 'base64'));

            // Step B: scroll to 300px
            await pageCdp.send('Runtime.evaluate', {
                expression: `window.scrollTo(0, 300); window.dispatchEvent(new Event('scroll'));`
            });
            await sleep(600);

            const state300 = await pageCdp.send('Runtime.evaluate', {
                expression: `(() => {
                    const header = document.getElementById('siteHeader');
                    const nav = header ? header.querySelector('.site-nav') : null;
                    if (!header || !nav) return { error: 'header or nav not found' };
                    const csN = window.getComputedStyle(nav);
                    return {
                        isScrolled: header.classList.contains('is-scrolled'),
                        bg: csN.backgroundColor,
                        border: csN.borderColor,
                        shadow: csN.boxShadow,
                        maxWidth: csN.maxWidth,
                        borderRadius: csN.borderRadius,
                        scrollY: window.scrollY
                    };
                })()`,
                returnByValue: true
            });

            // Capture screenshot at scrollY 300
            const shot300 = await pageCdp.send('Page.captureScreenshot', { format: 'png' });
            fs.writeFileSync(path.join(SCREENSHOT_DIR, `home_scrollY_300_${vp.name}.png`), Buffer.from(shot300.data, 'base64'));

            // Step C: scroll back to 0px
            await pageCdp.send('Runtime.evaluate', {
                expression: `window.scrollTo(0, 0); window.dispatchEvent(new Event('scroll'));`
            });
            await sleep(600);

            const stateBack0 = await pageCdp.send('Runtime.evaluate', {
                expression: `(() => {
                    const header = document.getElementById('siteHeader');
                    const nav = header ? header.querySelector('.site-nav') : null;
                    if (!header || !nav) return { error: 'header or nav not found' };
                    const csN = window.getComputedStyle(nav);
                    return {
                        isScrolled: header.classList.contains('is-scrolled'),
                        bg: csN.backgroundColor,
                        border: csN.borderColor,
                        shadow: csN.boxShadow,
                        maxWidth: csN.maxWidth,
                        scrollY: window.scrollY
                    };
                })()`,
                returnByValue: true
            });

            // Capture screenshot at scroll back to 0
            const shotBack0 = await pageCdp.send('Page.captureScreenshot', { format: 'png' });
            fs.writeFileSync(path.join(SCREENSHOT_DIR, `home_scrollY_back0_${vp.name}.png`), Buffer.from(shotBack0.data, 'base64'));

            const s0 = state0.result.value;
            const s300 = state300.result.value;
            const sBack = stateBack0.result.value;

            const pass0 = !s0.isScrolled && (s0.bg === 'rgba(0, 0, 0, 0)' || s0.bg === 'transparent' || s0.bg.includes('/ 0'));
            const maxWVal = parseFloat(s300.maxWidth);
            // On desktop/large screens max-width is 760px; on smaller screens it's <= 760px
            const pass300 = s300.isScrolled && (maxWVal <= 761) && (s300.borderRadius === '999px' || parseFloat(s300.borderRadius) >= 200);
            const passBack = !sBack.isScrolled && (sBack.bg === 'rgba(0, 0, 0, 0)' || sBack.bg === 'transparent' || sBack.bg.includes('/ 0'));

            results.navbarScrollTests.push({
                viewport: vp.name,
                scrollY_0: pass0 ? 'PASS (Transparent, no strip)' : `FAIL (${JSON.stringify(s0)})`,
                scrollY_300: pass300 ? 'PASS (760px Pill, 999px radius)' : `FAIL (${JSON.stringify(s300)})`,
                scrollY_back0: passBack ? 'PASS (Dissolved back to hero)' : `FAIL (${JSON.stringify(sBack)})`,
                allPassed: pass0 && pass300 && passBack
            });

            console.log(`  scrollY 0:     ${pass0 ? '✓ PASS (Transparent, seamless)' : '✗ FAIL'}`);
            console.log(`  scrollY 300:   ${pass300 ? '✓ PASS (Floating pill 760px)' : '✗ FAIL'}`);
            console.log(`  scrollY back0: ${passBack ? '✓ PASS (Seamless dissolve back)' : '✗ FAIL'}`);
        }

        // ================================================================
        // 2. AUTOMATED AUDIT ON PAGES: TYPOGRAPHY, ZERO BLUE, STRICT RADII
        // ================================================================
        console.log('\n--- TEST 2: Computed Typography, Zero Blue & Strict Border-Radius ---');

        const TEST_PAGES = [
            { name: 'Home', url: 'http://127.0.0.1:8000/', auth: null },
            { name: 'Projects', url: 'http://127.0.0.1:8000/projects', auth: null },
            { name: 'About', url: 'http://127.0.0.1:8000/about', auth: null },
            { name: 'Contact', url: 'http://127.0.0.1:8000/contact', auth: null },
            { name: 'Candidate Login', url: 'http://127.0.0.1:8000/login', auth: null },
            { name: 'Admin Login', url: 'http://127.0.0.1:8000/admin/login', auth: null },
            { name: 'Candidate Dashboard', url: 'http://127.0.0.1:8000/dashboard', auth: 'candidate' },
            { name: 'Aptitude Instructions', url: 'http://127.0.0.1:8000/dashboard/test/instructions', auth: 'candidate' },
            { name: 'Aptitude Active Test', url: 'http://127.0.0.1:8000/dashboard/test/start', auth: 'candidate' },
            { name: 'Admin Dashboard', url: 'http://127.0.0.1:8000/admin/dashboard', auth: 'admin' }
        ];

        // Standard desktop viewport for page checks
        await pageCdp.send('Emulation.setDeviceMetricsOverride', {
            width: 1440,
            height: 900,
            deviceScaleFactor: 1,
            mobile: false
        });

        for (const pg of TEST_PAGES) {
            console.log(`\nAuditing Page: ${pg.name} (${pg.url})`);

            if (pg.auth === 'candidate') {
                await pageCdp.send('Network.setCookie', {
                    name: 'PHPSESSID',
                    value: candSessId,
                    domain: '127.0.0.1',
                    path: '/'
                });
            } else if (pg.auth === 'admin') {
                await pageCdp.send('Network.setCookie', {
                    name: 'PHPSESSID',
                    value: adminSessId,
                    domain: '127.0.0.1',
                    path: '/'
                });
            } else {
                await pageCdp.send('Network.clearBrowserCookies');
            }

            await pageCdp.send('Page.navigate', { url: pg.url });
            await sleep(800);

            // Screenshot the page
            const pageShot = await pageCdp.send('Page.captureScreenshot', { format: 'png' });
            const pageShotName = `page_${pg.name.toLowerCase().replace(/[^a-z0-9]+/g, '_')}.png`;
            fs.writeFileSync(path.join(SCREENSHOT_DIR, pageShotName), Buffer.from(pageShot.data, 'base64'));

            const evalAudit = await pageCdp.send('Runtime.evaluate', {
                expression: `(() => {
                    const winW = window.innerWidth;
                    const maxW = Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);
                    const hasOverflow = maxW > (winW + 1);

                    // 1. Check fonts on key elements
                    const elementsToCheck = ['body', 'h1', 'h2', 'h3', 'p', 'button', 'input', 'th', 'td'];
                    const fontFamilies = {};
                    let nonDMSansCount = 0;
                    for (const sel of elementsToCheck) {
                        const el = document.querySelector(sel);
                        if (el) {
                            const ff = window.getComputedStyle(el).fontFamily;
                            fontFamilies[sel] = ff;
                            if (!ff.includes('DM Sans')) {
                                nonDMSansCount++;
                            }
                        }
                    }

                    // 2. Check for Blue colors (#0d6efd, rgb(13, 110, 253), or hue 200-260)
                    function rgbToHsl(r, g, b) {
                        r /= 255; g /= 255; b /= 255;
                        const max = Math.max(r, g, b), min = Math.min(r, g, b);
                        let h, s, l = (max + min) / 2;
                        if (max === min) { h = s = 0; }
                        else {
                            const d = max - min;
                            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                            switch(max) {
                                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                                case g: h = (b - r) / d + 2; break;
                                case b: h = (r - g) / d + 4; break;
                            }
                            h *= 60;
                        }
                        return [h, s, l];
                    }

                    let blueViolations = [];
                    const allEls = document.querySelectorAll('*');
                    for (const el of allEls) {
                        const cs = window.getComputedStyle(el);
                        for (const prop of ['color', 'backgroundColor', 'borderColor']) {
                            const val = cs[prop];
                            if (!val || val === 'rgba(0, 0, 0, 0)' || val === 'transparent') continue;
                            const m = val.match(/rgba?\\((\\d+),\\s*(\\d+),\\s*(\\d+)/);
                            if (m) {
                                const r = parseInt(m[1]), g = parseInt(m[2]), b = parseInt(m[3]);
                                const [h, s, l] = rgbToHsl(r, g, b);
                                // Blue hue check between 200 and 260 with saturation > 0.25 and lightness < 0.95
                                if (h >= 200 && h <= 260 && s > 0.25 && l < 0.95) {
                                    blueViolations.push({ tag: el.tagName, class: el.className, prop, val, h: Math.round(h) });
                                }
                            }
                        }
                    }

                    // 3. Check border-radius tokens
                    // Permitted tokens: 0px, 8px, 12px, 20px, 999px, 50% (and standard inner derivatives like 7px, 11px, 19px)
                    const allowedRadii = new Set([
                        '0px', '8px', '12px', '20px', '999px', '50%',
                        '7px', '11px', '19px' // calc(token - 1px)
                    ]);

                    let radiusViolations = [];
                    for (const el of allEls) {
                        const cs = window.getComputedStyle(el);
                        const r = cs.borderRadius;
                        if (!r || r === '0px') continue;
                        
                        // Check if true circle (50% or width == height and r == width/2)
                        const w = Math.round(parseFloat(cs.width));
                        const h = Math.round(parseFloat(cs.height));
                        const rPx = Math.round(parseFloat(r));
                        if (w > 0 && Math.abs(w - h) <= 2 && Math.abs(rPx - w/2) <= 1) {
                            continue; // 50% circle
                        }

                        // Parse 4 corners
                        const parts = r.split(' ');
                        for (const p of parts) {
                            if (!allowedRadii.has(p)) {
                                radiusViolations.push({ tag: el.tagName, class: el.className, borderRadius: r });
                                break;
                            }
                        }
                    }

                    let overflowCause = null;
                    if (hasOverflow) {
                        for (const el of document.querySelectorAll('*')) {
                            const rect = el.getBoundingClientRect();
                            if (rect.right > winW + 1) {
                                overflowCause = { tag: el.tagName, class: el.className, id: el.id, right: Math.round(rect.right), winW };
                                break;
                            }
                        }
                    }

                    return {
                        hasOverflow,
                        overflowCause,
                        fontFamilies,
                        nonDMSansCount,
                        blueViolations: blueViolations.slice(0, 5),
                        blueCount: blueViolations.length,
                        radiusViolations: radiusViolations.slice(0, 5),
                        radiusViolationCount: radiusViolations.length
                    };
                })()`,
                returnByValue: true
            });

            const audit = evalAudit.result.value;

            console.log(`  Fonts check:       ${audit.nonDMSansCount === 0 ? '✓ PASS (All DM Sans)' : '✗ FAIL (' + audit.nonDMSansCount + ' elements non-DM-Sans)'}`);
            console.log(`  Blue color check:  ${audit.blueCount === 0 ? '✓ PASS (0 blue)' : '✗ FAIL (' + audit.blueCount + ' violations)'}`);
            console.log(`  Radius tokens:     ${audit.radiusViolationCount === 0 ? '✓ PASS (100% token compliant)' : '✗ FAIL (' + audit.radiusViolationCount + ' violations: ' + JSON.stringify(audit.radiusViolations) + ')'}`);
            console.log(`  Horizontal scroll: ${!audit.hasOverflow ? '✓ PASS (Clean)' : '✗ FAIL (Overflowing: ' + JSON.stringify(audit.overflowCause) + ')'}`);

            results.typographyTests.push({ page: pg.name, passed: audit.nonDMSansCount === 0, fonts: audit.fontFamilies });
            results.colorTests.push({ page: pg.name, passed: audit.blueCount === 0, blueCount: audit.blueCount });
            results.radiusTests.push({ page: pg.name, passed: audit.radiusViolationCount === 0, violations: audit.radiusViolationCount });
        }

        pageCdp.close();
        rootCdp.close();
    } finally {
        chrome.kill();
    }

    console.log('\n========================================================================');
    console.log('FINAL VERIFICATION SUMMARY');
    console.log('========================================================================');
    console.table(results.navbarScrollTests);
    console.log('\nConsole Errors Count: ' + results.consoleErrors.length);

    const allNavbarPassed = results.navbarScrollTests.every(t => t.allPassed);
    const allTypographyPassed = results.typographyTests.every(t => t.passed);
    const allColorsPassed = results.colorTests.every(t => t.passed);
    const allRadiusPassed = results.radiusTests.every(t => t.passed);
    const zeroConsoleErrors = results.consoleErrors.length === 0;

    if (allNavbarPassed && allTypographyPassed && allColorsPassed && allRadiusPassed && zeroConsoleErrors) {
        console.log('\n✓ ALL CHECKS PASSED: 100% compliant with DM Sans, Deep Forest Green, Scroll-Shrink Navbar, and Strict Border Radius Tokens.');
        process.exit(0);
    } else {
        console.log('\n✗ AUDIT REPORTED ISSUES: Please check the failures above.');
        process.exit(1);
    }
}

main().catch(err => {
    console.error(err);
    process.exit(1);
});
