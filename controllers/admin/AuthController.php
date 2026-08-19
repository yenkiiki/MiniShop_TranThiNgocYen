<?php
namespace Controllers\Admin;

class AuthController
{
    public function login()
    {
        require_once __DIR__ . "/../../views/admin/login.php";
    }

   public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user']['id'])) {
            try {
                $userDAO = new \DAO\UserDAO();
                $userDAO->updateRememberToken($_SESSION['user']['id'], null);
            } catch (\Exception $e) {
            }
        }

        if (isset($_COOKIE['remember_token'])) {
            setcookie("remember_token", "", time() - 3600, "/");
        }

        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        session_destroy();

        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/login");
        exit();
    }
}