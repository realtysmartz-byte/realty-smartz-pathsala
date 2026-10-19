<?php
ob_start();
$activeCat = $_GET['cat'] ?? 'all';
?>

<!-- Page Header (Plain H1 + One Line) -->
<section class="hero section-pad pb-4">
    <div class="container">
        <h1>Featured Projects</h1>
        <hr class="gold-rule">
        <p class="lead mb-0">Handpicked premium real estate developments curated for professional site walkthroughs.</p>
    </div>
</section>

<!-- Filter Row + 3-Column Image Grid -->
<section class="section-pad pt-2">
    <div class="container">
        <!-- Filter Row -->
        <div class="d-flex flex-wrap gap-2 mb-5">
            <a href="<?= url('/projects') ?>" class="btn btn-sm <?= $activeCat === 'all' ? 'btn-primary' : 'btn-secondary' ?>">All Projects</a>
            <a href="<?= url('/projects?cat=featured') ?>" class="btn btn-sm <?= $activeCat === 'featured' ? 'btn-primary' : 'btn-secondary' ?>">Featured</a>
            <a href="<?= url('/projects?cat=luxury') ?>" class="btn btn-sm <?= $activeCat === 'luxury' ? 'btn-primary' : 'btn-secondary' ?>">Luxury Residences</a>
            <a href="<?= url('/projects?cat=new') ?>" class="btn btn-sm <?= $activeCat === 'new' ? 'btn-primary' : 'btn-secondary' ?>">New Launches</a>
            <a href="<?= url('/projects?cat=intl') ?>" class="btn btn-sm <?= $activeCat === 'intl' ? 'btn-primary' : 'btn-secondary' ?>">International (UAE)</a>
        </div>

        <!-- 3-Column Image Grid (No card borders) -->
        <div class="projects-grid">
            <?php if (empty($projects)): ?>
                <div class="content-placeholder-block" style="grid-column: 1 / -1;">
                    Content to be added
                </div>
            <?php else: ?>
                <?php foreach ($projects as $proj): ?>
                    <a href="<?= !empty($proj['slug']) ? url('/projects/' . $proj['slug']) : url('/projects') ?>" class="project-item">
                        <div class="project-item-thumb">
                            <?php if (!empty($proj['image_url'])): ?>
                                <img src="<?= asset($proj['image_url']) ?>" alt="<?= e($proj['title']) ?>" width="400" height="250" loading="lazy">
                            <?php else: ?>
                                <img src="<?= asset('/assets/img/gallery/topic1.png') ?>" alt="<?= e($proj['title']) ?>" width="400" height="250" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="project-item-title"><?= e($proj['title']) ?></div>
                        <div class="project-item-meta"><?= e($proj['location'] ?? 'Gurgaon, Haryana') ?> &bull; <?= e($proj['price_range'] ?? 'On Request') ?></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "Featured Projects | Realty Smartz Pathshala";
$pageDescription = "Handpicked premium real estate developments curated for professional site walkthroughs.";
require APP_PATH . '/views/layouts/main.php';
?>
