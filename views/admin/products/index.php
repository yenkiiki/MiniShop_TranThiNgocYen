<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Sản Phẩm</h3>
    <a href="#" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Thêm sản phẩm</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá bán</th>
                        <th>Giá KM</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= $p->getId() ?></td>
                                <td><img src="../../uploads/products/<?= $p->getImage() ?>" class="rounded" width="50" alt="prod"></td>
                                <td class="fw-bold"><?= htmlspecialchars($p->getProname()) ?></td>
                                <td class="text-primary fw-bold"><?= number_format($p->getPrice(), 0, ',', '.') ?>đ</td>
                                <td class="text-danger"><?= number_format($p->getDiscountPrice(), 0, ',', '.') ?>đ</td>
                                <td><span class="badge bg-info text-dark"><?= $p->getQuantity() ?></span></td>
                                <td>
                                    <span class="badge bg-<?= $p->getStatus() == 1 ? 'success' : 'secondary' ?>">
                                        <?= $p->getStatus() == 1 ? 'Đang bán' : 'Ẩn' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa sản phẩm này?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Chưa có dữ liệu sản phẩm</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>