<?php
namespace Middleware;

use DAO\UserDAO;

class AuthMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_GET['controller']) && $_GET['controller'] === 'auth') {
            return;
        }

        if (isset($_SESSION["user"])) {
            return;
        }

        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            try {
                $userDAO = new UserDAO();
                $user = $userDAO->findByRememberToken($token);

                if ($user && $user->status == 1) {
                    $_SESSION["user"] = [
                        'id' => $user->id,
                        'username' => $user->username,
                        'fullName' => $user->fullName ?? $user->fullname ?? $user->full_name ?? 'Admin',
                        'status' => $user->status,
                        'role' => $user->role ?? 1
                    ];
                    return;
                }
            } catch (\Exception $e) {
            }
        }

        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/login");
        exit();
    }
}