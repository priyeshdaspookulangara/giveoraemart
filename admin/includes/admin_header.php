<?php
// admin/includes/admin_header.php - CMS Admin Panel Header
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Require Admin or Editor role
require_role(['admin', 'editor']);

$siteTitle = get_site_setting('site_title', 'HopeCare CMS Admin');
$currentUser = current_user();
$activeNav = $activeNav ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= htmlspecialchars($siteTitle) ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Summernote WYSIWYG Editor CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #1e293b;
            color: #f8fafc;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 2px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #334155;
        }
        .sidebar .nav-link i {
            width: 24px;
        }
        .content-wrapper {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar p-3 d-flex flex-column">
        <a href="index.php" class="d-flex align-items-center mb-4 text-decoration-none text-white px-2">
            <i class="fa-solid fa-hand-holding-heart fa-2x text-success me-2"></i>
            <div>
                <span class="fs-5 fw-bold d-block lh-1">Charity CMS</span>
                <span class="small text-secondary">Control Panel</span>
            </div>
        </a>

        <div class="text-uppercase small text-secondary fw-bold px-2 mb-2">Core Navigation</div>
        <nav class="nav nav-pills flex-column mb-auto">
            <a href="index.php" class="nav-link <?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="campaigns.php" class="nav-link <?= $activeNav === 'campaigns' ? 'active' : '' ?>">
                <i class="fa-solid fa-bullseye"></i> Campaigns
            </a>
            <a href="donations.php" class="nav-link <?= $activeNav === 'donations' ? 'active' : '' ?>">
                <i class="fa-solid fa-hand-holding-dollar"></i> Master Ledger
            </a>
            <a href="beneficiaries.php" class="nav-link <?= $activeNav === 'beneficiaries' ? 'active' : '' ?>">
                <i class="fa-solid fa-heart-circle-check"></i> Beneficiary Aid
            </a>

            <div class="text-uppercase small text-secondary fw-bold px-2 mt-4 mb-2">Content Manager</div>
            <a href="pages.php" class="nav-link <?= $activeNav === 'pages' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-lines"></i> Static Pages
            </a>
            <a href="posts.php" class="nav-link <?= $activeNav === 'posts' ? 'active' : '' ?>">
                <i class="fa-solid fa-newspaper"></i> Blog / News
            </a>
            <a href="media.php" class="nav-link <?= $activeNav === 'media' ? 'active' : '' ?>">
                <i class="fa-solid fa-images"></i> Media Manager
            </a>

            <div class="text-uppercase small text-secondary fw-bold px-2 mt-4 mb-2">Administration</div>
            <a href="settings.php" class="nav-link <?= $activeNav === 'settings' ? 'active' : '' ?>">
                <i class="fa-solid fa-sliders"></i> Global Settings
            </a>
            <?php if (has_role('admin')): ?>
            <a href="users.php" class="nav-link <?= $activeNav === 'users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i> User Manager
            </a>
            <?php endif; ?>
        </nav>

        <hr class="border-secondary">

        <div class="px-2">
            <div class="small text-secondary mb-1">Signed in as:</div>
            <div class="fw-bold text-white mb-2"><?= htmlspecialchars($currentUser['name']) ?></div>
            <div class="d-flex gap-2">
                <a href="../index.php" target="_blank" class="btn btn-sm btn-outline-light w-50" title="View Public Front-End">
                    <i class="fa-solid fa-globe"></i> Website
                </a>
                <a href="../logout.php" class="btn btn-sm btn-outline-danger w-50">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="content-wrapper">
        <div class="mb-3">
            <?= display_flash_messages() ?>
        </div>
