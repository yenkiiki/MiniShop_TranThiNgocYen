<?php
$pageTitle = "Quản Lý Sản Phẩm";

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Sản Phẩm</h3>
    <a href="create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá bán</th>
                        <th>Giá KM</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php $stt = 1; foreach ($products as $p): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $stt++ ?></td>
                                <td>
                                    <?php if ($p->getImage()): ?>
                                        <img src="../../uploads/products/<?= $p->getImage() ?>" class="rounded border" width="50" height="50" style="object-fit: cover;" alt="prod">
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">No img</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($p->getProname() ?? '') ?></td>
                                <td class="text-primary fw-bold"><?= number_format($p->getPrice(), 0, ',', '.') ?>đ</td>
                                <td class="text-danger fw-bold"><?= number_format($p->getDiscountPrice(), 0, ',', '.') ?>đ</td>
                                <td><span class="badge bg-info text-dark"><?= $p->getQuantity() ?></span></td>
                                <td>
                                    <?php if ($p->getStatus() == 1): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Đang bán</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-ban me-1"></i>Ngừng bán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= method_exists($p, 'getCreatedAt') && $p->getCreatedAt() ? date('d/m/Y H:i', strtotime($p->getCreatedAt())) : '---' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="detail.php?id=<?= $p->getId() ?>" class="btn btn-sm btn-outline-info" title="Chi tiết"><i class="fa-solid fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $p->getId() ?>" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="delete.php?id=<?= $p->getId() ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Chưa có dữ liệu sản phẩm</td></tr>
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