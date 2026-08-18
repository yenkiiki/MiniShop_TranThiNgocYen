<?php
namespace Middleware;
class RoleMiddleware
{
    public static function check($requiredRole = 1)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;

        // Nếu chưa đăng nhập
        if (!$user) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/views/admin/login.php");
            exit();
        }

        // Lấy role linh hoạt (hỗ trợ cả Object lẫn Array)
        $userRole = null;
        if (is_object($user)) {
            $userRole = $user->role ?? null;
        } elseif (is_array($user)) {
            $userRole = $user['role'] ?? null;
        }

        // Kiểm tra quyền: Chấp nhận nếu role là 1, '1', hoặc 'admin'
        $isAuthorized = false;
        if ($userRole == $requiredRole || $userRole === 'admin' || $userRole === 1) {
            $isAuthorized = true;
        }

        if (!$isAuthorized) {
            // Nếu không đúng quyền thì mới đá về login kèm thông báo
            header("Location: /MINISHOP_TRANTHINGOCYEN/views/admin/login.php?error=unauthorized");
            exit();
        }
    }
}