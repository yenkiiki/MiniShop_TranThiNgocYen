<?php ob_start(); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý tài khoản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?controller=user&action=index">Danh sách</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-info-circle me-1"></i> Thông tin chi tiết: <strong><?= htmlspecialchars($user->userName) ?></strong></div>
            <div>
                <a href="index.php?controller=user&action=edit&id=<?= $user->id ?>" class="btn btn-warning text-white btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                <a href="index.php?controller=user&action=delete&id=<?= $user->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?');"><i class="fas fa-trash"></i> Xóa</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr><th class="w-25 bg-light">ID</th><td><?= $user->id ?></td></tr>
                    <tr><th class="bg-light">Họ và tên</th><td class="fw-bold text-primary"><?= htmlspecialchars($user->fullName) ?></td></tr>
                    <tr><th class="bg-light">Username</th><td><?= htmlspecialchars($user->userName) ?></td></tr>
                    <tr><th class="bg-light">Email</th><td><?= htmlspecialchars($user->email) ?></td></tr>
                    <tr><th class="bg-light">Số điện thoại</th><td><?= !empty($user->phone) ? htmlspecialchars($user->phone) : '<span class="text-muted">Chưa cập nhật</span>' ?></td></tr>
                    <tr><th class="bg-light">Địa chỉ</th><td><?= !empty($user->address) ? htmlspecialchars($user->address) : '<span class="text-muted">Chưa cập nhật</span>' ?></td></tr>
                    <tr>
                        <th class="bg-light">Vai trò</th>
                        <td>
                            <?php $role = $roleList[$user->role] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary']; ?>
                            <span class="badge <?= $role['class'] ?>"><?= $role['label'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Trạng thái</th>
                        <td>
                            <?php $stt = $statusList[$user->status] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary']; ?>
                            <span class="badge <?= $stt['class'] ?>"><?= $stt['label'] ?></span>
                        </td>
                    </tr>
                    <tr><th class="bg-light">Ngày tạo</th><td><?= htmlspecialchars($user->createdAt) ?></td></tr>
                </tbody>
            </table>
            <a href="index.php?controller=user&action=index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>