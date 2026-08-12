<?php
class AuthMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["user"])) {
        header("Location: /MINISHOP_TRANTHINGOCYEN/views/admin/login.php");
            exit;
        }
    }
}