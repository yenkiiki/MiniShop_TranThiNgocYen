<?php
// Bật hiển thị lỗi để dễ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/ProductDAO.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../../dao/BrandDAO.php";
require_once __DIR__ . "/../../../models/Product.php";

$productDAO = new ProductDAO();
$categoryDAO = new CategoryDAO();
$brandDAO = new BrandDAO();

// Lấy danh sách danh mục và thương hiệu để hiển thị ra dropdown <select>
$categories = $categoryDAO->getAll();
$brands = $brandDAO->getAll();

$errors = [];
$successMessage = "";

// XỬ LÝ KHI SUBMIT FORM THÊM MỚI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu từ form
    $productName = trim($_POST["productName"] ?? "");
    $slug = trim($_POST["slug"] ?? "");
    $categoryId = isset($_POST["categoryId"]) ? (int)$_POST["categoryId"] : 0;
    $brandId = isset($_POST["brandId"]) ? (int)$_POST["brandId"] : 0;
    $price = isset($_POST["price"]) ? (float)$_POST["price"] : 0;
    $discountPrice = isset($_POST["discountPrice"]) ? (float)$_POST["discountPrice"] : 0;
    $quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 0;
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

    // Validation dữ liệu đầu vào
    if ($productName === "") {
        $errors[] = "Tên sản phẩm không được để trống.";
    }
    if ($categoryId === 0) {
        $errors[] = "Vui lòng chọn danh mục.";
    }
    if ($brandId === 0) {
        $errors[] = "Vui lòng chọn thương hiệu.";
    }
    if ($price <= 0) {
        $errors[] = "Giá bán phải lớn hơn 0.";
    }
    if ($quantity < 0) {
        $errors[] = "Số lượng không hợp lệ (phải lớn hơn hoặc bằng 0).";
    }

    // Xử lý upload hình ảnh sản phẩm
    $imageName = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Chỉ cho phép các định dạng ảnh phổ biến
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Đặt tên file độc lập bằng uniqid để tránh trùng lặp
            $imageName = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../../../uploads/products/';
            
            // Tạo thư mục nếu chưa tồn tại
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $imageName;
            if(!move_uploaded_file($fileTmpPath, $dest_path)) {
                $errors[] = "Đã xảy ra lỗi khi tải file ảnh lên server.";
            }
        } else {
            $errors[] = "Định dạng file ảnh không hợp lệ (chỉ chấp nhận: jpg, jpeg, png, webp, gif).";
        }
    }

    // Nếu không có lỗi, tiến hành lưu vào cơ sở dữ liệu
    if (empty($errors)) {
        try {
            $product = new Product();
            $product->categoryId = $categoryId; // Chỉ lưu categoryId
            $product->brandId = $brandId;       // Chỉ lưu brandId
            $product->proName = $productName;
            $product->slug = !empty($slug) ? $slug : strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productName)));
            $product->price = $price;
            $product->discountPrice = $discountPrice;
            $product->quantity = $quantity;
            $product->image = $imageName;
            $product->description = $description;
            $product->status = $status;

            $result = $productDAO->insert($product);
            if ($result) {
                header("Location: index.php?msg=create_success");
                exit();
            } else {
                $errors[] = "Thêm sản phẩm thất bại. Vui lòng thử lại!";
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// Tiêu đề trang
$pageTitle = "Thêm mới sản phẩm - Mini Shop";

// Bắt đầu buffer nội dung
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Thêm mới sản phẩm</li>
    </ol>

    <!-- Hiển thị danh sách lỗi nếu có -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle"></i> Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form thêm mới sản phẩm -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Form thêm mới sản phẩm
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="productName" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="productName" name="productName" value="<?= htmlspecialchars($_POST['productName'] ?? '') ?>" placeholder="Nhập tên sản phẩm...">
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-bold">Slug (Đường dẫn thân thiện)</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống...">
                    </div>
                </div>

              <div class="row mb-3">
    <div class="col-md-6">
        <label for="categoryId" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
        <select name="categoryId" id="categoryId" class="form-select">
            <option value="0">-- Chọn danh mục sản phẩm --</option>
            <?php foreach ($categories as $item): ?>
                <option value="<?= $item->id ?>" <?= (isset($_POST['categoryId']) && $_POST['categoryId'] == $item->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item->cateName) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="brandId" class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
        <select name="brandId" id="brandId" class="form-select">
            <option value="0">-- Chọn thương hiệu sản phẩm --</option>
            <?php foreach ($brands as $item): ?>
                <option value="<?= $item->id ?>" <?= (isset($_POST['brandId']) && $_POST['brandId'] == $item->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item->brandName) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" step="1000" class="form-control" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="discountPrice" class="form-label fw-bold">Giá khuyến mãi (VNĐ)</label>
                        <input type="number" step="1000" class="form-control" id="discountPrice" name="discountPrice" value="<?= htmlspecialchars($_POST['discountPrice'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-bold">Số lượng trong kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($_POST['quantity'] ?? '10') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="image" class="form-label fw-bold">Hình ảnh sản phẩm</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <div class="form-text">Hỗ trợ các định dạng: JPG, JPEG, PNG, WEBP, GIF.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label fw-bold">Trạng thái hiển thị</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="Nhập mô tả sản phẩm..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>