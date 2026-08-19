<?php
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết đơn hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="order">Danh sách đơn hàng</a></li>
        <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
    </ol>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
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
                <a href="order" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>

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
                                            $stmtP = $conn->prepare("SELECT proname FROM products WHERE id = ?");
                                            $stmtP->bind_param("i", $detail->productId);
                                            $stmtP->execute();
                                            $resP = $stmtP->get_result();
                                            if ($rowP = $resP->fetch_assoc()) {
                                                $productName = $rowP['proname'];
                                            }
                                        } catch (Exception $ex) {
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
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>