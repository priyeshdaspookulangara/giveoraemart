<?php
// models/DonationModel.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/CampaignModel.php';

class DonationModel {

    public static function create($donor_id, $campaign_id, $amount, $transaction_id, $payment_status = 'completed') {
        $sql = "INSERT INTO donations (donor_id, campaign_id, amount, transaction_id, payment_status) VALUES (?, ?, ?, ?, ?)";
        $donationId = db_execute($sql, [$donor_id, $campaign_id, $amount, $transaction_id, $payment_status], "iidss");

        if ($donationId && $payment_status === 'completed') {
            CampaignModel::updateRaisedAmount($campaign_id, $amount);
        }
        return $donationId;
    }

    public static function getAll($limit = null, $status = null) {
        $sql = "SELECT d.*, c.title as campaign_title, u.name as donor_name, u.email as donor_email
                FROM donations d
                JOIN campaigns c ON d.campaign_id = c.id
                LEFT JOIN users u ON d.donor_id = u.id";

        $params = [];
        $types = "";

        if ($status) {
            $sql .= " WHERE d.payment_status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql .= " ORDER BY d.donated_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        return db_execute($sql, $params, $types);
    }

    public static function getByCampaignId($campaign_id, $limit = 10) {
        $sql = "SELECT d.*, u.name as donor_name
                FROM donations d
                LEFT JOIN users u ON d.donor_id = u.id
                WHERE d.campaign_id = ? AND d.payment_status = 'completed'
                ORDER BY d.donated_at DESC LIMIT " . (int)$limit;
        return db_execute($sql, [$campaign_id], "i");
    }

    public static function getById($id) {
        $sql = "SELECT d.*, c.title as campaign_title, c.slug as campaign_slug, u.name as donor_name, u.email as donor_email
                FROM donations d
                JOIN campaigns c ON d.campaign_id = c.id
                LEFT JOIN users u ON d.donor_id = u.id
                WHERE d.id = ?";
        return db_fetch_one($sql, [$id], "i");
    }

    public static function getByTransactionId($txnId) {
        $sql = "SELECT d.*, c.title as campaign_title, c.slug as campaign_slug, u.name as donor_name, u.email as donor_email
                FROM donations d
                JOIN campaigns c ON d.campaign_id = c.id
                LEFT JOIN users u ON d.donor_id = u.id
                WHERE d.transaction_id = ?";
        return db_fetch_one($sql, [$txnId], "s");
    }

    public static function getStats() {
        $row = db_fetch_one("SELECT COUNT(*) as total_donations, SUM(amount) as total_amount FROM donations WHERE payment_status = 'completed'");
        $uniqueDonors = db_fetch_one("SELECT COUNT(DISTINCT IFNULL(donor_id, transaction_id)) as total_donors FROM donations WHERE payment_status = 'completed'");
        return [
            'total_donations' => $row['total_donations'] ?? 0,
            'total_amount' => $row['total_amount'] ?? 0.00,
            'total_donors' => $uniqueDonors['total_donors'] ?? 0
        ];
    }
}
