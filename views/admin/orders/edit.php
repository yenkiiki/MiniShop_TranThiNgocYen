<?php
$pageTitle = "Hiệu chỉnh đơn hàng #" . ($order ? $order->orderCode : '') . " - Mini Shop";
ob_start();

$customerName = $customer ? $customer->fullName : '';
$customerPhone = $customer ? $customer->phone : '';
$customerEmail = $customer ? ($customer->email ?? '') : '';
$customerAddress = $customer ? ($customer->address ?? '') : '';
$paymentMethod = $order ? $order->paymentMethod : 'COD';
$shippingFee = $order ? (float)$order->shippingFee : 0;
$totalAmount = $order ? (float)$order->totalAmount : 0;
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                ✏️ Hiệu chỉnh đơn hàng: <span class="text-primary fw-bold">#<?= htmlspecialchars($order->orderCode ?? '') ?></span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/order">Danh sách đơn hàng</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/order/detail/<?= $order->id ?? 0 ?>">#<?= htmlspecialchars($order->orderCode ?? '') ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= BASE_URL ?>admin/order/detail/<?= $order->id ?? 0 ?>" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Quay lại chi tiết
            </a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <form action="<?= BASE_URL ?>admin/order/edit/<?= $order->id ?>" method="POST" id="editOrderForm">
            <?= csrf_field() ?>

            <div class="row g-4">
                <!-- Cột trái: Thông tin khách hàng & Giao hàng -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-user-edit me-2"></i>Thông tin người nhận hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Họ và tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($customerName) ?>" required placeholder="Nhập họ tên khách hàng">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customerPhone) ?>" required placeholder="Nhập số điện thoại">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Email nhận thông báo (tùy chọn)</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customerEmail) ?>" placeholder="ví_dụ: khachhang@gmail.com">
                                <div class="form-text">Nếu có email, hệ thống có thể gửi xác nhận đơn tự động.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Địa chỉ giao hàng</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành..."><?= htmlspecialchars($customerAddress) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Thông tin thanh toán, Phí ship & Trạng thái -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 fw-bold text-primary">
                                <i class="fas fa-cogs me-2"></i>Thiết lập đơn hàng & Thanh toán
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Hình thức thanh toán</label>
                                <select name="payment_method" class="form-select">
                                    <option value="COD" <?= ($paymentMethod === 'COD' || empty($paymentMethod)) ? 'selected' : '' ?>>
                                        💵 Thanh toán khi nhận hàng (COD)
                                    </option>
                                    <option value="Chuyển khoản" <?= ($paymentMethod === 'Chuyển khoản' || $paymentMethod === 'ChuyenKhoan') ? 'selected' : '' ?>>
                                        💳 Chuyển khoản ngân hàng
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Trạng thái đơn hàng</label>
                                <select name="status" class="form-select">
                                    <?php foreach ($statusList as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= $order->status == $k ? 'selected' : '' ?>>
                                            <?= $v['label'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Phí vận chuyển (Phí Ship - VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" step="1000" min="0" name="shipping_fee" id="shippingFeeInput" class="form-control" value="<?= (int)$shippingFee ?>">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <div class="form-text">Thay đổi phí ship sẽ tự động tính lại tổng tiền đơn hàng.</div>
                            </div>

                            <!-- Tính toán tổng tiền trực quan -->
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                    <span>Tổng tiền hàng (Subtotal):</span>
                                    <span id="itemsSubtotalDisplay" data-subtotal="<?= $itemsSubtotal ?>" class="fw-bold text-dark">
                                        <?= number_format($itemsSubtotal, 0, ',', '.') ?> đ
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                    <span>Phí vận chuyển:</span>
                                    <span id="shippingFeeDisplay" class="fw-bold text-info">
                                        <?= number_format($shippingFee, 0, ',', '.') ?> đ
                                    </span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Tổng thanh toán dự kiến:</span>
                                    <span id="grandTotalDisplay" class="fw-bold fs-5 text-danger">
                                        <?= number_format($totalAmount, 0, ',', '.') ?> đ
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Ghi chú đơn hàng</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú thêm về đơn hàng..."><?= htmlspecialchars($order->note ?? '') ?></textarea>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="send_email" id="sendEmailSwitch" value="1" <?= !empty($customerEmail) ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="sendEmailSwitch">
                                    Gửi email xác nhận cập nhật cho khách sau khi lưu
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm chỉ xem -->
            <div class="card border-0 shadow-sm mt-4 rounded-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-boxes me-2 text-primary"></i>Danh sách sản phẩm trong đơn</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-center small text-muted">
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th class="text-start">Tên sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orderDetails)): ?>
                                    <?php $stt = 1; ?>
                                    <?php foreach ($orderDetails as $item): ?>
                                        <tr class="text-center">
                                            <td class="text-muted"><?= $stt++ ?></td>
                                            <td class="text-start fw-bold"><?= htmlspecialchars($item->productName ?? ('Sản phẩm #' . $item->productId)) ?></td>
                                            <td><?= number_format($item->price, 0, ',', '.') ?> đ</td>
                                            <td><span class="badge bg-light text-dark border"><?= $item->quantity ?></span></td>
                                            <td class="text-end fw-bold text-danger"><?= number_format($item->subtotal, 0, ',', '.') ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nút Lưu & Hủy -->
            <div class="d-flex justify-content-end gap-2 mt-4 mb-5">
                <a href="<?= BASE_URL ?>admin/order/detail/<?= $order->id ?>" class="btn btn-secondary px-4">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="fas fa-save me-2"></i> Lưu thay đổi đơn hàng
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingInput = document.getElementById('shippingFeeInput');
    const itemsSubtotalEl = document.getElementById('itemsSubtotalDisplay');
    const shippingDisplayEl = document.getElementById('shippingFeeDisplay');
    const grandTotalEl = document.getElementById('grandTotalDisplay');

    if (shippingInput && itemsSubtotalEl && shippingDisplayEl && grandTotalEl) {
        const subtotal = parseFloat(itemsSubtotalEl.getAttribute('data-subtotal')) || 0;

        function formatCurrency(number) {
            return new Intl.NumberFormat('vi-VN').format(number) + ' đ';
        }

        shippingInput.addEventListener('input', function() {
            let fee = parseFloat(this.value) || 0;
            if (fee < 0) fee = 0;
            const total = subtotal + fee;

            shippingDisplayEl.textContent = formatCurrency(fee);
            grandTotalEl.textContent = formatCurrency(total);
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>
