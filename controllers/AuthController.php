<?php
// controllers/AuthController.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    public static function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                set_flash_message('error', 'Invalid security token (CSRF). Please try again.');
                header('Location: login.php');
                exit;
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                set_flash_message('error', 'Please fill in all required fields.');
                header('Location: login.php');
                exit;
            }

            $user = UserModel::verifyCredentials($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                set_flash_message('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');

                if (in_array($user['role'], ['admin', 'editor'])) {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                set_flash_message('error', 'Invalid email address or password.');
                header('Location: login.php');
                exit;
            }
        }
    }

    public static function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($csrfToken)) {
                set_flash_message('error', 'Invalid security token (CSRF). Please try again.');
                header('Location: register.php');
                exit;
            }

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'donor';

            // Allow public registration for 'donor' or 'beneficiary' only
            if (!in_array($role, ['donor', 'beneficiary'])) {
                $role = 'donor';
            }

            if (empty($name) || empty($email) || empty($password)) {
                set_flash_message('error', 'All fields are required.');
                header('Location: register.php');
                exit;
            }

            if ($password !== $confirmPassword) {
                set_flash_message('error', 'Passwords do not match.');
                header('Location: register.php');
                exit;
            }

            if (strlen($password) < 6) {
                set_flash_message('error', 'Password must be at least 6 characters long.');
                header('Location: register.php');
                exit;
            }

            if (UserModel::getByEmail($email)) {
                set_flash_message('error', 'An account with this email address already exists.');
                header('Location: register.php');
                exit;
            }

            $userId = UserModel::create($name, $email, $password, $role);
            if ($userId) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;

                set_flash_message('success', 'Registration successful! Welcome to HopeCare.');
                header('Location: index.php');
                exit;
            } else {
                set_flash_message('error', 'Failed to register account. Please try again.');
                header('Location: register.php');
                exit;
            }
        }
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_start();
        set_flash_message('info', 'You have been logged out.');
        header('Location: login.php');
        exit;
    }
}
