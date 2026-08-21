<?php
$pageTitle = "Cập nhật thông tin khách hàng - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">✏️ Cập nhật khách hàng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/customer">Danh sách khách hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cập nhật</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= BASE_URL ?>admin/customer" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Form cập nhật thông tin khách hàng</h6>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>admin/customer/edit/<?= $customer->id ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span>:</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên..." required value="<?= htmlspecialchars($customer->fullName) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span>:</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại..." required value="<?= htmlspecialchars($customer->phone) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email..." value="<?= htmlspecialchars($customer->email ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= ($customer->status == 1) ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= ($customer->status == 0) ? 'selected' : '' ?>>Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ:</label>
                    <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ..." value="<?= htmlspecialchars($customer->address ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ghi chú:</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Nhập ghi chú..."><?= htmlspecialchars($customer->note ?? '') ?></textarea>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Cập nhật</button>
                    <a href="<?= BASE_URL ?>admin/customer" class="btn btn-secondary px-4"><i class="fas fa-arrow-left me-1"></i> Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>