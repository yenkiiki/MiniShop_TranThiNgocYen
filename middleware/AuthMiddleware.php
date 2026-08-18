<?php
namespace Middleware;

class AuthMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đang ở controller auth (trang đăng nhập) thì cho qua luôn, không check nữa
        if (isset($_GET['controller']) && $_GET['controller'] === 'auth') {
            return;
        }

        // Kiểm tra nếu chưa đăng nhập thì đá về trang login chuẩn
        if (!isset($_SESSION["user"])) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/index.php?area=admin&controller=auth&action=login");
            exit();
        }
    }
}