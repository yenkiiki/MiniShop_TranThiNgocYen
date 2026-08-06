<?php
// 1. Tiêu đề trang
$pageTitle = "Quản Lý Thương Hiệu";

// 2. Bắt đầu đệm dữ liệu
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Thương Hiệu</h3>
<!-- SỬA TỪ: <a href="create.php" ...> -->
<!-- THÀNH: -->
<a href="brands/create.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Thêm thương hiệu</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Tên thương hiệu</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($brands)): ?>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td><?= $brand->getId() ?></td>
                                <td>
                                    <?php if ($brand->getImage()): ?>
                                        <img src="../../uploads/brands/<?= $brand->getImage() ?>" class="rounded" width="50" alt="logo">
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">No img</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($brand->getBrandname() ?? '') ?></td>
                                <td><code><?= htmlspecialchars($brand->getSlug() ?? '') ?></code></td>
                                <td>
                                    <span class="badge bg-<?= $brand->getStatus() == 1 ? 'success' : 'secondary' ?>">
                                        <?= $brand->getStatus() == 1 ? 'Kích hoạt' : 'Khóa' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $brand->getId() ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="delete.php?id=<?= $brand->getId() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Chưa có dữ liệu thương hiệu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// 3. Gom nội dung vào $content và xả đệm
$content = ob_get_clean();

// 4. Nhúng master layout
include __DIR__ . '/../layouts/master.php';
?>