<?php
// Bật hiển thị lỗi để dễ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/OrderDAO.php";

$orderDAO = new OrderDAO();
$error = "";
$order = null;
$orderDetails = [];
$customerName = "Khách lẻ / Khách vãng lai";
$userName = "Chưa phân công";

// 1. Nhận orderId từ URL
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    header("Location: index.php");
    exit();
}

try {
    // 2. Gọi phương thức findById() trong OrderDAO để lấy thông tin đơn hàng
    $order = $orderDAO->findById($orderId);

    if ($order) {
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

        // 3. Lấy danh sách sản phẩm của đơn hàng
        $orderDetails = $orderDAO->getDetailsByOrderId($orderId);
    } else {
        $error = "Không tìm thấy đơn hàng!";
    }
} catch (Exception $e) {
    $error = "Lỗi hệ thống: " . $e->getMessage();
}

// Mảng định nghĩa trạng thái đơn hàng
$statusList = [
    0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
    1 => ['label' => 'Đang xử lý', 'class' => 'bg-info text-dark'],
    2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
    3 => ['label' => 'Hoàn thành', 'class' => 'bg-success text-white'],
    4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white']
];

$pageTitle = "Chi tiết đơn hàng - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết đơn hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Danh sách đơn hàng</a></li>
        <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <!-- THÔNG TIN CHUNG ĐƠN HÀNG -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-info-circle me-1"></i> Thông tin đơn hàng: <strong><?= htmlspecialchars($order->orderCode) ?></strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p><strong>Mã đơn hàng:</strong> <span class="text-primary fw-bold"><?= htmlspecialchars($order->orderCode) ?></span></p>
                        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($customerName) ?></p>
                        <p><strong>Nhân viên xử lý:</strong> <?= htmlspecialchars($userName) ?></p>
                        <p><strong>Ngày đặt:</strong> <?= $order->createdAt ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold fs-5"><?= number_format($order->totalAmount, 0, ',', '.') ?> đ</span></p>
                        <p><strong>Trạng thái:</strong> 
                            <?php 
                                $sttKey = (int)$order->status;
                                $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                            ?>
                            <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                        </p>
                        <p><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($order->note ?? 'Không có ghi chú')) ?></p>
                    </div>
                </div>
                <a href="index.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- DANH SÁCH CHI TIẾT ĐƠN HÀNG -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-box-open me-1"></i> Danh sách sản phẩm thuộc đơn hàng
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã sản phẩm (ID)</th>
                                <th class="text-start">Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orderDetails)): ?>
                                <?php $stt = 1; ?>
                                <?php foreach ($orderDetails as $detail): ?>
                                    <?php
                                        $productName = "Sản phẩm #" . $detail->productId;
                                        try {
                                            $db = new Database();
                                            $conn = $db->getConnection();
                                            // Đã sửa lại cột 'name' thành 'proname' cho khớp với CSDL của bạn
                                            $stmtP = $conn->prepare("SELECT proname FROM products WHERE id = ?");
                                            $stmtP->bind_param("i", $detail->productId);
                                            $stmtP->execute();
                                            $resP = $stmtP->get_result();
                                            if ($rowP = $resP->fetch_assoc()) {
                                                $productName = $rowP['proname'];
                                            }
                                        } catch (Exception $ex) {
                                            // Bỏ qua nếu lỗi
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $stt++ ?></td>
                                        <td><?= $detail->productId ?></td>
                                        <td class="text-start"><?= htmlspecialchars($productName) ?></td>
                                        <td><?= $detail->quantity ?></td>
                                        <td><?= number_format($detail->price, 0, ',', '.') ?> đ</td>
                                        <td><span class="text-danger fw-bold"><?= number_format($detail->subtotal, 0, ',', '.') ?> đ</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Không có sản phẩm nào trong đơn hàng này.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>