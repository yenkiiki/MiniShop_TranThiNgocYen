<?php
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../dao/UserDAO.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa remember_token trong Database nếu user đang đăng nhập
if (isset($_SESSION['user']) && $_SESSION['user'] instanceof User) {
    try {
        $userDAO = new UserDAO();
        $userDAO->updateRememberToken($_SESSION['user']->id, null);
    } catch (Exception $e) {
        // Bỏ qua lỗi ngầm
    }
}

// Xóa Cookie remember_token trên trình duyệt
if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

// Hủy toàn bộ Session
session_unset();
session_destroy();

// Điều hướng về trang đăng nhập
header("Location: login.php");
exit();