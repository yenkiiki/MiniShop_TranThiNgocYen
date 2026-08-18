<?php
namespace Controllers\Admin;

use DAO\CategoryDAO;
use Models\Category;
use Exception;

class CategoryController {
    private CategoryDAO $categoryDAO;

    public function __construct() {
        $this->categoryDAO = new CategoryDAO();
    }

    public function create() {
        $cateName = $slug = $description = "";
        $status = 1;
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $cateName = trim($_POST["cateName"] ?? "");
            $slug = trim($_POST["slug"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $status = (int)($_POST["status"] ?? 1);

            if (empty($cateName)) {
                $errors[] = "Tên danh mục không được để trống.";
            }
            if (empty($slug)) {
                $errors[] = "Slug không được để trống.";
            }

            $imageName = null;
            if (empty($errors) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowedExtensions)) {
                    $imageName = md5(time() . $fileName) . '.' . $ext;
                    $dir = __DIR__ . "/../../uploads/categories/";
                    
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    if (!move_uploaded_file($fileTmpPath, $dir . $imageName)) {
                        $errors[] = "Đã xảy ra lỗi khi di chuyển file tải lên.";
                    }
                } else {
                    $errors[] = "Định dạng ảnh không hợp lệ. Chỉ chấp nhận: jpg, jpeg, png, gif, webp.";
                }
            }

            if (empty($errors)) {
                try {
                    $category = new Category();
                    $category->cateName = $cateName;
                    $category->slug = $slug;
                    $category->description = $description;
                    $category->status = $status;
                    $category->image = $imageName;

                    if ($this->categoryDAO->insert($category)) {
                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category?msg=create_success");
                        exit();
                    } else {
                        $errors[] = "Thêm mới danh mục thất bại. Vui lòng thử lại.";
                    }
                } catch (Exception $e) {
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        $pageTitle = "Thêm mới danh mục";
        require_once __DIR__ . '/../../views/admin/categories/create.php';
    }

    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $category = $this->categoryDAO->findById($id);

        if (!$category) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category");
            exit();
        }

        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $cateName = trim($_POST["cateName"] ?? "");
            $slug = trim($_POST["slug"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

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

            $imageName = $category->image; 
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../uploads/categories/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $dest_path = $uploadFileDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $imageName = $newFileName;
                        
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

            if (empty($errors)) {
                try {
                    $category->cateName = $cateName;
                    $category->slug = $slug;
                    $category->description = $description;
                    $category->status = $status;
                    $category->image = $imageName;

                    $result = $this->categoryDAO->update($category);

                    if ($result) {
                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category?msg=update_success");
                        exit();
                    } else {
                        $errors[] = "Cập nhật danh mục thất bại. Vui lòng thử lại.";
                    }
                } catch (Exception $e) {
                    $errors[] = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        $pageTitle = "Cập nhật danh mục - Mini Shop";
        require_once __DIR__ . '/../../views/admin/categories/edit.php';
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

            if ($id > 0) {
                try {
                    $catOld = $this->categoryDAO->findById($id);
                    $result = $this->categoryDAO->delete($id);
                    
                    if ($result) {
                        if ($catOld && !empty($catOld->image)) {
                            $imagePath = __DIR__ . "/../../uploads/categories/" . $catOld->image;
                            if (file_exists($imagePath)) {
                                @unlink($imagePath);
                            }
                        }
                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category?msg=delete_success");
                        exit();
                    }
                } catch (Exception $e) {
                    header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category?msg=error");
                    exit();
                }
            }
        }
        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category");
        exit();
    }

    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $category = $this->categoryDAO->findById($id);

        if (!$category) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/category");
            exit();
        }

        $pageTitle = "Chi tiết danh mục - Mini Shop";
        require_once __DIR__ . '/../../views/admin/categories/detail.php';
    }

    public function index() {
        $message = "";
        $error = "";

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'delete_success') {
                $message = "Xóa danh mục thành công!";
            } elseif ($_GET['msg'] === 'error') {
                $error = "Không thể xóa danh mục này vì đang có sản phẩm liên kết hoặc xảy ra lỗi!";
            }
        }

        $statusList = [
            0 => ['label' => 'Ẩn', 'class' => 'bg-secondary'],
            1 => ['label' => 'Hiển thị', 'class' => 'bg-success']
        ];

        // 1. Thu thập tham số tìm kiếm và lọc
        $keyword = trim($_GET["keyword"] ?? "");
        $searchStatus = isset($_GET["search_status"]) ? trim($_GET["search_status"]) : "";
        
        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30])) {
            $limit = 10;
        }

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // 2. Truyền cả keyword và searchStatus vào DAO đếm và lấy dữ liệu
        $totalRecords = $this->categoryDAO->count($keyword, $searchStatus);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $limit; // Cập nhật lại offset nếu page vượt quá tổng số trang
        }

        $categories = $this->categoryDAO->getPage($limit, $offset, $keyword, $searchStatus);
        $pageTitle = "Quản lý danh mục - Mini Shop";

        require_once __DIR__ . '/../../views/admin/categories/index.php';
    }
}