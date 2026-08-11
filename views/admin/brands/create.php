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
$success = "";

// XỬ LÝ KHI SUBMIT FORM THÊM MỚI (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brandName = isset($_POST['brandname']) ? trim($_POST['brandname']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    $imageName = "";

    // Validate dữ liệu cơ bản
    if (empty($brandName)) {
        $error = "Tên thương hiệu không được để trống!";
    } else {
        // Tự động tạo slug nếu người dùng không nhập
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brandName), '-'));
        }

        // Xử lý upload hình ảnh (Cần form có enctype="multipart/form-data")
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            // Các định dạng ảnh cho phép
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                // Tạo tên file mới độc đáo để tránh trùng lặp
                $imageName = time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/../../../uploads/brands/';
                
                // Kiểm tra và tạo thư mục nếu chưa tồn tại
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $dest_path = $uploadFileDir . $imageName;
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $error = "Lỗi khi tải lên hình ảnh!";
                }
            } else {
                $error = "Chỉ chấp nhận các định dạng ảnh: " . implode(', ', $allowedExtensions);
            }
        }

        // Nếu không có lỗi, tiến hành lưu vào cơ sở dữ liệu thông qua BrandDAO
        if (empty($error)) {
            try {
                $brand = new Brand();
                $brand->brandName = $brandName;
                $brand->slug = $slug;
                $brand->image = $imageName;
                $brand->description = $description;
                $brand->status = $status;

                $result = $brandDAO->insert($brand);
                if ($result) {
                    header("Location: index.php?msg=insert_success");
                    exit();
                } else {
                    $error = "Thêm mới thương hiệu thất bại!";
                }
            } catch (Exception $e) {
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
}

// Tiêu đề trang
$pageTitle = "Thêm thương hiệu mới - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách thương hiệu</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <!-- HIỂN THỊ LỖI NẾU CÓ -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- FORM THÊM MỚI THƯƠNG HIỆU -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i> Form thêm mới thương hiệu
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên thương hiệu <span class="text-danger">*</span>:</label>
                        <input type="text" name="brandname" class="form-control" placeholder="Nhập tên thương hiệu..." required 
                               value="<?= isset($_POST['brandname']) ? htmlspecialchars($_POST['brandname']) : '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Slug (Đường dẫn thân thiện):</label>
                        <input type="text" name="slug" class="form-control" placeholder="Tự động tạo nếu để trống..." 
                               value="<?= isset($_POST['slug']) ? htmlspecialchars($_POST['slug']) : '' ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <!-- Khung Preview Ảnh / Logo -->
                        <div class="mb-3 text-center" id="preview">
                            <span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa chọn ảnh logo thương hiệu</span>
                        </div>

                        <label class="form-label fw-bold">Hình ảnh logo:</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <div class="form-text">Chọn file ảnh định dạng jpg, jpeg, png, gif, webp.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ẩn / Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả chi tiết:</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả về thương hiệu..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Lưu thông tin
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