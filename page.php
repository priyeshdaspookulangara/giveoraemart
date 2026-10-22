<?php
// page.php - Dynamic CMS Static Page Renderer

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/controllers/CmsController.php';

$slug = $_GET['slug'] ?? '';
$page = CmsController::renderPage($slug);

if (!$page):
?>
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <i class="fa-solid fa-file-circle-xmark fa-4x text-muted mb-3"></i>
            <h2 class="fw-bold text-dark">Page Not Found</h2>
            <p class="text-muted">The requested page does not exist or is currently unpublished.</p>
            <a href="index.php" class="btn btn-success rounded-pill px-4 mt-2">Return Home</a>
        </div>
    </div>
</div>
<?php
else:
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($page['title']) ?></li>
            </ol>
        </nav>
        <h1 class="fw-bold text-dark mb-0"><?= htmlspecialchars($page['title']) ?></h1>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="cms-page-content text-dark lh-lg">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endif;
require_once __DIR__ . '/includes/footer.php';
?>
