<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Interview Application Form</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand: #1F5C4A;
            --brand-hover: #17483A;
            --brand-soft: #E8F1EE;
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --surface-2: #F0ECE1;
            --line: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
            --danger: #B3261E;
            --danger-soft: #FDF2F2;
            --r-sm: 8px;
            --r-md: 12px;
            --r-lg: 20px;
            --r-full: 9999px;
            --font-main: 'DM Sans', system-ui, -apple-system, sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Standalone Header with ONLY Pathshala Logo */
        .public-header {
            background-color: var(--surface);
            border-bottom: 1px solid var(--line);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .public-header a {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }

        .public-logo {
            height: 42px;
            width: auto;
            display: block;
        }

        /* Container */
        .form-container {
            width: 100%;
            max-width: 680px;
            margin: 32px auto;
            padding: 0 16px 48px;
            flex: 1;
        }

        .form-card {
            background-color: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 36px 32px;
            box-shadow: 0 4px 20px rgba(38, 51, 45, 0.04);
        }

        @media (max-width: 480px) {
            .form-container {
                margin: 16px auto;
                padding: 0 12px 32px;
            }
            .form-card {
                padding: 24px 18px;
                border-radius: var(--r-md);
            }
        }

        .form-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
            margin-bottom: 28px;
            text-align: center;
        }

        /* Field Layout */
        .field-group {
            margin-bottom: 24px;
        }

        .field-label {
            display: block;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .field-label .req {
            color: var(--danger);
            margin-left: 2px;
        }

        /* Inputs, Selects, Textareas */
        .form-control {
            width: 100%;
            height: 48px;
            font-size: 16px;
            font-family: inherit;
            color: var(--text);
            background-color: #FFFFFF;
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            padding: 0 16px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(31, 92, 74, 0.15);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            background-color: #FFFDFD;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(179, 38, 30, 0.15);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2326332D' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 44px;
            cursor: pointer;
        }

        textarea.form-control {
            height: auto;
            min-height: 96px;
            padding: 12px 16px;
            resize: vertical;
        }

        .field-error {
            display: none;
            color: var(--danger);
            font-size: 13px;
            font-weight: 500;
            margin-top: 6px;
        }

        .field-error.show {
            display: block;
        }

        /* Pill Radio Group Design */
        fieldset.radio-fieldset {
            border: none;
            padding: 0;
            margin: 0;
        }

        legend.field-label {
            float: none;
            width: 100%;
            margin-bottom: 8px;
        }

        .radio-pills {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .radio-pill-label {
            flex: 1;
            min-width: 130px;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: #FFFFFF;
            border: 1px solid var(--line);
            border-radius: var(--r-full);
            padding: 0 18px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            color: var(--text);
            position: relative;
            transition: all 0.15s ease;
            user-select: none;
        }

        /* Visually hidden real input */
        .radio-pill-label input[type="radio"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 1;
        }

        /* Indicator dot */
        .radio-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .radio-dot::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--brand);
            opacity: 0;
            transform: scale(0);
            transition: all 0.15s ease;
        }

        /* Selected State */
        .radio-pill-label:has(input[type="radio"]:checked) {
            background-color: var(--brand-soft);
            border: 1.5px solid var(--brand);
            color: var(--brand);
            font-weight: 600;
        }

        .radio-pill-label:has(input[type="radio"]:checked) .radio-dot {
            border-color: var(--brand);
        }

        .radio-pill-label:has(input[type="radio"]:checked) .radio-dot::after {
            opacity: 1;
            transform: scale(1);
        }

        /* Keyboard Focus Visible */
        .radio-pill-label:has(input[type="radio"]:focus-visible) {
            outline: 2px solid var(--brand);
            outline-offset: 2px;
        }

        /* Employment History Section */
        .emp-table-wrapper {
            margin-top: 10px;
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            overflow: hidden;
            background: #FFFFFF;
        }

        .emp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .emp-table th {
            background-color: var(--surface-2);
            color: var(--text);
            font-weight: 600;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--line);
            font-size: 12.5px;
        }

        .emp-table td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
        }

        .emp-table input {
            width: 100%;
            height: 40px;
            font-size: 14px;
            padding: 0 10px;
            border: 1px solid var(--line);
            border-radius: var(--r-sm);
            background: #FFFFFF;
            font-family: inherit;
        }

        .emp-table input:focus {
            border-color: var(--brand);
            outline: none;
            box-shadow: 0 0 0 2px rgba(31, 92, 74, 0.12);
        }

        .btn-remove-row {
            background: transparent;
            border: none;
            color: var(--danger);
            cursor: pointer;
            padding: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--r-sm);
        }

        .btn-remove-row:hover {
            background-color: var(--danger-soft);
        }

        /* Under 768px: Employment rows become stacked cards with NO horizontal scroll */
        @media (max-width: 767.98px) {
            .emp-table-wrapper {
                border: none;
                background: transparent;
                border-radius: 0;
            }
            .emp-table, .emp-table thead, .emp-table tbody, .emp-table tr, .emp-table td {
                display: block;
                width: 100%;
            }
            .emp-table thead {
                display: none;
            }
            .emp-table tr.emp-row {
                background: #FFFFFF;
                border: 1px solid var(--line);
                border-radius: var(--r-md);
                padding: 16px;
                margin-bottom: 14px;
                position: relative;
            }
            .emp-table td {
                padding: 6px 0;
                border: none;
            }
            .emp-card-field-label {
                display: block;
                font-size: 12.5px;
                font-weight: 600;
                color: var(--muted);
                margin-bottom: 4px;
            }
            .emp-row-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
                padding-bottom: 6px;
                border-bottom: 1px dashed var(--line);
            }
            .emp-row-title {
                font-size: 13.5px;
                font-weight: 700;
                color: var(--brand);
            }
        }

        @media (min-width: 768px) {
            .emp-card-field-label, .emp-row-header {
                display: none;
            }
        }

        .btn-add-row {
            margin-top: 10px;
            background: var(--surface);
            border: 1px dashed var(--brand);
            color: var(--brand);
            border-radius: var(--r-md);
            height: 42px;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .btn-add-row:hover {
            background-color: var(--brand-soft);
        }

        /* Consent Checkbox */
        .consent-wrapper {
            margin-top: 28px;
            margin-bottom: 24px;
        }

        .consent-label {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            font-size: 14.5px;
            line-height: 1.45;
            color: var(--text);
            user-select: none;
        }

        .consent-checkbox {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            border-radius: 4px;
            accent-color: var(--brand);
            flex-shrink: 0;
            cursor: pointer;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            height: 52px;
            background-color: var(--brand);
            color: #FFFFFF;
            border: none;
            border-radius: var(--r-md);
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.15s ease, opacity 0.15s ease;
        }

        .btn-submit:hover:not(:disabled) {
            background-color: var(--brand-hover);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #FFFFFF;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Honeypot field (hidden from real users) */
        .hp-trap {
            position: absolute;
            left: -9999px;
            top: -9999px;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <!-- ONLY Pathshala Logo header (click lands on /) -->
    <header class="public-header" role="banner">
        <a href="/" aria-label="Realty Smartz Pathshala Homepage">
            <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" class="public-logo">
        </a>
    </header>

    <main class="form-container" role="main">
        <div class="form-card">
            <h1 class="form-title">Interview Application Form</h1>

            <form id="interviewForm" method="POST" action="<?= url('/q/' . urlencode($slug)) ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="signed_token" value="<?= e($signedToken ?? '') ?>">
                <input type="hidden" name="idempotency_token" value="<?= e($idempotencyToken) ?>">

                <!-- Honeypot -->
                <div class="hp-trap" aria-hidden="true">
                    <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    <input type="text" name="hp_field" tabindex="-1" autocomplete="off">
                </div>

                <!-- 1. HR Name (dropdown, required, fixed list from hr_names.php) -->
                <div class="field-group" id="group_hr_name">
                    <label for="hr_name" class="field-label">HR Name <span class="req" aria-hidden="true">*</span></label>
                    <select id="hr_name" name="hr_name" class="form-control" required aria-required="true">
                        <option value="" disabled selected>Select</option>
                        <?php foreach ($hrNames as $hn): ?>
                            <option value="<?= e($hn) ?>" <?= (isset($oldInput['hr_name']) && $oldInput['hr_name'] === $hn) ? 'selected' : '' ?>><?= e($hn) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error" id="err_hr_name"><?= e($errors['hr_name'] ?? 'Please select an HR Name.') ?></div>
                </div>

                <!-- 2. Post Applied For (text, required) -->
                <div class="field-group" id="group_post_applied">
                    <label for="post_applied" class="field-label">Post Applied For <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="post_applied" name="post_applied" class="form-control" maxlength="255" required aria-required="true" value="<?= e($oldInput['post_applied'] ?? '') ?>">
                    <div class="field-error" id="err_post_applied"><?= e($errors['post_applied'] ?? 'Post Applied For is required.') ?></div>
                </div>

                <!-- 3. Full Name (text, required) -->
                <div class="field-group" id="group_full_name">
                    <label for="full_name" class="field-label">Full Name <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="form-control" maxlength="100" required aria-required="true" value="<?= e($oldInput['full_name'] ?? '') ?>">
                    <div class="field-error" id="err_full_name"><?= e($errors['full_name'] ?? 'Full Name is required.') ?></div>
                </div>

                <!-- 4. Mail ID (email, required) -->
                <div class="field-group" id="group_email">
                    <label for="email" class="field-label">Mail ID <span class="req" aria-hidden="true">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="255" required aria-required="true" value="<?= e($oldInput['email'] ?? '') ?>">
                    <div class="field-error" id="err_email"><?= e($errors['email'] ?? 'A valid Mail ID is required.') ?></div>
                </div>

                <!-- 5. Contact Number (required, 10-digit Indian mobile starting 6-9, numeric keypad) -->
                <div class="field-group" id="group_mobile">
                    <label for="mobile" class="field-label">Contact Number <span class="req" aria-hidden="true">*</span></label>
                    <input type="tel" id="mobile" name="mobile" class="form-control" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="14" placeholder="10-digit mobile number" required aria-required="true" value="<?= e($oldInput['mobile'] ?? '') ?>">
                    <div class="field-error" id="err_mobile"><?= e($errors['mobile'] ?? 'Valid 10-digit mobile number starting with 6-9 is required.') ?></div>
                </div>

                <!-- 6. Experience / Fresher (RADIO group, required) -->
                <div class="field-group" id="group_experience_type">
                    <fieldset class="radio-fieldset" aria-required="true">
                        <legend class="field-label">Experience / Fresher <span class="req" aria-hidden="true">*</span></legend>
                        <div class="radio-pills">
                            <label class="radio-pill-label">
                                <input type="radio" name="experience_type" value="experience" <?= (isset($oldInput['experience_type']) && $oldInput['experience_type'] === 'experience') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>Experience</span>
                            </label>
                            <label class="radio-pill-label">
                                <input type="radio" name="experience_type" value="fresher" <?= (isset($oldInput['experience_type']) && $oldInput['experience_type'] === 'fresher') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>Fresher</span>
                            </label>
                        </div>
                    </fieldset>
                    <div class="field-error" id="err_experience_type"><?= e($errors['experience_type'] ?? 'Please select Experience or Fresher.') ?></div>
                </div>

                <!-- 7. Years in Real Estate (number, step 0.5, min 0; shown and required only when Experience is selected) -->
                <div class="field-group" id="group_years_in_real_estate" style="display: none;">
                    <label for="years_in_real_estate" class="field-label">Years in Real Estate <span class="req" aria-hidden="true">*</span></label>
                    <input type="number" id="years_in_real_estate" name="years_in_real_estate" class="form-control" step="0.5" min="0" placeholder="e.g. 2.5" value="<?= e($oldInput['years_in_real_estate'] ?? '') ?>">
                    <div class="field-error" id="err_years_in_real_estate"><?= e($errors['years_in_real_estate'] ?? 'Years in Real Estate is required (min 0).') ?></div>
                </div>

                <!-- 8. Employment history (only when Experience is selected) -->
                <div class="field-group" id="group_employment_history" style="display: none;">
                    <label class="field-label">Employment history <span class="req" aria-hidden="true">*</span></label>
                    <div class="emp-table-wrapper">
                        <table class="emp-table" id="empTable" aria-label="Employment history">
                            <thead>
                                <tr>
                                    <th>Name of the Organisation (from current/last to first)</th>
                                    <th>Designation</th>
                                    <th>Salary</th>
                                    <th>HR Name &amp; Number</th>
                                    <th>Duration</th>
                                    <th style="width: 44px;"></th>
                                </tr>
                            </thead>
                            <tbody id="empTableBody">
                                <!-- Initial 3 rows -->
                                <?php for ($i = 0; $i < 3; $i++): ?>
                                <tr class="emp-row" data-index="<?= $i ?>">
                                    <td>
                                        <div class="emp-row-header">
                                            <span class="emp-row-title">Organisation #<?= $i + 1 ?></span>
                                            <?php if ($i > 0): ?>
                                                <button type="button" class="btn-remove-row" onclick="removeEmpRow(this)" aria-label="Remove organisation row">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <span class="emp-card-field-label">Name of the Organisation</span>
                                        <input type="text" name="employment_rows[<?= $i ?>][organisation]" placeholder="Organisation name" maxlength="255" value="<?= e($oldInput['employment_rows'][$i]['organisation'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <span class="emp-card-field-label">Designation</span>
                                        <input type="text" name="employment_rows[<?= $i ?>][designation]" placeholder="Designation" maxlength="255" value="<?= e($oldInput['employment_rows'][$i]['designation'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <span class="emp-card-field-label">Salary</span>
                                        <input type="text" name="employment_rows[<?= $i ?>][salary]" placeholder="Salary" maxlength="100" value="<?= e($oldInput['employment_rows'][$i]['salary'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <span class="emp-card-field-label">HR Name &amp; Number</span>
                                        <input type="text" name="employment_rows[<?= $i ?>][hr_name_number]" placeholder="HR Name &amp; Contact" maxlength="255" value="<?= e($oldInput['employment_rows'][$i]['hr_name_number'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <span class="emp-card-field-label">Duration</span>
                                        <input type="text" name="employment_rows[<?= $i ?>][duration]" placeholder="e.g. 2 years" maxlength="100" value="<?= e($oldInput['employment_rows'][$i]['duration'] ?? '') ?>">
                                    </td>
                                    <td class="d-none d-md-table-cell" style="text-align: center;">
                                        <?php if ($i > 0): ?>
                                            <button type="button" class="btn-remove-row" onclick="removeEmpRow(this)" aria-label="Remove organisation row">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn-add-row" id="btnAddEmpRow" onclick="addEmpRow()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add row</span>
                    </button>
                    <div class="field-error" id="err_employment_history"><?= e($errors['employment_history'] ?? 'Please fill at least the first organisation row.') ?></div>
                </div>

                <!-- 9. Vehicle Available: RADIO, required: "Yes", "No" -->
                <div class="field-group" id="group_vehicle_available">
                    <fieldset class="radio-fieldset" aria-required="true">
                        <legend class="field-label">Vehicle Available <span class="req" aria-hidden="true">*</span></legend>
                        <div class="radio-pills">
                            <label class="radio-pill-label">
                                <input type="radio" name="vehicle_available" value="yes" <?= (isset($oldInput['vehicle_available']) && $oldInput['vehicle_available'] === 'yes') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>Yes</span>
                            </label>
                            <label class="radio-pill-label">
                                <input type="radio" name="vehicle_available" value="no" <?= (isset($oldInput['vehicle_available']) && $oldInput['vehicle_available'] === 'no') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>No</span>
                            </label>
                        </div>
                    </fieldset>
                    <div class="field-error" id="err_vehicle_available"><?= e($errors['vehicle_available'] ?? 'Please select Vehicle Available.') ?></div>
                </div>

                <!-- 10. Current Location (text, required) -->
                <div class="field-group" id="group_current_location">
                    <label for="current_location" class="field-label">Current Location <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="current_location" name="current_location" class="form-control" maxlength="255" required aria-required="true" value="<?= e($oldInput['current_location'] ?? '') ?>">
                    <div class="field-error" id="err_current_location"><?= e($errors['current_location'] ?? 'Current Location is required.') ?></div>
                </div>

                <!-- 11. Higher Education (text, required) -->
                <div class="field-group" id="group_higher_education">
                    <label for="higher_education" class="field-label">Higher Education <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" id="higher_education" name="higher_education" class="form-control" maxlength="255" required aria-required="true" value="<?= e($oldInput['higher_education'] ?? '') ?>">
                    <div class="field-error" id="err_higher_education"><?= e($errors['higher_education'] ?? 'Higher Education is required.') ?></div>
                </div>

                <!-- 12. Any References: RADIO, required: "Yes", "No" -->
                <div class="field-group" id="group_has_references">
                    <fieldset class="radio-fieldset" aria-required="true">
                        <legend class="field-label">Any References <span class="req" aria-hidden="true">*</span></legend>
                        <div class="radio-pills">
                            <label class="radio-pill-label">
                                <input type="radio" name="has_references" value="yes" <?= (isset($oldInput['has_references']) && $oldInput['has_references'] === 'yes') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>Yes</span>
                            </label>
                            <label class="radio-pill-label">
                                <input type="radio" name="has_references" value="no" <?= (isset($oldInput['has_references']) && $oldInput['has_references'] === 'no') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>No</span>
                            </label>
                        </div>
                    </fieldset>
                    <div class="field-error" id="err_has_references"><?= e($errors['has_references'] ?? 'Please select Any References.') ?></div>
                </div>

                <!-- 12b. Reference details (textarea, required when Any References = Yes) -->
                <div class="field-group" id="group_reference_details" style="display: none;">
                    <label for="reference_details" class="field-label">Reference details <span class="req" aria-hidden="true">*</span></label>
                    <textarea id="reference_details" name="reference_details" class="form-control" maxlength="1000"><?= e($oldInput['reference_details'] ?? '') ?></textarea>
                    <div class="field-error" id="err_reference_details"><?= e($errors['reference_details'] ?? 'Reference details are required.') ?></div>
                </div>

                <!-- 13. Last Company HR and TL Name (text, only when Experience) -->
                <div class="field-group" id="group_last_company_hr_tl" style="display: none;">
                    <label for="last_company_hr_tl" class="field-label">Last Company HR and TL Name</label>
                    <input type="text" id="last_company_hr_tl" name="last_company_hr_tl" class="form-control" maxlength="255" value="<?= e($oldInput['last_company_hr_tl'] ?? '') ?>">
                    <div class="field-error" id="err_last_company_hr_tl"><?= e($errors['last_company_hr_tl'] ?? '') ?></div>
                </div>

                <!-- 14. Reason to Leave (text, only when Experience) -->
                <div class="field-group" id="group_reason_to_leave" style="display: none;">
                    <label for="reason_to_leave" class="field-label">Reason to Leave</label>
                    <input type="text" id="reason_to_leave" name="reason_to_leave" class="form-control" maxlength="255" value="<?= e($oldInput['reason_to_leave'] ?? '') ?>">
                    <div class="field-error" id="err_reason_to_leave"><?= e($errors['reason_to_leave'] ?? '') ?></div>
                </div>

                <!-- 15. Immediate Joiner (Yes / No): RADIO, required -->
                <div class="field-group" id="group_immediate_joiner">
                    <fieldset class="radio-fieldset" aria-required="true">
                        <legend class="field-label">Immediate Joiner (Yes / No) <span class="req" aria-hidden="true">*</span></legend>
                        <div class="radio-pills">
                            <label class="radio-pill-label">
                                <input type="radio" name="immediate_joiner" value="yes" <?= (isset($oldInput['immediate_joiner']) && $oldInput['immediate_joiner'] === 'yes') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>Yes</span>
                            </label>
                            <label class="radio-pill-label">
                                <input type="radio" name="immediate_joiner" value="no" <?= (isset($oldInput['immediate_joiner']) && $oldInput['immediate_joiner'] === 'no') ? 'checked' : '' ?> required>
                                <span class="radio-dot" aria-hidden="true"></span>
                                <span>No</span>
                            </label>
                        </div>
                    </fieldset>
                    <div class="field-error" id="err_immediate_joiner"><?= e($errors['immediate_joiner'] ?? 'Please select Immediate Joiner.') ?></div>
                </div>

                <!-- 16. Consent checkbox (required) -->
                <div class="consent-wrapper" id="group_consent">
                    <label class="consent-label" for="consent">
                        <input type="checkbox" id="consent" name="consent" class="consent-checkbox" required aria-required="true" <?= !empty($oldInput['consent']) ? 'checked' : '' ?>>
                        <span>I agree that my details may be stored for recruitment.</span>
                    </label>
                    <div class="field-error" id="err_consent"><?= e($errors['consent'] ?? 'You must agree that your details may be stored for recruitment.') ?></div>
                </div>

                <!-- 17. Submit button "Submit" -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="spinner" id="btnSpinner" aria-hidden="true"></span>
                    <span id="btnText">Submit</span>
                </button>
            </form>
        </div>
    </main>

    <script>
        (function() {
            const form = document.getElementById('interviewForm');
            const submitBtn = document.getElementById('btnSubmit');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnText = document.getElementById('btnText');
            const storageKey = 'rsp_interview_draft_<?= md5($slug) ?>';

            // Auto-load draft if present and no server-side oldInput
            try {
                const saved = localStorage.getItem(storageKey);
                if (saved) {
                    const data = JSON.parse(saved);
                    for (const [k, v] of Object.entries(data)) {
                        const input = form.querySelector(`[name="${k}"]`);
                        if (input) {
                            if (input.type === 'radio') {
                                const matched = form.querySelector(`[name="${k}"][value="${v}"]`);
                                if (matched) matched.checked = true;
                            } else if (input.type === 'checkbox') {
                                input.checked = Boolean(v);
                            } else if (!input.value) {
                                input.value = v;
                            }
                        }
                    }
                }
            } catch(e) {}

            // Save input state to local draft on input changes
            form.addEventListener('input', saveDraft);
            form.addEventListener('change', saveDraft);

            function saveDraft() {
                try {
                    const fd = new FormData(form);
                    const obj = {};
                    fd.forEach((v, k) => {
                        if (k !== 'csrf_token' && k !== 'idempotency_token' && !k.startsWith('website_url')) {
                            obj[k] = v;
                        }
                    });
                    localStorage.setItem(storageKey, JSON.stringify(obj));
                } catch(e) {}
            }

            // Experience vs Fresher toggling
            const expRadios = form.querySelectorAll('input[name="experience_type"]');
            const groupYears = document.getElementById('group_years_in_real_estate');
            const groupEmp = document.getElementById('group_employment_history');
            const groupHrTl = document.getElementById('group_last_company_hr_tl');
            const groupReason = document.getElementById('group_reason_to_leave');

            function updateExperienceVisibility() {
                const checked = form.querySelector('input[name="experience_type"]:checked');
                const isExp = (checked && checked.value === 'experience');

                groupYears.style.display = isExp ? 'block' : 'none';
                groupEmp.style.display = isExp ? 'block' : 'none';
                groupHrTl.style.display = isExp ? 'block' : 'none';
                groupReason.style.display = isExp ? 'block' : 'none';

                const yearsInput = document.getElementById('years_in_real_estate');
                if (isExp) {
                    yearsInput.setAttribute('required', 'required');
                } else {
                    yearsInput.removeAttribute('required');
                    // Clear values when fresher selected
                    yearsInput.value = '';
                    const hrTl = document.getElementById('last_company_hr_tl');
                    if (hrTl) hrTl.value = '';
                    const rsn = document.getElementById('reason_to_leave');
                    if (rsn) rsn.value = '';
                    // Clear employment rows
                    document.querySelectorAll('#empTableBody input').forEach(inp => inp.value = '');
                }
            }

            expRadios.forEach(r => r.addEventListener('change', updateExperienceVisibility));
            updateExperienceVisibility();

            // References Yes vs No toggling
            const refRadios = form.querySelectorAll('input[name="has_references"]');
            const groupRefDetails = document.getElementById('group_reference_details');
            const refDetailsInput = document.getElementById('reference_details');

            function updateReferenceVisibility() {
                const checked = form.querySelector('input[name="has_references"]:checked');
                const isYes = (checked && checked.value === 'yes');
                groupRefDetails.style.display = isYes ? 'block' : 'none';
                if (isYes) {
                    refDetailsInput.setAttribute('required', 'required');
                } else {
                    refDetailsInput.removeAttribute('required');
                    refDetailsInput.value = '';
                }
            }

            refRadios.forEach(r => r.addEventListener('change', updateReferenceVisibility));
            updateReferenceVisibility();

            // Clear inline errors immediately when radio option is chosen
            ['experience_type', 'vehicle_available', 'has_references', 'immediate_joiner'].forEach(name => {
                form.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                    r.addEventListener('change', () => {
                        const err = document.getElementById('err_' + name);
                        if (err) err.classList.remove('show');
                    });
                });
            });

            // Mobile input sanitation: strip +91 and spaces on input
            const mobileInput = document.getElementById('mobile');
            mobileInput.addEventListener('input', function() {
                let v = this.value.replace(/[^\d+]/g, '');
                if (v.startsWith('+91')) v = v.substring(3);
                else if (v.startsWith('91') && v.length > 10) v = v.substring(2);
                this.value = v.replace(/[^\d]/g, '').slice(0, 10);
            });

            // Inline validation on blur
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.addEventListener('blur', () => validateField(el));
            });

            function validateField(el) {
                const id = el.id || el.name;
                const errDiv = document.getElementById('err_' + id);
                let valid = true;
                let message = '';

                if (el.name === 'hr_name') {
                    if (!el.value) { valid = false; message = 'Please select an HR Name.'; }
                } else if (el.name === 'post_applied') {
                    if (!el.value.trim()) { valid = false; message = 'Post Applied For is required.'; }
                } else if (el.name === 'full_name') {
                    if (!el.value.trim()) { valid = false; message = 'Full Name is required.'; }
                } else if (el.name === 'email') {
                    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!el.value.trim() || !re.test(el.value.trim())) { valid = false; message = 'A valid Mail ID is required.'; }
                } else if (el.name === 'mobile') {
                    const re = /^[6-9]\d{9}$/;
                    if (!re.test(el.value.trim())) { valid = false; message = 'Enter a valid 10-digit Indian mobile number.'; }
                } else if (el.name === 'years_in_real_estate') {
                    const isExp = form.querySelector('input[name="experience_type"]:checked')?.value === 'experience';
                    if (isExp && (el.value === '' || parseFloat(el.value) < 0)) {
                        valid = false; message = 'Years in Real Estate is required (min 0).';
                    }
                } else if (el.name === 'current_location') {
                    if (!el.value.trim()) { valid = false; message = 'Current Location is required.'; }
                } else if (el.name === 'higher_education') {
                    if (!el.value.trim()) { valid = false; message = 'Higher Education is required.'; }
                } else if (el.name === 'reference_details') {
                    const isYes = form.querySelector('input[name="has_references"]:checked')?.value === 'yes';
                    if (isYes && !el.value.trim()) { valid = false; message = 'Reference details are required.'; }
                } else if (el.name === 'consent') {
                    if (!el.checked) { valid = false; message = 'You must agree that your details may be stored for recruitment.'; }
                }

                if (!valid) {
                    el.classList.add('is-invalid');
                    if (errDiv) {
                        errDiv.textContent = message;
                        errDiv.classList.add('show');
                    }
                } else {
                    el.classList.remove('is-invalid');
                    if (errDiv) errDiv.classList.remove('show');
                }
                return valid;
            }

            // Form Submit handling
            form.addEventListener('submit', function(e) {
                let firstInvalid = null;
                let isFormValid = true;

                // Validate HR Name
                const hrSelect = document.getElementById('hr_name');
                if (!validateField(hrSelect)) {
                    isFormValid = false;
                    if (!firstInvalid) firstInvalid = hrSelect;
                }

                // Validate text fields
                ['post_applied', 'full_name', 'email', 'mobile'].forEach(name => {
                    const inp = document.getElementById(name);
                    if (!validateField(inp)) {
                        isFormValid = false;
                        if (!firstInvalid) firstInvalid = inp;
                    }
                });

                // Validate Experience radio
                const expChecked = form.querySelector('input[name="experience_type"]:checked');
                const errExp = document.getElementById('err_experience_type');
                if (!expChecked) {
                    isFormValid = false;
                    errExp.classList.add('show');
                    if (!firstInvalid) firstInvalid = document.getElementById('group_experience_type');
                } else {
                    errExp.classList.remove('show');
                    if (expChecked.value === 'experience') {
                        const yearsInp = document.getElementById('years_in_real_estate');
                        if (!validateField(yearsInp)) {
                            isFormValid = false;
                            if (!firstInvalid) firstInvalid = yearsInp;
                        }

                        // Validate employment first row
                        const firstOrg = document.querySelector('input[name="employment_rows[0][organisation]"]');
                        const firstDesig = document.querySelector('input[name="employment_rows[0][designation]"]');
                        const errEmp = document.getElementById('err_employment_history');
                        if (!firstOrg?.value.trim() || !firstDesig?.value.trim()) {
                            isFormValid = false;
                            errEmp.classList.add('show');
                            if (!firstInvalid) firstInvalid = firstOrg || document.getElementById('group_employment_history');
                        } else {
                            errEmp.classList.remove('show');
                        }
                    }
                }

                // Validate Vehicle Available
                const vehChecked = form.querySelector('input[name="vehicle_available"]:checked');
                const errVeh = document.getElementById('err_vehicle_available');
                if (!vehChecked) {
                    isFormValid = false;
                    errVeh.classList.add('show');
                    if (!firstInvalid) firstInvalid = document.getElementById('group_vehicle_available');
                } else {
                    errVeh.classList.remove('show');
                }

                // Current Location & Higher Education
                ['current_location', 'higher_education'].forEach(name => {
                    const inp = document.getElementById(name);
                    if (!validateField(inp)) {
                        isFormValid = false;
                        if (!firstInvalid) firstInvalid = inp;
                    }
                });

                // Any References
                const refChecked = form.querySelector('input[name="has_references"]:checked');
                const errRef = document.getElementById('err_has_references');
                if (!refChecked) {
                    isFormValid = false;
                    errRef.classList.add('show');
                    if (!firstInvalid) firstInvalid = document.getElementById('group_has_references');
                } else {
                    errRef.classList.remove('show');
                    if (refChecked.value === 'yes') {
                        const refInp = document.getElementById('reference_details');
                        if (!validateField(refInp)) {
                            isFormValid = false;
                            if (!firstInvalid) firstInvalid = refInp;
                        }
                    }
                }

                // Immediate Joiner
                const immChecked = form.querySelector('input[name="immediate_joiner"]:checked');
                const errImm = document.getElementById('err_immediate_joiner');
                if (!immChecked) {
                    isFormValid = false;
                    errImm.classList.add('show');
                    if (!firstInvalid) firstInvalid = document.getElementById('group_immediate_joiner');
                } else {
                    errImm.classList.remove('show');
                }

                // Consent
                const consentInp = document.getElementById('consent');
                if (!validateField(consentInp)) {
                    isFormValid = false;
                    if (!firstInvalid) firstInvalid = consentInp;
                }

                if (!isFormValid) {
                    e.preventDefault();
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        if (typeof firstInvalid.focus === 'function') firstInvalid.focus();
                    }
                    return;
                }

                // Clean draft on valid submit
                try { localStorage.removeItem(storageKey); } catch(e) {}

                // Loading state on button
                submitBtn.disabled = true;
                btnSpinner.style.display = 'inline-block';
                btnText.textContent = 'Submitting...';
            });
        })();

        // Add / Remove employment history rows
        function addEmpRow() {
            const tbody = document.getElementById('empTableBody');
            const rowCount = tbody.querySelectorAll('tr.emp-row').length;
            if (rowCount >= 6) return;

            const nextIndex = rowCount;
            const tr = document.createElement('tr');
            tr.className = 'emp-row';
            tr.setAttribute('data-index', nextIndex);
            tr.innerHTML = `
                <td>
                    <div class="emp-row-header">
                        <span class="emp-row-title">Organisation #${nextIndex + 1}</span>
                        <button type="button" class="btn-remove-row" onclick="removeEmpRow(this)" aria-label="Remove organisation row">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                    <span class="emp-card-field-label">Name of the Organisation</span>
                    <input type="text" name="employment_rows[${nextIndex}][organisation]" placeholder="Organisation name" maxlength="255">
                </td>
                <td>
                    <span class="emp-card-field-label">Designation</span>
                    <input type="text" name="employment_rows[${nextIndex}][designation]" placeholder="Designation" maxlength="255">
                </td>
                <td>
                    <span class="emp-card-field-label">Salary</span>
                    <input type="text" name="employment_rows[${nextIndex}][salary]" placeholder="Salary" maxlength="100">
                </td>
                <td>
                    <span class="emp-card-field-label">HR Name &amp; Number</span>
                    <input type="text" name="employment_rows[${nextIndex}][hr_name_number]" placeholder="HR Name &amp; Contact" maxlength="255">
                </td>
                <td>
                    <span class="emp-card-field-label">Duration</span>
                    <input type="text" name="employment_rows[${nextIndex}][duration]" placeholder="e.g. 2 years" maxlength="100">
                </td>
                <td class="d-none d-md-table-cell" style="text-align: center;">
                    <button type="button" class="btn-remove-row" onclick="removeEmpRow(this)" aria-label="Remove organisation row">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            if (tbody.querySelectorAll('tr.emp-row').length >= 6) {
                document.getElementById('btnAddEmpRow').style.display = 'none';
            }
        }

        function removeEmpRow(btn) {
            const row = btn.closest('tr.emp-row');
            if (row) {
                row.remove();
                document.getElementById('btnAddEmpRow').style.display = 'inline-flex';
                // Re-index remaining rows
                document.querySelectorAll('#empTableBody tr.emp-row').forEach((r, idx) => {
                    r.setAttribute('data-index', idx);
                    const title = r.querySelector('.emp-row-title');
                    if (title) title.textContent = `Organisation #${idx + 1}`;
                    r.querySelectorAll('input').forEach(inp => {
                        inp.name = inp.name.replace(/\[\d+\]/, `[${idx}]`);
                    });
                });
            }
        }
    </script>
</body>
</html>
