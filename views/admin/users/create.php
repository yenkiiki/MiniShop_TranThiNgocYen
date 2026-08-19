<?php 
$pageTitle = "Thêm mới tài khoản người dùng";
ob_start(); 
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý tài khoản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/user">Danh sách tài khoản</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            Lỗi: Vui lòng kiểm tra lại thông tin nhập vào!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-plus me-1"></i> Thêm mới tài khoản người dùng</div>
        <div class="card-body">
            <form method="POST" action="/MINISHOP_TRANTHINGOCYEN/admin/user/store">
                <?= csrf_field() ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ...">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Vai trò</label>
                        <select name="role" class="form-select">
                            <?php foreach ($roleList as $key => $label): ?>
                                <option value="<?= $key ?>"><?= is_array($label) ? $label['label'] : $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statusList as $key => $label): ?>
                                <option value="<?= $key ?>"><?= is_array($label) ? $label['label'] : $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Lưu thông tin</button>
                    <a href="/MINISHOP_TRANTHINGOCYEN/admin/user" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>