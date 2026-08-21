<?php
$pageTitle = "Chi tiết đơn hàng #" . ($order ? $order->orderCode : '') . " - Mini Shop";
ob_start();

$sttKey = $order ? (int)$order->status : 0;
$badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary text-white', 'icon' => 'fa-question'];
$paymentMethod = $order ? $order->paymentMethod : 'COD';
$isBank = ($paymentMethod === 'ChuyenKhoan' || $paymentMethod === 'Chuyển khoản');

$itemsSubtotal = 0;
if (!empty($orderDetails)) {
    foreach ($orderDetails as $d) {
        $itemsSubtotal += $d->subtotal;
    }
} else {
    $itemsSubtotal = $order ? ($order->totalAmount - $order->shippingFee) : 0;
}
?>

<div class="container-fluid px-4">
    <!-- Header & Action bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-3 gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                📦 Chi tiết đơn hàng: <span class="text-primary fw-bold">#<?= htmlspecialchars($order->orderCode ?? '') ?></span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/order">Danh sách đơn hàng</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#<?= htmlspecialchars($order->orderCode ?? '') ?></li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>admin/order" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Danh sách
            </a>
            <?php if ($order): ?>
                <a href="<?= BASE_URL ?>admin/order/edit/<?= $order->id ?>" class="btn btn-warning btn-sm text-dark px-3 fw-bold">
                    <i class="fas fa-edit me-1"></i> Hiệu chỉnh đơn
                </a>
                <?php if (!empty($customerEmail)): ?>
                    <a href="<?= BASE_URL ?>admin/order/sendEmail/<?= $order->id ?>" class="btn btn-info text-white btn-sm px-3" onclick="return confirm('Gửi email xác nhận đơn hàng này tới: <?= htmlspecialchars($customerEmail) ?>?');">
                        <i class="fas fa-envelope me-1"></i> Gửi email khách
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print();">
                    <i class="fas fa-print me-1"></i> In hóa đơn
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <!-- Thanh tiến trình trạng thái trực quan -->
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-body py-4">
                <?php if ($sttKey == 4): ?>
                    <div class="alert alert-danger mb-0 d-flex align-items-center">
                        <i class="fas fa-times-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Đơn hàng đã bị hủy</h5>
                            <small>Đơn hàng này đã kết thúc với trạng thái Hủy đơn.</small>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row text-center position-relative">
                        <!-- Step 0 -->
                        <div class="col-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm <?= $sttKey >= 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px;">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div class="fw-bold small <?= $sttKey >= 0 ? 'text-dark' : 'text-muted' ?>">1. Chờ xác nhận</div>
                        </div>
                        <!-- Step 1 -->
                        <div class="col-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm <?= $sttKey >= 1 ? 'bg-info text-white' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px;">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <div class="fw-bold small <?= $sttKey >= 1 ? 'text-info' : 'text-muted' ?>">2. Đã xác nhận</div>
                        </div>
                        <!-- Step 2 -->
                        <div class="col-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm <?= $sttKey >= 2 ? 'bg-primary text-white' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px;">
                                <i class="fas fa-truck fa-lg"></i>
                            </div>
                            <div class="fw-bold small <?= $sttKey >= 2 ? 'text-primary' : 'text-muted' ?>">3. Đang giao</div>
                        </div>
                        <!-- Step 3 -->
                        <div class="col-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm <?= $sttKey >= 3 ? 'bg-success text-white' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px;">
                                <i class="fas fa-check-double fa-lg"></i>
                            </div>
                            <div class="fw-bold small <?= $sttKey >= 3 ? 'text-success' : 'text-muted' ?>">4. Đã giao</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Thông tin khách hàng -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-user-circle me-2"></i>Thông tin người nhận hàng
                        </h6>
                        <?php if (!empty($order->customerId)): ?>
                            <a href="<?= BASE_URL ?>admin/customer/detail/<?= $order->customerId ?>" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.75rem;">
                                Hồ sơ khách
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 140px;"><i class="fas fa-user me-2"></i>Họ và tên:</td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($customerName) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-phone me-2"></i>Số điện thoại:</td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($customerPhone ?: 'Chưa có') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-envelope me-2"></i>Email:</td>
                                    <td>
                                        <?php if (!empty($customerEmail)): ?>
                                            <span class="text-dark"><?= htmlspecialchars($customerEmail) ?></span>
                                            <a href="<?= BASE_URL ?>admin/order/sendEmail/<?= $order->id ?>" class="badge bg-info text-white text-decoration-none ms-2" title="Gửi email ngay">
                                                <i class="fas fa-paper-plane me-1"></i> Gửi mail
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Khách không cung cấp email</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ nhận:</td>
                                    <td class="text-dark"><?= htmlspecialchars($customerAddress ?: 'Chưa có địa chỉ') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-sticky-note me-2"></i>Ghi chú khách:</td>
                                    <td><?= nl2br(htmlspecialchars($customerNote ?: 'Không có')) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Thông tin đơn hàng & Thanh toán -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Thông tin đơn hàng & Thanh toán
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 150px;"><i class="fas fa-barcode me-2"></i>Mã đơn hàng:</td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($order->orderCode) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar-alt me-2"></i>Thời gian đặt:</td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($order->createdAt)) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-user-tie me-2"></i>Nhân viên xử lý:</td>
                                    <td><?= htmlspecialchars($userName) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-credit-card me-2"></i>Hình thức TT:</td>
                                    <td>
                                        <?php if ($isBank): ?>
                                            <span class="badge bg-primary text-white px-2 py-1 shadow-sm">
                                                <i class="fas fa-university me-1"></i> Chuyển khoản ngân hàng
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success text-white px-2 py-1 shadow-sm">
                                                <i class="fas fa-money-bill-wave me-1"></i> Thanh toán khi nhận hàng (COD)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-info-circle me-2"></i>Trạng thái:</td>
                                    <td>
                                        <span class="badge <?= $badgeInfo['class'] ?> px-2 py-1">
                                            <i class="fas <?= $badgeInfo['icon'] ?> me-1"></i> <?= $badgeInfo['label'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-comment-alt me-2"></i>Ghi chú đơn hàng:</td>
                                    <td><?= nl2br(htmlspecialchars($order->note ?: 'Không có')) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-dark"><i class="fas fa-box-open me-2 text-primary"></i>Danh sách sản phẩm trong đơn</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center text-muted" style="font-size: 0.85rem;">
                                <th style="width: 50px;">STT</th>
                                <th style="width: 80px;">Hình ảnh</th>
                                <th class="text-start">Tên sản phẩm</th>
                                <th style="width: 140px;">Đơn giá</th>
                                <th style="width: 100px;">Số lượng</th>
                                <th style="width: 150px;" class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orderDetails)): ?>
                                <?php $stt = 1; ?>
                                <?php foreach ($orderDetails as $item): ?>
                                    <tr class="text-center">
                                        <td class="text-muted"><?= $stt++ ?></td>
                                        <td>
                                            <?php if (!empty($item->productImage)): ?>
                                                <img src="<?= PRODUCT_IMAGE_URL . htmlspecialchars($item->productImage) ?>" alt="Sản phẩm" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light border rounded d-inline-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-box fa-lg"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($item->productName ?? ('Sản phẩm #' . $item->productId)) ?></div>
                                            <small class="text-muted">Mã SP (ID): #<?= $item->productId ?></small>
                                        </td>
                                        <td><?= number_format($item->price, 0, ',', '.') ?> đ</td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1 fw-bold"><?= $item->quantity ?></span></td>
                                        <td class="text-end fw-bold text-danger"><?= number_format($item->subtotal, 0, ',', '.') ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Không có sản phẩm nào trong đơn hàng này.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light p-4">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng tiền hàng:</span>
                            <span class="fw-bold"><?= number_format($itemsSubtotal, 0, ',', '.') ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển (Ship):</span>
                            <span class="fw-bold text-info"><?= number_format($order->shippingFee, 0, ',', '.') ?> đ</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-6">Tổng thanh toán:</span>
                            <span class="fw-bold fs-5 text-danger"><?= number_format($order->totalAmount, 0, ',', '.') ?> đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khối cập nhật trạng thái nhanh -->
        <div class="card border-0 shadow-sm mb-4 rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-exchange-alt me-2"></i>Cập nhật trạng thái đơn hàng</h6>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>admin/order/updateStatus" method="POST" class="row g-3 align-items-center">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $order->id ?>">
                    <input type="hidden" name="redirect_to_detail" value="1">

                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Chuyển sang trạng thái:</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statusList as $key => $value): ?>
                                <option value="<?= $key ?>" <?= $order->status == $key ? 'selected' : '' ?>>
                                    <?= $value['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 pt-md-4">
                        <?php if (!empty($customerEmail)): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="send_email_notify" id="notifyEmailCheck" value="1" checked>
                                <label class="form-check-label small" for="notifyEmailCheck">
                                    Gửi email thông báo cho khách (<?= htmlspecialchars($customerEmail) ?>)
                                </label>
                            </div>
                        <?php else: ?>
                            <span class="small text-muted fst-italic">Khách không có email để nhận thông báo</span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 pt-md-4 text-md-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Lưu trạng thái
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>