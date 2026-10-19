<?php
ob_start();
$postUrl = url('/blog/' . $post['slug']);
?>
<article class="hero section-pad">
    <div class="container" style="max-width: 720px;">
        <!-- Breadcrumb / Back Link -->
        <div class="mb-4">
            <a href="<?= url('/blog') ?>" class="small text-muted text-decoration-none">&larr; Back to all articles</a>
        </div>

        <div class="eyebrow mb-2">
            <?= e($post['category_name'] ?? 'Real Estate') ?>
        </div>

        <h1 class="h2 mb-3">
            <?= e($post['title']) ?>
        </h1>

        <hr class="gold-rule">

        <div class="d-flex align-items-center gap-3 text-caption pb-3 border-bottom mb-4" style="border-color: var(--line) !important;">
            <span>By <?= e($post['author_name']) ?></span>
            <span>&bull;</span>
            <span><?= date('d M Y', strtotime($post['published_at'])) ?></span>
            <span>&bull;</span>
            <span><?= (int)($post['reading_time_minutes'] ?? 4) ?> min read</span>
        </div>

        <!-- Featured Image if available -->
        <div class="overflow-hidden mb-4 border" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
            <img src="<?= asset('/assets/img/gallery/topic' . (($post['id'] % 3) + 1) . '.png') ?>" alt="<?= e($post['title']) ?>" class="w-100" style="aspect-ratio: 16/9; object-fit: cover;" loading="lazy">
        </div>

        <!-- Article Content -->
        <div class="article-body mb-5" style="font-size: 16px; line-height: 1.8; color: var(--text);">
            <?= $post['content'] ?>
        </div>

        <!-- Share / Footer Strip -->
        <div class="p-4 border d-flex flex-wrap justify-content-between align-items-center gap-3" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important; background-color: var(--surface);">
            <span class="small fw-bold">Share article:</span>
            <div class="d-flex gap-2">
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-secondary">WhatsApp</a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-secondary">LinkedIn</a>
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-secondary">Twitter</a>
            </div>
        </div>
    </div>
</article>

<?php
$content = ob_get_clean();
$pageTitle = e($post['title']) . " | Realty Smartz Pathshala";
$pageDescription = e($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 160));
require APP_PATH . '/views/layouts/main.php';
?>
