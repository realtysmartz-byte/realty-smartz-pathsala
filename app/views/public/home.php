<?php
$pageTitle = "Realty Smartz Pathshala | Master the Art of Real Estate";
$pageDescription = "Empowering businesses with skilled professionals — we build high-performing sales teams that drive smarter strategies and stronger results.";
ob_start();
?>

<!-- 1. HERO SECTION (Two Columns, Left-aligned H1, Real Photo Right, No Gradient) -->
<section class="hero section-pad">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left Column: Editorial Typography Block -->
            <div class="col-lg-6">
                <span class="eyebrow">India's #1 Real Estate Training</span>
                <h1 class="mb-3">Master the Art of Real Estate</h1>
                <hr class="gold-rule">
                <p class="lead mb-4">
                    Empowering businesses with skilled professionals — we build high-performing sales teams that drive smarter strategies and stronger results.
                </p>
                <div class="d-flex flex-wrap gap-3 hero-cta-group">
                    <a href="<?= url('/register') ?>" class="btn btn-primary btn-lg">
                        Start Candidate Onboarding
                    </a>
                    <a href="<?= url('/projects') ?>" class="btn btn-secondary btn-lg">
                        Explore Projects
                    </a>
                </div>
            </div>

            <!-- Right Column: Organic Hero Graphic with Transparent Background (No Border) -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="hero-image-wrap text-center w-100">
                    <img src="<?= asset('/assets/img/gallery/about3.png') ?>" alt="Realty Smartz Pathshala Training Session" class="img-fluid hero-img" width="600" height="450" loading="eager" fetchpriority="high">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. STATS ROW (Single Horizontal Row Separated by Thin Vertical Lines, No Cards) -->
<section class="pb-5">
    <div class="container">
        <div class="stats-row" data-reveal>
            <div class="stat-item">
                <div class="stat-value">500+</div>
                <div class="stat-label">Professionals Trained</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">50+</div>
                <div class="stat-label">Live Projects</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">98%</div>
                <div class="stat-label">Placement Rate</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">10+</div>
                <div class="stat-label">Years Active</div>
            </div>
        </div>
    </div>
</section>

<!-- 3. SERVICES (Editorial Numbered List: Number, Title, Description, Thin Divider Lines) -->
<section class="section-pad">
    <div class="container">
        <div class="mb-5">
            <span class="eyebrow">What We Offer</span>
            <h2>Our Core Services</h2>
            <hr class="gold-rule">
        </div>

        <div class="editorial-list" data-reveal>
            <!-- Service 1 -->
            <div class="editorial-item">
                <div class="editorial-num">01</div>
                <h3 class="editorial-title">Real Estate Expertise</h3>
                <p class="editorial-desc">
                    Delivering top-tier property solutions with trusted market insights and proven strategies.
                </p>
            </div>

            <!-- Service 2 -->
            <div class="editorial-item">
                <div class="editorial-num">02</div>
                <h3 class="editorial-title">Project Training</h3>
                <p class="editorial-desc">
                    Empowering employees through expert-led training on the latest real estate developments.
                </p>
            </div>

            <!-- Service 3 -->
            <div class="editorial-item">
                <div class="editorial-num">03</div>
                <h3 class="editorial-title">Employee Achievements</h3>
                <p class="editorial-desc">
                    Celebrating our team's milestones, growth, and outstanding contributions every step.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 4. ONBOARDING STEPS (Horizontal Timeline on Desktop, Vertical on Mobile) -->
<section class="section-pad border-top" style="border-color: var(--line) !important;">
    <div class="container">
        <div class="mb-4">
            <span class="eyebrow">Candidate Pipeline</span>
            <h2>How Onboarding Works</h2>
            <hr class="gold-rule">
            <p class="mb-0 text-muted">A structured 5-step pathway from registration to direct placement.</p>
        </div>

        <div class="timeline-track" data-reveal>
            <div class="timeline-step">
                <div class="timeline-step-num">1</div>
                <div class="timeline-step-title">Register Online</div>
                <p class="timeline-step-text">Create your account with your name, email, and mobile number.</p>
            </div>
            <div class="timeline-step">
                <div class="timeline-step-num">2</div>
                <div class="timeline-step-title">Verify Email</div>
                <p class="timeline-step-text">Authenticate your profile with the 6-digit OTP sent to your inbox.</p>
            </div>
            <div class="timeline-step">
                <div class="timeline-step-num">3</div>
                <div class="timeline-step-title">Profile &amp; Documents</div>
                <p class="timeline-step-text">Provide your background details and upload Aadhaar and PAN cards.</p>
            </div>
            <div class="timeline-step">
                <div class="timeline-step-num">4</div>
                <div class="timeline-step-title">Aptitude Test</div>
                <p class="timeline-step-text">Complete the 15-question assessment with instant scoring.</p>
            </div>
            <div class="timeline-step">
                <div class="timeline-step-num">5</div>
                <div class="timeline-step-title">HR Approval</div>
                <p class="timeline-step-text">Profile review and direct placement allocation with partner developers.</p>
            </div>
        </div>
    </div>
</section>

<!-- 5. FEATURED PROJECTS (3-Column Image Grid, Title + Location Under Image, NO Card Borders) -->
<section class="section-pad">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <span class="eyebrow">Explore Inventory</span>
                <h2 class="mb-0">Featured Projects</h2>
                <hr class="gold-rule">
            </div>
            <a href="<?= url('/projects') ?>" class="btn btn-secondary">
                View All Projects
            </a>
        </div>

        <div class="projects-grid" data-reveal>
            <?php
            $featuredHome = !empty($projects) ? array_slice($projects, 0, 6) : [];
            if (!empty($featuredHome)):
                foreach ($featuredHome as $proj):
            ?>
                    <a href="<?= !empty($proj['slug']) ? url('/projects/' . $proj['slug']) : url('/projects') ?>" class="project-item">
                        <div class="project-item-thumb">
                            <img src="<?= asset($proj['image_url'] ?? $proj['cover_image'] ?? '/assets/img/gallery/topic1.png') ?>" alt="<?= e($proj['title']) ?>" width="400" height="250" loading="lazy">
                        </div>
                        <div class="project-item-title"><?= e($proj['title']) ?></div>
                        <div class="project-item-meta"><?= e($proj['location'] ?? 'Gurgaon, Haryana') ?> &bull; <?= e($proj['price_range'] ?? 'On Request') ?></div>
                    </a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- 6. LATEST FROM THE BLOG (3 Items, Image on Top, Title, Date) -->
<section class="section-pad border-top" style="border-color: var(--line) !important;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <span class="eyebrow">Market Insights</span>
                <h2 class="mb-0">Latest From the Blog</h2>
                <hr class="gold-rule">
            </div>
            <a href="<?= url('/blog') ?>" class="btn btn-secondary">
                View All Articles
            </a>
        </div>

        <div class="blog-grid" data-reveal>
            <!-- Article 1 -->
            <a href="<?= url('/blog/why-real-estate-sales-is-indias-highest-earning-career') ?>" class="blog-item">
                <div class="blog-item-thumb">
                    <img src="<?= asset('/assets/img/gallery/topic1.png') ?>" alt="Real Estate Sales Career" width="400" height="250" loading="lazy">
                </div>
                <div class="blog-item-date">October 2026</div>
                <div class="blog-item-title">
                    Why Real Estate Sales is India's Highest Earning Career in 2026
                </div>
                <p class="small text-muted mb-0">Market absorption and multi-crore transactions in Gurgaon.</p>
            </a>

            <!-- Article 2 -->
            <a href="<?= url('/blog/5-core-closing-skills-every-luxury-property-consultant-needs') ?>" class="blog-item">
                <div class="blog-item-thumb">
                    <img src="<?= asset('/assets/img/gallery/topic2.png') ?>" alt="Luxury Property Closing Skills" width="400" height="250" loading="lazy">
                </div>
                <div class="blog-item-date">October 2026</div>
                <div class="blog-item-title">
                    5 Core Closing Skills Every Luxury Property Consultant Needs
                </div>
                <p class="small text-muted mb-0">Consultative listening, micro-market pricing, and urgency creation.</p>
            </a>

            <!-- Article 3 -->
            <a href="<?= url('/blog/understanding-circle-rates-rera-carpet-area-calculations') ?>" class="blog-item">
                <div class="blog-item-thumb">
                    <img src="<?= asset('/assets/img/gallery/topic3.png') ?>" alt="RERA and Carpet Area" width="400" height="250" loading="lazy">
                </div>
                <div class="blog-item-date">October 2026</div>
                <div class="blog-item-title">
                    Understanding Circle Rates, RERA, and Carpet Area Calculations
                </div>
                <p class="small text-muted mb-0">Protecting clients through property documentation mastery.</p>
            </a>
        </div>
    </div>
</section>

<!-- 7. CLOSING CTA BAND (Simple Full-Width Band in --surface-2, 1 Heading + 1 Button) -->
<section class="closing-cta-band bg-surface-2">
    <div class="container text-center">
        <h2 class="mb-3">Ready to Start Your Real Estate Career?</h2>
        <hr class="gold-rule gold-rule-center">
        <p class="text-muted mx-auto mb-4" style="max-width: 520px;">
            Join our structured onboarding program and master real estate sales, documentation, and client closing.
        </p>
        <a href="<?= url('/register') ?>" class="btn btn-primary btn-lg">
            Start Candidate Onboarding
        </a>
    </div>
</section>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/main.php';
?>
