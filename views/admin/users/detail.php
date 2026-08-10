<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../../../dao/UserDAO.php";

$userDAO = new UserDAO();

$roleList = [
    0 => ['label' => 'Thành viên', 'class' => 'bg-secondary text-white'],
    1 => ['label' => 'Quản trị viên', 'class' => 'bg-primary text-white']
];

$statusList = [
    0 => ['label' => 'Khóa', 'class' => 'bg-danger text-white'],
    1 => ['label' => 'Hoạt động', 'class' => 'bg-success text-white']
];

// Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Lấy thông tin user từ Database thông qua DAO
$user = $userDAO->findById($id);
if (!$user) {
    header("Location: index.php?msg=not_found");
    exit();
}

$pageTitle = "Chi tiết tài khoản người dùng";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý tài khoản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách tài khoản</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-1"></i> Thông tin chi tiết tài khoản: <strong><?= htmlspecialchars($user->userName) ?></strong>
            </div>
            <div>
                <a href="edit.php?id=<?= $user->id ?>" class="btn btn-warning text-white btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                <a href="index.php?action=delete&id=<?= $user->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');"><i class="fas fa-trash"></i> Xóa</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th class="w-25 bg-light">ID</th>
                            <td><?= $user->id ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Họ và tên</th>
                            <td class="fw-bold text-primary"><?= htmlspecialchars($user->fullName) ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tên đăng nhập (Username)</th>
                            <td><?= htmlspecialchars($user->userName) ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Email</th>
                            <td><?= htmlspecialchars($user->email) ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Số điện thoại</th>
                            <td><?= !empty($user->phone) ? htmlspecialchars($user->phone) : '<span class="text-muted italic">Chưa cập nhật</span>' ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Địa chỉ</th>
                            <td><?= !empty($user->address) ? htmlspecialchars($user->address) : '<span class="text-muted italic">Chưa cập nhật</span>' ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Vai trò</th>
                            <td>
                                <?php 
                                    $roleKey = $user->role;
                                    $roleInfo = $roleList[$roleKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                ?>
                                <span class="badge <?= $roleInfo['class'] ?>"><?= $roleInfo['label'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Trạng thái</th>
                            <td>
                                <?php 
                                    $sttKey = $user->status;
                                    $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                ?>
                                <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày tạo</th>
                            <td><?= !empty($user->createdAt) ? htmlspecialchars($user->createdAt) : '<span class="text-muted">Không xác định</span>' ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày cập nhật gần nhất</th>
                            <td><?= !empty($user->updatedAt) ? htmlspecialchars($user->updatedAt) : '<span class="text-muted">Chưa cập nhật</span>' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>