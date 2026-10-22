<?php
// controllers/AdminController.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/CampaignModel.php';
require_once __DIR__ . '/../models/DonationModel.php';
require_once __DIR__ . '/../models/BeneficiaryModel.php';
require_once __DIR__ . '/../models/CmsPageModel.php';
require_once __DIR__ . '/../models/CmsPostModel.php';
require_once __DIR__ . '/../models/SiteSettingModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AdminController {

    public static function checkAdminAuth() {
        require_role(['admin', 'editor']);
    }

    public static function getDashboardMetrics() {
        self::checkAdminAuth();
        return [
            'campaign_stats' => CampaignModel::getStats(),
            'donation_stats' => DonationModel::getStats(),
            'beneficiary_stats' => BeneficiaryModel::getStats(),
            'recent_donations' => DonationModel::getAll(5),
            'recent_beneficiaries' => BeneficiaryModel::getAll()
        ];
    }

    public static function handleCampaignSave() {
        self::checkAdminAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash_message('error', 'CSRF validation failed.');
                header('Location: campaigns.php');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) $slug = slugify($title);
            $description = trim($_POST['description'] ?? '');
            $goalAmount = (float)($_POST['goal_amount'] ?? 0.00);
            $status = $_POST['status'] ?? 'active';
            $imageUrl = trim($_POST['image_url'] ?? '');

            if ($id > 0) {
                CampaignModel::update($id, $title, $slug, $description, $goalAmount, $status, $imageUrl);
                set_flash_message('success', 'Campaign updated successfully.');
            } else {
                CampaignModel::create($title, $slug, $description, $goalAmount, $status, $imageUrl);
                set_flash_message('success', 'Campaign created successfully.');
            }
            header('Location: campaigns.php');
            exit;
        }
    }

    public static function handlePageSave() {
        self::checkAdminAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash_message('error', 'CSRF validation failed.');
                header('Location: pages.php');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) $slug = slugify($title);
            $content = $_POST['content'] ?? '';
            $status = $_POST['status'] ?? 'published';

            if ($id > 0) {
                CmsPageModel::update($id, $title, $slug, $content, $status);
                set_flash_message('success', 'Page updated successfully.');
            } else {
                CmsPageModel::create($title, $slug, $content, $status);
                set_flash_message('success', 'Page created successfully.');
            }
            header('Location: pages.php');
            exit;
        }
    }

    public static function handlePostSave() {
        self::checkAdminAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash_message('error', 'CSRF validation failed.');
                header('Location: posts.php');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) $slug = slugify($title);
            $content = $_POST['content'] ?? '';
            $featuredImage = trim($_POST['featured_image'] ?? '');
            $status = $_POST['status'] ?? 'published';
            $authorId = $_SESSION['user_id'] ?? 1;

            if ($id > 0) {
                CmsPostModel::update($id, $title, $slug, $content, $featuredImage, $status);
                set_flash_message('success', 'Post updated successfully.');
            } else {
                CmsPostModel::create($title, $slug, $authorId, $content, $featuredImage, $status);
                set_flash_message('success', 'Post created successfully.');
            }
            header('Location: posts.php');
            exit;
        }
    }

    public static function handleSettingsSave() {
        self::checkAdminAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash_message('error', 'CSRF validation failed.');
                header('Location: settings.php');
                exit;
            }

            $settings = $_POST['settings'] ?? [];
            if (is_array($settings)) {
                SiteSettingModel::updateMultiple($settings);
                set_flash_message('success', 'Global settings updated successfully.');
            }
            header('Location: settings.php');
            exit;
        }
    }

    public static function handleBeneficiaryStatusUpdate() {
        self::checkAdminAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash_message('error', 'CSRF validation failed.');
                header('Location: beneficiaries.php');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? 'pending';

            BeneficiaryModel::updateStatus($id, $status);
            set_flash_message('success', 'Beneficiary request status updated.');
            header('Location: beneficiaries.php');
            exit;
        }
    }

    public static function handleUserRoleUpdate() {
        require_role('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                set_flash_message('error', 'CSRF validation failed.');
                header('Location: users.php');
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $role = $_POST['role'] ?? 'donor';

            UserModel::updateRole($id, $role);
            set_flash_message('success', 'User role updated.');
            header('Location: users.php');
            exit;
        }
    }
}
