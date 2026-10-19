<?php
ob_start();
?>

<!-- Page Header (Plain H1 + One Line) -->
<section class="hero section-pad pb-4">
    <div class="container">
        <h1>About Realty Smartz Pathshala</h1>
        <hr class="gold-rule">
        <p class="lead mb-0">Empowering businesses with skilled professionals — we build high-performing sales teams.</p>
    </div>
</section>

<!-- Content on the same Grid -->
<section class="section-pad pt-2">
    <div class="container">
        <!-- Two Column Overview -->
        <div class="row align-items-center g-5 mb-5 pb-4">
            <div class="col-lg-6">
                <h2 class="mb-3">Learn New Skills with Top Realtors</h2>
                <hr class="gold-rule">
                <p class="mb-4">
                    Learn new skills, gain real-world insights, and grow your career by working alongside top-performing realtors at Realty Smartz. We equip team members with in-depth real estate knowledge, sales skills, and live project insights.
                </p>
                <div class="d-flex flex-column gap-3">
                    <div class="border-bottom pb-3" style="border-color: var(--line) !important;">
                        <h4 class="mb-1">Live Market Exposure</h4>
                        <p class="small text-muted mb-0">Learn directly from experts actively closing deals in Gurgaon's dynamic real estate market.</p>
                    </div>
                    <div class="border-bottom pb-3" style="border-color: var(--line) !important;">
                        <h4 class="mb-1">Expert Mentorship</h4>
                        <p class="small text-muted mb-0">Get one-on-one guidance from seasoned professionals with proven sales strategies.</p>
                    </div>
                    <div>
                        <h4 class="mb-1">Live Project Training</h4>
                        <p class="small text-muted mb-0">Work on actual live projects — gain practical knowledge that accelerates your career.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="about-image-wrap w-100 overflow-hidden text-center" style="border-radius: var(--r-lg); border: none !important;">
                    <img src="<?= asset('/assets/img/gallery/towers.jpg') ?>" alt="Realty Smartz Luxury Developments & Practical Training" class="img-fluid" style="max-height: 440px; width: 100%; height: auto; object-fit: cover; border-radius: var(--r-lg); border: none !important; box-shadow: 0 16px 36px rgba(38, 51, 45, 0.08);" width="600" height="450" loading="eager">
                </div>
            </div>
        </div>

        <!-- Recognition & Achievements -->
        <div class="row g-4 pt-4 border-top" style="border-color: var(--line) !important;">
            <div class="col-md-4">
                <h3>Record-Breaking Sales</h3>
                <p class="small text-muted mb-0">Our team consistently surpasses sales targets and delivers exceptional results across live projects in Gurgaon.</p>
            </div>
            <div class="col-md-4">
                <h3>Client Excellence</h3>
                <p class="small text-muted mb-0">Employees recognized for outstanding service, transparent RERA advisory, and long-term client relationships.</p>
            </div>
            <div class="col-md-4">
                <h3>Culture of Growth</h3>
                <p class="small text-muted mb-0">We celebrate individual milestones, fostering a culture of continuous learning, appreciation, and leadership opportunities.</p>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "About Us | Realty Smartz Pathshala";
$pageDescription = "Learn new skills with top realtors. Discover our training pillars, market exposure, and employee growth milestones.";
require APP_PATH . '/views/layouts/main.php';
?>
