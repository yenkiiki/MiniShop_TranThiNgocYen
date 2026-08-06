<?php
require_once __DIR__ . '/../../../dao/BrandDAO.php';
require_once __DIR__ . '/../../../models/Brand.php';

// 1. Tiêu đề trang
$pageTitle = "Thêm Thương Hiệu Mới";

$errors = [];
$brandName = "";
$slug = "";
$description = "";
$status = 1;

// 2. Xử lý submit Form (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $brandName = trim($_POST["brandName"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // Kiểm tra dữ liệu đầu vào (Validation)
    if (empty($brandName)) {
        $errors[] = "Tên thương hiệu không được để trống.";
    }

    if (empty($slug)) {
        $errors[] = "Slug không được để trống.";
    }

    // Xử lý Upload Logo
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            
            // Thư mục lưu ảnh nằm ở thư mục uploads/brands/ ở gốc dự án
            $uploadDir = __DIR__ . '/../../../uploads/brands/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
                $imageName = $newFileName;
            } else {
                $errors[] = "Không thể tải tệp ảnh lên máy chủ.";
            }
        } else {
            $errors[] = "Định dạng ảnh không hợp lệ (Cho phép: JPG, JPEG, PNG, WEBP, GIF).";
        }
    }

    // Lưu vào CSDL nếu không có lỗi
    if (empty($errors)) {
        try {
            // Khởi tạo đối tượng Brand theo Constructor chuẩn của bạn
            $brand = new Brand(
                null,
                $brandName,
                $slug,
                $imageName,
                $description,
                $status
            );

            $brandDAO = new BrandDAO();
            if ($brandDAO->insert($brand)) {
                // Chuyển hướng về trang quản lý thông qua cấu trúc index.php?view=brands ở admin
                header("Location: /MINISHOP_TRANTHINGOCYEN/views/admin/index.php?view=brands");
                exit();
            } else {
                $errors[] = "Thêm mới thương hiệu thất bại. Vui lòng thử lại!";
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// 3. Bắt đầu đệm dữ liệu giao diện
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Thêm Thương Hiệu Mới</h3>
    <a href="/MINISHOP_TRANTHINGOCYEN/views/admin/index.php?view=brands" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<!-- Hiển thị thông báo lỗi nếu có -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Vui lòng sửa các lỗi sau:</strong>
        <ul class="mb-0 mt-2 ps-3">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Tên thương hiệu -->
            <div class="mb-3">
                <label for="brandName" class="form-label fw-bold">Tên thương hiệu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="brandName" name="brandName" value="<?= htmlspecialchars($brandName) ?>" placeholder="Nhập tên thương hiệu (VD: Asus, Corsair...)" required>
            </div>

            <!-- Slug -->
            <div class="mb-3">
                <label for="slug" class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" placeholder="nhap-slug-thuong-hieu" required>
            </div>

            <!-- Logo -->
            <div class="mb-3">
                <label for="image" class="form-label fw-bold">Logo thương hiệu</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <!-- Mô tả -->
            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả về thương hiệu..."><?= htmlspecialchars($description) ?></textarea>
            </div>

            <!-- Trạng thái -->
            <div class="mb-4">
                <label class="form-label fw-bold d-block">Trạng thái</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status_1" value="1" <?= $status == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_1">
                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Kích hoạt</span>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="status_0" value="0" <?= $status == 0 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_0">
                        <span class="badge bg-secondary"><i class="fa-solid fa-lock me-1"></i>Khóa</span>
                    </label>
                </div>
            </div>

            <hr class="my-4">

            <!-- Các nút thao tác -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Lưu lại
                </button>
                <button type="reset" class="btn btn-light border">
                    <i class="fa-solid fa-rotate-left me-1"></i> Làm mới
                </button>
                <a href="/MINISHOP_TRANTHINGOCYEN/views/admin/index.php?view=brands" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark me-1"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>

<?php
// 4. Gom toàn bộ nội dung vào biến $content và xả đệm
$content = ob_get_clean();

// 5. Nhúng Layout dùng chung (từ thư mục brands lùi ra 2 cấp để vào views/admin/layouts)
include __DIR__ . '/../layouts/master.php';
?>