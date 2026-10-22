<?php
// models/UserModel.php

require_once __DIR__ . '/../config/db.php';

class UserModel {

    public static function getById($id) {
        return db_fetch_one("SELECT id, name, email, role, created_at FROM users WHERE id = ?", [$id], "i");
    }

    public static function getByEmail($email) {
        return db_fetch_one("SELECT * FROM users WHERE email = ?", [$email], "s");
    }

    public static function create($name, $email, $password, $role = 'donor') {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        return db_execute($sql, [$name, $email, $hashedPassword, $role], "ssss");
    }

    public static function getAll() {
        return db_execute("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
    }

    public static function updateRole($id, $role) {
        $validRoles = ['admin', 'editor', 'donor', 'beneficiary'];
        if (!in_array($role, $validRoles)) {
            return false;
        }
        return db_execute("UPDATE users SET role = ? WHERE id = ?", [$role, $id], "si");
    }

    public static function verifyCredentials($email, $password) {
        $user = self::getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
}
