<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../models/Customer.php";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";

$customerDAO = new CustomerDAO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$customer = $customerDAO->findById($id);
if (!$customer) {
    header("Location: index.php");
    exit();
}

$statusList = [
    0 => ['label' => 'Khóa', 'class' => 'bg-danger text-white'],
    1 => ['label' => 'Hoạt động', 'class' => 'bg-success text-white']
];

$pageTitle = "Chi tiết thông tin khách hàng";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý khách hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách khách hàng</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-eye me-1"></i> Chi tiết thông tin khách hàng</div>
            <div>
                <a href="edit.php?id=<?= $customer->id ?>" class="btn btn-warning text-white btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                <a href="index.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 200px;" class="bg-light">ID:</th>
                    <td><?= $customer->id ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Họ tên:</th>
                    <td class="fw-bold text-primary"><?= htmlspecialchars($customer->fullName) ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Số điện thoại:</th>
                    <td><?= htmlspecialchars($customer->phone) ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Email:</th>
                    <td><?= htmlspecialchars($customer->email ?? 'Chưa cập nhật') ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Địa chỉ:</th>
                    <td><?= htmlspecialchars($customer->address ?? 'Chưa cập nhật') ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Trạng thái:</th>
                    <td>
                        <?php 
                            $sttKey = $customer->status;
                            $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                        ?>
                        <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Ghi chú:</th>
                    <td><?= nl2br(htmlspecialchars($customer->note ?? 'Không có ghi chú')) ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Ngày tạo:</th>
                    <td><?= htmlspecialchars($customer->createdAt) ?></td>
                </tr>
                <tr>
                    <th class="bg-light">Cập nhật lần cuối:</th>
                    <td><?= htmlspecialchars($customer->updatedAt) ?></td>
                </tr>
            </table>

            <div class="mt-4">
                <a href="edit.php?id=<?= $customer->id ?>" class="btn btn-warning text-white"><i class="fas fa-edit"></i> Chỉnh sửa</a>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>