<?php
$pageTitle = "Quản Lý Tài Khoản Nhân Viên";

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Tài Khoản Nhân Viên</h3>
    <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-user-plus me-1"></i>Tạo tài khoản</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>Họ tên</th>
                        <th>Tên đăng nhập</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php $stt = 1; foreach ($users as $u): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $stt++ ?></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($u->getFullname() ?? '') ?></td>
                                <td><code><?= htmlspecialchars($u->getUsername() ?? '') ?></code></td>
                                <td><?= htmlspecialchars($u->getEmail() ?? '') ?></td>
                                <td>
                                    <?php if ($u->getRole() == 1): ?>
                                        <span class="badge bg-danger"><i class="fa-solid fa-user-shield me-1"></i>Quản trị</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark"><i class="fa-solid fa-user me-1"></i>Nhân viên</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u->getStatus() == 1): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-lock me-1"></i>Khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= method_exists($u, 'getCreatedAt') && $u->getCreatedAt() ? date('d/m/Y H:i', strtotime($u->getCreatedAt())) : '---' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="detail.php?id=<?= $u->getId() ?>" class="btn btn-sm btn-outline-info" title="Chi tiết"><i class="fa-solid fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $u->getId() ?>" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="delete.php?id=<?= $u->getId() ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có dữ liệu tài khoản</td></tr>
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