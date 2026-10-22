<?php
// controllers/DonationController.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/CampaignModel.php';
require_once __DIR__ . '/../models/DonationModel.php';

class DonationController {

    public static function handleCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                set_flash_message('error', 'Invalid security token (CSRF). Please try again.');
                header('Location: campaigns.php');
                exit;
            }

            $campaignId = (int)($_POST['campaign_id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0.00);
            $isAnonymous = !empty($_POST['is_anonymous']);

            $campaign = CampaignModel::getById($campaignId);
            if (!$campaign) {
                set_flash_message('error', 'Invalid or non-existent campaign.');
                header('Location: campaigns.php');
                exit;
            }

            if ($amount <= 0) {
                set_flash_message('error', 'Please enter a valid donation amount.');
                header('Location: campaign-detail.php?slug=' . urlencode($campaign['slug']));
                exit;
            }

            $donorId = null;
            if (!$isAnonymous && is_logged_in()) {
                $donorId = $_SESSION['user_id'];
            }

            // Generate unique transaction ID
            $transactionId = 'TXN-' . strtoupper(bin2hex(random_bytes(4)));

            $donationId = DonationModel::create($donorId, $campaignId, $amount, $transactionId, 'completed');

            if ($donationId) {
                set_flash_message('success', 'Thank you for your generous donation!');
                header("Location: donation-success.php?txn=" . urlencode($transactionId));
                exit;
            } else {
                set_flash_message('error', 'Processing failed. Please try again.');
                header('Location: campaign-detail.php?slug=' . urlencode($campaign['slug']));
                exit;
            }
        }
    }
}
