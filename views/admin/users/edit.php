<?php ob_start(); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý tài khoản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?controller=user&action=index">Danh sách</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">Lỗi: Vui lòng kiểm tra lại thông tin!</div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Chỉnh sửa tài khoản: <strong><?= htmlspecialchars($user->userName) ?></strong></div>
        <div class="card-body">
            <form method="POST" action="index.php?controller=user&action=update">
                <input type="hidden" name="id" value="<?= $user->id ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user->fullName) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user->userName) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mật khẩu mới</label>
                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                    </div>
                </div>

                <!-- Bổ sung trường Số điện thoại và Địa chỉ -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user->phone ?? '') ?>" placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user->address ?? '') ?>" placeholder="Nhập địa chỉ...">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Vai trò</label>
                        <select name="role" class="form-select">
                            <?php foreach ($roleList as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $user->role == $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statusList as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $user->status == $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="index.php?controller=user&action=index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>