<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../../../dao/UserDAO.php";

$userDAO = new UserDAO();
$error = "";

$roleList = [
    0 => 'Thành viên',
    1 => 'Quản trị viên'
];

$statusList = [
    0 => 'Khóa',
    1 => 'Hoạt động'
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST['fullname'] ?? '');
    $userName = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = isset($_POST['role']) ? (int)$_POST['role'] : 0;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if (empty($fullName) || empty($userName) || empty($password) || empty($email)) {
        $error = "Vui lòng nhập đầy đủ các trường bắt buộc (Họ tên, Username, Mật khẩu, Email)!";
    } else {
        try {
            $user = new User();
            $user->fullName = $fullName;
            $user->userName = $userName;
            // Mã hóa mật khẩu bảo mật (hoặc để nguyên tùy dự án của bạn, ở đây dùng password_hash chuẩn)
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->email = $email;
            $user->phone = $phone;
            $user->address = $address;
            $user->role = $role;
            $user->status = $status;

            if ($userDAO->insert($user)) {
                header("Location: index.php?msg=insert_success");
                exit();
            } else {
                $error = "Thêm mới tài khoản thất bại!";
            }
        } catch (Exception $e) {
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

$pageTitle = "Thêm mới tài khoản người dùng";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý tài khoản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="indexx.php">Danh sách tài khoản</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i> Thêm mới tài khoản người dùng
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên đăng nhập (Username) <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Vai trò</label>
                        <select name="role" class="form-select">
                            <?php foreach ($roleList as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($_POST['role']) && $_POST['role'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statusList as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($_POST['status']) && $_POST['status'] == $key) ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Lưu thông tin</button>
                    <a href="indexx.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>