<?php
namespace Controllers\Admin;

use DAO\UserDAO;
use Models\User;
class UserController {
    private UserDAO $userDAO;

    public function __construct() {
        $this->userDAO = new UserDAO();
    }

    public function index(): void {
        $message = "";
        $error = "";

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'delete_success') $message = "Xóa tài khoản thành công!";
            elseif ($_GET['msg'] === 'update_success') $message = "Cập nhật thành công!";
            elseif ($_GET['msg'] === 'insert_success') $message = "Thêm mới thành công!";
        }

        if (isset($_GET['error'])) {
            if ($_GET['error'] === 'delete_fail') $error = "Xóa tài khoản thất bại!";
            elseif ($_GET['error'] === 'delete_exception') $error = "Không thể xóa tài khoản này vì đang có dữ liệu liên quan!";
        }

        $roleList = [
            0 => ['label' => 'Thành viên', 'class' => 'bg-secondary text-white'],
            1 => ['label' => 'Quản trị viên', 'class' => 'bg-primary text-white']
        ];

        $statusList = [
            0 => ['label' => 'Khóa', 'class' => 'bg-danger text-white'],
            1 => ['label' => 'Hoạt động', 'class' => 'bg-success text-white']
        ];

        // Nhận tham số tìm kiếm, lọc, phân trang và limit
        $keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
        $searchRole = isset($_GET["search_role"]) && $_GET["search_role"] !== "" ? (int)$_GET["search_role"] : "";
        $searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30])) {
            $limit = 10;
        }

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->userDAO->countSearch($keyword, $searchRole, $searchStatus);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $users = [];
        try {
            $users = $this->userDAO->getPage($limit, $offset, $keyword, $searchRole, $searchStatus);
        } catch (Exception $e) {
            $error = "Lỗi tải dữ liệu: " . $e->getMessage();
        }

        $pageTitle = "Quản lý tài khoản người dùng";
        require_once __DIR__ . "/../../views/admin/users/index.php";
    }
public function create(): void {
    $roleList = [0 => 'Thành viên', 1 => 'Quản trị viên'];
    $statusList = [0 => 'Khóa', 1 => 'Hoạt động'];
    $pageTitle = "Thêm mới tài khoản người dùng";
    
    require_once __DIR__ . "/../../views/admin/users/create.php";
}

public function store(): void {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $fullName = trim($_POST['fullname'] ?? '');
        $userName = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($fullName) || empty($userName) || empty($password) || empty($email)) {
            header("Location: index.php?controller=user&action=create&error=missing_fields");
            exit();
        }

        try {
            $user = new User();
            $user->fullName = $fullName;
            $user->userName = $userName;
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->email = $email;
            $user->phone = trim($_POST['phone'] ?? '');
            $user->address = trim($_POST['address'] ?? '');
            $user->role = (int)($_POST['role'] ?? 0);
            $user->status = (int)($_POST['status'] ?? 1);

            if ($this->userDAO->insert($user)) {
                header("Location: index.php?controller=user&action=index&msg=insert_success");
                exit();
            }
        } catch (Exception $e) {
            header("Location: index.php?controller=user&action=create&error=exception");
            exit();
        }
    }
}
public function edit(): void {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $user = $this->userDAO->findById($id);

    if (!$user) {
        header("Location: index.php?controller=user&action=index&msg=not_found");
        exit();
    }

    $roleList = [0 => 'Thành viên', 1 => 'Quản trị viên'];
    $statusList = [0 => 'Khóa', 1 => 'Hoạt động'];
    $pageTitle = "Chỉnh sửa tài khoản người dùng";

    require_once __DIR__ . "/../../views/admin/users/edit.php";
}

public function update(): void {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $user = $this->userDAO->findById($id);

    if (!$user || $_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php?controller=user&action=index");
        exit();
    }

    $fullName = trim($_POST['fullname'] ?? '');
    $userName = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($fullName) || empty($userName) || empty($email)) {
        header("Location: index.php?controller=user&action=edit&id=$id&error=missing_fields");
        exit();
    }

    try {
        $user->fullName = $fullName;
        $user->userName = $userName;
        $user->email = $email;
        $user->phone = trim($_POST['phone'] ?? '');
        $user->address = trim($_POST['address'] ?? '');
        $user->role = (int)($_POST['role'] ?? 0);
        $user->status = (int)($_POST['status'] ?? 1);

        if (!empty($password)) {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userDAO->update($user)) {
            header("Location: index.php?controller=user&action=index&msg=update_success");
            exit();
        }
    } catch (Exception $e) {
        header("Location: index.php?controller=user&action=edit&id=$id&error=exception");
        exit();
    }
}
public function detail(): void {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $user = $this->userDAO->findById($id);

    if (!$user) {
        header("Location: index.php?controller=user&action=index&msg=not_found");
        exit();
    }

    $roleList = [
        0 => ['label' => 'Thành viên', 'class' => 'bg-secondary text-white'],
        1 => ['label' => 'Quản trị viên', 'class' => 'bg-primary text-white']
    ];

    $statusList = [
        0 => ['label' => 'Khóa', 'class' => 'bg-danger text-white'],
        1 => ['label' => 'Hoạt động', 'class' => 'bg-success text-white']
    ];

    $pageTitle = "Chi tiết tài khoản người dùng";
    require_once __DIR__ . "/../../views/admin/users/detail.php";
}
    public function delete(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            try {
                if ($this->userDAO->delete($id)) {
                    header("Location: index.php?controller=user&action=index&msg=delete_success");
                    exit();
                } else {
                    header("Location: index.php?controller=user&action=index&error=delete_fail");
                    exit();
                }
            } catch (Exception $e) {
                header("Location: index.php?controller=user&action=index&error=delete_exception");
                exit();
            }
        }
        header("Location: index.php?controller=user&action=index");
        exit();
    }
}