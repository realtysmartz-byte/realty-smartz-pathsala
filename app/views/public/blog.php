<?php
ob_start();
$posts = $postsData['data'] ?? [];
$currentPage = $postsData['page'] ?? 1;
$lastPage = $postsData['last_page'] ?? 1;
$activeCategory = $_GET['category'] ?? '';
$searchQuery = $_GET['q'] ?? '';
?>

<!-- Page Header (Plain H1 + One Line) -->
<section class="hero section-pad pb-4">
    <div class="container">
        <h1>Real Estate Knowledge Hub</h1>
        <hr class="gold-rule">
        <p class="lead mb-0">Authoritative insights on career growth, luxury micro-market dynamics, and RERA updates in Gurgaon.</p>
    </div>
</section>

<!-- Filter & Search Bar + Articles Grid -->
<section class="section-pad pt-2">
    <div class="container">
        <!-- Search & Category Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('/blog') ?>" class="btn btn-sm <?= empty($activeCategory) ? 'btn-primary' : 'btn-secondary' ?>">All Topics</a>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= url('/blog?category=' . $cat['slug']) ?>" class="btn btn-sm <?= ($activeCategory === $cat['slug']) ? 'btn-primary' : 'btn-secondary' ?>">
                            <?= e($cat['name']) ?> (<?= $cat['post_count'] ?? 0 ?>)
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form action="<?= url('/blog') ?>" method="GET" class="d-flex gap-2" style="max-width: 320px; width: 100%;">
                <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="Search articles..." class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-secondary" aria-label="Search">Search</button>
            </form>
        </div>

        <!-- Articles Grid (3-column clean grid, no cards) -->
        <div class="blog-grid">
            <?php if (empty($posts)): ?>
                <div class="content-placeholder-block" style="grid-column: 1 / -1;">
                    Content to be added
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="blog-item">
                        <div class="blog-item-thumb">
                            <img src="<?= asset('/assets/img/gallery/topic' . (($post['id'] % 3) + 1) . '.png') ?>" alt="<?= e($post['title']) ?>" width="400" height="250" loading="lazy">
                        </div>
                        <div class="blog-item-date">
                            <?= date('M d, Y', strtotime($post['published_at'] ?? 'now')) ?> &bull; <?= (int)($post['reading_time_minutes'] ?? 4) ?> min read
                        </div>
                        <a href="<?= url('/blog/' . $post['slug']) ?>" class="blog-item-title">
                            <?= e($post['title']) ?>
                        </a>
                        <p class="small text-muted mb-3">
                            <?= e($post['excerpt'] ?? substr(strip_tags($post['content'] ?? ''), 0, 130) . '...') ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($lastPage > 1): ?>
            <div class="d-flex justify-content-center gap-2 mt-5">
                <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                    <a href="<?= url('/blog?page=' . $i . ($activeCategory ? '&category=' . urlencode($activeCategory) : '') . ($searchQuery ? '&q=' . urlencode($searchQuery) : '')) ?>" class="btn btn-sm <?= $currentPage === $i ? 'btn-primary' : 'btn-secondary' ?>" style="min-width: 40px;">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "Knowledge Hub | Realty Smartz Pathshala";
$pageDescription = "Authoritative real estate insights on career growth, luxury closing skills, and RERA guidelines.";
require APP_PATH . '/views/layouts/main.php';
?>
