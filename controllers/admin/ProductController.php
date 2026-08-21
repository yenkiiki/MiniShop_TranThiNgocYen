<?php
namespace Controllers\Admin;

use DAO\ProductDAO;
use DAO\CategoryDAO;
use DAO\BrandDAO;
use DAO\ProductVariantDAO;
use Models\Product;
use Models\ProductImage;
use Models\ProductVariant;
use Exception;

class ProductController
{
    private $productDAO;
    private $categoryDAO;
    private $brandDAO;
    private $variantDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
        $this->brandDAO = new BrandDAO();
        $this->variantDAO = new ProductVariantDAO();
    }

    // 1. Hiển thị danh sách sản phẩm
    public function index()
    {
        $message = "";
        $error = "";

        // Xử lý xóa sản phẩm khi có yêu cầu POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

            if ($id > 0) {
                try {
                    $result = $this->productDAO->delete($id);
                    if ($result) {
                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=delete_success");
                        exit();
                    } else {
                        $error = "Xóa sản phẩm thất bại. Vui lòng thử lại!";
                    }
                } catch (Exception $e) {
                    $error = "Không thể xóa sản phẩm này do ràng buộc dữ liệu!";
                }
            }
        }

        if (isset($_GET['msg']) && $_GET['msg'] === 'delete_success') {
            $message = "Xóa sản phẩm thành công!";
        }

        // Đọc các tham số tìm kiếm, giới hạn, trang từ URL
        $keyword = trim($_GET["keyword"] ?? "");
        $limit = (int) ($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30])) {
            $limit = 10;
        }

        $page = (int) ($_GET["page"] ?? 1);
        if ($page < 1)
            $page = 1;
        $offset = ($page - 1) * $limit;

        // Tính toán tổng số lượng bản ghi và phân trang
        $totalRecords = $this->productDAO->count("products", "productname", $keyword);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $products = $this->productDAO->getPage($limit, $offset, $keyword);
        $pageTitle = "Quản lý sản phẩm - Mini Shop";

        require_once __DIR__ . '/../../views/admin/products/index.php';
    }

    // 2. Xem chi tiết sản phẩm
public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index");
            exit();
        }

        $product = $this->productDAO->findById($id);
        if (!$product) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=not_found");
            exit();
        }

        // Lấy tên danh mục
        $cateName = 'Chưa phân loại';
        if (!empty($product->categoryId) && isset($this->categoryDAO)) {
            $category = $this->categoryDAO->findById($product->categoryId);
            if ($category) {
                $cateName = $category->catename ?? $category->categoryName ?? 'Chưa phân loại';
            }
        }

        // Lấy tên thương hiệu
        $brandName = 'Không có thương hiệu';
        if (!empty($product->brandId) && isset($this->brandDAO)) {
            $brand = $this->brandDAO->findById($product->brandId);
            if ($brand) {
                $brandName = $brand->brandname ?? $brand->brandName ?? 'Không có thương hiệu';
            }
        }

        $productImages = $this->productDAO->getImagesByProductId($id);
        $variants = $this->variantDAO->getByProductId($id);
        $pageTitle = "Chi tiết sản phẩm - Mini Shop";

        // CHỈ gọi file view. Trong file view sẽ tự nhúng master.php
        require_once __DIR__ . '/../../views/admin/products/detail.php';
    }
    public function create()
    {
        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();

        $errors = [];
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $categoryId = (int) ($_POST["categoryId"] ?? 0);
            $brandId = (int) ($_POST["brandId"] ?? 0);
            $productName = trim($_POST["productName"] ?? "");
            $slug = trim($_POST["slug"] ?? "");

            if (empty($slug) && !empty($productName)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productName)));
            }

            $price = (float) ($_POST["price"] ?? 0);
            $discountPrice = (float) ($_POST["discountPrice"] ?? 0);
            $quantity = (int) ($_POST["quantity"] ?? 0);
            $description = trim($_POST["description"] ?? "");
            $status = isset($_POST["status"]) ? (int) $_POST["status"] : 1;
            $image = "";

            if ($productName === "")
                $errors[] = "Tên sản phẩm không được để trống.";
            if ($categoryId === 0)
                $errors[] = "Vui lòng chọn danh mục.";
            if ($brandId === 0)
                $errors[] = "Vui lòng chọn thương hiệu.";
            if ($price <= 0)
                $errors[] = "Giá bán phải lớn hơn 0.";
            if ($quantity < 0)
                $errors[] = "Số lượng không hợp lệ (phải lớn hơn hoặc bằng 0).";

            $fileName = $_FILES["image"]["name"] ?? "";
            $tmpName = $_FILES["image"]["tmp_name"] ?? "";
            $fileSize = $_FILES["image"]["size"] ?? 0;
            $error = $_FILES["image"]["error"] ?? UPLOAD_ERR_OK;

            if ($fileName != "") {
                if ($error != UPLOAD_ERR_OK)
                    $errors[] = "Upload hình ảnh chính không thành công.";
                $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowExtensions))
                    $errors[] = "Chỉ cho phép file ảnh chính là JPG, JPEG, PNG hoặc WEBP.";
                if ($fileSize > 2 * 1024 * 1024)
                    $errors[] = "Kích thước hình ảnh chính phải <= 2 MB.";
            }

            if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
                foreach ($_FILES["images"]["name"] as $key => $subName) {
                    if ($_FILES["images"]["error"][$key] === UPLOAD_ERR_OK) {
                        $subExtension = strtolower(pathinfo($subName, PATHINFO_EXTENSION));
                        $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
                        if (!in_array($subExtension, $allowExtensions))
                            $errors[] = "Hình ảnh phụ '$subName' không đúng định dạng cho phép.";
                        if ($_FILES["images"]["size"][$key] > 2 * 1024 * 1024)
                            $errors[] = "Hình ảnh phụ '$subName' vượt quá kích thước 2 MB.";
                    }
                }
            }

            if (empty($errors)) {
                if ($fileName != "") {
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $image = time() . "_" . $slug . "." . $extension;
                    $uploadPath = __DIR__ . "/../../uploads/products/" . $image;
                    $uploadDir = dirname($uploadPath);
                    if (!is_dir($uploadDir))
                        mkdir($uploadDir, 0755, true);
                    move_uploaded_file($tmpName, $uploadPath);
                }

                try {
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

                    $productId = $this->productDAO->insert($product);

                    if ($productId) {
                        $galleryImages = [];
                        if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
                            $uploadDirGallery = __DIR__ . "/../../uploads/products/";
                            if (!is_dir($uploadDirGallery))
                                mkdir($uploadDirGallery, 0755, true);

                            foreach ($_FILES["images"]["name"] as $key => $subName) {
                                if ($_FILES["images"]["error"][$key] === UPLOAD_ERR_OK) {
                                    $subTmpName = $_FILES["images"]["tmp_name"][$key];
                                    $subExtension = strtolower(pathinfo($subName, PATHINFO_EXTENSION));
                                    $subImageName = time() . "_sub_" . uniqid() . "." . $subExtension;
                                    $subDestination = $uploadDirGallery . $subImageName;

                                    if (move_uploaded_file($subTmpName, $subDestination)) {
                                        $productImage = new ProductImage();
                                        $productImage->productId = $productId;
                                        $productImage->image = $subImageName;
                                        $productImage->sortOrder = $key;
                                        $this->productDAO->insertImage($productImage);
                                        $galleryImages[] = $subImageName;
                                    }
                                }
                            }
                        }

                        // Đồng bộ biến thể sản phẩm (Variant 1 -> Main Image, Variant 2 -> Sub Image 1, Variant 3 -> Sub Image 2,...)
                        $variantsInput = $_POST['variants'] ?? [];
                        if (!empty($variantsInput)) {
                            $this->variantDAO->syncVariants($productId, $variantsInput, $image, $galleryImages);
                        }

                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=add_success");
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

        $pageTitle = "Thêm mới sản phẩm - Mini Shop";
        ob_start();
        require_once __DIR__ . '/../../views/admin/products/create.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../../views/admin/layouts/master.php';
    }

    public function delete()
    {
        // Chỉ cho phép truy cập qua POST để bảo mật
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index");
            exit();
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id > 0) {
            try {
                // Thực hiện xóa
                $result = $this->productDAO->delete($id);
                if ($result) {
                    header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=delete_success");
                    exit();
                } else {
                    header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=delete_fail");
                    exit();
                }
            } catch (Exception $e) {
                header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=delete_error");
                exit();
            }
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index");
            exit();
        }

        $productOld = $this->productDAO->findById($id);
        if (!$productOld) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=not_found");
            exit();
        }

        // Xử lý xóa ảnh phụ (Gallery) trực tiếp trong Controller
        if (isset($_GET['action']) && $_GET['action'] == 'delete_sub') {
            $imageId = isset($_GET['image_id']) ? (int) $_GET['image_id'] : 0;

            if ($imageId > 0) {
                $productImages = $this->productDAO->getImagesByProductId($id);
                foreach ($productImages as $img) {
                    if ($img->id === $imageId) {
                        $filePath = __DIR__ . "/../../uploads/products/" . $img->image;
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                        break;
                    }
                }
                $this->productDAO->deleteImage($imageId);
            }

            // Xóa xong chuyển hướng lại về trang edit hiện tại
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/edit?id=" . $id);
            exit();
        }

        $categories = $this->categoryDAO->getAll();
        $brands = $this->brandDAO->getAll();
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $categoryId = (int) ($_POST["categoryId"] ?? 0);
            $brandId = (int) ($_POST["brandId"] ?? 0);
            $proName = trim($_POST["proName"] ?? "");
            $slug = trim($_POST["slug"] ?? "");

            if (empty($slug) && !empty($proName)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $proName)));
            } else {
                if (empty($slug) && property_exists($productOld, 'slug')) {
                    $slug = $productOld->slug;
                }
            }

            $price = (float) ($_POST["price"] ?? 0);
            $discountPrice = (float) ($_POST["discountPrice"] ?? 0);
            $quantity = (int) ($_POST["quantity"] ?? 0);
            $description = trim($_POST["description"] ?? "");
            $status = isset($_POST["status"]) ? (int) $_POST["status"] : 1;

            $image = $productOld->image;

            if ($proName === "")
                $errors[] = "Tên sản phẩm không được để trống.";
            if ($categoryId === 0)
                $errors[] = "Vui lòng chọn danh mục.";
            if ($brandId === 0)
                $errors[] = "Vui lòng chọn thương hiệu.";
            if ($price < 0)
                $errors[] = "Giá bán không hợp lệ.";
            if ($quantity < 0)
                $errors[] = "Số lượng không hợp lệ.";

            $fileName = $_FILES["image"]["name"] ?? "";
            $tmpName = $_FILES["image"]["tmp_name"] ?? "";
            $fileSize = $_FILES["image"]["size"] ?? 0;
            $errorUpload = $_FILES["image"]["error"] ?? UPLOAD_ERR_OK;

            $allowExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            $maxSize = 2 * 1024 * 1024;

            if ($fileName != "") {
                if ($errorUpload != UPLOAD_ERR_OK)
                    $errors[] = "Upload hình ảnh chính không thành công.";
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowExtensions))
                    $errors[] = "Định dạng ảnh chính không hợp lệ.";
                if ($fileSize > $maxSize)
                    $errors[] = "Kích thước ảnh chính vượt quá 2MB.";
            }

            if (empty($errors)) {
                if ($fileName != "") {
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $image = time() . "_" . $slug . "." . $extension;
                    $uploadPath = __DIR__ . "/../../uploads/products/" . $image;

                    $uploadDir = dirname($uploadPath);
                    if (!is_dir($uploadDir))
                        mkdir($uploadDir, 0755, true);

                    if (!empty($productOld->image)) {
                        $oldImagePath = __DIR__ . "/../../uploads/products/" . $productOld->image;
                        if (file_exists($oldImagePath))
                            @unlink($oldImagePath);
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

                    $this->productDAO->update($productOld);

                    // Xử lý thêm các ảnh phụ (Gallery) mới nếu có
                    if (isset($_FILES['sub_images']) && !empty($_FILES['sub_images']['name'][0])) {
                        foreach ($_FILES['sub_images']['name'] as $key => $subName) {
                            if ($_FILES['sub_images']['error'][$key] == UPLOAD_ERR_OK) {
                                $subTmp = $_FILES['sub_images']['tmp_name'][$key];
                                $subExt = strtolower(pathinfo($subName, PATHINFO_EXTENSION));
                                $newSubImageName = time() . "_" . uniqid() . "_" . $slug . "." . $subExt;
                                $subUploadPath = __DIR__ . "/../../uploads/products/" . $newSubImageName;

                                if (move_uploaded_file($subTmp, $subUploadPath)) {
                                    $productImageModel = new ProductImage();
                                    $productImageModel->productId = $id;
                                    $productImageModel->image = $newSubImageName;
                                    $productImageModel->sortOrder = 0;
                                    $this->productDAO->insertImage($productImageModel);
                                }
                            }
                        }
                    }

                    // Lấy toàn bộ ảnh phụ sau khi cập nhật
                    $currentSubImages = $this->productDAO->getImagesByProductId($id);
                    $allGallery = [];
                    foreach ($currentSubImages as $cImg) {
                        $allGallery[] = $cImg->image;
                    }

                    // Đồng bộ danh sách biến thể (Variant 1 -> Main Image, Variant 2 -> Sub Image 1, Variant 3 -> Sub Image 2,...)
                    $variantsInput = $_POST['variants'] ?? [];
                    if (isset($_POST['variants'])) {
                        $this->variantDAO->syncVariants($id, $variantsInput, $image, $allGallery);
                    }

                    header("Location: /MINISHOP_TRANTHINGOCYEN/admin/product/index?msg=update_success");
                    exit();

                } catch (Exception $e) {
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        $variants = $this->variantDAO->getByProductId($id);
        $pageTitle = "Cập nhật sản phẩm - Mini Shop";
        require_once __DIR__ . '/../../views/admin/products/edit.php';
    }
}