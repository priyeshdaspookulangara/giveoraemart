<?php
// index.php - Home Page

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/models/CampaignModel.php';
require_once __DIR__ . '/models/DonationModel.php';
require_once __DIR__ . '/models/BeneficiaryModel.php';
require_once __DIR__ . '/models/CmsPostModel.php';

$heroTitle = get_site_setting('hero_title', 'Empower Lives, Inspire Hope');
$heroSubtitle = get_site_setting('hero_subtitle', 'Join our mission to provide clean water, healthcare, and education to underprivileged communities.');

$activeCampaigns = CampaignModel::getAll('active');
$donationStats = DonationModel::getStats();
$campaignStats = CampaignModel::getStats();
$beneficiaryStats = BeneficiaryModel::getStats();
$recentPosts = CmsPostModel::getAll('published', 3);
?>

<!-- Hero Banner -->
<section class="hero-section text-center text-md-start py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-success px-3 py-2 text-uppercase mb-3 fw-bold">Together We Can Make A Difference</span>
                <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($heroTitle) ?></h1>
                <p class="lead mb-4 opacity-90"><?= htmlspecialchars($heroSubtitle) ?></p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="campaigns.php" class="btn btn-success btn-lg rounded-pill px-4 shadow">
                        <i class="fa-solid fa-heart me-2"></i> Explore Campaigns
                    </a>
                    <a href="request-assistance.php" class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fa-solid fa-hand-holding-hand me-2"></i> Apply for Aid
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Counters -->
<section class="py-5 bg-white shadow-sm">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="stat-card p-4 shadow-sm border-0 rounded-4">
                    <i class="fa-solid fa-hand-holding-dollar text-success fa-2x mb-2"></i>
                    <h2 class="fw-bold mb-0 text-dark"><?= format_currency($donationStats['total_amount']) ?></h2>
                    <span class="text-muted small">Total Funds Raised</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card p-4 shadow-sm border-0 rounded-4">
                    <i class="fa-solid fa-users text-primary fa-2x mb-2"></i>
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($donationStats['total_donors']) ?></h2>
                    <span class="text-muted small">Generous Donors</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card p-4 shadow-sm border-0 rounded-4">
                    <i class="fa-solid fa-bullseye text-warning fa-2x mb-2"></i>
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($campaignStats['total_campaigns']) ?></h2>
                    <span class="text-muted small">Fundraising Drives</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card p-4 shadow-sm border-0 rounded-4">
                    <i class="fa-solid fa-heart-pulse text-danger fa-2x mb-2"></i>
                    <h2 class="fw-bold mb-0 text-dark"><?= number_format($beneficiaryStats['approved_requests']) ?></h2>
                    <span class="text-muted small">Beneficiaries Assisted</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Active Campaigns Section -->
<section class="py-5">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-success fw-bold text-uppercase small">Urgent Causes</span>
                <h2 class="fw-bold text-dark mb-0">Active Fundraising Drives</h2>
            </div>
            <a href="campaigns.php" class="btn btn-outline-success rounded-pill px-3">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php if (empty($activeCampaigns)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No active campaigns available at the moment. Please check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($activeCampaigns, 0, 3) as $c):
                    $pct = calculate_percentage($c['raised_amount'], $c['goal_amount']);
                    $img = !empty($c['image_url']) ? $c['image_url'] : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80';
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-campaign h-100 shadow-sm border-0">
                        <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($c['title']) ?>" style="height: 220px; object-fit: cover;">
                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($c['title']) ?></h5>
                            <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($c['description'], 0, 110)) ?>...</p>

                            <div class="my-3">
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span class="text-success"><?= format_currency($c['raised_amount']) ?> raised</span>
                                    <span class="text-muted">Goal: <?= format_currency($c['goal_amount']) ?></span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-end text-muted small mt-1"><?= $pct ?>% funded</div>
                            </div>

                            <a href="campaign-detail.php?slug=<?= urlencode($c['slug']) ?>" class="btn btn-success w-100 rounded-pill fw-bold">
                                <i class="fa-solid fa-heart me-1"></i> Donate Now
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest News & Blog Feed -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small">News & Updates</span>
                <h2 class="fw-bold text-dark mb-0">Stories of Hope & Change</h2>
            </div>
            <a href="blog.php" class="btn btn-outline-primary rounded-pill px-3">Read More <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php foreach ($recentPosts as $post):
                $img = !empty($post['featured_image']) ? $post['featured_image'] : 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80';
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['title']) ?>" style="height: 180px; object-fit: cover;">
                    <div class="card-body p-4 d-flex flex-column">
                        <span class="text-muted small mb-2"><i class="fa-regular fa-calendar me-1"></i> <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                        <h5 class="card-title fw-bold text-dark mb-3"><?= htmlspecialchars($post['title']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= strip_tags(substr($post['content'], 0, 120)) ?>...</p>
                        <a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="text-success fw-bold text-decoration-none">Read Full Story <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5 bg-success text-white text-center">
    <div class="container py-3">
        <h2 class="display-6 fw-bold mb-3">Need Financial or Medical Aid?</h2>
        <p class="lead mb-4 max-width-600 mx-auto opacity-90">If you or your family are facing emergency hardship, submit an assistance request to our beneficiary evaluation board today.</p>
        <a href="request-assistance.php" class="btn btn-light btn-lg rounded-pill px-5 text-success fw-bold shadow">
            Submit Aid Request
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
