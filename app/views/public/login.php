<?php
ob_start();
?>
<section class="hero section-pad d-flex align-items-center" style="min-height: 80vh;">
    <div class="container" style="max-width: 980px;">
        <div class="row g-4 align-items-stretch">
            <!-- Left: Form -->
            <div class="col-lg-6">
                <div class="p-4 p-md-5 bg-white border h-100 d-flex flex-column justify-content-between overflow-hidden" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <div class="eyebrow mb-2">Candidate Portal</div>
                        <h1 class="h2 mb-2">Welcome Back</h1>
                        <p class="small text-muted mb-4">Sign in to check your onboarding status or complete your documentation wizard.</p>

                        <form action="<?= url('/login') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label" for="login_email">Registered Email</label>
                                <input type="email" id="login_email" name="email" class="form-control" placeholder="name@example.com" value="<?= e(old('email')) ?>" required autocomplete="email" autofocus>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0" for="login_password">Password</label>
                                    <a href="<?= url('/forgot-password') ?>" class="small text-primary text-decoration-none">Forgot password?</a>
                                </div>
                                <input type="password" id="login_password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                <label class="form-check-label small text-muted" for="remember">Keep me signed in</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Sign In to Portal
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 mt-4 border-top text-center small text-muted" style="border-color: var(--line) !important;">
                        Don't have an onboarding profile yet?<br>
                        <a href="<?= url('/register') ?>" class="text-primary fw-bold text-decoration-none mt-1 d-inline-block">
                            Start Candidate Registration &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Photo & Editorial Info (Desktop) -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="h-100 border overflow-hidden position-relative d-flex flex-column justify-content-end p-4 bg-white" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <img src="<?= asset('/assets/img/gallery/towers.jpg') ?>" alt="Realty Smartz Pathshala Campus" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" loading="lazy">
                    <div class="position-relative p-4 bg-white border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                        <div class="small fw-bold text-dark mb-1">Professional Real Estate Training</div>
                        <div class="small text-muted">Equipping candidate cohorts with in-depth micro-market mastery, negotiation frameworks, and live project immersion in Gurgaon.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "Candidate Login | Realty Smartz Pathshala";
$pageDescription = "Log in to your candidate onboarding portal to submit documents, track status, and take the aptitude assessment.";
require APP_PATH . '/views/layouts/main.php';
?>
