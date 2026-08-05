<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Khách Hàng</h3>
    <a href="#" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Thêm khách hàng</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td><?= $c->getId() ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($c->getFullname()) ?></td>
                                <td><?= htmlspecialchars($c->getPhone()) ?></td>
                                <td><?= htmlspecialchars($c->getEmail()) ?></td>
                                <td><?= htmlspecialchars($c->getAddress()) ?></td>
                                <td>
                                    <span class="badge bg-<?= $c->getStatus() == 1 ? 'success' : 'secondary' ?>">
                                        <?= $c->getStatus() == 1 ? 'Hoạt động' : 'Khóa' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa khách hàng này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Chưa có dữ liệu khách hàng</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>