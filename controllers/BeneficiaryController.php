<?php
// controllers/BeneficiaryController.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/BeneficiaryModel.php';

class BeneficiaryController {

    public static function handleSubmit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                set_flash_message('error', 'Invalid security token (CSRF). Please try again.');
                header('Location: request-assistance.php');
                exit;
            }

            $fullName = trim($_POST['full_name'] ?? '');
            $contactNumber = trim($_POST['contact_number'] ?? '');
            $assistanceType = trim($_POST['assistance_type'] ?? '');
            $requestedAmount = (float)($_POST['requested_amount'] ?? 0.00);
            $details = trim($_POST['details'] ?? '');

            if (empty($fullName) || empty($contactNumber) || empty($assistanceType) || $requestedAmount <= 0 || empty($details)) {
                set_flash_message('error', 'Please complete all required fields accurately.');
                header('Location: request-assistance.php');
                exit;
            }

            $id = BeneficiaryModel::create($fullName, $contactNumber, $assistanceType, $requestedAmount, $details);

            if ($id) {
                set_flash_message('success', 'Your application for aid has been submitted successfully. Our team will review your request.');
                header('Location: request-assistance.php?success=1');
                exit;
            } else {
                set_flash_message('error', 'An error occurred while submitting your application. Please try again.');
                header('Location: request-assistance.php');
                exit;
            }
        }
    }
}
