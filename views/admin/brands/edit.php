<?php
// Bật hiển thị lỗi để debug trong quá trình phát triển
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../models/Brand.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";

$brandDAO = new BrandDAO();
$error = "";

// Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Lấy thông tin thương hiệu hiện tại từ CSDL
$brand = $brandDAO->findById($id);
if (!$brand) {
    header("Location: index.php?msg=not_found");
    exit();
}

// XỬ LÝ KHI SUBMIT FORM CẬP NHẬT (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandName = isset($_POST['brandname']) ? trim($_POST['brandname']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    
    // Giữ nguyên ảnh cũ mặc định
    $imageName = $brand->image;

    // Validate dữ liệu cơ bản
    if (empty($brandName)) {
        $error = "Tên thương hiệu không được để trống!";
    } else {
        // Tự động tạo slug nếu người dùng để trống
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brandName), '-'));
        }

        // Xử lý upload hình ảnh mới (nếu có chọn file)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Các định dạng ảnh cho phép
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                // Tạo tên file mới độc đáo
                $newImageName = time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/../../../uploads/brands/';
                
                // Kiểm tra và tạo thư mục nếu chưa tồn tại
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $dest_path = $uploadFileDir . $newImageName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Xóa ảnh cũ trên server nếu tồn tại ảnh cũ
                    if (!empty($brand->image) && file_exists($uploadFileDir . $brand->image)) {
                        @unlink($uploadFileDir . $brand->image);
                    }
                    $imageName = $newImageName;
                } else {
                    $error = "Lỗi khi tải lên hình ảnh mới!";
                }
            } else {
                $error = "Chỉ chấp nhận các định dạng ảnh: " . implode(', ', $allowedExtensions);
            }
        }

        // Nếu không có lỗi, tiến hành cập nhật vào CSDL thông qua BrandDAO
        if (empty($error)) {
            try {
                // Gán giá trị mới cho đối tượng brand
                $brand->brandName = $brandName;
                $brand->slug = $slug;
                $brand->image = $imageName;
                $brand->description = $description;
                $brand->status = $status;

                $result = $brandDAO->update($brand);
                if ($result) {
                    header("Location: index.php?msg=update_success");
                    exit();
                } else {
                    $error = "Cập nhật thương hiệu thất bại!";
                }
            } catch (Exception $e) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
}

// Tiêu đề trang
$pageTitle = "Chỉnh sửa thương hiệu - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách thương hiệu</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>

    <!-- HIỂN THỊ LỖI NẾU CÓ -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- FORM CHỈNH SỬA THƯƠNG HIỆU -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Form chỉnh sửa thương hiệu: <b><?= htmlspecialchars($brand->brandName) ?></b>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên thương hiệu <span class="text-danger">*</span>:</label>
                        <input type="text" name="brandname" class="form-control" placeholder="Nhập tên thương hiệu..." required 
                               value="<?= htmlspecialchars($_POST['brandname'] ?? $brand->brandName) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Slug (Đường dẫn thân thiện):</label>
                        <input type="text" name="slug" class="form-control" placeholder="Tự động tạo nếu để trống..." 
                               value="<?= htmlspecialchars($_POST['slug'] ?? $brand->slug) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Hình ảnh logo:</label>
                        <input type="file" name="image" class="form-control mb-2" accept="image/*">
                        <div class="form-text">Chọn file mới nếu muốn thay đổi ảnh logo hiện tại.</div>
                        
                        <!-- Hiển thị ảnh hiện tại -->
                        <?php if (!empty($brand->image)): ?>
                            <div class="mt-2">
                                <span class="d-block text-muted mb-1">Ảnh hiện tại:</span>
                                <img src="../../../uploads/brands/<?= htmlspecialchars($brand->image) ?>" alt="Brand Logo" style="width: 80px; height: 80px; object-fit: cover;" class="rounded border">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <?php $currentStatus = $_POST['status'] ?? $brand->status; ?>
                            <option value="1" <?= ($currentStatus == 1) ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="0" <?= ($currentStatus == 0) ? 'selected' : '' ?>>Ẩn / Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả chi tiết:</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả về thương hiệu..."><?= htmlspecialchars($_POST['description'] ?? $brand->description) ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>