<?php
// Giao diện thuần túy MVC, không chứa logic xử lý CSDL
$pageTitle = "Thêm mới danh mục";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/category">Danh mục</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i> Thêm mới danh mục
        </div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
                </div>
            <?php endif; ?>

            <!-- ĐÃ SỬA ACTION THÀNH ĐƯỜNG DẪN SẠCH -->
            <form action="/MINISHOP_TRANTHINGOCYEN/admin/category/create" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <?php if (isset($_SESSION["csrf_token"])): ?>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tên danh mục</label>
                    <input type="text" name="cateName" class="form-control" value="<?= htmlspecialchars($cateName ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($slug ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= (isset($status) && $status == 1) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (isset($status) && $status == 0) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <a href="/MINISHOP_TRANTHINGOCYEN/admin/category" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php"; 
?>