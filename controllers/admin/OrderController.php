<?php
namespace Controllers\Admin;

use Config\Database;
use DAO\OrderDAO;
use DAO\CustomerDAO;
use Models\Order;
use Models\Customer;
use Services\EmailService;
use Exception;

class OrderController
{
    private OrderDAO $orderDAO;
    private CustomerDAO $customerDAO;

    public function __construct()
    {
        require_once __DIR__ . "/../../dao/OrderDAO.php";
        require_once __DIR__ . "/../../dao/CustomerDAO.php";
        require_once __DIR__ . "/../../services/EmailService.php";
        $this->orderDAO = new OrderDAO();
        $this->customerDAO = new CustomerDAO();
    }

    public function index()
    {
        $message = "";
        $error = "";

        $statusList = [
            0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark', 'icon' => 'fa-clock'],
            1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark', 'icon' => 'fa-check-circle'],
            2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white', 'icon' => 'fa-truck'],
            3 => ['label' => 'Đã giao', 'class' => 'bg-success text-white', 'icon' => 'fa-check-double'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white', 'icon' => 'fa-times-circle']
        ];

        // Xử lý cập nhật nhanh trạng thái từ Modal danh sách
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateStatus'])) {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $status = isset($_POST['status']) ? (int) $_POST['status'] : 0;

            if ($id > 0) {
                try {
                    $result = $this->orderDAO->updateStatus($id, $status);
                    if ($result) {
                        // Tùy chọn gửi email nếu chọn
                        if (!empty($_POST['send_email_notify'])) {
                            $order = $this->orderDAO->findById($id);
                            if ($order && $order->customerId) {
                                $cust = $this->customerDAO->findById($order->customerId);
                                if ($cust && !empty($cust->email)) {
                                    $details = $this->orderDAO->getDetailsByOrderId($id);
                                    EmailService::sendOrderConfirmation($order, $cust, $details);
                                }
                            }
                        }
                        header("Location: " . BASE_URL . "admin/order?msg=update_success");
                        exit();
                    } else {
                        $error = "Cập nhật trạng thái thất bại!";
                    }
                } catch (Exception $e) {
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'update_success') $message = "Cập nhật đơn hàng thành công!";
            elseif ($_GET['msg'] === 'email_success') $message = "Đã gửi email xác nhận cho khách hàng thành công!";
            elseif ($_GET['msg'] === 'email_fail') $error = "Gửi email thất bại. Vui lòng kiểm tra email của khách hàng!";
        }

        $keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
        $searchStatus = (isset($_GET["search_status"]) && $_GET["search_status"] !== "") ? (int)$_GET["search_status"] : "";
        $searchPayment = isset($_GET["search_payment"]) ? trim($_GET["search_payment"]) : "";

        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30, 50])) {
            $limit = 10;
        }

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->orderDAO->countSearch($keyword, $searchStatus, $searchPayment);
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $orders = [];
        $statistics = [
            'total' => 0, 'pending' => 0, 'confirmed' => 0, 'shipping' => 0, 'completed' => 0, 'cancelled' => 0, 'revenue' => 0
        ];

        try {
            $orders = $this->orderDAO->getPage($limit, $offset, $keyword, $searchStatus, $searchPayment);
            $statistics = $this->orderDAO->getStatistics();
        } catch (Exception $e) {
            $error = "Lỗi tải dữ liệu: " . $e->getMessage();
        }

        $pageTitle = "Quản lý đơn hàng - Mini Shop";

        require_once __DIR__ . "/../../views/admin/orders/index.php";
    }

    public function detail()
    {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($orderId <= 0) {
            header("Location: " . BASE_URL . "admin/order");
            exit();
        }

        $message = "";
        $error = "";

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'update_success') $message = "Cập nhật thông tin đơn hàng thành công!";
            elseif ($_GET['msg'] === 'status_success') $message = "Cập nhật trạng thái đơn hàng thành công!";
            elseif ($_GET['msg'] === 'email_success') $message = "Đã gửi email xác nhận thành công tới khách hàng!";
            elseif ($_GET['msg'] === 'email_fail') $error = "Không thể gửi email. Khách hàng chưa có địa chỉ email hợp lệ!";
        }

        $order = null;
        $customer = null;
        $orderDetails = [];
        $customerName = "Khách lẻ / Khách vãng lai";
        $customerPhone = "";
        $customerEmail = "";
        $customerAddress = "";
        $customerNote = "";
        $userName = "Chưa phân công";

        $statusList = [
            0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark', 'icon' => 'fa-clock'],
            1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark', 'icon' => 'fa-check-circle'],
            2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white', 'icon' => 'fa-truck'],
            3 => ['label' => 'Đã giao', 'class' => 'bg-success text-white', 'icon' => 'fa-check-double'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white', 'icon' => 'fa-times-circle']
        ];

        try {
            $order = $this->orderDAO->findById($orderId);

            if ($order) {
                // Lấy thông tin khách hàng
                if (!empty($order->customerId)) {
                    $customer = $this->customerDAO->findById($order->customerId);
                    if ($customer) {
                        $customerName = $customer->fullName;
                        $customerPhone = $customer->phone;
                        $customerEmail = $customer->email ?? '';
                        $customerAddress = $customer->address ?? '';
                        $customerNote = $customer->note ?? '';
                    }
                }

                // Lấy thông tin nhân viên
                if (!empty($order->userId)) {
                    $db = new Database();
                    $conn = $db->getConnection();
                    $stmtU = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
                    $stmtU->bind_param("i", $order->userId);
                    $stmtU->execute();
                    $resU = $stmtU->get_result();
                    if ($rowU = $resU->fetch_assoc()) {
                        $userName = $rowU['fullname'];
                    }
                }

                $orderDetails = $this->orderDAO->getDetailsByOrderId($orderId);
            } else {
                $error = "Không tìm thấy thông tin đơn hàng!";
            }
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }

        $pageTitle = "Chi tiết đơn hàng #" . ($order ? $order->orderCode : '') . " - Mini Shop";

        require_once __DIR__ . "/../../views/admin/orders/detail.php";
    }

    public function edit()
    {
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($orderId <= 0) {
            header("Location: " . BASE_URL . "admin/order");
            exit();
        }

        $error = "";
        $order = null;
        $customer = null;
        $orderDetails = [];

        $statusList = [
            0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
            1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
            2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
            3 => ['label' => 'Đã giao', 'class' => 'bg-success text-white'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white']
        ];

        try {
            $order = $this->orderDAO->findById($orderId);
            if (!$order) {
                header("Location: " . BASE_URL . "admin/order");
                exit();
            }

            if (!empty($order->customerId)) {
                $customer = $this->customerDAO->findById($order->customerId);
            }

            $orderDetails = $this->orderDAO->getDetailsByOrderId($orderId);
            $itemsSubtotal = $this->orderDAO->calculateOrderSubtotal($orderId);
            if ($itemsSubtotal <= 0) {
                $itemsSubtotal = $order->totalAmount - $order->shippingFee;
                if ($itemsSubtotal < 0) $itemsSubtotal = $order->totalAmount;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Nhận thông tin khách hàng
                $fullname = trim($_POST['fullname'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $address = trim($_POST['address'] ?? '');

                // Nhận thông tin đơn hàng
                $paymentMethod = trim($_POST['payment_method'] ?? 'COD');
                $shippingFee = max(0, (float)($_POST['shipping_fee'] ?? 0));
                $status = (int)($_POST['status'] ?? 0);
                $note = trim($_POST['note'] ?? '');
                $sendEmail = !empty($_POST['send_email']);

                if (empty($fullname)) {
                    $error = "Họ tên khách hàng không được để trống!";
                } elseif (empty($phone)) {
                    $error = "Số điện thoại không được để trống!";
                } else {
                    // Cập nhật thông tin khách hàng nếu có
                    if ($customer) {
                        $customer->fullName = $fullname;
                        $customer->phone = $phone;
                        $customer->email = !empty($email) ? $email : null;
                        $customer->address = !empty($address) ? $address : null;
                        $this->customerDAO->update($customer);
                    }

                    // Tính lại tổng tiền: Tiền hàng + Phí vận chuyển
                    $calculatedTotal = $itemsSubtotal + $shippingFee;

                    // Cập nhật đơn hàng
                    $this->orderDAO->updateOrderFullInfo(
                        $orderId,
                        $paymentMethod,
                        $shippingFee,
                        $calculatedTotal,
                        $note,
                        $status
                    );

                    // Tùy chọn gửi email xác nhận nếu khách có email và admin chọn gửi
                    if ($sendEmail && !empty($email)) {
                        $updatedOrder = $this->orderDAO->findById($orderId);
                        $updatedCustomer = $customer ?? (object)[
                            'fullName' => $fullname,
                            'phone' => $phone,
                            'email' => $email,
                            'address' => $address
                        ];
                        EmailService::sendOrderConfirmation($updatedOrder, $updatedCustomer, $orderDetails);
                    }

                    header("Location: " . BASE_URL . "admin/order/detail/" . $orderId . "?msg=update_success");
                    exit();
                }
            }
        } catch (Exception $e) {
            $error = "Lỗi xử lý: " . $e->getMessage();
        }

        $pageTitle = "Chỉnh sửa đơn hàng #" . ($order ? $order->orderCode : '') . " - Mini Shop";

        require_once __DIR__ . "/../../views/admin/orders/edit.php";
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            $status = isset($_POST['status']) ? (int) $_POST['status'] : 0;
            $sendEmail = !empty($_POST['send_email_notify']);

            if ($id > 0) {
                try {
                    $result = $this->orderDAO->updateStatus($id, $status);
                    if ($result) {
                        if ($sendEmail) {
                            $order = $this->orderDAO->findById($id);
                            if ($order && $order->customerId) {
                                $cust = $this->customerDAO->findById($order->customerId);
                                if ($cust && !empty($cust->email)) {
                                    $details = $this->orderDAO->getDetailsByOrderId($id);
                                    EmailService::sendOrderConfirmation($order, $cust, $details);
                                }
                            }
                        }
                        $redirectUrl = isset($_POST['redirect_to_detail']) 
                            ? BASE_URL . "admin/order/detail/" . $id . "?msg=status_success"
                            : BASE_URL . "admin/order?msg=update_success";
                        header("Location: " . $redirectUrl);
                        exit();
                    }
                } catch (Exception $e) {
                    // Fall through
                }
            }
        }

        header("Location: " . BASE_URL . "admin/order");
        exit();
    }

    public function sendEmail()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: " . BASE_URL . "admin/order");
            exit();
        }

        try {
            $order = $this->orderDAO->findById($id);
            if ($order && !empty($order->customerId)) {
                $customer = $this->customerDAO->findById($order->customerId);
                if ($customer && !empty($customer->email)) {
                    $details = $this->orderDAO->getDetailsByOrderId($id);
                    $res = EmailService::sendOrderConfirmation($order, $customer, $details);
                    if ($res['success']) {
                        header("Location: " . BASE_URL . "admin/order/detail/" . $id . "?msg=email_success");
                        exit();
                    }
                }
            }
        } catch (Exception $e) {
            // Error
        }

        header("Location: " . BASE_URL . "admin/order/detail/" . $id . "?msg=email_fail");
        exit();
    }
}