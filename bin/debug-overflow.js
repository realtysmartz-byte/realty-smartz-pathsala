const { chromium } = require('@playwright/test');

(async () => {
    const browser = await chromium.launch({ channel: 'chrome', headless: true });
    const page = await browser.newPage();
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('http://127.0.0.1:8000/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');

    const vps = [
        { width: 360, height: 740 },
        { width: 768, height: 1024 },
        { width: 1024, height: 768 },
        { width: 1440, height: 900 },
        { width: 1920, height: 1080 }
    ];

    for (const vp of vps) {
        await page.setViewportSize(vp);
        await page.goto('http://127.0.0.1:8000/admin/dashboard');
        await page.waitForLoadState('networkidle');
        const res = await page.evaluate((vp) => {
            const docW = document.documentElement.clientWidth;
            const scrollW = document.documentElement.scrollWidth;
            const bad = [];
            document.querySelectorAll('*').forEach(el => {
                const r = el.getBoundingClientRect();
                if (r.right > docW + 1) {
                    bad.push({ tag: el.tagName, class: el.className, right: Math.round(r.right), docW });
                }
            });
            return { vp: `${vp.width}x${vp.height}`, docW, scrollW, hasHScroll: scrollW > docW, bad: bad.slice(0, 5) };
        }, vp);
        console.log(JSON.stringify(res));
    }
    await browser.close();
})();
