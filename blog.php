<?php
// blog.php - News & Impact Stories Public Feed

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/models/CmsPostModel.php';

$posts = CmsPostModel::getAll('published');
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <h1 class="fw-bold text-dark mb-1">News & Impact Stories</h1>
        <p class="text-muted mb-0">Follow our journey, community updates, and success stories worldwide.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php if (empty($posts)): ?>
            <div class="col-12 text-center py-5">
                <i class="fa-regular fa-newspaper fa-3x text-muted mb-3"></i>
                <p class="text-muted">No published stories available yet. Check back soon!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post):
                $img = !empty($post['featured_image']) ? $post['featured_image'] : 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80';
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['title']) ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="text-muted small mb-2">
                            <i class="fa-regular fa-calendar me-1"></i> <?= date('F j, Y', strtotime($post['created_at'])) ?>
                            <?php if (!empty($post['author_name'])): ?>
                                <span class="ms-2"><i class="fa-regular fa-user me-1"></i> <?= htmlspecialchars($post['author_name']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title fw-bold text-dark mb-3"><?= htmlspecialchars($post['title']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= strip_tags(substr($post['content'], 0, 130)) ?>...</p>
                        <a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-outline-success rounded-pill mt-3">Read Article <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
