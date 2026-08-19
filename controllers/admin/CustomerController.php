<?php
namespace Controllers\Admin;

use DAO\CustomerDAO;
use Models\Customer;
use Exception;

class CustomerController {
    private CustomerDAO $customerDAO;

    public function __construct() {
        $this->customerDAO = new CustomerDAO();
    }

    public function index(): void {
        $message = "";
        $error = "";

        $statusList = [
            0 => ['label' => 'Khóa', 'class' => 'bg-danger text-white'],
            1 => ['label' => 'Hoạt động', 'class' => 'bg-success text-white']
        ];

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'delete_success') $message = "Xóa khách hàng thành công!";
            elseif ($_GET['msg'] === 'update_success') $message = "Cập nhật thành công!";
            elseif ($_GET['msg'] === 'insert_success') $message = "Thêm mới thành công!";
            elseif ($_GET['msg'] === 'delete_fail') $error = "Xóa khách hàng thất bại!";
        }

        $keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
        $searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30])) {
            $limit = 10;
        }

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->customerDAO->count($keyword, $searchStatus);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $customers = [];
        try {
            $customers = $this->customerDAO->getPage($limit, $offset, $keyword, $searchStatus);
        } catch (Exception $e) {
            $error = "Lỗi tải dữ liệu: " . $e->getMessage();
        }

        require_once __DIR__ . "/../../views/admin/customers/index.php";
    }

    public function create(): void {
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $note = isset($_POST['note']) ? trim($_POST['note']) : '';
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            if (empty($fullName)) {
                $error = "Họ tên không được để trống!";
            } elseif (empty($phone)) {
                $error = "Số điện thoại không được để trống!";
            } else {
                try {
                    $customer = new Customer();
                    $customer->fullName = $fullName;
                    $customer->phone = $phone;
                    $customer->email = !empty($email) ? $email : null;
                    $customer->address = !empty($address) ? $address : null;
                    $customer->note = !empty($note) ? $note : null;
                    $customer->status = $status;

                    if ($this->customerDAO->insert($customer)) {
                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer?msg=insert_success");
                        exit();
                    } else {
                        $error = "Thêm mới khách hàng thất bại!";
                    }
                } catch (Exception $e) {
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . "/../../views/admin/customers/create.php";
    }

    public function edit(): void {
        $error = "";

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer");
            exit();
        }

        $customer = $this->customerDAO->findById($id);
        if (!$customer) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $note = isset($_POST['note']) ? trim($_POST['note']) : '';
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            if (empty($fullName)) {
                $error = "Họ tên không được để trống!";
            } elseif (empty($phone)) {
                $error = "Số điện thoại không được để trống!";
            } else {
                try {
                    $customer->fullName = $fullName;
                    $customer->phone = $phone;
                    $customer->email = !empty($email) ? $email : null;
                    $customer->address = !empty($address) ? $address : null;
                    $customer->note = !empty($note) ? $note : null;
                    $customer->status = $status;

                    if ($this->customerDAO->update($customer)) {
                        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer?msg=update_success");
                        exit();
                    } else {
                        $error = "Cập nhật khách hàng thất bại!";
                    }
                } catch (Exception $e) {
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
            }
        }

        require_once __DIR__ . "/../../views/admin/customers/edit.php";
    }

    public function detail(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer");
            exit();
        }

        $customer = $this->customerDAO->findById($id);
        
        if (!$customer) {
            header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer");
            exit();
        }

        require_once __DIR__ . "/../../views/admin/customers/detail.php";
    }

    public function delete(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            try {
                if ($this->customerDAO->delete($id)) {
                    header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer?msg=delete_success");
                    exit();
                }
            } catch (Exception $e) {
            }
        }
        header("Location: /MINISHOP_TRANTHINGOCYEN/admin/customer?msg=delete_fail");
        exit();
    }
}