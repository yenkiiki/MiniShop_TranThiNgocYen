<?php
$pageTitle = "Chi tiết thương hiệu - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <!-- Đã sửa: dùng đường dẫn thân thiện -->
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/brand">Danh sách thương hiệu</a></li>
        <li class="breadcrumb-item active">Chi tiết thương hiệu</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i> Thông tin chi tiết thương hiệu: <b><?= htmlspecialchars($brand->brandName ?? '') ?></b>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <!-- ... (Các phần TR giữ nguyên như cũ) ... -->
                <tr>
                    <th style="width: 200px;">ID:</th>
                    <td><?= $brand->id ?? '' ?></td>
                </tr>
                <tr>
                    <th>Tên thương hiệu:</th>
                    <td class="fw-bold text-primary"><?= htmlspecialchars($brand->brandName ?? '') ?></td>
                </tr>
                <tr>
                    <th>Logo thương hiệu:</th>
                    <td>
                        <?php if (!empty($brand->image)): ?>
                           <img src="/MINISHOP_TRANTHINGOCYEN/uploads/brands/<?= htmlspecialchars($brand->image) ?>" alt="Logo" style="width: 100px; height: 100px; object-fit: cover;" class="rounded border">
                        <?php else: ?>
                            <span class="text-muted">Không có ảnh</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <!-- ... (Các phần khác giữ nguyên) ... -->
            </table>

            <div class="mt-4">
                <!-- SỬA Ở ĐÂY: Dùng link thân thiện -->
                <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand/edit/<?= $brand->id ?>" class="btn btn-warning text-white">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../layouts/master.php";
?>