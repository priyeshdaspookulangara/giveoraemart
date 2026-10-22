<?php
// post.php - Dynamic CMS Blog Post Renderer

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/controllers/CmsController.php';

$slug = $_GET['slug'] ?? '';
$post = CmsController::renderPost($slug);

if (!$post):
?>
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <i class="fa-solid fa-file-circle-xmark fa-4x text-muted mb-3"></i>
            <h2 class="fw-bold text-dark">Article Not Found</h2>
            <p class="text-muted">The requested post does not exist or is currently unpublished.</p>
            <a href="blog.php" class="btn btn-success rounded-pill px-4 mt-2">Back to News Feed</a>
        </div>
    </div>
</div>
<?php
else:
    $img = !empty($post['featured_image']) ? $post['featured_image'] : 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="blog.php" class="text-success text-decoration-none">News & Stories</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($post['title']) ?></li>
            </ol>
        </nav>
        <h1 class="fw-bold text-dark mb-2"><?= htmlspecialchars($post['title']) ?></h1>
        <div class="text-muted small">
            <i class="fa-regular fa-calendar me-1"></i> <?= date('F j, Y', strtotime($post['created_at'])) ?>
            <?php if (!empty($post['author_name'])): ?>
                <span class="ms-3"><i class="fa-regular fa-user me-1"></i> By <?= htmlspecialchars($post['author_name']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <img src="<?= htmlspecialchars($img) ?>" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;" alt="<?= htmlspecialchars($post['title']) ?>">
                <div class="card-body p-4 p-md-5">
                    <div class="post-content text-dark lh-lg">
                        <?= $post['content'] ?>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="blog.php" class="btn btn-outline-secondary rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Back to News Feed</a>
                        <a href="campaigns.php" class="btn btn-success rounded-pill fw-bold"><i class="fa-solid fa-heart me-1"></i> Support Our Work</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endif;
require_once __DIR__ . '/includes/footer.php';
?>
