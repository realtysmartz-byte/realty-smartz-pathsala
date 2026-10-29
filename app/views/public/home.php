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
                <!-- Eyebrow with Pulsing Live Activity Indicator -->
                <div class="hero-live-badge mb-3">
                    <span class="live-dot-pulse"></span>
                    <span class="hero-live-text">ADMISSIONS OPEN &bull; OCTOBER 2026 BATCH &bull; 100% PLACEMENT SUPPORT</span>
                </div>

                <!-- Dynamic Animated Headline with Word Rotator -->
                <h1 class="hero-title mb-3">
                    Master the Art of<br>
                    <span class="rotator-text-wrap">
                        <span class="rotator-text active" id="heroRotatorText">Real Estate Sales</span>
                        <span class="rotator-cursor">|</span>
                    </span>
                </h1>
                <hr class="gold-rule">
                <p class="lead mb-4">
                    Empowering businesses with skilled professionals — we build high-performing sales teams that drive smarter strategies, lucrative commissions, and stronger results.
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
                <div class="hero-image-wrap text-center w-100 position-relative">
                    <img src="<?= asset('/assets/img/gallery/about3.png') ?>" alt="Realty Smartz Pathshala Training Session" class="img-fluid hero-img" width="600" height="450" loading="eager" fetchpriority="high">
                    <!-- Floating Highlight Badge -->
                    <div class="hero-floating-card">
                        <div class="hero-floating-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C9A24A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        </div>
                        <div class="text-start">
                            <div class="hero-floating-title">98% Placement Rate</div>
                            <div class="hero-floating-sub">Top Tier Developer Allocation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- LIVE MARQUEE TICKER (Interactive Real-Time Ecosystem) -->
<div class="live-ticker-strip">
    <div class="live-ticker-track">
        <div class="live-ticker-content">
            <span class="ticker-pill"><span class="ticker-sparkle">✦</span> LIVE COHORT 2026</span>
            <span class="ticker-item">Over 500+ Professionals Placed in Top Real Estate Firms</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Hiring Developers: DLF, Godrej, BPTP, M3M, Signature Global, Eldeco, Adani</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Highest Package: ₹18.5 LPA</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">100% Practical Field Training &amp; Closing Mastery</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Instant Aptitude Assessment &amp; Fast-Track Selection</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Gurugram's Premier Real Estate Finishing School</span>
            <span class="ticker-dot">•</span>
        </div>
        <div class="live-ticker-content" aria-hidden="true">
            <span class="ticker-pill"><span class="ticker-sparkle">✦</span> LIVE COHORT 2026</span>
            <span class="ticker-item">Over 500+ Professionals Placed in Top Real Estate Firms</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Hiring Developers: DLF, Godrej, BPTP, M3M, Signature Global, Eldeco, Adani</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Highest Package: ₹18.5 LPA</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">100% Practical Field Training &amp; Closing Mastery</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Instant Aptitude Assessment &amp; Fast-Track Selection</span>
            <span class="ticker-dot">•</span>
            <span class="ticker-item">Gurugram's Premier Real Estate Finishing School</span>
            <span class="ticker-dot">•</span>
        </div>
    </div>
</div>

<!-- 2. STATS ROW (Single Horizontal Row Separated by Thin Vertical Lines, Dynamic Count Up) -->
<section class="pb-5">
    <div class="container">
        <div class="stats-row" data-reveal>
            <div class="stat-item">
                <div class="stat-value" data-counter="500" data-suffix="+">500+</div>
                <div class="stat-label">Professionals Trained</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" data-counter="50" data-suffix="+">50+</div>
                <div class="stat-label">Live Projects</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" data-counter="98" data-suffix="%">98%</div>
                <div class="stat-label">Placement Rate</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" data-counter="10" data-suffix="+">10+</div>
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
