<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Danh Mục</h3>
    <a href="#" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Thêm danh mục</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat->getId() ?></td>
                                <td><img src="../../uploads/categories/<?= $cat->getImage() ?>" class="rounded" width="50" alt="img"></td>
                                <td class="fw-bold"><?= htmlspecialchars($cat->getCatename()) ?></td>
                                <td><code><?= htmlspecialchars($cat->getSlug()) ?></code></td>
                                <td>
                                    <span class="badge bg-<?= $cat->getStatus() == 1 ? 'success' : 'secondary' ?>">
                                        <?= $cat->getStatus() == 1 ? 'Hiển thị' : 'Ẩn' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu danh mục</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>