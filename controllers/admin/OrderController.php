<?php
namespace Controllers\Admin;
use Config\Database;
use DAO\OrderDAO;
use Models\Order;
class OrderController
{
    private $orderDAO;

    public function __construct()
    {
        require_once __DIR__ . "/../../dao/OrderDAO.php";
        $this->orderDAO = new OrderDAO();
    }
public function detail()
    {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($orderId <= 0) {
            header("Location: index.php?controller=order&action=index");
            exit();
        }

        $error = "";
        $order = null;
        $orderDetails = [];
        $customerName = "Khách lẻ / Khách vãng lai";
        $userName = "Chưa phân công";

        $statusList = [
            0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
            1 => ['label' => 'Đang xử lý', 'class' => 'bg-info text-dark'],
            2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
            3 => ['label' => 'Hoàn thành', 'class' => 'bg-success text-white'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white']
        ];

        try {
            $order = $this->orderDAO->findById($orderId);

            if ($order) {
                require_once __DIR__ . "/../../config/Database.php";
                $db = new Database();
                $conn = $db->getConnection();

                // Lấy tên khách hàng
                if (!empty($order->customerId)) {
                    $stmtC = $conn->prepare("SELECT fullname FROM customers WHERE id = ?");
                    $stmtC->bind_param("i", $order->customerId);
                    $stmtC->execute();
                    $resC = $stmtC->get_result();
                    if ($rowC = $resC->fetch_assoc()) {
                        $customerName = $rowC['fullname'];
                    }
                }

                // Lấy tên nhân viên xử lý
                if (!empty($order->userId)) {
                    $stmtU = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
                    $stmtU->bind_param("i", $order->userId);
                    $stmtU->execute();
                    $resU = $stmtU->get_result();
                    if ($rowU = $resU->fetch_assoc()) {
                        $userName = $rowU['fullname'];
                    }
                }

                // Lấy danh sách sản phẩm của đơn hàng
                $orderDetails = $this->orderDAO->getDetailsByOrderId($orderId);
            } else {
                $error = "Không tìm thấy đơn hàng!";
            }
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }

        $pageTitle = "Chi tiết đơn hàng - Mini Shop";

        // Gọi sang file View chi tiết
        require_once __DIR__ . "/../../views/admin/orders/detail.php";
    }
    public function index()
    {
        $message = "";
        $error = "";

        // Mảng định nghĩa trạng thái đơn hàng
        $statusList = [
            0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
            1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
            2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
            3 => ['label' => 'Hoàn thành', 'class' => 'bg-success text-white'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white']
        ];

        // XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG NHANH (METHOD POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateStatus'])) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $status = isset($_POST['status']) ? (int) $_POST['status'] : 0;

            if ($id > 0) {
                try {
                    $result = $this->orderDAO->updateStatus($id, $status);
                    if ($result) {
                        header("Location: index.php?controller=order&action=index&msg=update_success");
                        exit();
                    } else {
                        $error = "Cập nhật trạng thái thất bại!";
                    }
                } catch (Exception $e) {
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        if (isset($_GET['msg']) && $_GET['msg'] === 'update_success') {
            $message = "Cập nhật trạng thái đơn hàng thành công!";
        }

        // --- NHẬN CÁC THAM SỐ TÌM KIẾM, LỌC, PHÂN TRANG ---
        $keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
        $searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

        $limit = (int)($_GET["limit"] ?? 2);
        if (!in_array($limit, [10, 20, 30])) {
            $limit = 2; // Hoặc đổi mặc định thành 10 tùy bạn
        }

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->orderDAO->countSearch($keyword, $searchStatus);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $orders = [];
        try {
            $orders = $this->orderDAO->getPage($limit, $offset, $keyword, $searchStatus);
        } catch (Exception $e) {
            $error = "Lỗi tải dữ liệu: " . $e->getMessage();
        }

        $pageTitle = "Quản lý đơn hàng - Mini Shop";

        // Gọi sang file View giao diện
        require_once __DIR__ . "/../../views/admin/orders/index.php";
    }
}