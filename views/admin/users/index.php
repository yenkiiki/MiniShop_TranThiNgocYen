<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Tài Khoản Nhân Viên</h3>
    <a href="#" class="btn btn-primary"><i class="fa-solid fa-user-plus me-1"></i>Tạo tài khoản</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u->getId() ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($u->getFullname()) ?></td>
                                <td><code><?= htmlspecialchars($u->getUsername()) ?></code></td>
                                <td><?= htmlspecialchars($u->getEmail()) ?></td>
                                <td>
                                    <span class="badge bg-<?= $u->getRole() == 1 ? 'danger' : 'info' ?>">
                                        <?= $u->getRole() == 1 ? 'Quản trị' : 'Nhân viên' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $u->getStatus() == 1 ? 'success' : 'secondary' ?>">
                                        <?= $u->getStatus() == 1 ? 'Hoạt động' : 'Khóa' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tài khoản này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Chưa có dữ liệu tài khoản</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>