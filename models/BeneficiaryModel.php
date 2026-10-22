<?php
// models/BeneficiaryModel.php

require_once __DIR__ . '/../config/db.php';

class BeneficiaryModel {

    public static function create($full_name, $contact_number, $assistance_type, $requested_amount, $details) {
        $sql = "INSERT INTO beneficiaries (full_name, contact_number, assistance_type, requested_amount, status, details) VALUES (?, ?, ?, ?, 'pending', ?)";
        return db_execute($sql, [$full_name, $contact_number, $assistance_type, $requested_amount, $details], "sssds");
    }

    public static function getAll($status = null) {
        if ($status) {
            return db_execute("SELECT * FROM beneficiaries WHERE status = ? ORDER BY created_at DESC", [$status], "s");
        }
        return db_execute("SELECT * FROM beneficiaries ORDER BY created_at DESC");
    }

    public static function getById($id) {
        return db_fetch_one("SELECT * FROM beneficiaries WHERE id = ?", [$id], "i");
    }

    public static function updateStatus($id, $status) {
        $validStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        return db_execute("UPDATE beneficiaries SET status = ? WHERE id = ?", [$status, $id], "si");
    }

    public static function getStats() {
        $row = db_fetch_one("SELECT
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
            SUM(CASE WHEN status = 'approved' THEN requested_amount ELSE 0 END) as approved_amount
            FROM beneficiaries");
        return [
            'total_requests' => $row['total_requests'] ?? 0,
            'pending_requests' => $row['pending_requests'] ?? 0,
            'approved_requests' => $row['approved_requests'] ?? 0,
            'approved_amount' => $row['approved_amount'] ?? 0.00
        ];
    }
}
