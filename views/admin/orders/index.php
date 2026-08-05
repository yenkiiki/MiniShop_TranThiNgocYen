<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Đơn Hàng</h3>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng ID</th>
                        <th>Tổng tiền</th>
                        <th>Ghi chú</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><?= $o->getId() ?></td>
                                <td><span class="badge bg-dark"><?= htmlspecialchars($o->getOrderCode()) ?></span></td>
                                <td>Khách hàng #<?= $o->getCustomerId() ?></td>
                                <td class="text-danger fw-bold"><?= number_format($o->getTotalAmount(), 0, ',', '.') ?>đ</td>
                                <td><?= htmlspecialchars($o->getNote()) ?></td>
                                <td>
                                    <?php
                                    $status = $o->getStatus();
                                    if ($status == 0) echo '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
                                    elseif ($status == 1) echo '<span class="badge bg-success">Hoàn thành</span>';
                                    else echo '<span class="badge bg-danger">Đã hủy</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye"></i> Xem</a>
                                    <a href="#" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Chưa có dữ liệu đơn hàng</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>