const { test, expect } = require('@playwright/test');
const { execSync } = require('child_process');

function getCandidateOtp(email) {
    return execSync(`C:\\xampp\\php\\php.exe bin/get_otp.php ${email}`, { cwd: 'd:\\Realty Smartz\\Realty Pathsala' }).toString().trim();
}

test.describe('Candidate Lock After Submission & LinkedIn Required', () => {

    test('LinkedIn is strictly required in Onboarding Step B', async ({ page }) => {
        const rand = Math.floor(Math.random() * 90000000) + 10000000;
        const email = `testcandidate_${rand}@example.com`;
        const mobile = `98${String(rand).slice(0, 8)}`;

        await page.goto('/register');
        await page.fill('#reg_full_name', 'Lock Test Candidate');
        await page.fill('#reg_father_name', 'Father Malhotra');
        await page.fill('#reg_email', email);
        await page.fill('#reg_mobile', mobile);
        await page.fill('#registerPassword', 'Candidate@2026!');
        await page.fill('#reg_password_confirmation', 'Candidate@2026!');
        await page.click('#btnSubmitReg');

        // Fast-verify OTP
        await page.waitForURL('**/verify-email**');
        const otp = getCandidateOtp(email);
        await page.fill('#otp_input', otp);
        await page.click('button[type="submit"]');

        // Arrive on onboarding
        await page.waitForURL('**/dashboard/onboarding**');

        // Complete Step A
        await page.fill('#father_name', 'Father Malhotra');
        await page.fill('#dob', '1998-05-15');
        await page.selectOption('#gender', 'male');
        await page.fill('#address', '123 Test Street, Cyber City');
        await page.fill('#city', 'Gurugram');
        await page.fill('#state', 'Haryana');
        await page.fill('#pincode', '122002');
        await page.click('#form_step_a button[type="submit"]');

        // Arrive on Step B
        await page.waitForTimeout(1000);
        const stepB = page.locator('#step_b_section');
        await expect(stepB).toBeVisible();

        // 1. Check LinkedIn field has HTML5 required attribute
        const linkedinInput = page.locator('#soc_linkedin');
        await expect(linkedinInput).toHaveAttribute('required', '');

        // 2. Clear old toast and test empty check
        await page.evaluate(() => {
            const t = document.getElementById('wizardGlobalToast');
            if (t) t.style.display = 'none';
        });
        await linkedinInput.fill('');
        const isEmptyValid = await page.evaluate(() => validateStepB());
        expect(isEmptyValid).toBe(false);
        const toast = page.locator('#wizardGlobalToast');
        await expect(toast).toContainText(/LinkedIn profile URL is required/i);

        // 3. Try with invalid LinkedIn URL
        await linkedinInput.fill('https://facebook.com/myname');
        const isWrongUrlValid = await page.evaluate(() => validateStepB());
        expect(isWrongUrlValid).toBe(false);
        await expect(toast).toContainText(/valid LinkedIn URL/i);

        // 4. Fill valid LinkedIn URL and advance
        await linkedinInput.fill('https://www.linkedin.com/in/test-candidate');
        await page.click('#form_step_b button[type="submit"]');

        // Should advance to Step C
        await page.waitForTimeout(1000);
        const stepC = page.locator('#step_c_section');
        await expect(stepC).toBeVisible();
    });

    test('Candidate Profile is Permanently Locked After Final Step Submission', async ({ page }) => {
        const rand = Math.floor(Math.random() * 90000000) + 10000000;
        const email = `locked_${rand}@example.com`;
        const mobile = `97${String(rand).slice(0, 8)}`;

        await page.goto('/register');
        await page.fill('#reg_full_name', 'Locked Profile User');
        await page.fill('#reg_father_name', 'Father Malhotra');
        await page.fill('#reg_email', email);
        await page.fill('#reg_mobile', mobile);
        await page.fill('#registerPassword', 'Candidate@2026!');
        await page.fill('#reg_password_confirmation', 'Candidate@2026!');
        await page.click('#btnSubmitReg');

        await page.waitForURL('**/verify-email**');
        const otp2 = getCandidateOtp(email);
        await page.fill('#otp_input', otp2);
        await page.click('button[type="submit"]');
        await page.waitForURL('**/dashboard/onboarding**');

        // Step A
        await page.fill('#father_name', 'Father Name');
        await page.fill('#dob', '1995-10-10');
        await page.selectOption('#gender', 'female');
        await page.fill('#address', 'Sector 69, Gurugram');
        await page.fill('#city', 'Gurugram');
        await page.fill('#state', 'Haryana');
        await page.fill('#pincode', '122001');
        await page.click('#form_step_a button[type="submit"]');

        // Step B
        await page.waitForTimeout(1000);
        await page.fill('#soc_linkedin', 'https://linkedin.com/in/locked-user');
        await page.click('#form_step_b button[type="submit"]');

        // Step C
        await page.waitForTimeout(1000);
        await page.fill('#aadhaar_number', '9999 8888 7777');
        await page.fill('#pan_number', 'ABCDE1234F');

        // Upload Aadhaar and PAN
        const dummyPdf = Buffer.from('%PDF-1.4 1 0 obj<</Type/Catalog>>endobj xref 0 1 trailer<</Root 1 0 R>>%%EOF');
        const uploadAadhaarPromise = page.waitForResponse(res => res.url().includes('/document/upload') && res.status() === 200);
        await page.setInputFiles('#file_aadhaar_front', {
            name: 'aadhaar_card.pdf',
            mimeType: 'application/pdf',
            buffer: dummyPdf
        });
        await uploadAadhaarPromise;

        const uploadPanPromise = page.waitForResponse(res => res.url().includes('/document/upload') && res.status() === 200);
        await page.setInputFiles('#file_pan_card', {
            name: 'pan_card.pdf',
            mimeType: 'application/pdf',
            buffer: dummyPdf
        });
        await uploadPanPromise;

        await page.fill('#emergency_name_1', 'Emergency Contact');
        await page.fill('#emergency_rel_1', 'Mother');
        await page.fill('#emergency_phone_1', '9876543210');
        const vResult = await page.evaluate(() => validateStepC());
        expect(vResult).toBe(true);

        const savePromise = page.waitForResponse(res => res.url().includes('/save-step') && res.status() === 200);
        await page.evaluate(() => saveStepData('step_c', true));
        await savePromise;

        // Step D
        await expect(page.locator('#step_d_section')).toBeVisible({ timeout: 10000 });
        await page.check('#declaration_accepted');

        console.log('Submitting Step D...');
        const submitPromise = page.waitForResponse(res => {
            if (res.url().includes('/submit')) {
                console.log(`[SUBMIT RESPONSE] ${res.status()} ${res.url()}`);
                return true;
            }
            return false;
        });
        await page.click('#btnSubmitFinal');
        const submitResponse = await submitPromise;
        console.log('Submit response status:', submitResponse.status());
        const submitJson = await submitResponse.json();
        console.log('Submit response body:', submitJson);

        // Should redirect to aptitude assessment
        console.log('Waiting for URL redirect...');
        await page.waitForURL('**/dashboard/aptitude**', { timeout: 15000 });
        console.log('Redirected to:', page.url());

        // Now return to /dashboard/onboarding
        console.log('Navigating to /dashboard/onboarding?step=step_a...');
        await page.goto('/dashboard/onboarding?step=step_a');
        await page.waitForLoadState('networkidle');
        console.log('Navigated to /dashboard/onboarding?step=step_a. Current step check...');

        // 1. Locked banner must be visible
        await expect(page.locator('#step_a_section').locator('..').locator('text=Profile Locked').first()).toBeVisible();
        console.log('Assertion 1: Profile Locked banner visible ✓');

        // 2. Step A inputs must be readonly / disabled
        await expect(page.locator('#father_name')).toHaveAttribute('readonly', '');
        await expect(page.locator('#dob')).toHaveAttribute('readonly', '');
        await expect(page.locator('#gender')).toHaveAttribute('disabled', '');
        await expect(page.locator('#address')).toHaveAttribute('readonly', '');
        console.log('Assertion 2: Step A inputs readonly/disabled ✓');

        // 3. Step A button should be Next view, NOT submit
        const stepABtn = page.locator('#form_step_a button');
        await expect(stepABtn).toContainText(/Next: View Social Links/i);
        await stepABtn.click();
        console.log('Assertion 3: Step A button clicked to Step B ✓');

        // 4. Step B inputs must be readonly
        await expect(page.locator('#soc_linkedin')).toHaveAttribute('readonly', '');
        const stepBBtn = page.locator('#form_step_b button:has-text("Next: View Documents")');
        await expect(stepBBtn).toBeVisible();
        await stepBBtn.click();
        console.log('Assertion 4: Step B inputs readonly & clicked to Step C ✓');

        // 5. Step C inputs must be readonly and Replace buttons absent
        await expect(page.locator('#aadhaar_number')).toHaveAttribute('readonly', '');
        await expect(page.locator('#pan_number')).toHaveAttribute('readonly', '');
        await expect(page.locator('button:has-text("Replace")')).toHaveCount(0);
        console.log('Assertion 5: Step C inputs readonly & Replace buttons absent ✓');

        // 6. Direct API save-step call should be blocked with HTTP 403
        console.log('Testing direct API save-step call...');
        const saveRes = await page.evaluate(async () => {
            const token = document.querySelector('input[name="_csrf_token"]')?.value || '';
            const fd = new FormData();
            fd.append('_csrf_token', token);
            fd.append('step', 'step_a');
            fd.append('father_name', 'Hacked Father');
            const res = await fetch('/dashboard/onboarding/save-step', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            return { status: res.status, json: await res.json() };
        });
        expect(saveRes.status).toBe(403);
        expect(saveRes.json.success).toBe(false);
        console.log('Assertion 6: Direct API save-step returned 403 ✓');

        // 7. Direct Document upload API call should be blocked with HTTP 403
        console.log('Testing direct Document upload API call...');
        const uploadRes = await page.evaluate(async () => {
            const token = document.querySelector('input[name="_csrf_token"]')?.value || '';
            const fd = new FormData();
            fd.append('_csrf_token', token);
            fd.append('document_type', 'profile_photo');
            fd.append('file', new Blob(['test'], { type: 'text/plain' }), 'hack.txt');
            const res = await fetch('/dashboard/document/upload', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            return { status: res.status, json: await res.json() };
        });
        expect(uploadRes.status).toBe(403);
        expect(uploadRes.json.success).toBe(false);
        console.log('Assertion 7: Direct Document upload API returned 403 ✓');
    });
});
