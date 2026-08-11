<?php
// Bật hiển thị lỗi để dễ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../models/Category.php";

$categoryDAO = new CategoryDAO();

// Nhận id từ trang index.php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Gọi phương thức findById để lấy thông tin danh mục
$category = $categoryDAO->findById($id);

// Nếu không tìm thấy danh mục theo ID, chuyển hướng về trang danh sách
if (!$category) {
    header("Location: index.php");
    exit();
}

$errors = [];

// Đọc dữ liệu từ các điều khiển trên Form bằng phương thức POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cateName = trim($_POST["cateName"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // Kiểm tra dữ liệu đầu vào (Validation)
    if ($cateName === "") {
        $errors[] = "Tên danh mục không được để trống.";
    } elseif (mb_strlen($cateName) > 255) {
        $errors[] = "Tên danh mục không được vượt quá 255 ký tự.";
    }

    if ($slug === "") {
        $errors[] = "Slug không được để trống.";
    } elseif (mb_strlen($slug) > 255) {
        $errors[] = "Slug không được vượt quá 255 ký tự.";
    }

    // Xử lý upload hình ảnh mới (nếu có chọn)
    $imageName = $category->image; // Giữ lại ảnh cũ mặc định
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Chỉ cho phép các định dạng ảnh phổ biến
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Tạo tên file mới độc lập tránh trùng lặp
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../../../uploads/categories/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Nếu upload thành công, gán tên ảnh mới
                $imageName = $newFileName;
                
                // Xóa ảnh cũ trên server nếu tồn tại
                if (!empty($category->image)) {
                    $oldImagePath = $uploadFileDir . $category->image;
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
            } else {
                $errors[] = "Đã xảy ra lỗi khi di chuyển file tải lên.";
            }
        } else {
            $errors[] = "Định dạng ảnh không hợp lệ. Chỉ chấp nhận: jpg, jpeg, png, gif, webp.";
        }
    }

    // Nếu Validation thành công, tiến hành cập nhật dữ liệu
    if (empty($errors)) {
        try {
            // Gán dữ liệu mới vào đối tượng category hiện tại
            $category->cateName = $cateName;
            $category->slug = $slug;
            $category->description = $description;
            $category->status = $status;
            $category->image = $imageName; // Cập nhật tên ảnh mới hoặc giữ nguyên ảnh cũ

            // Gọi phương thức update() trong CategoryDAO để cập nhật dữ liệu
            $result = $categoryDAO->update($category);

            if ($result) {
                // Nếu cập nhật thành công: Chuyển về trang index.php
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Cập nhật danh mục thất bại. Vui lòng thử lại.";
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// Khai báo biến $pageTitle
$pageTitle = "Cập nhật danh mục - Mini Shop";

// Sử dụng ob_start() và ob_get_clean() để truyền nội dung vào master.php
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh mục sản phẩm</a></li>
        <li class="breadcrumb-item active">Cập nhật</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-edit me-1"></i>
                Cập nhật danh mục
            </div>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="card-body">
            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Thêm enctype="multipart/form-data" để hỗ trợ upload file -->
            <form method="POST" enctype="multipart/form-data">
                <!-- ID -->
                <input type="hidden" name="categoryId" value="<?= $category->id ?>">

                <!-- Tên danh mục -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="cateName" class="form-control" value="<?= htmlspecialchars($category->cateName) ?>">
                </div>

                <!-- Slug -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category->slug ?? '') ?>">
                </div>

                <!-- Hình ảnh -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh danh mục</label>
                    <div class="mb-2">
                        <?php if (!empty($category->image)): ?>
                            <img src="../../../uploads/categories/<?= htmlspecialchars($category->image) ?>" 
                                 alt="<?= htmlspecialchars($category->cateName) ?>" 
                                 class="img-thumbnail rounded shadow-sm" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        <?php else: ?>
                            <span class="text-muted small">Chưa có ảnh</span>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-text text-muted">Chọn ảnh mới nếu bạn muốn thay thế ảnh hiện tại.</div>
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="description" rows="5" class="form-control"><?= htmlspecialchars($category->description ?? '') ?></textarea>
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Trạng thái</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" value="1" <?= $category->status == 1 ? "checked" : "" ?>>
                        <label class="form-check-label">Hiển thị</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" value="0" <?= $category->status == 0 ? "checked" : "" ?>>
                        <label class="form-check-label">Ẩn</label>
                    </div>
                </div>

                <!-- Button -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <button type="reset" class="btn btn-warning text-white">
                        <i class="fas fa-redo"></i> Làm mới
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