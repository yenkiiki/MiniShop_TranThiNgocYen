<?php
namespace Controllers\Admin;

use DAO\SaleDAO;
use DAO\ProductDAO;
use Models\Sale;
use Exception;

class SaleController
{
    private SaleDAO $saleDAO;
    private ProductDAO $productDAO;

    public function __construct()
    {
        $this->saleDAO = new SaleDAO();
        $this->productDAO = new ProductDAO();
    }

    public function index()
    {
        $message = "";
        $error = "";

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'create_success') $message = "Thêm sản phẩm giảm giá thành công!";
            elseif ($_GET['msg'] === 'update_success') $message = "Cập nhật sản phẩm giảm giá thành công!";
            elseif ($_GET['msg'] === 'delete_success') $message = "Đã xóa chương trình giảm giá và khôi phục giá gốc!";
            elseif ($_GET['msg'] === 'toggle_success') $message = "Đã thay đổi trạng thái áp dụng giảm giá!";
        }

        $keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
        $searchStatus = (isset($_GET["search_status"]) && $_GET["search_status"] !== "") ? (int)$_GET["search_status"] : "";

        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30, 50])) {
            $limit = 10;
        }

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->saleDAO->countSearch($keyword, $searchStatus);
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $sales = [];
        $statistics = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'avg_discount' => 0
        ];

        try {
            $sales = $this->saleDAO->getPage($limit, $offset, $keyword, $searchStatus);
            $statistics = $this->saleDAO->getStatistics();
        } catch (Exception $e) {
            $error = "Lỗi tải dữ liệu: " . $e->getMessage();
        }

        ob_start();
        require __DIR__ . "/../../views/admin/sales/index.php";
        $content = ob_get_clean();
        include __DIR__ . "/../../views/admin/layouts/master.php";
    }

    public function create()
    {
        $error = "";
        $products = $this->saleDAO->getAvailableProductsForSale();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $discountPercent = (int)($_POST['discount_percent'] ?? 0);
            $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $description = trim($_POST['description'] ?? '');
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            if ($productId <= 0) {
                $error = "Vui lòng chọn một sản phẩm từ danh sách.";
            } elseif ($discountPercent <= 0 || $discountPercent >= 100) {
                $error = "Mức giảm giá phải từ 1% đến 99%.";
            } else {
                try {
                    $sale = new Sale();
                    $sale->productId = $productId;
                    $sale->discountPercent = $discountPercent;
                    $sale->startDate = $startDate;
                    $sale->endDate = $endDate;
                    $sale->description = $description;
                    $sale->status = $status;

                    $newId = $this->saleDAO->create($sale);
                    if ($newId) {
                        header("Location: " . BASE_URL . "admin/sale?msg=create_success");
                        exit();
                    } else {
                        $error = "Không thể tạo chương trình giảm giá cho sản phẩm này.";
                    }
                } catch (Exception $e) {
                    $error = "Lỗi: " . $e->getMessage();
                }
            }
        }

        ob_start();
        require __DIR__ . "/../../views/admin/sales/create.php";
        $content = ob_get_clean();
        include __DIR__ . "/../../views/admin/layouts/master.php";
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: " . BASE_URL . "admin/sale");
            exit();
        }

        $sale = $this->saleDAO->findById($id);
        if (!$sale) {
            header("Location: " . BASE_URL . "admin/sale");
            exit();
        }

        $error = "";
        $products = $this->saleDAO->getAvailableProductsForSale();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)($_POST['product_id'] ?? $sale->productId);
            $discountPercent = (int)($_POST['discount_percent'] ?? 0);
            $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $description = trim($_POST['description'] ?? '');
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            if ($productId <= 0) {
                $error = "Vui lòng chọn sản phẩm.";
            } elseif ($discountPercent <= 0 || $discountPercent >= 100) {
                $error = "Mức giảm giá phải từ 1% đến 99%.";
            } else {
                try {
                    $sale->productId = $productId;
                    $sale->discountPercent = $discountPercent;
                    $sale->startDate = $startDate;
                    $sale->endDate = $endDate;
                    $sale->description = $description;
                    $sale->status = $status;

                    if ($this->saleDAO->update($sale)) {
                        header("Location: " . BASE_URL . "admin/sale?msg=update_success");
                        exit();
                    } else {
                        $error = "Không thể cập nhật chương trình giảm giá.";
                    }
                } catch (Exception $e) {
                    $error = "Lỗi: " . $e->getMessage();
                }
            }
        }

        ob_start();
        require __DIR__ . "/../../views/admin/sales/edit.php";
        $content = ob_get_clean();
        include __DIR__ . "/../../views/admin/layouts/master.php";
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        if ($id > 0) {
            try {
                $this->saleDAO->delete($id);
                header("Location: " . BASE_URL . "admin/sale?msg=delete_success");
                exit();
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "admin/sale");
                exit();
            }
        }
        header("Location: " . BASE_URL . "admin/sale");
        exit();
    }

    public function toggleStatus()
    {
        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        if ($id > 0) {
            try {
                $this->saleDAO->toggleStatus($id);
                header("Location: " . BASE_URL . "admin/sale?msg=toggle_success");
                exit();
            } catch (Exception $e) {
                header("Location: " . BASE_URL . "admin/sale");
                exit();
            }
        }
        header("Location: " . BASE_URL . "admin/sale");
        exit();
    }
}
