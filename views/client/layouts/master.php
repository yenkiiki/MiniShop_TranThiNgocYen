<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?? "MINISHOP" ?></title>
    <link href="<?= BASE_URL ?>assets/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . "/header.php"; ?>
    <main class="min-vh-100">
        <div class="container-fluid p-4">
            <?= $content ?? '' ?>
        </div>
    </main>
    <?php include __DIR__ . "/footer.php"; ?>
    <script src="<?= BASE_URL ?>assets/bootstrap.bundle.min.js"></script>
    <script>const BASE_URL = "<?= BASE_URL ?>";
        const CSRF_TOKEN = "<?= $_SESSION['csrf_token'] ?? '' ?>";</script>

    <script src="<?= BASE_URL ?>/public/js/cart.js"></script>
    <!-- Bootstrap Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">MINISHOP</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Đã thêm sản phẩm thành công!
        </div>
    </div>
</div>
</body>

</html>