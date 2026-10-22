<?php
// admin/media.php - Secure Media & File Asset Uploader

$activeNav = 'media';
require_once __DIR__ . '/includes/admin_header.php';

$uploadDir = __DIR__ . '/../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'CSRF validation failed.');
        header('Location: media.php');
        exit;
    }

    $file = $_FILES['media_file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($fileExt, $allowedExtensions) || !in_array($mimeType, $allowedMimes)) {
            set_flash_message('error', 'Invalid file type. Only JPG, PNG, WEBP, and GIF images are allowed.');
        } else if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            set_flash_message('error', 'File size exceeds maximum allowed limit of 5MB.');
        } else {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $publicUrl = 'uploads/' . $newFileName;
                set_flash_message('success', 'File uploaded successfully: ' . htmlspecialchars($publicUrl));
            } else {
                set_flash_message('error', 'Failed to move uploaded file.');
            }
        }
    } else {
        set_flash_message('error', 'Error uploading file. Please try again.');
    }
    header('Location: media.php');
    exit;
}

// Get list of existing uploaded files
$uploadedFiles = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..' && is_file($uploadDir . $f)) {
            $uploadedFiles[] = [
                'name' => $f,
                'path' => 'uploads/' . $f,
                'size' => filesize($uploadDir . $f),
                'date' => filemtime($uploadDir . $f)
            ];
        }
    }
    usort($uploadedFiles, function($a, $b) { return $b['date'] - $a['date']; });
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Media Uploader</h2>
        <p class="text-muted small mb-0">Upload and manage images for campaign banners, blog articles, and static pages.</p>
    </div>
</div>

<!-- Upload Card -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i>Upload New Image</h5>
    <form action="media.php" method="POST" enctype="multipart/form-data">
        <?= csrf_input() ?>
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <input type="file" class="form-control form-control-lg" name="media_file" accept="image/jpeg,image/png,image/webp,image/gif" required>
                <div class="form-text small">Accepted formats: JPG, PNG, WEBP, GIF (Max file size: 5MB).</div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                    <i class="fa-solid fa-upload me-1"></i> Upload Image
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Uploaded Assets Gallery -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h5 class="fw-bold text-dark mb-0">Uploaded Media Library</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <?php if (empty($uploadedFiles)): ?>
                <div class="col-12 text-center py-4 text-muted">No media files uploaded yet.</div>
            <?php else: ?>
                <?php foreach ($uploadedFiles as $file): ?>
                <div class="col-md-3 col-6">
                    <div class="card h-100 border rounded-3 overflow-hidden shadow-sm">
                        <img src="../<?= htmlspecialchars($file['path']) ?>" class="card-img-top" style="height: 140px; object-fit: cover;" alt="<?= htmlspecialchars($file['name']) ?>">
                        <div class="card-body p-3">
                            <p class="card-text small text-truncate fw-bold mb-1" title="<?= htmlspecialchars($file['name']) ?>"><?= htmlspecialchars($file['name']) ?></p>
                            <span class="text-muted small d-block mb-2"><?= round($file['size'] / 1024, 1) ?> KB • <?= date('M j, Y', $file['date']) ?></span>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control font-monospace small" value="<?= htmlspecialchars($file['path']) ?>" readonly id="file_<?= md5($file['name']) ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($file['path']) ?>'); alert('Image URL path copied!');" title="Copy Path">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
