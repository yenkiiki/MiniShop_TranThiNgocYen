<?php
namespace Middleware;
class AutoLoginMiddleware
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu chưa có session đăng nhập, nhưng có cookie 'remember_token'
        if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            try {
                $userDAO = new UserDAO();
                $user = $userDAO->findByRememberToken($token);

                // Nếu token hợp lệ và tài khoản đang hoạt động (status = 1)
                if ($user && $user->status == 1) {
                    // Tự động khôi phục session cho người dùng
                    $_SESSION['user'] = $user;
                }
            } catch (Exception $e) {
                // Bỏ qua lỗi nếu cơ sở dữ liệu có vấn đề lúc check ngầm
            }
        }
    }
}