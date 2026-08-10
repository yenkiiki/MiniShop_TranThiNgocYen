<?php
// Bật hiển thị lỗi để debug
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

$error = "";
$message = "";

// 1. Nhận productId từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// 2. Gọi phương thức getById() (hoặc findById tùy DAO của Product) để lấy thông tin sản phẩm
// Giả định phương thức trong ProductDAO là findById hoặc getById
$product = $productDAO->findById($id);
if (!$product) {
    header("Location: index.php?msg=not_found");
    exit();
}

// 3. Gọi phương thức getAll() trong CategoryDAO và BrandDAO để lấy danh sách danh mục và thương hiệu
$categories = $categoryDAO->getAll();
$brands = $brandDAO->getAll();

// 4. Xử lý khi form được submit (Phương thức POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu từ Form
    $proName = trim($_POST['proName'] ?? '');
    $categoryId = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : 0;
    $brandId = isset($_POST['brandId']) ? (int)$_POST['brandId'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    // Xử lý hình ảnh (giữ ảnh cũ nếu không chọn ảnh mới)
    $image = $product->image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../../../uploads/products/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Xóa ảnh cũ nếu có
                if (!empty($product->image) && file_exists($uploadFileDir . $product->image)) {
                    @unlink($uploadFileDir . $product->image);
                }
                $image = $newFileName;
            }
        }
    }

    // 5. Kiểm tra dữ liệu đầu vào (Validation)
    if (empty($proName)) {
        $error = "Tên sản phẩm không được để trống!";
    } elseif ($categoryId <= 0) {
        $error = "Vui lòng chọn danh mục sản phẩm!";
    } elseif ($brandId <= 0) {
        $error = "Vui lòng chọn thương hiệu sản phẩm!";
    } elseif ($price < 0) {
        $error = "Giá sản phẩm không hợp lệ!";
    } else {
        try {
            // Cập nhật lại giá trị cho đối tượng sản phẩm
            $product->proName = $proName;
            $product->categoryId = $categoryId;
            $product->brandId = $brandId;
            $product->price = $price;
            $product->quantity = $quantity;
            $product->description = $description;
            $product->status = $status;
            $product->image = $image;

            // 6. Gọi phương thức update() trong ProductDAO
            $result = $productDAO->update($product);

            if ($result) {
                // Nếu cập nhật thành công, chuyển về index.php
                header("Location: index.php?msg=update_success");
                exit();
            } else {
                $error = "Cập nhật sản phẩm thất bại. Vui lòng thử lại!";
            }
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// Đặt tiêu đề trang
$pageTitle = "Cập nhật sản phẩm - Mini Shop";

// Bắt đầu buffer nội dung để truyền vào layout chung
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Cập nhật sản phẩm</li>
    </ol>

    <!-- Hiển thị thông báo lỗi nếu có -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Form cập nhật sản phẩm
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                
                <!-- Tên sản phẩm -->
                <div class="mb-3">
                    <label for="proName" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="proName" name="proName" value="<?= htmlspecialchars($_POST['proName'] ?? $product->proName) ?>" required>
                </div>

                <div class="row">
                    <!-- Danh mục -->
                    <div class="col-md-6 mb-3">
                        <label for="categoryId" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="categoryId" id="categoryId" class="form-select" required>
                            <option value="0">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $item): ?>
                                <option value="<?= $item->id ?>" 
                                    <?= (isset($_POST['categoryId']) ? $_POST['categoryId'] == $item->id : $product->categoryId == $item->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item->cateName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Thương hiệu -->
                    <div class="col-md-6 mb-3">
                        <label for="brandId" class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                        <select name="brandId" id="brandId" class="form-select" required>
                            <option value="0">-- Chọn thương hiệu --</option>
                            <?php foreach ($brands as $item): ?>
                                <option value="<?= $item->id ?>" 
                                    <?= (isset($_POST['brandId']) ? $_POST['brandId'] == $item->id : $product->brandId == $item->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item->brandName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Giá -->
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label fw-bold">Giá sản phẩm (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? $product->price) ?>" required>
                    </div>

                    <!-- Kho / Số lượng -->
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label fw-bold">Số lượng kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($_POST['quantity'] ?? $product->quantity) ?>" required>
                    </div>
                </div>

                <!-- Hình ảnh -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Hình ảnh sản phẩm</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <div class="mt-2">
                        <?php if (!empty($product->image)): ?>
                            <img src="../../../uploads/products/<?= htmlspecialchars($product->image) ?>" alt="" width="80" height="80" style="object-fit: cover;" class="rounded border">
                            <span class="text-muted ms-2 small">Ảnh hiện tại</span>
                        <?php else: ?>
                            <span class="text-muted small">Chưa có hình ảnh</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả sản phẩm</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? $product->description) ?></textarea>
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" <?= (isset($_POST['status']) ? $_POST['status'] == 1 : $product->status == 1) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (isset($_POST['status']) ? $_POST['status'] == 0 : $product->status == 0) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>

                <!-- Nút lưu và quay lại -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>

            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>