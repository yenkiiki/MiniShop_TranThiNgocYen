<?php
$pageTitle = "Thêm khách hàng mới";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý khách hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?controller=customer&action=index">Danh sách khách hàng</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-plus me-1"></i> Form thêm khách hàng mới</div>
        <div class="card-body">
            <form action="index.php?controller=customer&action=create" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span>:</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên..." required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span>:</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại..." required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email..." value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <?php $currentStatus = $_POST['status'] ?? 1; ?>
                            <option value="1" <?= ($currentStatus == 1) ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= ($currentStatus == 0) ? 'selected' : '' ?>>Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ:</label>
                    <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ..." value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ghi chú:</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Nhập ghi chú..."><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Lưu thông tin</button>
                    <a href="index.php?controller=customer&action=index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>