<?php
$pageTitle = "Quản Lý Khách Hàng";

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Khách Hàng</h3>
    <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-user-plus me-1"></i>Thêm khách hàng</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>Họ và tên</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php $stt = 1; foreach ($customers as $c): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $stt++ ?></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($c->getFullname() ?? '') ?></td>
                                <td><?= htmlspecialchars($c->getPhone() ?? '') ?></td>
                                <td><?= htmlspecialchars($c->getEmail() ?? '') ?></td>
                                <td><?= htmlspecialchars($c->getAddress() ?? '') ?></td>
                                <td>
                                    <?php if ($c->getStatus() == 1): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-user-check me-1"></i>Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-user-slash me-1"></i>Khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= method_exists($c, 'getCreatedAt') && $c->getCreatedAt() ? date('d/m/Y H:i', strtotime($c->getCreatedAt())) : '---' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="detail.php?id=<?= $c->getId() ?>" class="btn btn-sm btn-outline-info" title="Chi tiết"><i class="fa-solid fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $c->getId() ?>" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="delete.php?id=<?= $c->getId() ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu khách hàng</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/master.php';
?>