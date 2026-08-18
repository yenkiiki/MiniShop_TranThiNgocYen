<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Quản trị Mini Shop' ?></title>
    <link href="<?= defined('BASE_URL') ? BASE_URL : '/MINISHOP_TRANTHINGOCYEN/' ?>assets/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/header.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . "/sidebar.php"; ?>
            
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <?php include __DIR__ . "/footer.php"; ?>
    <script src="<?= defined('BASE_URL') ? BASE_URL : '/MINISHOP_TRANTHINGOCYEN/' ?>assets/bootstrap.bundle.min.js"></script>
    <script src="<?= defined('BASE_URL') ? BASE_URL : '/MINISHOP_TRANTHINGOCYEN/' ?>assets/admin/admin.js"></script>
</body>
</html>