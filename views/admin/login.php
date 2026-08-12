<?php
session_start();
// var_dump($_SESSION);
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../dao/UserDAO.php";

$errors = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validate dữ liệu trống ở Server
    if ($username === "") {
        $errors["username"] = "Vui lòng nhập tên đăng nhập.";
    }
    if ($password === "") {
        $errors["password"] = "Vui lòng nhập mật khẩu.";
    }

    // Nếu không có lỗi cơ bản thì kiểm tra thông tin trong CSDL
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
                // Đăng nhập thành công, lưu User vào Session theo yêu cầu của cô
                $_SESSION["user"] = $user;

                // Chuyển hướng vào trang Dashboard quản trị
              header("Location: /MINISHOP_TRANTHINGOCYEN/views/admin/dashboard.php");
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
    <!-- Sử dụng Bootstrap 5 CSS -->
    <link href="<?= BASE_URL ?>assets/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4 fw-bold text-primary">Đăng nhập Admin</h3>

                    <?php if (isset($errors["system"])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $errors["system"] ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" novalidate>
                        <!-- Tên đăng nhập -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên đăng nhập</label>
                            <input type="text" 
                                   name="username" 
                                   required
                                   class="form-control <?= isset($errors["username"]) ? 'is-invalid' : '' ?>" 
                                   placeholder="Nhập tên đăng nhập" 
                                   value="<?= htmlspecialchars($username) ?>">
                            <?php if (isset($errors["username"])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors["username"] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mật khẩu -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu</label>
                            <input type="password" 
                                   name="password" 
                                   required
                                   class="form-control <?= isset($errors["password"]) ? 'is-invalid' : '' ?>" 
                                   placeholder="Nhập mật khẩu">
                            <?php if (isset($errors["password"])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors["password"] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Ghi nhớ đăng nhập -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>

                        <!-- Nút Submit -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">Đăng nhập</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3 text-muted small">
                &copy; Mini Shop Management System
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="<?= BASE_URL ?>assets/bootstrap.bundle.min.js"></script>
</body>
</html>