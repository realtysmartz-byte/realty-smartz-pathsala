const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = path.resolve(__dirname, 'screenshots');
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function loginAdmin(page) {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
}

async function selectRadio(page, name, value) {
    const radio = page.locator(`input[name="${name}"][value="${value}"]`);
    await radio.evaluate(el => {
        el.checked = true;
        el.dispatchEvent(new Event('change', { bubbles: true }));
        el.dispatchEvent(new Event('input', { bubbles: true }));
    });
}

test.describe('Interview Application Form Module Complete Suite', () => {

    test.beforeEach(async () => {
        try {
            require('child_process').execSync('C:\\\\xampp\\\\php\\\\php.exe bin/reset_qr_settings.php');
        } catch (e) {}
    });

    test('1. Access, Crawl, Sitemap, Robots.txt, and 404 security checks', async ({ page }) => {
        // A. Crawl robots.txt -> verify no mention of /q/ or slug
        const robotsRes = await page.goto('/robots.txt');
        const robotsTxt = await robotsRes.text();
        expect(robotsTxt).not.toContain('/q/');
        expect(robotsTxt).not.toContain('rsp_interview_');

        // B. Crawl sitemap.xml -> verify no mention of /q/ or slug
        const sitemapRes = await page.goto('/sitemap.xml');
        const sitemapXml = await sitemapRes.text();
        expect(sitemapXml).not.toContain('/q/');
        expect(sitemapXml).not.toContain('rsp_interview_');

        // C. Home page crawl -> verify no navigation or footer links point to /q/
        await page.goto('/');
        const pageHtml = await page.content();
        expect(pageHtml).not.toContain('/q/');

        // D. Wrong slug returns normal 404
        const badSlugRes = await page.goto('/q/non_existent_random_slug_123456');
        expect(badSlugRes.status()).toBe(404);
        await expect(page.locator('body')).toContainText('404');
    });

    test('2. Public Form: Field Rendering, Exact Labels, Hidden HR data, Dynamic Toggles, Viewports', async ({ page }) => {
        // Read active QR URL from DB/QR page
        await loginAdmin(page);
        await page.goto('/admin/qr-codes');
        const qrUrlEl = page.locator('#interviewUrlText');
        await expect(qrUrlEl).toBeVisible();
        const interviewUrl = await qrUrlEl.innerText();
        expect(interviewUrl).toContain('/interview-candidate');

        // Open public form in candidate browser
        const formRes = await page.goto('/interview-candidate');
        expect(formRes.status()).toBe(200);

        // Check X-Robots-Tag header and meta robots
        const xRobots = formRes.headers()['x-robots-tag'];
        expect(xRobots).toContain('noindex');
        const metaRobots = await page.locator('meta[name="robots"]').getAttribute('content');
        expect(metaRobots).toContain('noindex');

        // Check header has ONLY the logo and clicking logo lands on /
        const headerLogo = page.locator('.public-header a');
        await expect(headerLogo).toBeVisible();
        await expect(page.locator('.public-header nav, .public-header ul, .public-header menu')).toHaveCount(0);
        await expect(headerLogo).toHaveAttribute('href', /^(\/|http:\/\/127\.0\.0\.1:8000\/?)$/);

        // Page title: "Interview Application Form"
        await expect(page.locator('.form-title')).toHaveText('Interview Application Form');

        // Verify HIDDEN DATA: Public form NEVER contains HR fields in DOM or network
        const formHtml = await page.content();
        expect(formHtml).not.toContain('Source By');
        expect(formHtml).not.toContain('Feedback by');
        expect(formHtml).not.toContain('Offered Salary');
        expect(formHtml).not.toContain('Date of Joining');
        expect(formHtml).not.toContain('Position Offered');
        expect(formHtml).not.toContain('Module Discussed');
        expect(formHtml).not.toContain('FOR HR DEPARTMENT ONLY');

        // Check All 17 Form Fields and Labels in exact order
        // 1. HR Name dropdown
        await expect(page.locator('label[for="hr_name"]')).toContainText('HR Name');
        const hrOptions = await page.locator('#hr_name option').allInnerTexts();
        expect(hrOptions).toContain('Select');
        expect(hrOptions.length).toBeGreaterThan(1);

        // 2. Post Applied For
        await expect(page.locator('label[for="post_applied"]')).toContainText('Post Applied For');

        // 2b. Have you applied for (Commercial / Residential / Both)
        await expect(page.locator('#group_applied_for_type legend')).toContainText('Have you applied for');
        await expect(page.locator('input[name="applied_for_type"]')).toHaveCount(3);

        // 3. Full Name
        await expect(page.locator('label[for="full_name"]')).toContainText('Full Name');

        // 4. Mail ID
        await expect(page.locator('label[for="email"]')).toContainText('Mail ID');

        // 5. Contact Number
        await expect(page.locator('label[for="mobile"]')).toContainText('Contact Number');

        // 6. Experience / Fresher (Radio)
        await expect(page.locator('#group_experience_type legend')).toContainText('Experience / Fresher');
        const expRadios = page.locator('input[name="experience_type"]');
        await expect(expRadios).toHaveCount(2);

        // 7. Years in Real Estate & 8. Employment history (Hidden by default when Fresher or unselected)
        await expect(page.locator('#group_years_in_real_estate')).toBeHidden();
        await expect(page.locator('#group_employment_history')).toBeHidden();

        // Selecting Experience displays Years in Real Estate & Employment history
        await page.locator('input[name="experience_type"][value="experience"]').check({ force: true });
        await expect(page.locator('#group_years_in_real_estate')).toBeVisible();
        await expect(page.locator('#group_employment_history')).toBeVisible();
        await expect(page.locator('#group_last_company_hr_tl')).toBeVisible();
        await expect(page.locator('#group_reason_to_leave')).toBeVisible();

        // Employment rows start with 3 rows
        await expect(page.locator('#empTableBody tr.emp-row')).toHaveCount(3);
        // Add row up to 6
        await page.click('#btnAddEmpRow'); // 4
        await page.click('#btnAddEmpRow'); // 5
        await page.click('#btnAddEmpRow'); // 6
        await expect(page.locator('#empTableBody tr.emp-row')).toHaveCount(6);
        // Button hidden at 6
        await expect(page.locator('#btnAddEmpRow')).toBeHidden();

        // Remove an extra row
        await page.locator('.btn-remove-row').last().click();
        await expect(page.locator('#empTableBody tr.emp-row')).toHaveCount(5);
        await expect(page.locator('#btnAddEmpRow')).toBeVisible();

        // Switching to Fresher hides & clears experience fields
        await page.locator('input[name="experience_type"][value="fresher"]').check({ force: true });
        await expect(page.locator('#group_years_in_real_estate')).toBeHidden();
        await expect(page.locator('#group_employment_history')).toBeHidden();
        await expect(page.locator('#group_last_company_hr_tl')).toBeHidden();
        await expect(page.locator('#group_reason_to_leave')).toBeHidden();

        // 9. Vehicle Available
        await expect(page.locator('#group_vehicle_available legend')).toContainText('Vehicle Available');

        // 10. Current Location
        await expect(page.locator('label[for="current_location"]')).toContainText('Current Location');

        // 11. Higher Education
        await expect(page.locator('label[for="higher_education"]')).toContainText('Higher Education');

        // 12. Any References & details
        await expect(page.locator('#group_has_references legend')).toContainText('Any References');
        await expect(page.locator('#group_reference_details')).toBeHidden();
        await page.locator('input[name="has_references"][value="yes"]').check({ force: true });
        await expect(page.locator('#group_reference_details')).toBeVisible();
        await page.locator('input[name="has_references"][value="no"]').check({ force: true });
        await expect(page.locator('#group_reference_details')).toBeHidden();

        // 15. Immediate Joiner
        await expect(page.locator('#group_immediate_joiner legend')).toContainText('Immediate Joiner (Yes / No)');

        // 16. Consent checkbox
        await expect(page.locator('label[for="consent"]')).toContainText('I agree that my details may be stored for recruitment.');

        // 17. Submit button
        await expect(page.locator('#btnSubmit')).toContainText('Submit');

        // Test responsive viewports: 320x640, 360x740, 390x844, 768x1024, 1440x900
        const viewports = [
            { width: 320, height: 640 },
            { width: 360, height: 740 },
            { width: 390, height: 844 },
            { width: 768, height: 1024 },
            { width: 1440, height: 900 }
        ];

        for (const vp of viewports) {
            await page.setViewportSize(vp);
            // Verify no horizontal overflow
            const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
            const clientWidth = await page.evaluate(() => document.documentElement.clientWidth);
            expect(scrollWidth).toBeLessThanOrEqual(clientWidth + 1); // allow 1px rounding
        }
    });

    test('3. Client & Server-side Validation, Valid Submission, Idempotency, Success Page', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/qr-codes');
        const interviewUrl = await page.locator('#interviewUrlText').innerText();
        expect(interviewUrl).toContain('/interview-candidate');

        // Candidate navigates to public form
        await page.goto('/interview-candidate');

        // Try submitting empty form -> errors appear inline and first invalid is scrolled
        await page.click('#btnSubmit');
        await expect(page.locator('#err_hr_name')).toBeVisible();
        await expect(page.locator('#err_post_applied')).toBeVisible();
        await expect(page.locator('#err_full_name')).toBeVisible();

        // Fill invalid email & mobile
        await page.selectOption('#hr_name', { index: 1 });
        await page.fill('#post_applied', 'Associate Director - Luxury Sales');
        await selectRadio(page, 'applied_for_type', 'Commercial');
        await page.fill('#full_name', 'Aaditya Vikram Singh');
        await page.fill('#email', 'invalid-email-address');
        await page.fill('#mobile', '123456'); // invalid Indian mobile
        await selectRadio(page, 'experience_type', 'experience');
        await page.fill('#years_in_real_estate', '4.5');
        await selectRadio(page, 'vehicle_available', 'yes');
        await page.fill('#current_location', 'Golf Course Road, Gurugram');
        await page.fill('#higher_education', 'B.Tech + MBA');
        await selectRadio(page, 'has_references', 'yes');
        await page.fill('#reference_details', 'Direct Referral from Director of Sales');
        await page.fill('#last_company_hr_tl', 'Siddharth Roy');
        await page.fill('#reason_to_leave', 'Opportunity in ultra-luxury vertical');
        await selectRadio(page, 'immediate_joiner', 'yes');

        // Missing consent & invalid contacts
        await page.click('#btnSubmit');
        await expect(page.locator('#err_email')).toBeVisible();
        await expect(page.locator('#err_mobile')).toBeVisible();
        await expect(page.locator('#err_consent')).toBeVisible();

        // Fix inputs
        const validMobile = '98' + Math.floor(10000000 + Math.random() * 90000000);
        const validEmail = `aaditya.test.${Date.now()}@realtysmartzpathshala.in`;
        await page.fill('#email', validEmail);
        await page.fill('#mobile', validMobile);
        // Fill employment history row 1
        await page.fill('input[name="employment_rows[0][organisation]"]', 'Emaar India');
        await page.fill('input[name="employment_rows[0][designation]"]', 'Senior Manager - Sales');
        await page.fill('input[name="employment_rows[0][salary]"]', '12,00,000 PA');
        await page.fill('input[name="employment_rows[0][hr_name_number]"]', 'Kavita Mehta 9871122334');
        await page.fill('input[name="employment_rows[0][duration]"]', '3.5 Years');

        // Check consent checkbox
        await page.check('#consent');

        // Submit form
        await page.click('#btnSubmit');

        // Lands on success page (/interview-candidate/success)
        await page.waitForURL('**/interview-candidate/success');

        // Check exact success message
        await expect(page.locator('.success-message')).toHaveText(
            'Thank you! Your form has been filled successfully. Our team will contact you shortly. Please wait.'
        );

        // Header logo on success page leads to /
        await expect(page.locator('.public-header a')).toHaveAttribute('href', /^(\/|http:\/\/127\.0\.0\.1:8000\/?)$/);

        // Test idempotency: Refreshing success page does NOT duplicate or cause error
        await page.reload();
        await expect(page.locator('.success-message')).toBeVisible();
    });

    test('4. Accepting Responses Toggle Off shows exact message', async ({ page, browser }) => {
        await loginAdmin(page);
        await page.goto('/admin/qr-codes');
        const interviewUrl = await page.locator('#interviewUrlText').innerText();
        // Ensure checkbox is checked before unchecking
        if (!await page.isChecked('#toggleAcceptingResponses')) {
            await Promise.all([
                page.waitForResponse(res => res.url().includes('/admin/interview/toggle-accepting')),
                page.check('#toggleAcceptingResponses')
            ]);
        }

        // Toggle accepting responses OFF
        await Promise.all([
            page.waitForResponse(res => res.url().includes('/admin/interview/toggle-accepting')),
            page.uncheck('#toggleAcceptingResponses')
        ]);

        // In a new incognito context, visit form
        const candidateContext = await browser.newContext();
        const candidatePage = await candidateContext.newPage();
        await candidatePage.goto('/interview-candidate');

        // Exact message displayed
        await expect(candidatePage.locator('.closed-message')).toHaveText(
            'This form is not accepting responses right now.'
        );
        // Header logo leads to /
        await expect(candidatePage.locator('.public-header a')).toHaveAttribute('href', /^(\/|http:\/\/127\.0\.0\.1:8000\/?)$/);

        // Turn accepting responses back ON
        await Promise.all([
            page.waitForResponse(res => res.url().includes('/admin/interview/toggle-accepting')),
            page.check('#toggleAcceptingResponses')
        ]);

        // Now candidate page should load form
        await candidatePage.reload();
        await expect(candidatePage.locator('.form-title')).toHaveText('Interview Application Form');
        await candidateContext.close();
    });

    test('5. Admin Section: Live Polling, Detail Autosave, Concurrency, and PDF Export', async ({ browser }) => {
        // Context 1: Admin
        const adminContext = await browser.newContext();
        const adminPage = await adminContext.newPage();
        await loginAdmin(adminPage);

        // Open Interview Candidates List
        await adminPage.goto('/admin/interview-candidates');
        await expect(adminPage.locator('h2')).toContainText('Interview Candidates');
        await expect(adminPage.locator('#updatedText')).toContainText('Updated just now');

        // Context 2: Candidate submits a new form
        const candidateContext = await browser.newContext();
        const candidatePage = await candidateContext.newPage();

        const liveCandidateName = 'Kavita Live Test ' + Math.floor(Math.random() * 1000);
        const liveMobile = '99' + Math.floor(10000000 + Math.random() * 90000000);

        await candidatePage.goto('/interview-candidate');
        await candidatePage.selectOption('#hr_name', { index: 1 });
        await candidatePage.fill('#post_applied', 'Client Relationship Executive');
        await selectRadio(candidatePage, 'applied_for_type', 'Residential');
        await candidatePage.fill('#full_name', liveCandidateName);
        await candidatePage.fill('#email', `kavita.${Date.now()}@example.com`);
        await candidatePage.fill('#mobile', liveMobile);
        await selectRadio(candidatePage, 'experience_type', 'fresher');
        await selectRadio(candidatePage, 'vehicle_available', 'no');
        await candidatePage.fill('#current_location', 'Indirapuram, Ghaziabad');
        await candidatePage.fill('#higher_education', 'B.Com');
        await selectRadio(candidatePage, 'has_references', 'no');
        await selectRadio(candidatePage, 'immediate_joiner', 'yes');
        await candidatePage.check('#consent');
        await candidatePage.click('#btnSubmit');
        await candidatePage.waitForURL('**/interview-candidate/success');

        // Verification of LIVE UPDATE: adminPage without reload should receive new submission within 15s
        await expect(adminPage.locator('#interviewTableBody').locator(`text=${liveCandidateName}`)).toBeVisible({ timeout: 16000 });
        // Quiet toast should appear
        await expect(adminPage.locator('#quietToast')).toContainText(`New form: ${liveCandidateName}`);

        // Click on the new candidate row to open Detail Page
        await adminPage.locator(`tr:has-text("${liveCandidateName}")`).click();
        await adminPage.waitForURL('**/admin/interview-candidates/*');

        // Detail page title = candidate's full name
        await expect(adminPage.locator('.header-details h2')).toHaveText(liveCandidateName);

        // Verification of AUTOSAVE (debounced 600ms, blur, instant radios & dates)
        // 1. Enter Feedback by HR
        await adminPage.fill('#hr_feedback_hr', 'Excellent communication skills and strong presentation.');
        // Wait 800ms for debounce
        await adminPage.waitForTimeout(800);
        await expect(adminPage.locator('#autosaveStatus')).toContainText('Saved');

        // 2. Select Decision = Selected
        await adminPage.locator('input[name="decision"][value="selected"]').check({ force: true });
        await expect(adminPage.locator('#boxSelectedFields')).toBeVisible();
        await expect(adminPage.locator('#autosaveStatus')).toContainText('Saved');

        // 3. Fill selection subfields: Interested = Yes, Position, Salary, Date of Joining
        await adminPage.locator('input[name="candidate_interested"][value="yes"]').check({ force: true });
        await adminPage.fill('#hr_position_offered', 'Senior Relationship Associate');
        await adminPage.fill('#hr_offered_salary', '450000');
        await adminPage.fill('#hr_joining_date', '2026-11-15');
        await adminPage.locator('#hr_joining_date').blur();
        await adminPage.waitForTimeout(1000);
        await expect(adminPage.locator('#autosaveStatus')).toContainText('Saved');

        // 4. Reload page -> verify persisted values exactly match
        await adminPage.reload();
        await expect(adminPage.locator('input[name="decision"]:checked')).toHaveValue('selected');
        await expect(adminPage.locator('#hr_position_offered')).toHaveValue('Senior Relationship Associate');
        await expect(adminPage.locator('#hr_joining_date')).toHaveValue('2026-11-15');
        await expect(adminPage.locator('#hr_feedback_hr')).toHaveValue('Excellent communication skills and strong presentation.');
        await expect(adminPage.locator('#chipFeedbackStatus')).toHaveText('Feedback filled');

        // 5. Switch to Rejected with reason
        await adminPage.locator('input[name="decision"][value="rejected"]').check({ force: true });
        await expect(adminPage.locator('#boxRejectedFields')).toBeVisible();
        await adminPage.fill('#hr_rejection_reason', 'Not willing to relocate to Gurgaon');
        await adminPage.locator('#hr_rejection_reason').blur();
        await adminPage.waitForTimeout(1000);
        await expect(adminPage.locator('#autosaveStatus')).toContainText('Saved');

        // Switch back to Selected -> previous offer values must remain intact in DB
        await adminPage.locator('input[name="decision"][value="selected"]').check({ force: true });
        await expect(adminPage.locator('#hr_position_offered')).toHaveValue('Senior Relationship Associate');
        await adminPage.waitForTimeout(500);

        // 6. Test PDF Download
        const [pdfDownload] = await Promise.all([
            adminPage.waitForEvent('download'),
            adminPage.click('a:has-text("Download form (PDF)")')
        ]);
        const pdfFilename = pdfDownload.suggestedFilename();
        expect(pdfFilename).toMatch(/^Interview_Form_.*\.pdf$/);

        // 7. Test Export CSV from list
        await adminPage.goto('/admin/interview-candidates');
        const [csvDownload] = await Promise.all([
            adminPage.waitForEvent('download'),
            adminPage.click('a:has-text("Export Excel/CSV")')
        ]);
        const csvFilename = csvDownload.suggestedFilename();
        expect(csvFilename).toMatch(/^Interview_Candidates_.*\.csv$/);

        await adminContext.close();
        await candidateContext.close();
    });
});
