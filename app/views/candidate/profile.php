<?php
ob_start();
$profile = $candidate['profile'] ?? [];
$edu = $candidate['education'][0] ?? [];
$exp = $candidate['experience'][0] ?? [];
$bank = $candidate['bank'] ?? [];
$emg = $candidate['emergency'] ?? [];
$docs = $candidate['docs_by_type'] ?? [];
$isLocked = !empty($profile['is_locked']) && $candidate['status'] !== 'changes_requested';
$completion = (int)($profile['completion_percentage'] ?? 0);
$activeStep = $_GET['step'] ?? 'personal';
?>
<section class="portal-wrap" style="background:var(--bg-1);">
    <div class="rsp-container">
        <div class="portal-grid">
            <!-- Sidebar -->
            <aside class="portal-sidebar">
                <div class="portal-user-box">
                    <h3 style="font-size:1.2rem; color:var(--text); margin-bottom:4px;"><?= e($candidate['full_name']) ?></h3>
                    <div style="font-size:12px; color:var(--text-muted); margin-bottom:12px;">Candidate Profile #<?= $candidate['id'] ?></div>
                    <div style="background:rgba(255,255,255,0.08); height:6px; border-radius:4px; overflow:hidden; margin-bottom:8px;">
                        <div id="sideCompletionBar" style="width:<?= $completion ?>%; height:100%; background:var(--gold-gradient);"></div>
                    </div>
                    <div style="font-size:12px; color:var(--gold-light); font-weight:700;"><span id="sideCompletionPct"><?= $completion ?></span>% Completed</div>
                </div>

                <!-- Wizard Step Tabs -->
                <ul class="portal-nav-links">
                    <li class="<?= $activeStep === 'personal' ? 'active' : '' ?>">
                        <a href="?step=personal"><i class="fas fa-user"></i> A. Personal Details</a>
                    </li>
                    <li class="<?= $activeStep === 'identity' ? 'active' : '' ?>">
                        <a href="?step=identity"><i class="fas fa-id-badge"></i> B. Identity Proofs</a>
                    </li>
                    <li class="<?= $activeStep === 'education' ? 'active' : '' ?>">
                        <a href="?step=education"><i class="fas fa-graduation-cap"></i> C. Education & Degree</a>
                    </li>
                    <li class="<?= $activeStep === 'experience' ? 'active' : '' ?>">
                        <a href="?step=experience"><i class="fas fa-briefcase"></i> D. Work Experience</a>
                    </li>
                    <li class="<?= $activeStep === 'bank' ? 'active' : '' ?>">
                        <a href="?step=bank"><i class="fas fa-university"></i> E. Bank & Salary Account</a>
                    </li>
                    <li class="<?= $activeStep === 'emergency' ? 'active' : '' ?>">
                        <a href="?step=emergency"><i class="fas fa-phone-alt"></i> F. Emergency Contact</a>
                    </li>
                    <li class="<?= $activeStep === 'declaration' ? 'active' : '' ?>">
                        <a href="?step=declaration"><i class="fas fa-file-contract"></i> G. Consent & Signature</a>
                    </li>
                    <li style="margin-top:20px; border-top:1px solid var(--card-border); padding-top:10px;">
                        <a href="<?= url('/dashboard') ?>" style="color:var(--text-muted);"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                    </li>
                </ul>
            </aside>

            <!-- Main Wizard Form Area -->
            <div>
                <?php if ($isLocked): ?>
                    <div style="background:rgba(59,130,246,0.12); border:1px solid #3b82f6; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:center; gap:12px;">
                        <i class="fas fa-lock" style="color:#3b82f6; font-size:20px;"></i>
                        <span style="font-size:14px; color:var(--text);">Your profile and onboarding documents have been submitted successfully.</span>
                    </div>
                <?php endif; ?>

                <div class="portal-content-card">
                    <!-- ================================================================
                         SECTION A: PERSONAL INFORMATION
                         ================================================================ -->
                    <?php if ($activeStep === 'personal'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">A. Personal Details</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Provide your legal identity and demographic details.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= !empty($profile['father_name']) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= !empty($profile['father_name']) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= !empty($profile['father_name']) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <!-- Profile Photo Upload -->
                        <div style="background:rgba(255,255,255,0.02); border:1px dashed var(--glass-border); border-radius:12px; padding:20px; margin-bottom:28px; display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
                            <div style="width:76px; height:76px; border-radius:50%; background:var(--gold-gradient); overflow:hidden; display:flex; align-items:center; justify-content:center; font-size:24px; color:var(--primary); font-weight:800; border:1px solid var(--border);">
                                <?php if (!empty($docs['profile_photo'])): ?>
                                    <img src="<?= url('/document/download/' . $docs['profile_photo']['id']) ?>" style="width:100%; height:100%; object-fit:cover;" id="avatarPreview">
                                <?php else: ?>
                                    <span id="avatarPlaceholder"><?= strtoupper(substr($candidate['full_name'], 0, 1)) ?></span>
                                    <img id="avatarPreview" style="display:none; width:100%; height:100%; object-fit:cover;">
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <strong style="color:var(--text); font-size:14.5px; display:block; margin-bottom:4px;">Formal Passport Photo (Max 2 MB)</strong>
                                <p style="color:var(--text-muted); font-size:12.5px; margin-bottom:10px;">JPG, PNG, or WEBP. Automatically auto-compressed before upload.</p>
                                <?php if (!$isLocked): ?>
                                    <input type="file" id="photoFileInput" accept="image/*" style="display:none;" onchange="handleImageUpload(this, 'profile_photo')">
                                    <button type="button" class="btn-outline-gold" style="padding:6px 16px; font-size:12.5px;" onclick="document.getElementById('photoFileInput').click();">
                                        <i class="fas fa-camera"></i> <?= !empty($docs['profile_photo']) ? 'Change Photograph' : 'Upload Photograph' ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formPersonal">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="personal">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= e($candidate['full_name']) ?>" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date of Birth *</label>
                                    <input type="date" name="dob" class="form-control" value="<?= e($profile['dob'] ?? '') ?>" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Father's Name *</label>
                                    <input type="text" name="father_name" class="form-control" value="<?= e($profile['father_name'] ?? '') ?>" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Mother's Name *</label>
                                    <input type="text" name="mother_name" class="form-control" value="<?= e($profile['mother_name'] ?? '') ?>" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Gender *</label>
                                    <select name="gender" class="form-control" required <?= $isLocked ? 'disabled' : '' ?>>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?= ($profile['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= ($profile['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                        <option value="other" <?= ($profile['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Marital Status *</label>
                                    <select name="marital_status" class="form-control" required <?= $isLocked ? 'disabled' : '' ?>>
                                        <option value="">Select Status</option>
                                        <option value="single" <?= ($profile['marital_status'] ?? '') === 'single' ? 'selected' : '' ?>>Single</option>
                                        <option value="married" <?= ($profile['marital_status'] ?? '') === 'married' ? 'selected' : '' ?>>Married</option>
                                        <option value="divorced" <?= ($profile['marital_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Divorced</option>
                                        <option value="widowed" <?= ($profile['marital_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Widowed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Blood Group</label>
                                    <input type="text" name="blood_group" class="form-control" value="<?= e($profile['blood_group'] ?? '') ?>" placeholder="e.g. O+ve" <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Permanent Address *</label>
                                <textarea name="permanent_address" id="permAddrInput" class="form-control" rows="2" required <?= $isLocked ? 'disabled' : '' ?>><?= e($profile['permanent_address'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; color:var(--text-secondary); cursor:pointer;">
                                    <input type="checkbox" name="same_as_permanent" id="sameAsPermCheck" value="1" <?= !empty($profile['same_as_permanent']) ? 'checked' : '' ?> onchange="toggleCurrentAddress()" <?= $isLocked ? 'disabled' : '' ?> style="accent-color:var(--gold);">
                                    Current address is the same as permanent address
                                </label>
                            </div>

                            <div class="form-group" id="currentAddrGroup" style="<?= !empty($profile['same_as_permanent']) ? 'display:none;' : '' ?>">
                                <label class="form-label">Current / Present Address</label>
                                <textarea name="current_address" id="currAddrInput" class="form-control" rows="2" <?= $isLocked ? 'disabled' : '' ?>><?= e($profile['current_address'] ?? '') ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">City *</label>
                                    <input type="text" name="city" class="form-control" value="<?= e($profile['city'] ?? '') ?>" placeholder="e.g. Gurgaon" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">State *</label>
                                    <input type="text" name="state" class="form-control" value="<?= e($profile['state'] ?? '') ?>" placeholder="e.g. Haryana" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pincode *</label>
                                    <input type="text" name="pincode" class="form-control" value="<?= e($profile['pincode'] ?? '') ?>" placeholder="122001" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:24px;">
                                    <button type="button" class="btn-outline-gold" onclick="autoSaveSection('personal')">
                                        <i class="fas fa-save"></i> Save Draft
                                    </button>
                                    <button type="submit" class="btn-gold">
                                        Save & Next: Identity Proofs <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- ================================================================
                         SECTION B: IDENTITY DETAILS
                         ================================================================ -->
                    <?php if ($activeStep === 'identity'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">B. Identity Information</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Aadhaar & PAN are strictly encrypted at rest with AES-256 in compliance with DPDP standards.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= !empty($profile['pan_number']) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= !empty($profile['pan_number']) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= !empty($profile['pan_number']) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formIdentity">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="identity">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Aadhaar Card Number *</label>
                                    <input type="text" name="aadhaar_number" class="form-control"
                                           value="<?= !empty($profile['aadhaar_last4']) ? 'XXXX-XXXX-' . $profile['aadhaar_last4'] : '' ?>"
                                           placeholder="12-digit Aadhaar number" <?= $isLocked ? 'disabled' : '' ?>
                                           onfocus="if(this.value.includes('XXXX')) this.value='';" required>
                                    <span style="font-size:12px; color:var(--text-muted);"><i class="fas fa-shield-alt" style="color:var(--gold);"></i> Masked in UI and encrypted at rest with AES-256.</span>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">PAN Card Number *</label>
                                    <input type="text" name="pan_number" class="form-control" style="text-transform:uppercase;"
                                           value="<?= e($profile['pan_number'] ?? '') ?>"
                                           placeholder="e.g. ABCDE1234F" pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Passport Number (Optional)</label>
                                <input type="text" name="passport_number" class="form-control" style="text-transform:uppercase;"
                                       value="<?= e($profile['passport_number'] ?? '') ?>" placeholder="e.g. Z1234567" <?= $isLocked ? 'disabled' : '' ?>>
                            </div>

                            <!-- Document Uploads for Identity -->
                            <div style="margin:28px 0; padding-top:20px; border-top:1px solid var(--card-border);">
                                <h4 style="font-size:1.15rem; color:var(--text); margin-bottom:14px;">Upload Identity Proofs</h4>

                                <div class="form-row">
                                    <!-- Aadhaar Card Doc -->
                                    <div style="background:rgba(255,255,255,0.02); border:1px solid var(--card-border); border-radius:12px; padding:16px;">
                                        <strong style="color:var(--text); font-size:13.5px; display:block; margin-bottom:4px;">Aadhaar Card Copy *</strong>
                                        <p style="font-size:12px; color:var(--text-muted); margin-bottom:12px;">Front & Back in PDF, JPG, PNG (Max 2 MB)</p>
                                        <?php if (!empty($docs['aadhaar_card'])): ?>
                                            <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:8px 12px; font-size:12.5px; margin-bottom:8px;">
                                                <span style="color:#22c55e;"><i class="fas fa-check"></i> <?= e($docs['aadhaar_card']['original_name']) ?></span>
                                                <a href="<?= url('/document/download/' . $docs['aadhaar_card']['id']) ?>" target="_blank" style="color:var(--gold-light);">View</a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!$isLocked): ?>
                                            <input type="file" id="fileAadhaar" style="display:none;" onchange="handleDocUpload(this, 'aadhaar_card')">
                                            <button type="button" class="btn-outline-gold" style="width:100%; padding:7px; font-size:12.5px;" onclick="document.getElementById('fileAadhaar').click();">
                                                <i class="fas fa-upload"></i> <?= !empty($docs['aadhaar_card']) ? 'Replace Aadhaar' : 'Upload Aadhaar' ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- PAN Card Doc -->
                                    <div style="background:rgba(255,255,255,0.02); border:1px solid var(--card-border); border-radius:12px; padding:16px;">
                                        <strong style="color:var(--text); font-size:13.5px; display:block; margin-bottom:4px;">PAN Card Copy *</strong>
                                        <p style="font-size:12px; color:var(--text-muted); margin-bottom:12px;">Clear scan in PDF, JPG, PNG (Max 2 MB)</p>
                                        <?php if (!empty($docs['pan_card'])): ?>
                                            <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:8px 12px; font-size:12.5px; margin-bottom:8px;">
                                                <span style="color:#22c55e;"><i class="fas fa-check"></i> <?= e($docs['pan_card']['original_name']) ?></span>
                                                <a href="<?= url('/document/download/' . $docs['pan_card']['id']) ?>" target="_blank" style="color:var(--gold-light);">View</a>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!$isLocked): ?>
                                            <input type="file" id="filePan" style="display:none;" onchange="handleDocUpload(this, 'pan_card')">
                                            <button type="button" class="btn-outline-gold" style="width:100%; padding:7px; font-size:12.5px;" onclick="document.getElementById('filePan').click();">
                                                <i class="fas fa-upload"></i> <?= !empty($docs['pan_card']) ? 'Replace PAN' : 'Upload PAN' ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:24px;">
                                    <button type="button" class="btn-outline-gold" onclick="autoSaveSection('identity')">
                                        <i class="fas fa-save"></i> Save Draft
                                    </button>
                                    <button type="submit" class="btn-gold">
                                        Save & Next: Education <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- ================================================================
                         SECTION C: EDUCATION
                         ================================================================ -->
                    <?php if ($activeStep === 'education'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">C. Highest Education & Document</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Provide details of your highest educational qualification and upload the certificate.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= !empty($edu['highest_qualification']) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= !empty($edu['highest_qualification']) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= !empty($edu['highest_qualification']) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formEducation">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="education">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Highest Qualification *</label>
                                    <input type="text" name="highest_qualification" class="form-control" value="<?= e($edu['highest_qualification'] ?? '') ?>" placeholder="e.g. B.Tech / MBA / B.Com / Any Graduate" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Institute / University Name *</label>
                                    <input type="text" name="institute" class="form-control" value="<?= e($edu['institute'] ?? '') ?>" placeholder="e.g. Delhi University / Amity University" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Year of Passing *</label>
                                    <input type="number" name="year_of_passing" class="form-control" value="<?= e($edu['year_of_passing'] ?? date('Y')) ?>" min="1980" max="<?= date('Y') + 1 ?>" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Percentage / CGPA *</label>
                                    <input type="text" name="percentage_cgpa" class="form-control" value="<?= e($edu['percentage_cgpa'] ?? '') ?>" placeholder="e.g. 74% or 7.8 CGPA" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <!-- Last Study Document Upload -->
                            <div style="background:rgba(255,255,255,0.02); border:1px solid var(--card-border); border-radius:12px; padding:20px; margin:24px 0;">
                                <strong style="color:var(--text); font-size:14px; display:block; margin-bottom:4px;">Last Study Document / Degree / Mark Sheet *</strong>
                                <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:14px;">Upload degree certificate, provisional certificate, or final semester mark sheet (PDF, JPG, PNG under 2 MB).</p>

                                <?php if (!empty($docs['last_study'])): ?>
                                    <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:10px 14px; font-size:13px; margin-bottom:12px;">
                                        <span style="color:#22c55e;"><i class="fas fa-file-pdf"></i> <?= e($docs['last_study']['original_name']) ?> (v<?= $docs['last_study']['version'] ?>)</span>
                                        <a href="<?= url('/document/download/' . $docs['last_study']['id']) ?>" target="_blank" style="color:var(--gold-light); font-weight:600;">View Document</a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!$isLocked): ?>
                                    <input type="file" id="fileStudy" style="display:none;" onchange="handleDocUpload(this, 'last_study')">
                                    <button type="button" class="btn-outline-gold" style="padding:8px 20px; font-size:13px;" onclick="document.getElementById('fileStudy').click();">
                                        <i class="fas fa-upload"></i> <?= !empty($docs['last_study']) ? 'Replace Degree Document' : 'Upload Degree Document' ?>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:24px;">
                                    <button type="button" class="btn-outline-gold" onclick="autoSaveSection('education')">
                                        <i class="fas fa-save"></i> Save Draft
                                    </button>
                                    <button type="submit" class="btn-gold">
                                        Save & Next: Experience <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- ================================================================
                         SECTION D: WORK EXPERIENCE
                         ================================================================ -->
                    <?php if ($activeStep === 'experience'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">D. Work Experience</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Provide details of your past employment. If you are a fresher, toggle the Fresher checkbox.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= (!empty($exp) || !empty($exp['is_fresher'])) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= (!empty($exp) || !empty($exp['is_fresher'])) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= (!empty($exp) || !empty($exp['is_fresher'])) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formExperience">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="experience">

                            <div class="form-group" style="background:rgba(201,147,58,0.08); border:1px solid var(--glass-border); padding:16px 20px; border-radius:10px;">
                                <label style="display:flex; align-items:center; gap:10px; font-weight:700; color:var(--text); cursor:pointer;">
                                    <input type="checkbox" name="is_fresher" id="fresherCheck" value="1" <?= !empty($exp['is_fresher']) ? 'checked' : '' ?> onchange="toggleFresherFields()" <?= $isLocked ? 'disabled' : '' ?> style="accent-color:var(--gold); width:18px; height:18px;">
                                    I am a Fresher (No prior corporate work experience)
                                </label>
                                <p style="color:var(--text-muted); font-size:12.5px; margin:6px 0 0 28px;">Selecting this skips previous company documents and tailors your onboarding to our foundation cohort.</p>
                            </div>

                            <div id="experienceFieldsGroup" style="<?= !empty($exp['is_fresher']) ? 'display:none;' : '' ?>">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Previous Company Name</label>
                                        <input type="text" name="company" class="form-control" value="<?= e($exp['company'] ?? '') ?>" placeholder="e.g. DLF / Anarock / Square Yards" <?= $isLocked ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Designation / Role</label>
                                        <input type="text" name="designation" class="form-control" value="<?= e($exp['designation'] ?? '') ?>" placeholder="e.g. Sales Consultant / Business Development" <?= $isLocked ? 'disabled' : '' ?>>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Joining Date</label>
                                        <input type="date" name="joining_date" class="form-control" value="<?= e($exp['joining_date'] ?? '') ?>" <?= $isLocked ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Last Working Date</label>
                                        <input type="date" name="last_working_date" class="form-control" value="<?= e($exp['last_working_date'] ?? '') ?>" <?= $isLocked ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Last Salary / CTC</label>
                                        <input type="text" name="last_salary" class="form-control" value="<?= e($exp['last_salary'] ?? '') ?>" placeholder="e.g. ₹5.5 LPA" <?= $isLocked ? 'disabled' : '' ?>>
                                    </div>
                                </div>

                                <!-- Offer Letter & Relieving Letter Uploads -->
                                <div class="form-row" style="margin-top:20px;">
                                    <div style="background:rgba(255,255,255,0.02); border:1px solid var(--card-border); border-radius:12px; padding:16px;">
                                        <strong style="color:var(--text); font-size:13.5px; display:block; margin-bottom:4px;">Offer Letter</strong>
                                        <p style="font-size:12px; color:var(--text-muted); margin-bottom:10px;">PDF or Image under 2 MB</p>
                                        <?php if (!empty($docs['offer_letter'])): ?>
                                            <div style="font-size:12.5px; color:#22c55e; margin-bottom:8px;"><i class="fas fa-check"></i> <?= e($docs['offer_letter']['original_name']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!$isLocked): ?>
                                            <input type="file" id="fileOffer" style="display:none;" onchange="handleDocUpload(this, 'offer_letter')">
                                            <button type="button" class="btn-outline-gold" style="width:100%; padding:7px; font-size:12.5px;" onclick="document.getElementById('fileOffer').click();">
                                                Upload Offer Letter
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <div style="background:rgba(255,255,255,0.02); border:1px solid var(--card-border); border-radius:12px; padding:16px;">
                                        <strong style="color:var(--text); font-size:13.5px; display:block; margin-bottom:4px;">Relieving / Experience Letter</strong>
                                        <p style="font-size:12px; color:var(--text-muted); margin-bottom:10px;">PDF or Image under 2 MB</p>
                                        <?php if (!empty($docs['relieving_letter'])): ?>
                                            <div style="font-size:12.5px; color:#22c55e; margin-bottom:8px;"><i class="fas fa-check"></i> <?= e($docs['relieving_letter']['original_name']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!$isLocked): ?>
                                            <input type="file" id="fileRelieving" style="display:none;" onchange="handleDocUpload(this, 'relieving_letter')">
                                            <button type="button" class="btn-outline-gold" style="width:100%; padding:7px; font-size:12.5px;" onclick="document.getElementById('fileRelieving').click();">
                                                Upload Relieving Letter
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:24px;">
                                    <button type="button" class="btn-outline-gold" onclick="autoSaveSection('experience')">
                                        <i class="fas fa-save"></i> Save Draft
                                    </button>
                                    <button type="submit" class="btn-gold">
                                        Save & Next: Bank Details <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- ================================================================
                         SECTION E: BANK DETAILS
                         ================================================================ -->
                    <?php if ($activeStep === 'bank'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">E. Banking & Remittance Details</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Used for monthly payroll, allowances, and performance commissions. Encrypted with AES-256.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= !empty($bank['account_number_last4']) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= !empty($bank['account_number_last4']) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= !empty($bank['account_number_last4']) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formBank">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="bank">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Account Holder Name *</label>
                                    <input type="text" name="account_holder_name" class="form-control" value="<?= e($bank['account_holder_name'] ?? $candidate['full_name']) ?>" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Bank Name *</label>
                                    <input type="text" name="bank_name" class="form-control" value="<?= e($bank['bank_name'] ?? '') ?>" placeholder="e.g. HDFC Bank / ICICI Bank" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Account Number *</label>
                                    <input type="text" name="account_number" class="form-control"
                                           value="<?= !empty($bank['account_number_last4']) ? 'XXXX' . $bank['account_number_last4'] : '' ?>"
                                           placeholder="Full Account Number" onfocus="if(this.value.includes('XXXX')) this.value='';" required <?= $isLocked ? 'disabled' : '' ?>>
                                    <span style="font-size:12px; color:var(--text-muted);"><i class="fas fa-shield-alt" style="color:var(--gold);"></i> Encrypted at rest.</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">IFSC Code *</label>
                                    <input type="text" name="ifsc_code" class="form-control" style="text-transform:uppercase;" value="<?= e($bank['ifsc_code'] ?? '') ?>" placeholder="e.g. HDFC0001234" pattern="^[A-Za-z]{4}0[A-Za-z0-9]{6}$" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <!-- Cancelled Cheque Upload -->
                            <div style="background:rgba(255,255,255,0.02); border:1px solid var(--card-border); border-radius:12px; padding:20px; margin:24px 0;">
                                <strong style="color:var(--text); font-size:14px; display:block; margin-bottom:4px;">Cancelled Cheque / Passbook Leaf *</strong>
                                <p style="font-size:12.5px; color:var(--text-muted); margin-bottom:12px;">Ensure account number and IFSC are clearly legible (Max 2 MB).</p>

                                <?php if (!empty($docs['cancelled_cheque'])): ?>
                                    <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:10px 14px; font-size:13px; margin-bottom:12px;">
                                        <span style="color:#22c55e;"><i class="fas fa-check"></i> <?= e($docs['cancelled_cheque']['original_name']) ?></span>
                                        <a href="<?= url('/document/download/' . $docs['cancelled_cheque']['id']) ?>" target="_blank" style="color:var(--gold-light); font-weight:600;">View Cheque</a>
                                    </div>
                                <?php endif; ?>

                                <?php if (!$isLocked): ?>
                                    <input type="file" id="fileCheque" style="display:none;" onchange="handleDocUpload(this, 'cancelled_cheque')">
                                    <button type="button" class="btn-outline-gold" style="padding:8px 20px; font-size:13px;" onclick="document.getElementById('fileCheque').click();">
                                        <i class="fas fa-upload"></i> <?= !empty($docs['cancelled_cheque']) ? 'Replace Cheque Copy' : 'Upload Cancelled Cheque' ?>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:24px;">
                                    <button type="button" class="btn-outline-gold" onclick="autoSaveSection('bank')">
                                        <i class="fas fa-save"></i> Save Draft
                                    </button>
                                    <button type="submit" class="btn-gold">
                                        Save & Next: Emergency Contact <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- ================================================================
                         SECTION F: EMERGENCY CONTACT
                         ================================================================ -->
                    <?php if ($activeStep === 'emergency'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">F. Emergency Contact</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Provide details of an immediate family member for workplace safety records.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= !empty($emg['contact_name']) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= !empty($emg['contact_name']) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= !empty($emg['contact_name']) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formEmergency">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="emergency">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Contact Person Name *</label>
                                    <input type="text" name="contact_name" class="form-control" value="<?= e($emg['contact_name'] ?? '') ?>" placeholder="e.g. Ramesh Malhotra" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Relationship *</label>
                                    <input type="text" name="relationship" class="form-control" value="<?= e($emg['relationship'] ?? '') ?>" placeholder="e.g. Father / Mother / Spouse / Sibling" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Primary Mobile *</label>
                                    <input type="tel" name="mobile" class="form-control" value="<?= e($emg['mobile'] ?? '') ?>" placeholder="10-digit Mobile" pattern="[6-9][0-9]{9}" required <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Alternate Number</label>
                                    <input type="tel" name="alternate_mobile" class="form-control" value="<?= e($emg['alternate_mobile'] ?? '') ?>" placeholder="Alternate Mobile or Landline" <?= $isLocked ? 'disabled' : '' ?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Emergency Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Full residential address of emergency contact" <?= $isLocked ? 'disabled' : '' ?>><?= e($emg['address'] ?? '') ?></textarea>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:24px;">
                                    <button type="button" class="btn-outline-gold" onclick="autoSaveSection('emergency')">
                                        <i class="fas fa-save"></i> Save Draft
                                    </button>
                                    <button type="submit" class="btn-gold">
                                        Save & Next: Consent & Signature <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- ================================================================
                         SECTION G: DECLARATION & DIGITAL SIGNATURE
                         ================================================================ -->
                    <?php if ($activeStep === 'declaration'): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                            <div>
                                <h3 style="font-size:1.5rem; color:var(--text); margin-bottom:4px;">G. Declaration & Consent</h3>
                                <p style="color:var(--text-muted); font-size:13.5px; margin:0;">Affirm the accuracy of your submission and affix your digital signature.</p>
                            </div>
                            <span style="font-size:12px; padding:4px 12px; border-radius:20px; background:<?= !empty($profile['declaration_accepted']) ? 'rgba(34,197,94,0.15)' : 'rgba(245,158,11,0.15)' ?>; color:<?= !empty($profile['declaration_accepted']) ? '#22c55e' : '#f59e0b' ?>; font-weight:700;">
                                <?= !empty($profile['declaration_accepted']) ? 'COMPLETED' : 'PENDING' ?>
                            </span>
                        </div>

                        <form action="<?= url('/dashboard/profile/save') ?>" method="POST" id="formDeclaration">
                            <?= csrf_field() ?>
                            <input type="hidden" name="section" value="declaration">

                            <div style="background:rgba(255,255,255,0.03); border:1px solid var(--card-border); border-radius:12px; padding:24px; margin-bottom:28px; font-size:13.5px; color:var(--text-secondary); line-height:1.75;">
                                <strong style="color:var(--text); font-size:15px; display:block; margin-bottom:10px;">Candidate Declaration Statement:</strong>
                                <p style="margin-bottom:12px;">1. I hereby confirm that all information entered in this onboarding portal (personal, demographic, identity, educational certificates, past employment records, and banking credentials) is true, authentic, and complete.</p>
                                <p style="margin-bottom:12px;">2. I acknowledge that Realty Smartz Pathshala retains the right to verify my documents with issuing institutions and previous employers.</p>
                                <p style="margin-bottom:0;">3. I understand that any deliberate omission or fabricated credentials shall lead to immediate revocation of onboarding and termination of employment without compensation.</p>
                            </div>

                            <div class="form-group" style="margin-bottom:28px;">
                                <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer; font-size:14px; color:var(--text);">
                                    <input type="checkbox" name="declaration_accepted" value="1" <?= !empty($profile['declaration_accepted']) ? 'checked' : '' ?> required <?= $isLocked ? 'disabled' : '' ?> style="accent-color:var(--gold); width:20px; height:20px; margin-top:2px;">
                                    <span>I have read, understood, and solemnly accept the declaration above on this date (<?= date('d M Y') ?>).</span>
                                </label>
                            </div>

                            <!-- Digital Signature Canvas -->
                            <div style="margin-bottom:28px;">
                                <label class="form-label">Digital Signature (Draw below with mouse / finger) *</label>
                                <div style="background:#ffffff; border-radius:10px; padding:6px; display:inline-block; border:1px solid var(--border);">
                                    <canvas id="signaturePad" width="460" height="160" style="display:block; cursor:crosshair; background:#ffffff;"></canvas>
                                </div>
                                <input type="hidden" name="signature_data" id="signatureDataInput" value="<?= e($profile['signature_data'] ?? '') ?>">

                                <?php if (!$isLocked): ?>
                                    <div style="margin-top:10px;">
                                        <button type="button" id="clearSigBtn" class="btn-outline-gold" style="padding:5px 14px; font-size:12px;">
                                            <i class="fas fa-undo"></i> Clear Signature
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!$isLocked): ?>
                                <div style="display:flex; justify-content:space-between; margin-top:30px; padding-top:20px; border-top:1px solid var(--card-border);">
                                    <button type="submit" class="btn-outline-gold">
                                        <i class="fas fa-save"></i> Save Declaration
                                    </button>
                                    <button type="button" class="btn-gold" onclick="submitFullProfile()" style="padding:14px 28px; font-size:14.5px;">
                                        <i class="fas fa-lock"></i> Submit Entire Profile for Review
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client-Side Document & Canvas Helper Scripts -->
<script>
function toggleCurrentAddress() {
    const isSame = document.getElementById('sameAsPermCheck').checked;
    const group = document.getElementById('currentAddrGroup');
    if (group) group.style.display = isSame ? 'none' : 'block';
}

function toggleFresherFields() {
    const isFresher = document.getElementById('fresherCheck').checked;
    const group = document.getElementById('experienceFieldsGroup');
    if (group) group.style.display = isFresher ? 'none' : 'block';
}

// Upload handlers with client-side canvas compression
async function handleImageUpload(input, docType) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    showToast('Compressing image on client canvas...', 'info');
    const compressed = await compressImageFile(file, 1600, 0.85);

    const formData = new FormData();
    formData.append('file', compressed);
    formData.append('document_type', docType);
    formData.append('_csrf_token', '<?= csrf_token() ?>');

    try {
        const resp = await fetch('<?= url('/dashboard/document/upload') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const res = await resp.json();
        if (res.success) {
            showToast('Photograph uploaded successfully!', 'success');
            const preview = document.getElementById('avatarPreview');
            const placeholder = document.getElementById('avatarPlaceholder');
            if (placeholder) placeholder.style.display = 'none';
            if (preview) {
                preview.src = res.document.download_url + '?t=' + Date.now();
                preview.style.display = 'block';
            }
        } else {
            showToast(res.error || 'Upload failed.', 'error');
        }
    } catch (e) {
        showToast('Network error during upload.', 'error');
    }
}

async function handleDocUpload(input, docType) {
    if (!input.files || !input.files[0]) return;
    let file = input.files[0];

    if (file.type.startsWith('image/')) {
        showToast('Compressing image...', 'info');
        file = await compressImageFile(file, 1600, 0.85);
    } else if (file.size > 2097152) {
        showToast('File exceeds 2 MB limit. Please compress online before uploading.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('document_type', docType);
    formData.append('_csrf_token', '<?= csrf_token() ?>');

    showToast('Uploading document...', 'info');

    try {
        const resp = await fetch('<?= url('/dashboard/document/upload') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const res = await resp.json();
        if (res.success) {
            showToast('Document uploaded successfully!', 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            showToast(res.error || 'Upload failed.', 'error');
        }
    } catch (e) {
        showToast('Network error during upload.', 'error');
    }
}

// Auto-save via AJAX
async function autoSaveSection(section) {
    const form = document.querySelector('form[action*="dashboard/profile/save"]');
    if (!form) return;

    const formData = new FormData(form);
    formData.append('is_ajax', '1');

    try {
        const resp = await fetch('<?= url('/dashboard/profile/save') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const res = await resp.json();
        if (res.success) {
            showToast('Draft auto-saved! (' + res.completion_percentage + '% complete)', 'success');
            const sideBar = document.getElementById('sideCompletionBar');
            const sidePct = document.getElementById('sideCompletionPct');
            if (sideBar) sideBar.style.width = res.completion_percentage + '%';
            if (sidePct) sidePct.innerText = res.completion_percentage;
        } else {
            showToast(res.error || 'Could not save draft.', 'error');
        }
    } catch (e) {
        showToast('Auto-save network error.', 'error');
    }
}

function submitFullProfile() {
    if (!confirm('Are you sure you want to submit your profile for HR verification? Once submitted, your profile will be locked for review.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= url('/dashboard/profile/submit') ?>';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_csrf_token';
    csrf.value = '<?= csrf_token() ?>';

    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}
</script>
<?php
$content = ob_get_clean();
$pageTitle = "Complete Onboarding Profile | Realty Smartz Pathshala";
require APP_PATH . '/views/layouts/main.php';
?>
