<?php
// includes/footer.php - Responsive Public Footer
require_once __DIR__ . '/helpers.php';
$footerText = get_site_setting('footer_text', '© 2025 HopeCare Charity Foundation. All Rights Reserved.');
$siteTitle = get_site_setting('site_title', 'HopeCare Charity Foundation');
?>
<footer class="bg-dark text-white mt-auto pt-5 pb-3">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-success fw-bold mb-3"><i class="fa-solid fa-hand-holding-heart me-2"></i><?= htmlspecialchars($siteTitle) ?></h5>
                <p class="text-secondary small">Empowering underserved communities around the world through clean water, emergency healthcare, and sustainable educational opportunities.</p>
                <div class="d-flex gap-2 text-white">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3 text-light">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php" class="text-secondary text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="campaigns.php" class="text-secondary text-decoration-none">Active Campaigns</a></li>
                    <li class="mb-2"><a href="request-assistance.php" class="text-secondary text-decoration-none">Request Aid</a></li>
                    <li class="mb-2"><a href="blog.php" class="text-secondary text-decoration-none">Latest News</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3 text-light">CMS Static Pages</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="page.php?slug=about-us" class="text-secondary text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="page.php?slug=impact-stories" class="text-secondary text-decoration-none">Impact Stories</a></li>
                    <li class="mb-2"><a href="page.php?slug=privacy-policy" class="text-secondary text-decoration-none">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold mb-3 text-light">Contact Info</h6>
                <p class="text-secondary small mb-1"><i class="fa-solid fa-envelope me-2 text-success"></i><?= htmlspecialchars(get_site_setting('contact_email', 'contact@charity.org')) ?></p>
                <p class="text-secondary small mb-1"><i class="fa-solid fa-phone me-2 text-success"></i><?= htmlspecialchars(get_site_setting('helpline_number', '+1 (800) 555-0199')) ?></p>
                <p class="text-secondary small"><i class="fa-solid fa-location-dot me-2 text-success"></i>Global Operations Center</p>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center text-secondary small py-2">
            <?= htmlspecialchars($footerText) ?>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
