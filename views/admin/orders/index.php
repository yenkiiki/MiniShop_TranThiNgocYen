<?php
$pageTitle = "Quản Lý Đơn Hàng";

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Đơn Hàng</h3>
    <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tạo đơn hàng mới</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ghi chú</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php $stt = 1; foreach ($orders as $o): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $stt++ ?></td>
                                <td><span class="badge bg-dark"><?= htmlspecialchars($o->getOrderCode() ?? '') ?></span></td>
                                <td>Khách hàng #<?= $o->getCustomerId() ?></td>
                                <td class="text-danger fw-bold"><?= number_format($o->getTotalAmount(), 0, ',', '.') ?>đ</td>
                                <td><?= htmlspecialchars($o->getNote() ?? '') ?></td>
                                <td>
                                    <?php
                                    $status = $o->getStatus();
                                    if ($status == 0) echo '<span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chờ xử lý</span>';
                                    elseif ($status == 1) echo '<span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i>Hoàn thành</span>';
                                    else echo '<span class="badge bg-danger"><i class="fa-solid fa-times-circle me-1"></i>Đã hủy</span>';
                                    ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= method_exists($o, 'getCreatedAt') && $o->getCreatedAt() ? date('d/m/Y H:i', strtotime($o->getCreatedAt())) : '---' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="detail.php?id=<?= $o->getId() ?>" class="btn btn-sm btn-outline-info" title="Chi tiết"><i class="fa-solid fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $o->getId() ?>" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="delete.php?id=<?= $o->getId() ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Xóa đơn hàng này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu đơn hàng</td></tr>
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