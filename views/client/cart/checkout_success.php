<?php
$isVNPay = isset($_GET['vnpay']) && $_GET['vnpay'] == '1';
$transNo = $_GET['trans_no'] ?? ($_GET['vnp_TransactionNo'] ?? '');
$bankCode = $_GET['bank'] ?? ($_GET['vnp_BankCode'] ?? '');
$amount = $_GET['amount'] ?? '';
?>

<div class="container my-5">
    <div class="card shadow-sm p-4 p-md-5 border-0 mx-auto rounded-4 text-center bg-white" style="max-width: 650px;">
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4.5rem;"></i>
        </div>

        <?php if ($isVNPay): ?>
            <span class="badge bg-primary px-3 py-2 text-uppercase fw-bold mx-auto mb-2" style="font-size: 0.82rem; letter-spacing: 0.5px;">
                <i class="bi bi-shield-check me-1"></i> VNPAY PAYMENT SUCCESS
            </span>
            <h2 class="text-success fw-bold mb-2">Thanh toán VNPAY thành công!</h2>
            <p class="text-muted mb-4">Cảm ơn bạn đã tin tưởng mua sắm. Đơn hàng của bạn đã được thanh toán hoàn tất qua cổng VNPAY.</p>
        <?php else: ?>
            <h2 class="text-success fw-bold mb-2">Đặt hàng thành công!</h2>
            <p class="text-muted mb-4">Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được ghi nhận và sẽ được xử lý trong thời gian sớm nhất.</p>
        <?php endif; ?>
        
        <?php if (!empty($orderCode)): ?>
            <div class="p-3 bg-light rounded-3 border mb-4 text-start">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="text-muted small">Mã đơn hàng:</span>
                    <strong class="text-primary fs-6"><?= htmlspecialchars($orderCode) ?></strong>
                </div>

                <?php if ($isVNPay && !empty($transNo)): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted small">Mã giao dịch VNPAY:</span>
                        <strong class="text-dark"><?= htmlspecialchars($transNo) ?></strong>
                    </div>
                <?php endif; ?>

                <?php if ($isVNPay && !empty($bankCode)): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-muted small">Ngân hàng thanh toán:</span>
                        <strong class="text-info"><?= htmlspecialchars($bankCode) ?></strong>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Trạng thái:</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> <?= $isVNPay ? 'Đã thanh toán (VNPAY)' : 'Đã tiếp nhận đơn hàng' ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Thông báo gửi email -->
        <div class="alert alert-info d-flex align-items-center gap-2 text-start p-3 rounded-3 mb-4" role="alert" style="font-size: 0.88rem;">
            <i class="bi bi-envelope-check-fill fs-4 text-primary flex-shrink-0"></i>
            <div>
                Hóa đơn xác nhận chi tiết đã được gửi tự động tới email của bạn và quản trị viên <strong>tranngocyen280905@gmail.com</strong>.
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
            <a href="<?= BASE_URL ?>order/history" class="btn btn-outline-primary px-4 py-2.5 fw-semibold rounded-pill">
                <i class="bi bi-receipt me-1"></i> Xem lịch sử đơn hàng
            </a>
            <a href="<?= BASE_URL ?>" class="btn btn-primary px-4 py-2.5 fw-semibold rounded-pill">
                <i class="bi bi-house-door me-1"></i> Tiếp tục mua sắm
            </a>
        </div>
    </div>
</div>