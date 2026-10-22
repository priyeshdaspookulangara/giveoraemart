<?php
// tests/run_tests.php - Automated Verification & Test Suite

echo "===============================================\n";
echo " Charity & CMS Web Application Test Suite \n";
echo "===============================================\n\n";

$testsPassed = 0;
$testsFailed = 0;

function assertTest($condition, $testName) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "[PASS] $testName\n";
        $testsPassed++;
    } else {
        echo "[FAIL] $testName\n";
        $testsFailed++;
    }
}

// 1. Check schema.sql existence and contents
$schemaFile = __DIR__ . '/../schema.sql';
assertTest(file_exists($schemaFile), "schema.sql file exists");
$schemaContent = file_get_contents($schemaFile);
$requiredTables = ['users', 'campaigns', 'donations', 'beneficiaries', 'cms_pages', 'cms_posts', 'site_settings'];
foreach ($requiredTables as $table) {
    assertTest(strpos($schemaContent, "CREATE TABLE `$table`") !== false, "schema.sql contains table definition for '$table'");
}

// 2. Helper functions testing
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

// Test CSRF token generation and verification
$token = generate_csrf_token();
assertTest(!empty($token) && strlen($token) === 64, "CSRF token generated (length 64 hex characters)");
assertTest(verify_csrf_token($token) === true, "Valid CSRF token verifies successfully");
assertTest(verify_csrf_token("invalid_token_123") === false, "Invalid CSRF token fails verification");

// Test Slugify
assertTest(slugify("Clean Water & Wells Drive 2025!") === "clean-water-wells-drive-2025", "slugify() converts title to clean URL slug");

// Test Calculate Percentage
assertTest(calculate_percentage(5000, 10000) == 50.0, "calculate_percentage() computes correct percentage (50%)");
assertTest(calculate_percentage(15000, 10000) == 100.0, "calculate_percentage() caps percentage at 100%");

// Test Input Sanitization
$rawInput = "<script>alert('xss');</script>";
$sanitized = sanitize_input($rawInput);
assertTest($sanitized === "&lt;script&gt;alert(&#039;xss&#039;);&lt;/script&gt;", "sanitize_input() escapes HTML entities to prevent XSS");

// Test Models class loading
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CampaignModel.php';
require_once __DIR__ . '/../models/DonationModel.php';
require_once __DIR__ . '/../models/BeneficiaryModel.php';
require_once __DIR__ . '/../models/CmsPageModel.php';
require_once __DIR__ . '/../models/CmsPostModel.php';
require_once __DIR__ . '/../models/SiteSettingModel.php';

assertTest(class_exists('UserModel'), "UserModel loaded successfully");
assertTest(class_exists('CampaignModel'), "CampaignModel loaded successfully");
assertTest(class_exists('DonationModel'), "DonationModel loaded successfully");
assertTest(class_exists('BeneficiaryModel'), "BeneficiaryModel loaded successfully");
assertTest(class_exists('CmsPageModel'), "CmsPageModel loaded successfully");
assertTest(class_exists('CmsPostModel'), "CmsPostModel loaded successfully");
assertTest(class_exists('SiteSettingModel'), "SiteSettingModel loaded successfully");

// Test Password Hashing
$pass = "secureAdminPass123";
$hash = password_hash($pass, PASSWORD_BCRYPT);
assertTest(password_verify($pass, $hash), "password_hash() and password_verify() function correctly");

echo "\n-----------------------------------------------\n";
echo "Test Summary: $testsPassed Passed, $testsFailed Failed.\n";
echo "-----------------------------------------------\n";

if ($testsFailed > 0) {
    exit(1);
}
