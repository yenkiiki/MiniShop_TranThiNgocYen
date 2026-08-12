<?php
// Đưa User.php lên trên cùng đầu tiên
require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/CsrfMiddleware.php';

// Sau đó mới khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

AuthMiddleware::handle();
CsrfMiddleware::generateToken();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Quản trị Mini Shop' ?></title>
    <link href="<?= BASE_URL ?>assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include "sidebar.php"; ?>
            
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>