<?php
$pageTitle = "Chi tiết khách hàng";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết khách hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?controller=customer&action=index">Danh sách khách hàng</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-info-circle me-1"></i> Thông tin khách hàng</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th class="w-25">ID:</th>
                            <td><?= htmlspecialchars($customer->id) ?></td>
                        </tr>
                        <tr>
                            <th>Họ và tên:</th>
                            <td class="fw-bold text-primary"><?= htmlspecialchars($customer->fullName) ?></td>
                        </tr>
                        <tr>
                            <th>Số điện thoại:</th>
                            <td><?= htmlspecialchars($customer->phone) ?></td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?= htmlspecialchars($customer->email ?? 'Không có') ?></td>
                        </tr>
                        <tr>
                            <th>Địa chỉ:</th>
                            <td><?= htmlspecialchars($customer->address ?? 'Không có') ?></td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                <?php if ($customer->status == 1): ?>
                                    <span class="badge bg-success text-white">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-danger text-white">Khóa</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Ghi chú:</th>
                            <td><?= nl2br(htmlspecialchars($customer->note ?? 'Không có')) ?></td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td><?= htmlspecialchars($customer->createdAt) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="index.php?controller=customer&action=edit&id=<?= $customer->id ?>" class="btn btn-warning text-white"><i class="fas fa-edit"></i> Chỉnh sửa</a>
                <a href="index.php?controller=customer&action=index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>