<?php
class GuestMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION["user"])) {
            header("Location: dashboard.php");
            exit;
        }
    }
}