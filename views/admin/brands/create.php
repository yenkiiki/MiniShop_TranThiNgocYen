<?php
use Middleware\CsrfMiddleware;
require_once __DIR__ . "/../../../middleware/CsrfMiddleware.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
CsrfMiddleware::generateToken();

$pageTitle = "Thêm thương hiệu mới - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/brand">Danh sách thương hiệu</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-plus-circle me-1"></i> Form thêm mới thương hiệu</div>
        <div class="card-body">
            <!-- ĐÃ SỬA: Form action chuẩn URL thân thiện -->
            <form action="/MINISHOP_TRANTHINGOCYEN/admin/brand/create" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"] ?? '') ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên thương hiệu <span class="text-danger">*</span>:</label>
                        <input type="text" name="brandname" class="form-control" placeholder="Nhập tên thương hiệu..." required 
                               value="<?= htmlspecialchars($_POST['brandname'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Slug (Đường dẫn thân thiện):</label>
                        <input type="text" name="slug" class="form-control" placeholder="Tự động tạo nếu để trống..." 
                               value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3 text-center" id="preview">
                            <span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa chọn ảnh logo thương hiệu</span>
                        </div>
                        <label class="form-label fw-bold">Hình ảnh logo:</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <div class="form-text">Định dạng hỗ trợ: jpg, jpeg, png, gif, webp.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ẩn / Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả chi tiết:</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả về thương hiệu..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Lưu thông tin</button>
                    <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>