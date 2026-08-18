<?php
namespace Controllers\Admin;

use DAO\BrandDAO;
use Models\Brand;
use Middleware\CsrfMiddleware;
use Exception;

class BrandController {
    private $brandDAO;

    public function __construct() {
        $this->brandDAO = new BrandDAO();
    }

    public function index() {
        $message = $_GET['msg'] ?? "";
        $error = "";
        
        $statusList = [
            0 => ['label' => 'Ẩn / Khóa', 'class' => 'bg-danger text-white'],
            1 => ['label' => 'Đang hoạt động', 'class' => 'bg-success text-white']
        ];

        $keyword = trim($_GET["keyword"] ?? "");
        $searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";
        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30])) $limit = 10;
        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->brandDAO->count("brands", "brandname", $keyword, $searchStatus);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;
        if ($page > $totalPages) $page = $totalPages;

        $brands = [];
        try {
            $brands = $this->brandDAO->getPage($limit, $offset, $keyword, $searchStatus);
        } catch (Exception $e) {
            $error = "Lỗi tải dữ liệu: " . $e->getMessage();
        }

        require_once __DIR__ . "/../../views/admin/brands/index.php";
    }
    
    public function create() {
        $error = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();

            $brandName = trim($_POST['brandname'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);
            $imageName = "";

            if (empty($brandName)) {
                $error = "Tên thương hiệu không được để trống!";
            } else {
                if (empty($slug)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brandName), '-'));
                }

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['image']['tmp_name'];
                    $fileName = $_FILES['image']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($fileExtension, $allowedExtensions)) {
                        $imageName = time() . '_' . uniqid() . '.' . $fileExtension;
                        $uploadFileDir = __DIR__ . '/../../uploads/brands/';
                        
                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }
                        
                        if (!move_uploaded_file($fileTmpPath, $uploadFileDir . $imageName)) {
                            $error = "Lỗi khi tải lên hình ảnh!";
                        }
                    } else {
                        $error = "Chỉ chấp nhận các định dạng ảnh: " . implode(', ', $allowedExtensions);
                    }
                }

                if (empty($error)) {
                    try {
                        $brand = new Brand();
                        $brand->brandName = $brandName;
                        $brand->slug = $slug;
                        $brand->image = $imageName;
                        $brand->description = $description;
                        $brand->status = $status;

                        if ($this->brandDAO->insert($brand)) {
                            header('Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=insert_success');
                            exit;
                        } else {
                            $error = "Thêm mới thương hiệu thất bại!";
                        }
                    } catch (Exception $e) {
                        $error = "Lỗi hệ thống: " . $e->getMessage();
                    }
                }
            }
        }

        require_once __DIR__ . "/../../views/admin/brands/create.php";
    }

    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand");
            exit();
        }

        $brand = $this->brandDAO->findById($id);
        if (!$brand) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=not_found");
            exit();
        }

        require_once __DIR__ . "/../../views/admin/brands/detail.php";
    }

    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand");
            exit();
        }

        $brand = $this->brandDAO->findById($id);
        if (!$brand) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=not_found");
            exit();
        }

        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CsrfMiddleware::verify();

            $brandName = trim($_POST['brandname'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $status = (int)($_POST['status'] ?? 1);
            $imageName = $brand->image;

            if (empty($brandName)) {
                $error = "Tên thương hiệu không được để trống!";
            } else {
                if (empty($slug)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $brandName), '-'));
                }

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['image']['tmp_name'];
                    $fileName = $_FILES['image']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($fileExtension, $allowedExtensions)) {
                        $newImageName = time() . '_' . uniqid() . '.' . $fileExtension;
                        $uploadFileDir = __DIR__ . '/../../uploads/brands/';

                        if (!is_dir($uploadFileDir)) {
                            mkdir($uploadFileDir, 0755, true);
                        }

                        if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newImageName)) {
                            if (!empty($brand->image) && file_exists($uploadFileDir . $brand->image)) {
                                @unlink($uploadFileDir . $brand->image);
                            }
                            $imageName = $newImageName;
                        } else {
                            $error = "Lỗi khi tải lên hình ảnh mới!";
                        }
                    } else {
                        $error = "Chỉ chấp nhận các định dạng ảnh: " . implode(', ', $allowedExtensions);
                    }
                }

                if (empty($error)) {
                    try {
                        $brand->brandName = $brandName;
                        $brand->slug = $slug;
                        $brand->image = $imageName;
                        $brand->description = $description;
                        $brand->status = $status;

                        if ($this->brandDAO->update($brand)) {
                            header('Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=update_success');
                            exit;
                        } else {
                            $error = "Cập nhật thương hiệu thất bại!";
                        }
                    } catch (Exception $e) {
                        $error = "Lỗi hệ thống: " . $e->getMessage();
                    }
                }
            }
        }

        require_once __DIR__ . "/../../views/admin/brands/edit.php";
    }

   public function delete() {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    if ($id > 0) {
        try {
            $result = $this->brandDAO->delete($id);
            if ($result) {
                header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=delete_success");
            } else {
                header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=delete_fail");
            }
        } catch (Exception $e) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand?msg=delete_error");
        }
    } else {
        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/brand");
    }
    exit();
}
}