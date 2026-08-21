<?php
$pageTitle = "Chi tiết khách hàng - Mini Shop";
ob_start();

// Lấy danh sách đơn hàng của khách hàng này
$customerOrders = [];
try {
    require_once __DIR__ . "/../../../config/Database.php";
    $db = new \Config\Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $customer->id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $customerOrders[] = $r;
    }
} catch (\Exception $e) {
}

$statusList = [
    0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
    1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
    2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
    3 => ['label' => 'Đã giao', 'class' => 'bg-success text-white'],
    4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white']
];
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">👤 Chi tiết khách hàng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/customer">Danh sách khách hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($customer->fullName) ?></li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>admin/customer" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <a href="<?= BASE_URL ?>admin/customer/edit/<?= $customer->id ?>" class="btn btn-warning btn-sm text-dark px-3 fw-bold">
                <i class="fas fa-edit me-1"></i> Sửa thông tin
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột thông tin khách hàng -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-id-card me-2"></i>Hồ sơ khách hàng</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 130px;">ID Khách:</td>
                                <td class="fw-bold">#<?= htmlspecialchars($customer->id) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Họ và tên:</td>
                                <td class="fw-bold text-primary fs-6"><?= htmlspecialchars($customer->fullName) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Số điện thoại:</td>
                                <td class="fw-bold"><?= htmlspecialchars($customer->phone) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email:</td>
                                <td>
                                    <?php if (!empty($customer->email)): ?>
                                        <span class="text-dark"><?= htmlspecialchars($customer->email) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Chưa có email</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Địa chỉ:</td>
                                <td><?= htmlspecialchars($customer->address ?? 'Chưa cập nhật') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trạng thái:</td>
                                <td>
                                    <?php if ($customer->status == 1): ?>
                                        <span class="badge bg-success px-2 py-1">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger px-2 py-1">Khóa</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ghi chú:</td>
                                <td><?= nl2br(htmlspecialchars($customer->note ?? 'Không có ghi chú')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày tạo:</td>
                                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($customer->createdAt)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột lịch sử đơn hàng của khách -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>Lịch sử đơn hàng (<?= count($customerOrders) ?> đơn)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light small text-muted">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Hình thức thanh toán</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($customerOrders)): ?>
                                    <?php foreach ($customerOrders as $ord): ?>
                                        <?php
                                        $stt = (int)$ord['status'];
                                        $sttInfo = $statusList[$stt] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                        $rawPm = $ord['payment_method'] ?? 'COD';
                                        $isBank = ($rawPm === 'ChuyenKhoan' || $rawPm === 'Chuyển khoản' || stripos($rawPm, 'khoản') !== false);
                                        ?>
                                        <tr>
                                            <td class="fw-bold">
                                                <a href="<?= BASE_URL ?>admin/order/detail/<?= $ord['id'] ?>" class="text-decoration-none text-primary">
                                                    <?= htmlspecialchars($ord['order_code']) ?>
                                                </a>
                                            </td>
                                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></td>
                                            <td>
                                                <?php if ($isBank): ?>
                                                    <span class="badge bg-primary text-white px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                                        <i class="fas fa-university me-1"></i> Chuyển khoản ngân hàng
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success text-white px-2 py-1 shadow-sm" style="font-size: 0.75rem;">
                                                        <i class="fas fa-money-bill-wave me-1"></i> Thanh toán khi nhận hàng (COD)
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-danger"><?= number_format($ord['total_amount'], 0, ',', '.') ?> đ</td>
                                            <td><span class="badge <?= $sttInfo['class'] ?>"><?= $sttInfo['label'] ?></span></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>admin/order/detail/<?= $ord['id'] ?>" class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="py-4 text-muted">Khách hàng chưa có đơn hàng nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>