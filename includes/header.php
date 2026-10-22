<?php
// includes/header.php - Responsive Public Header with Bootstrap 5
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../models/CmsPageModel.php';

$siteTitle = get_site_setting('site_title', 'HopeCare Charity');
$helpline = get_site_setting('helpline_number', '+1 (800) 555-0199');
$publishedPages = CmsPageModel::getAll('published');
$currentUser = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteTitle) ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --bs-primary-rgb: 13, 110, 253;
            --brand-green: #198754;
            --brand-dark: #212529;
        }
        .hero-section {
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.9), rgba(13, 110, 253, 0.85)), url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            color: white;
            padding: 80px 0;
        }
        .card-campaign {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        .card-campaign:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            background: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<!-- Top Bar -->
<div class="bg-dark text-white py-1 px-3 small">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <i class="fa-solid fa-phone me-1 text-success"></i> Helpline: <?= htmlspecialchars($helpline) ?> |
            <i class="fa-solid fa-envelope me-1 ms-2 text-info"></i> <?= htmlspecialchars(get_site_setting('contact_email', 'contact@charity.org')) ?>
        </div>
        <div>
            <?php if ($currentUser): ?>
                <span class="me-2"><i class="fa-solid fa-user me-1"></i> Hello, <?= htmlspecialchars($currentUser['name']) ?> (<?= ucfirst($currentUser['role']) ?>)</span>
                <?php if (in_array($currentUser['role'], ['admin', 'editor'])): ?>
                    <a href="admin/index.php" class="text-warning text-decoration-none me-2"><i class="fa-solid fa-gauge me-1"></i> Admin Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="text-white text-decoration-none ms-2"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-white text-decoration-none me-3"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a>
                <a href="register.php" class="text-white text-decoration-none"><i class="fa-solid fa-user-plus me-1"></i> Register</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Navbar -->
<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand text-success" href="index.php">
            <i class="fa-solid fa-hand-holding-heart me-2"></i><?= htmlspecialchars($siteTitle) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="campaigns.php">Campaigns</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="request-assistance.php">Request Aid</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="blog.php">News & Stories</a>
                </li>
                <?php if (!empty($publishedPages)): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="pagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        About & Info
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="pagesDropdown">
                        <?php foreach ($publishedPages as $p): ?>
                            <li><a class="dropdown-item" href="page.php?slug=<?= urlencode($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <a href="campaigns.php" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-heart me-1"></i> Donate Now
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages Container -->
<div class="container mt-3">
    <?= display_flash_messages() ?>
</div>
