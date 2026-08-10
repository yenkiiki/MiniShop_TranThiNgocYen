<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../models/Customer.php";
require_once __DIR__ . "/../../../dao/CustomerDAO.php";

$customerDAO = new CustomerDAO();
$error = "";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$customer = $customerDAO->findById($id);
if (!$customer) {
    header("Location: index.php");
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

            if ($customerDAO->update($customer)) {
                header("Location: index.php?msg=update_success");
                exit();
            } else {
                $error = "Cập nhật khách hàng thất bại!";
            }
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

$pageTitle = "Cập nhật thông tin khách hàng";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý khách hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách khách hàng</a></li>
        <li class="breadcrumb-item active">Cập nhật</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Form cập nhật thông tin khách hàng</div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span>:</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Nhập họ tên..." required value="<?= htmlspecialchars($customer->fullName) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span>:</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại..." required value="<?= htmlspecialchars($customer->phone) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email..." value="<?= htmlspecialchars($customer->email ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= ($customer->status == 1) ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= ($customer->status == 0) ? 'selected' : '' ?>>Khóa</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ:</label>
                    <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ..." value="<?= htmlspecialchars($customer->address ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ghi chú:</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Nhập ghi chú..."><?= htmlspecialchars($customer->note ?? '') ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>