<?php
// controllers/CampaignController.php

require_once __DIR__ . '/../models/CampaignModel.php';
require_once __DIR__ . '/../models/DonationModel.php';
require_once __DIR__ . '/../includes/helpers.php';

class CampaignController {

    public static function index() {
        $status = $_GET['status'] ?? 'active';
        if (!in_array($status, ['active', 'completed', 'paused', 'all'])) {
            $status = 'active';
        }
        $campaigns = CampaignModel::getAll($status === 'all' ? null : $status);
        return [
            'campaigns' => $campaigns,
            'current_status' => $status
        ];
    }

    public static function detail($slug) {
        $campaign = CampaignModel::getBySlug($slug);
        if (!$campaign) {
            return null;
        }
        $recentDonations = DonationModel::getByCampaignId($campaign['id'], 10);
        return [
            'campaign' => $campaign,
            'recent_donations' => $recentDonations
        ];
    }
}
