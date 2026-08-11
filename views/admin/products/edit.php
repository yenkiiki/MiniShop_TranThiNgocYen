<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
require_once __DIR__ . "/../../../models/Product.php";
require_once __DIR__ . "/../../../models/ProductImage.php";

$productDAO = new ProductDAO();
$categoryDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

$errors = [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$productOld = $productDAO->findById($id);
if (!$productOld) {
    header("Location: index.php?msg=not_found");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete_sub') {
    $imageId = isset($_GET['image_id']) ? (int)$_GET['image_id'] : 0;
    
    if ($imageId > 0) {
        $productImages = $productDAO->getImagesByProductId($id);
        foreach ($productImages as $img) {
            if ($img->id === $imageId) {
                $filePath = __DIR__ . "/../../../uploads/products/" . $img->image;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                break;
            }
        }
        $productDAO->deleteImage($imageId);
    }
    
    header("Location: edit.php?id=" . $id);
    exit();
}

$categories = $categoryDAO->getAll();
$brands = $brandDAO->getAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryId = (int)($_POST["categoryId"] ?? 0);
    $brandId = (int)($_POST["brandId"] ?? 0);
    $proName = trim($_POST["proName"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    
    if (empty($slug) && !empty($proName)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $proName)));
    } else {
        if (empty($slug) && property_exists($productOld, 'slug')) {
            $slug = $productOld->slug;
        }
    }

    $price = (float)($_POST["price"] ?? 0);
    $discountPrice = (float)($_POST["discountPrice"] ?? 0);
    $quantity = (int)($_POST["quantity"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;
    
    $image = $productOld->image;

    if ($proName === "") $errors[] = "Tên sản phẩm không được để trống.";
    if ($categoryId === 0) $errors[] = "Vui lòng chọn danh mục.";
    if ($brandId === 0) $errors[] = "Vui lòng chọn thương hiệu.";
    if ($price < 0) $errors[] = "Giá bán không hợp lệ.";
    if ($quantity < 0) $errors[] = "Số lượng không hợp lệ.";

    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $errorUpload = $_FILES["image"]["error"] ?? UPLOAD_ERR_OK;

    $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
    $maxSize = 2 * 1024 * 1024;

    if ($fileName != "") {
        if ($errorUpload != UPLOAD_ERR_OK) $errors[] = "Upload hình ảnh chính không thành công.";
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowExtensions)) $errors[] = "Định dạng ảnh chính không hợp lệ.";
        if ($fileSize > $maxSize) $errors[] = "Kích thước ảnh chính vượt quá 2MB.";
    }

    if (empty($errors)) {
        if ($fileName != "") {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $image = time() . "_" . $slug . "." . $extension;
            $uploadPath = __DIR__ . "/../../../uploads/products/" . $image;
            
            $uploadDir = dirname($uploadPath);
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            if (!empty($productOld->image)) {
                $oldImagePath = __DIR__ . "/../../../uploads/products/" . $productOld->image;
                if (file_exists($oldImagePath)) @unlink($oldImagePath);
            }
            move_uploaded_file($tmpName, $uploadPath);
        }

        try {
            $productOld->categoryId = $categoryId;
            $productOld->brandId = $brandId;
            $productOld->proName = $proName;
            $productOld->slug = $slug;
            $productOld->price = $price;
            $productOld->discountPrice = $discountPrice;
            $productOld->quantity = $quantity;
            $productOld->image = $image;
            $productOld->description = $description;
            $productOld->status = $status;

            $productDAO->update($productOld);

            if (isset($_FILES['sub_images']) && !empty($_FILES['sub_images']['name'][0])) {
                foreach ($_FILES['sub_images']['name'] as $key => $subName) {
                    if ($_FILES['sub_images']['error'][$key] == UPLOAD_ERR_OK) {
                        $subTmp = $_FILES['sub_images']['tmp_name'][$key];
                        $subExt = strtolower(pathinfo($subName, PATHINFO_EXTENSION));
                        $newSubImageName = time() . "_" . uniqid() . "_" . $slug . "." . $subExt;
                        $subUploadPath = __DIR__ . "/../../../uploads/products/" . $newSubImageName;

                        if (move_uploaded_file($subTmp, $subUploadPath)) {
                            $productImageModel = new ProductImage();
                            $productImageModel->productId = $id;
                            $productImageModel->image = $newSubImageName;
                            $productImageModel->sortOrder = 0;
                            $productDAO->insertImage($productImageModel);
                        }
                    }
                }
            }

            header("Location: index.php?msg=update_success");
            exit();

        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

$pageTitle = "Cập nhật sản phẩm - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Cập nhật sản phẩm</li>
    </ol>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Form cập nhật sản phẩm
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="proName" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="proName" name="proName" value="<?= htmlspecialchars($_POST['proName'] ?? $productOld->proName) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-bold">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($_POST['slug'] ?? $productOld->slug) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="categoryId" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="categoryId" id="categoryId" class="form-select" required>
                            <option value="0">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $item): ?>
                                <option value="<?= $item->id ?>" <?= $productOld->categoryId == $item->id ? 'selected' : '' ?>><?= htmlspecialchars($item->cateName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brandId" class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                        <select name="brandId" id="brandId" class="form-select" required>
                            <option value="0">-- Chọn thương hiệu --</option>
                            <?php foreach ($brands as $item): ?>
                                <option value="<?= $item->id ?>" <?= $productOld->brandId == $item->id ? 'selected' : '' ?>><?= htmlspecialchars($item->brandName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-bold">Giá bán <span class="text-danger">*</span></label>
                        <input type="number" step="1000" class="form-control" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? $productOld->price) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="discountPrice" class="form-label fw-bold">Giá khuyến mãi</label>
                        <input type="number" step="1000" class="form-control" id="discountPrice" name="discountPrice" value="<?= htmlspecialchars($_POST['discountPrice'] ?? ($productOld->discountPrice ?? 0)) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-bold">Số lượng kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($_POST['quantity'] ?? $productOld->quantity) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 border-end">
                        <label class="form-label fw-bold text-primary">Hình ảnh chính</label>
                        <div class="text-center mb-3">
                            <?php if (!empty($productOld->image)): ?>
                                <img src="../../../uploads/products/<?= htmlspecialchars($productOld->image) ?>" class="img-thumbnail rounded shadow-sm" width="150" alt="">
                            <?php else: ?>
                                <span class="text-muted">Chưa có ảnh</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Đổi ảnh chính mới</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Hình ảnh phụ (Gallery)</label>
                        <?php 
                        $currentImages = $productDAO->getImagesByProductId($id);
                        if (!empty($currentImages)): 
                        ?>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <?php foreach ($currentImages as $subImg): ?>
                                    <div class="border p-2 rounded text-center bg-light" style="width: 100px;">
                                        <img src="../../../uploads/products/<?= htmlspecialchars($subImg->image) ?>" width="80" height="70" class="object-fit-cover rounded mb-2" alt="">
                                        <a href="edit.php?id=<?= $id ?>&action=delete_sub&image_id=<?= $subImg->id ?>" 
                                           class="btn btn-danger btn-sm w-100" 
                                           style="font-size: 11px;"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa hình ảnh phụ này không?');">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted fst-italic mb-3">Sản phẩm chưa có ảnh phụ nào.</div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="sub_images" class="form-label">Thêm ảnh phụ mới</label>
                            <input type="file" id="sub_images" name="sub_images[]" class="form-control" accept="image/*" multiple>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status" class="form-label fw-bold">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" <?= $productOld->status == 1 ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= $productOld->status == 0 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? $productOld->description) ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>