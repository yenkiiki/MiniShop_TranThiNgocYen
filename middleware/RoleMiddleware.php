<?php
require_once __DIR__ . '/../models/User.php';

class RoleMiddleware
{
    public static function check(int $requiredRole)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;

        if (!($user instanceof User)) {
            unset($_SESSION['user']);
            header("Location: " . BASE_URL . "views/admin/login.php");
            exit();
        }

        if ((int)$user->role !== $requiredRole) {
            // Hủy session của user 
            unset($_SESSION['user']);
            
            header("Location: " . BASE_URL . "views/admin/login.php?error=unauthorized");
            exit();
        }
    }
}