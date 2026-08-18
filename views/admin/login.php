<?php
use Middleware\GuestMiddleware;
use Middleware\CsrfMiddleware;
use DAO\UserDAO;

require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../dao/UserDAO.php";
require_once __DIR__ . "/../../middleware/GuestMiddleware.php";
require_once __DIR__ . "/../../middleware/CsrfMiddleware.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
GuestMiddleware::handle();
CsrfMiddleware::generateToken();

$errors = [];
$username = "";

if (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $errors["system"] = "Tài khoản của bạn không có quyền quản trị để truy cập khu vực này!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    CsrfMiddleware::verify();

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "") {
        $errors["username"] = "Vui lòng nhập tên đăng nhập.";
    }
    if ($password === "") {
        $errors["password"] = "Vui lòng nhập mật khẩu.";
    }

    if (empty($errors)) {
        try {
            $userDAO = new UserDAO();
            $user = $userDAO->findByUsername($username);

            if (!$user) {
                $errors["username"] = "Tên đăng nhập không tồn tại.";
            } elseif (!password_verify($password, $user->password)) {
                $errors["password"] = "Mật khẩu không chính xác.";
            } elseif ($user->status == 0) {
                $errors["username"] = "Tài khoản của bạn đã bị khóa!";
            } else {
                $_SESSION["user"] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'fullName' => $user->fullName ?? $user->fullname ?? $user->full_name ?? 'Admin',
                    'status' => $user->status,
                    'role' => $user->role ?? 1
                ];

                if (isset($_POST["remember"])) {
                    $token = bin2hex(random_bytes(32));
                    $userDAO->updateRememberToken($user->id, $token);
                    setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);
                } else {
                    if (isset($_COOKIE["remember_token"])) {
                        setcookie("remember_token", "", time() - 3600, "/");
                    }
                }
                header("Location: /MINISHOP_TRANTHINGOCYEN/admin/dashboard");
                exit();
            }
        } catch (Exception $e) {
            $errors["system"] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống - Mini Shop</title>
    <!-- Nhúng trực tiếp 2 file bootstrap sẵn có trong thư mục assets -->
    <link href="/MINISHOP_TRANTHINGOCYEN/assets/bootstrap.min.css" rel="stylesheet">
    <link href="/MINISHOP_TRANTHINGOCYEN/assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Phần form giao diện được dựng sẵn bằng các class bootstrap có sẵn -->
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow border-0 p-4">
                    <h3 class="text-center mb-4 fw-bold text-primary">Đăng nhập Admin</h3>

                    <?php if (isset($errors["system"])): ?>
                        <div class="alert alert-danger text-center"><?= $errors["system"] ?></div>
                    <?php endif; ?>

     <form action="/MINISHOP_TRANTHINGOCYEN/admin/login" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?? '' ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên đăng nhập</label>
                            <input type="text" name="username"
                                class="form-control <?= isset($errors["username"]) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($username) ?>">
                            <div class="invalid-feedback"><?= $errors["username"] ?? '' ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu</label>
                            <input type="password" name="password"
                                class="form-control <?= isset($errors["password"]) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= $errors["password"] ?? '' ?></div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">Đăng nhập</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="/MINISHOP_TRANTHINGOCYEN/assets/bootstrap.bundle.min.js"></script>
</body>

</html>