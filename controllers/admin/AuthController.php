<?php
namespace Controllers\Admin;

class AuthController
{
    public function login()
    {
        // Gọi file xử lý logic đăng nhập cũ của cậu vào đây
        require_once __DIR__ . "/../../views/admin/login.php";
    }

    public function logout()
    {
        // 1. Xóa session đăng xuất an toàn
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        session_destroy();

        // 2. Chuyển hướng về trang login sạch sẽ
        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/login");
        exit();
    }
}