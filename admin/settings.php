<?php
// admin/settings.php - Global Site Configuration Manager

$activeNav = 'settings';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/SiteSettingModel.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::handleSettingsSave();
    exit;
}

$settings = SiteSettingModel::getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Global Site Settings</h2>
        <p class="text-muted small mb-0">Manage global site branding, helpline info, and footer disclosures.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
    <form action="settings.php" method="POST">
        <?= csrf_input() ?>

        <h5 class="fw-bold text-success border-bottom pb-2 mb-4"><i class="fa-solid fa-globe me-2"></i>General Branding</h5>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="site_title" class="form-label fw-bold">Site Title / Charity Name</label>
                <input type="text" class="form-control form-control-lg" id="site_title" name="settings[site_title]" value="<?= htmlspecialchars($settings['site_title'] ?? 'HopeCare Charity Foundation') ?>" required>
            </div>
            <div class="col-md-3">
                <label for="currency_symbol" class="form-label fw-bold">Currency Symbol</label>
                <input type="text" class="form-control form-control-lg" id="currency_symbol" name="settings[currency_symbol]" value="<?= htmlspecialchars($settings['currency_symbol'] ?? '$') ?>" required>
            </div>
            <div class="col-md-3">
                <label for="helpline_number" class="form-label fw-bold">Helpline Number</label>
                <input type="text" class="form-control form-control-lg" id="helpline_number" name="settings[helpline_number]" value="<?= htmlspecialchars($settings['helpline_number'] ?? '+1 (800) 555-0199') ?>" required>
            </div>
            <div class="col-md-6">
                <label for="contact_email" class="form-label fw-bold">Public Contact Email</label>
                <input type="email" class="form-control form-control-lg" id="contact_email" name="settings[contact_email]" value="<?= htmlspecialchars($settings['contact_email'] ?? 'contact@hopecarecharity.org') ?>" required>
            </div>
        </div>

        <h5 class="fw-bold text-success border-bottom pb-2 mb-4"><i class="fa-solid fa-display me-2"></i>Homepage Hero Section</h5>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <label for="hero_title" class="form-label fw-bold">Hero Banner Main Title</label>
                <input type="text" class="form-control form-control-lg" id="hero_title" name="settings[hero_title]" value="<?= htmlspecialchars($settings['hero_title'] ?? 'Empower Lives, Inspire Hope') ?>" required>
            </div>
            <div class="col-12">
                <label for="hero_subtitle" class="form-label fw-bold">Hero Subtitle</label>
                <textarea class="form-control" id="hero_subtitle" name="settings[hero_subtitle]" rows="2" required><?= htmlspecialchars($settings['hero_subtitle'] ?? 'Join our mission to provide clean water, healthcare, and education to underprivileged communities around the world.') ?></textarea>
            </div>
        </div>

        <h5 class="fw-bold text-success border-bottom pb-2 mb-4"><i class="fa-solid fa-copyright me-2"></i>Footer & Disclosures</h5>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <label for="footer_text" class="form-label fw-bold">Footer Copyright Notice</label>
                <input type="text" class="form-control form-control-lg" id="footer_text" name="settings[footer_text]" value="<?= htmlspecialchars($settings['footer_text'] ?? '© 2025 HopeCare Charity Foundation. Empowering communities worldwide.') ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow">
            <i class="fa-solid fa-save me-2"></i> Save Site Settings
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
