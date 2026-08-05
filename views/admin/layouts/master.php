<?php
// Gọi Header
require_once __DIR__ . '/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar bên trái -->
        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <!-- Content bên phải -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
            <?php
            if (isset($contentView) && file_exists($contentView)) {
                require_once $contentView;
            } else {
                echo '<div class="alert alert-warning">Không tìm thấy giao diện!</div>';
            }
            ?>
        </main>
    </div>
</div>

<?php
// Gọi Footer
require_once __DIR__ . '/footer.php';
?>