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
$message = "";

// XỬ LÝ KHI SUBMIT FORM THÊM MỚI
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đọc dữ liệu từ Form
    $categoryId = (int) ($_POST["categoryId"] ?? 0);
    $brandId = (int) ($_POST["brandId"] ?? 0);
    $productName = trim($_POST["productName"] ?? "");
    $slug = trim($_POST["slug"] ?? "");

    // Nếu slug để trống, tự động tạo slug từ tên sản phẩm
    if (empty($slug) && !empty($productName)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productName)));
    }

    $price = (float) ($_POST["price"] ?? 0);
    $discountPrice = (float) ($_POST["discountPrice"] ?? 0);
    $quantity = (int) ($_POST["quantity"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $status = isset($_POST["status"]) ? (int) $_POST["status"] : 1;

    $image = ""; // Ảnh chính gán giá trị tạm thời

    // Validation dữ liệu đầu vào cơ bản
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

    // Đọc thông tin hình ảnh chính
    $fileName = $_FILES["image"]["name"] ?? "";
    $tmpName = $_FILES["image"]["tmp_name"] ?? "";
    $fileSize = $_FILES["image"]["size"] ?? 0;
    $error = $_FILES["image"]["error"] ?? UPLOAD_ERR_OK;

    // Validation hình ảnh chính
    if ($fileName != "") {
        if ($error != UPLOAD_ERR_OK) {
            $errors[] = "Upload hình ảnh chính không thành công.";
        }

        $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowExtensions)) {
            $errors[] = "Chỉ cho phép file ảnh chính là JPG, JPEG, PNG hoặc WEBP.";
        }

        // Tăng giới hạn lên 2 MB
        $maxSize = 2 * 1024 * 1024; 
        if ($fileSize > $maxSize) {
            $errors[] = "Kích thước hình ảnh chính phải <= 2 MB.";
        }
    }

    // Validation cho danh sách hình ảnh phụ (Gallery - nhiều hình)
    if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
        foreach ($_FILES["images"]["name"] as $key => $subName) {
            if ($_FILES["images"]["error"][$key] === UPLOAD_ERR_OK) {
                $subExtension = strtolower(pathinfo($subName, PATHINFO_EXTENSION));
                $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
                if (!in_array($subExtension, $allowExtensions)) {
                    $errors[] = "Hình ảnh phụ '$subName' không đúng định dạng cho phép.";
                }
                // Tăng giới hạn ảnh phụ lên 2 MB
                if ($_FILES["images"]["size"][$key] > 2 * 1024 * 1024) {
                    $errors[] = "Hình ảnh phụ '$subName' vượt quá kích thước 2 MB.";
                }
            }
        }
    }

    // NẾU KHÔNG CÓ LỖI -> TIẾN HÀNH UPLOAD VÀ LƯU VÀO CSDL
    if (empty($errors)) {
        // 1. Xử lý upload ảnh chính
        if ($fileName != "") {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $image = time() . "_" . $slug . "." . $extension;
            $uploadPath = __DIR__ . "/../../../uploads/products/" . $image;

            $uploadDir = dirname($uploadPath);
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            move_uploaded_file($tmpName, $uploadPath);
        }

        try {
            // 2. Tạo đối tượng Product và lưu vào bảng products
            $product = new Product();
            $product->categoryId = $categoryId;
            $product->brandId = $brandId;
            $product->proName = $productName;
            $product->slug = $slug;
            $product->price = $price;
            $product->discountPrice = $discountPrice;
            $product->quantity = $quantity;
            $product->image = $image;
            $product->description = $description;
            $product->status = $status;

            // Lưu sản phẩm và nhận về ID sản phẩm vừa tạo
            $productId = $productDAO->insert($product);

            if ($productId) {
                // 3. Xử lý Upload và lưu danh sách hình ảnh phụ (Gallery)
               // 3. Xử lý Upload và lưu danh sách hình ảnh phụ (Gallery)
if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
    $uploadDirGallery = __DIR__ . "/../../../uploads/products/";
    if (!is_dir($uploadDirGallery)) {
        mkdir($uploadDirGallery, 0755, true);
    }

    foreach ($_FILES["images"]["name"] as $key => $subName) {
        if ($_FILES["images"]["error"][$key] === UPLOAD_ERR_OK) {
            $subTmpName = $_FILES["images"]["tmp_name"][$key];
            $subExtension = strtolower(pathinfo($subName, PATHINFO_EXTENSION));
            
            // Tạo tên riêng biệt cho từng ảnh phụ tránh trùng lặp
            $subImageName = time() . "_sub_" . uniqid() . "." . $subExtension;
            $subDestination = $uploadDirGallery . $subImageName;

            if (move_uploaded_file($subTmpName, $subDestination)) {
                // Tạo đối tượng ProductImage và truyền vào hàm insertImage đúng chuẩn type-hint
                $productImage = new ProductImage();
                $productImage->productId = $productId;
                $productImage->image = $subImageName;
                $productImage->sortOrder = 0;

                $productDAO->insertImage($productImage);
            }
        }
    }
}

                header("Location: index.php?msg=add_success");
                exit();
            } else {
                $message = "Thêm sản phẩm thất bại.";
                $errors[] = "Thêm sản phẩm vào cơ sở dữ liệu thất bại. Vui lòng thử lại!";
            }
        } catch (Exception $e) {
            $errors[] = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// Tiêu đề trang
$pageTitle = "Thêm mới sản phẩm - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Thêm mới sản phẩm</li>
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

    <?php if ($message != ""): ?>
        <div class="alert alert-warning"><?= $message ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i> Form thêm mới sản phẩm
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="productName" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="productName" name="productName"
                            value="<?= htmlspecialchars($_POST['productName'] ?? '') ?>" placeholder="Nhập tên sản phẩm...">
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-bold">Slug (Đường dẫn thân thiện)</label>
                        <input type="text" class="form-control" id="slug" name="slug"
                            value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống...">
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
                        <input type="number" step="1000" class="form-control" id="price" name="price"
                            value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="discountPrice" class="form-label fw-bold">Giá khuyến mãi (VNĐ)</label>
                        <input type="number" step="1000" class="form-control" id="discountPrice" name="discountPrice"
                            value="<?= htmlspecialchars($_POST['discountPrice'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-bold">Số lượng trong kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                            value="<?= htmlspecialchars($_POST['quantity'] ?? '10') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Ảnh chính -->
                    <div class="col-md-6">
                        <div class="text-center mb-3" id="preview">
                            <span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh chính được chọn</span>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Hình ảnh chính sản phẩm</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <div class="form-text">Hỗ trợ JPG, JPEG, PNG, WEBP, GIF (Tối đa 2 MB).</div>
                        </div>
                    </div>

                    <!-- Ảnh phụ (Gallery - Nhiều hình) -->
                    <div class="col-md-6">
                        <div class="text-center mb-3" id="preview-gallery">
                            <span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh phụ nào được chọn</span>
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">Hình ảnh phụ (Gallery)</label>
                            <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                            <div class="form-text">Chọn nhiều hình ảnh (Mỗi ảnh tối đa 2 MB). Giữ Ctrl hoặc Shift để chọn hàng loạt.</div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status" class="form-label fw-bold">Trạng thái hiển thị</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="5"
                        placeholder="Nhập mô tả sản phẩm..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
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

<!-- JavaScript xem trước ảnh -->
<script>
    // Xem trước ảnh chính
    document.getElementById('image').addEventListener('change', function (event) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail rounded';
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh chính được chọn</span>';
        }
    });

    // Xem trước nhiều ảnh phụ (Gallery)
    document.getElementById('images').addEventListener('change', function (event) {
        const previewGallery = document.getElementById('preview-gallery');
        previewGallery.innerHTML = '';
        const files = event.target.files;
        if (files.length > 0) {
            const container = document.createElement('div');
            container.className = 'd-flex flex-wrap gap-2 justify-content-center';
            
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail rounded';
                    img.style.width = '70px';
                    img.style.height = '70px';
                    img.style.objectFit = 'cover';
                    container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
            previewGallery.appendChild(container);
        } else {
            previewGallery.innerHTML = '<span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh phụ nào được chọn</span>';
        }
    });
</script>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>