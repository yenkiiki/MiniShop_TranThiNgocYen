<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?? "MINISHOP" ?></title>
    <link href="/MiniShop_TranThiNgocYen/assets/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/MiniShop_TranThiNgocYen/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/header.php"; ?>
    <main class="min-vh-100">
        <div class="container-fluid p-4">
            <?= $content ?? '' ?>
        </div>
    </main>
    <?php include __DIR__ . "/footer.php"; ?>
    <script src="/MiniShop_TranThiNgocYen/assets/bootstrap.bundle.min.js"></script>
</body>
</html>