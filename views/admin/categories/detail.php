<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../models/Category.php";

$categoryDAO = new CategoryDAO();

// Nhận id từ URL và ép kiểu sang số nguyên
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Gọi phương thức findById để lấy thông tin danh mục từ DAO
$category = $categoryDAO->findById($id);

// Nếu không tìm thấy danh mục theo ID, chuyển hướng về trang danh sách
if (!$category) {
    header("Location: index.php");
    exit();
}

// Đặt tiêu đề trang
$pageTitle = "Chi tiết danh mục - Mini Shop";

// Sử dụng buffer để đưa nội dung vào layout chung master.php
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh mục sản phẩm</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-eye me-1"></i>
                Chi tiết thông tin danh mục
            </div>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width: 200px;" class="bg-light">ID Danh mục:</th>
                        <td><?= $category->id ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tên danh mục:</th>
                        <td class="fw-bold text-primary"><?= htmlspecialchars($category->cateName) ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Slug:</th>
                        <td><code><?= htmlspecialchars($category->slug ?? '') ?></code></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Hình ảnh:</th>
                        <td>
                            <?php if (!empty($category->image)): ?>
                                <img src="<?= BASE_URL ?>uploads/categories/<?= htmlspecialchars($category->image) ?>" alt="Image" style="max-height: 100px;" class="img-thumbnail">
                            <?php else: ?>
                                <span class="text-muted">Không có hình ảnh</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Mô tả:</th>
                        <td><?= nl2br(htmlspecialchars($category->description ?? 'Không có mô tả')) ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Trạng thái:</th>
                        <td>
                            <?php if ($category->status == 1): ?>
                                <span class="badge bg-success fs-6">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge bg-secondary fs-6">Ẩn</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Ngày tạo:</th>
                        <td><?= htmlspecialchars($category->createdAt ?? '') ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Ngày cập nhật:</th>
                        <td><?= htmlspecialchars($category->updatedAt ?? 'Chưa cập nhật') ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Các nút thao tác -->
            <div class="mt-4">
                <a href="edit.php?id=<?= $category->id ?>" class="btn btn-warning text-white">
                    <i class="fas fa-edit"></i> Sửa
                </a>
                <a href="delete.php?id=<?= $category->id ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');">
                    <i class="fas fa-trash"></i> Xóa
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>