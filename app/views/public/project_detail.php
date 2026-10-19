<?php
ob_start();
?>
<section class="hero section-pad">
    <div class="container">
        <!-- Breadcrumb -->
        <div class="mb-4">
            <a href="<?= url('/projects') ?>" class="small text-muted text-decoration-none">&larr; Back to all projects</a>
        </div>

        <div class="row g-5">
            <!-- Left Column: Project Overview -->
            <div class="col-lg-7">
                <div class="eyebrow mb-2">
                    <?= e($project['category']) ?> &bull; <?= e($project['location']) ?>
                </div>

                <h1 class="h2 mb-2">
                    <?= e($project['title']) ?>
                </h1>

                <hr class="gold-rule">

                <p class="lead fw-bold mb-4" style="color: var(--brand);">
                    Starting at <?= e($project['price_range']) ?>
                    <?php if (!empty($project['type_config'])): ?>
                        <span class="text-muted fw-normal fs-6"> &bull; <?= e($project['type_config']) ?></span>
                    <?php endif; ?>
                </p>

                <!-- Project Image -->
                <div class="overflow-hidden border mb-4" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <?php if (!empty($project['image_url'])): ?>
                        <img src="<?= asset($project['image_url']) ?>" alt="<?= e($project['title']) ?>" class="w-100" style="aspect-ratio: 16/10; object-fit: cover;" loading="lazy">
                    <?php else: ?>
                        <img src="<?= asset('/assets/img/gallery/topic1.png') ?>" alt="<?= e($project['title']) ?>" class="w-100" style="aspect-ratio: 16/10; object-fit: cover;" loading="lazy">
                    <?php endif; ?>
                </div>

                <h2 class="h3 mb-2">Project Description</h2>
                <hr class="gold-rule">
                <div class="mb-5 text-muted" style="font-size: 16px; line-height: 1.7;">
                    <?= nl2br(e($project['description'] ?? 'Exclusive landmark development equipped with signature high-rise architecture, cutting-edge smart home automation, and world-class residential amenities.')) ?>
                </div>

                <!-- Highlights -->
                <?php if (!empty($project['highlights_array'])): ?>
                    <h2 class="h3 mb-2">Key Highlights</h2>
                    <hr class="gold-rule">
                    <div class="row g-2 mb-4">
                        <?php foreach ($project['highlights_array'] as $h): ?>
                            <div class="col-sm-6">
                                <div class="p-3 bg-white border small fw-bold text-dark" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                    &bull; <?= e($h) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Consultation Enquiry Form -->
            <div class="col-lg-5">
                <div class="p-4 p-md-5 bg-white border position-sticky" style="top: 100px; border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div class="eyebrow mb-2">Direct Consultation</div>
                    <h2 class="h3 mb-2">Enquire on this Project</h2>
                    <hr class="gold-rule">
                    <p class="small text-muted mb-4">
                        Connect with our property advisory desk for floor plans, price sheets, and site walkthrough schedules.
                    </p>

                    <form action="<?= url('/contact/send') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="subject" value="Enquiry on <?= e($project['title']) ?>">

                        <div class="mb-3">
                            <label class="form-label" for="proj_name">Full Name *</label>
                            <input type="text" id="proj_name" name="name" class="form-control" placeholder="Your Name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="proj_phone">Mobile Number *</label>
                            <input type="tel" id="proj_phone" name="phone" class="form-control" placeholder="10-digit Mobile Number" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="proj_email">Email Address *</label>
                            <input type="email" id="proj_email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="proj_message">Message / Requirements</label>
                            <textarea id="proj_message" name="message" class="form-control" rows="3">I am interested in knowing more about <?= e($project['title']) ?>. Please share brochure and price details.</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            Submit Project Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = e($project['title']) . " | Realty Smartz Pathshala";
$pageDescription = e($project['title']) . " in " . e($project['location']) . " starting at " . e($project['price_range']) . ". Real estate training and advisory portfolio.";
require APP_PATH . '/views/layouts/main.php';
?>
