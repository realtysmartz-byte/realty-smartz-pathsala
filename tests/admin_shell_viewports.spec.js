const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = path.resolve(__dirname, 'screenshots');
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

const VIEWPORTS = [
    { width: 360, height: 740, name: 'mobile_360x740' },
    { width: 768, height: 1024, name: 'tablet_portrait_768x1024' },
    { width: 1024, height: 768, name: 'tablet_landscape_1024x768' },
    { width: 1440, height: 900, name: 'desktop_1440x900' },
    { width: 1920, height: 1080, name: 'desktop_1920x1080' }
];

async function loginAdmin(page) {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
}

test.describe('Admin Shell & Viewport Audits', () => {

    for (const vp of VIEWPORTS) {
        test(`Verify layout and shell at ${vp.width}x${vp.height} (${vp.name})`, async ({ page }) => {
            await page.setViewportSize({ width: vp.width, height: vp.height });
            await loginAdmin(page);

            await page.goto('/admin/dashboard');
            await page.waitForLoadState('networkidle');

            // 1. Check fonts: DM Sans
            const bodyFont = await page.evaluate(() => window.getComputedStyle(document.body).fontFamily);
            expect(bodyFont.toLowerCase()).toContain('dm sans');

            // 2. Check no horizontal scrollbar
            const hasHScroll = await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
            expect(hasHScroll).toBeFalsy();

            // 3. Desktop / Laptop checks (width >= 1024)
            if (vp.width >= 1024) {
                const sidebar = page.locator('.sidebar');
                await expect(sidebar).toBeVisible();

                // Sticky position checks
                const sidebarPos = await sidebar.evaluate(el => {
                    const style = window.getComputedStyle(el);
                    const rect = el.getBoundingClientRect();
                    return {
                        position: style.position,
                        top: rect.top,
                        height: rect.height,
                        width: rect.width,
                        left: rect.left
                    };
                });

                expect(sidebarPos.position).toBe('sticky');
                expect(sidebarPos.top).toBe(0);
                expect(sidebarPos.height).toBe(vp.height);
                expect(Math.round(sidebarPos.width)).toBe(240);

                // Content column starts exactly at x = 240px (no gap)
                const main = page.locator('.main');
                const mainRect = await main.evaluate(el => el.getBoundingClientRect());
                expect(Math.round(mainRect.left)).toBe(240);

                // Top bar spans full width of content column
                const topbar = page.locator('.topbar');
                const topbarRect = await topbar.evaluate(el => el.getBoundingClientRect());
                expect(Math.round(topbarRect.left)).toBe(240);
                expect(Math.round(topbarRect.width)).toBe(vp.width - 240);

                // Sidebar footer pinned to bottom (margin-top: auto)
                const footerAlignment = await page.locator('.sidebar .sidebar-footer').evaluate((el) => {
                    const sb = el.closest('.sidebar');
                    const sbRect = sb.getBoundingClientRect();
                    const fRect = el.getBoundingClientRect();
                    return {
                        pinnedToBottom: Math.abs(sbRect.bottom - fRect.bottom) <= 2,
                        bottomDiff: sbRect.bottom - fRect.bottom
                    };
                });
                expect(footerAlignment.pinnedToBottom).toBeTruthy();

                // Scroll page 2000px: verify sidebar top is still 0 and height equals viewport
                await page.evaluate(() => window.scrollTo(0, 2000));
                await page.waitForTimeout(100);

                const scrolledSidebarTop = await sidebar.evaluate(el => el.getBoundingClientRect().top);
                const scrolledSidebarHeight = await sidebar.evaluate(el => el.getBoundingClientRect().height);
                expect(Math.abs(scrolledSidebarTop)).toBeLessThan(1);
                expect(Math.abs(scrolledSidebarHeight - vp.height)).toBeLessThan(1);

                await page.evaluate(() => window.scrollTo(0, 0));
            }

            // 4. Check that no button or link is empty (must have text or SVG)
            const emptyElements = await page.evaluate(() => {
                const elements = document.querySelectorAll('button, a');
                const empty = [];
                for (const el of elements) {
                    if (el.offsetParent === null) continue; // ignore hidden
                    const text = el.innerText.trim();
                    const svg = el.querySelector('svg');
                    const img = el.querySelector('img');
                    const aria = el.getAttribute('aria-label');
                    if (!text && !svg && !img && !aria) {
                        empty.push({
                            tag: el.tagName,
                            html: el.outerHTML.substring(0, 80)
                        });
                    }
                }
                return empty;
            });
            expect(emptyElements.length).toBe(0);

            // 5. Check radius tokens: every border-radius matches 0, 8px, 12px, 20px, 999px / 50%
            const invalidRadius = await page.evaluate(() => {
                const valid = ['0px', '8px', '12px', '20px', '999px', '9999px', '50%'];
                const bad = [];
                const all = document.querySelectorAll('.app-shell *');
                for (const el of all) {
                    const r = window.getComputedStyle(el).borderRadius;
                    if (r && r !== '0px' && !valid.some(v => r.includes(v))) {
                        // Allow small subpixel variations like 7.99px or 8px
                        const num = parseFloat(r);
                        if (![0, 8, 12, 20, 999].includes(Math.round(num)) && !r.includes('50%')) {
                            bad.push({ tag: el.tagName, class: el.className, radius: r });
                        }
                    }
                }
                return bad.slice(0, 5);
            });
            expect(invalidRadius.length).toBe(0);

            // Screenshot
            const screenshotPath = path.join(SCREENSHOT_DIR, `admin_dashboard_${vp.name}.png`);
            await page.screenshot({ path: screenshotPath, fullPage: false });
        });
    }
});
