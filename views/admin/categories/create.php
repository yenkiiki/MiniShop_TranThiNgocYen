<?php
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../models/Category.php";
require_once __DIR__ . "/../../../middleware/CsrfMiddleware.php";

session_status() === PHP_SESSION_NONE && session_start();
CsrfMiddleware::generateToken();

$categoryDAO = new CategoryDAO();
$cateName = $slug = $description = "";
$status = 1;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    CsrfMiddleware::verify();
    $cateName = trim($_POST["cateName"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = (int)($_POST["status"] ?? 1);

    if (empty($cateName)) $errors[] = "Tên danh mục không được để trống.";
    if (empty($slug)) $errors[] = "Slug không được để trống.";

    $imageName = null;
    if (empty($errors) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageName = md5(time() . $_FILES['image']['name']) . '.' . $ext;
            $dir = __DIR__ . "/../../../uploads/categories/";
            !is_dir($dir) && mkdir($dir, 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $dir . $imageName);
        } else {
            $errors[] = "Định dạng ảnh không hợp lệ.";
        }
    }

    if (empty($errors)) {
        $category = new Category();
        $category->cateName = $cateName;
        $category->slug = $slug;
        $category->description = $description;
        $category->status = $status;
        $category->image = $imageName;

        if ($categoryDAO->insert($category)) {
            header("Location: index.php");
            exit();
        }
        $errors[] = "Thêm thất bại.";
    }
}

$pageTitle = "Thêm mới danh mục";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <div class="card mb-4">
        <div class="card-header">Thêm mới danh mục</div>
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION["csrf_token"] ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên danh mục</label>
                    <input type="text" name="cateName" class="form-control" value="<?= htmlspecialchars($cateName) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($slug) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Hình ảnh</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include "../layouts/master.php"; 
?>