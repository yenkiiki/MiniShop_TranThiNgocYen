<?php
// 1. Khai báo biến $pageTitle
$pageTitle = "Quản lý danh mục";

// 2. Bắt đầu bộ nhớ đệm Output Buffering
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Quản Lý Danh Mục</h3>
    <!-- Nút Thêm chuyển sang create.php theo đề bài -->
    <a href="create.php" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i>Thêm danh mục
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>Hình ảnh</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center" style="width: 180px;">Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php $stt = 1; // Khai báo biến STT chạy từ 1 ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <!-- STT tự tăng -->
                                <td class="text-center fw-bold"><?= $stt++ ?></td>
                                
                                <td>
                                    <?php if ($cat->getImage()): ?>
                                        <img src="../../uploads/categories/<?= $cat->getImage() ?>" class="rounded" width="45" height="45" style="object-fit:cover;" alt="img">
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Không ảnh</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="fw-bold text-primary"><?= htmlspecialchars($cat->getCatename() ?? '') ?></td>
                                
                                <td><code><?= htmlspecialchars($cat->getSlug() ?? '') ?></code></td>
                                
                                <!-- Trạng thái dùng Badge Bootstrap -->
                                <td>
                                    <?php if ($cat->getStatus() == 1): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Hiển thị</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-eye-slash me-1"></i>Ẩn</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Ngày tạo -->
                                <td>
                                    <small class="text-muted">
                                        <?= !empty($cat->getCreatedAt()) ? date('d/m/Y', strtotime($cat->getCreatedAt())) : 'N/A' ?>
                                    </small>
                                </td>
                                
                                <!-- Cột Chức năng đủ 3 nút: Chi tiết, Sửa, Xóa -->
                                <td class="text-center">
                                    <a href="detail.php?id=<?= $cat->getId() ?>" class="btn btn-sm btn-outline-info" title="Chi tiết">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?= $cat->getId() ?>" class="btn btn-sm btn-outline-warning" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $cat->getId() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có dữ liệu danh mục</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// 3. Gom nội dung đệm vào biến $content
$content = ob_get_clean();

// 4. Nhúng master layout
include __DIR__ . "/../layouts/master.php";
?>