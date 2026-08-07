<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../models/Category.php";

$categoryDAO = new CategoryDAO();

$cateName = "";
$slug = "";
$description = "";
$status = 1;
$errors = [];
$successMessage = "";

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

    // Nếu Validation thành công, tiếp tục xử lý và lưu dữ liệu vào cơ sở dữ liệu
    if (empty($errors)) {
        try {
            $category = new Category();
            $category->cateName = $cateName;
            $category->slug = $slug;
            $category->description = $description;
            $category->status = $status;
            $category->image = null; // Hoặc xử lý upload ảnh nếu có

            // Gọi phương thức insert() trong CategoryDAO để lưu dữ liệu
            $result = $categoryDAO->insert($category);

            if ($result) {
                // Sau khi thêm thành công, chuyển về trang index.php
                header("Location: index.php");
                exit();
            } else {
                // Nếu thêm thất bại, hiển thị thông báo lỗi phù hợp
                $errors[] = "Thêm danh mục thất bại. Vui lòng thử lại.";
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// Khai báo biến $pageTitle
$pageTitle = "Thêm mới danh mục";

// Sử dụng ob_start() và ob_get_clean() để truyền nội dung vào master.php
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh mục sản phẩm</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-plus-circle me-1"></i>
                Thêm mới danh mục
            </div>
            <!-- Quay lại: Chuyển về trang danh sách danh mục (index.php) -->
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

            <!-- Form thêm mới danh mục sử dụng phương thức POST -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="cateName" class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="cateName" name="cateName" value="<?= htmlspecialchars($cateName) ?>" placeholder="Nhập tên danh mục...">
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" placeholder="nhap-ten-danh-muc">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả ngắn cho danh mục..."><?= htmlspecialchars($description) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status1" value="1" <?= $status == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="status1">Hiển thị / Hoạt động</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status0" value="0" <?= $status == 0 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="status0">Ẩn / Ngừng hoạt động</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu
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