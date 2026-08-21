<div class="container my-4">
    <h2 class="mb-4"><i class="bi bi-credit-card"></i> Thanh toán đơn hàng</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>cart/checkout" method="POST" id="checkoutOrderForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- CỘT THÔNG TIN NHẬN HÀNG & THANH TOÁN -->
            <div class="col-md-7">
                <div class="card shadow-sm p-4 mb-4 border-0 rounded-4 bg-white">
                    <h4 class="mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Thông tin nhận hàng</h4>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" required placeholder="Nhập họ và tên của bạn" value="<?= htmlspecialchars($_SESSION['client_user']['fullName'] ?? $_SESSION['client_user']['fullname'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" required placeholder="Nhập số điện thoại liên hệ" value="<?= htmlspecialchars($_SESSION['client_user']['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email nhận xác nhận (tùy chọn)</label>
                            <input type="email" name="email" class="form-control" placeholder="Để nhận hóa đơn điện tử" value="<?= htmlspecialchars($_SESSION['client_user']['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."><?= htmlspecialchars($_SESSION['client_user']['address'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú đơn hàng (tùy chọn)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú về thời gian hoặc địa điểm giao hàng chi tiết hơn..."></textarea>
                    </div>

                    <h5 class="mb-3 mt-4 text-primary"><i class="bi bi-wallet2 me-2"></i>Hình thức thanh toán</h5>
                    <div class="p-3 border rounded-3 bg-light mb-3 d-flex flex-column gap-2">
                        <!-- 1. VNPAY -->
                        <div class="form-check p-3 rounded-3 border bg-white shadow-sm position-relative">
                            <input class="form-check-input" type="radio" name="payment_method" id="payVNPAY" value="VNPAY" checked>
                            <label class="form-check-label fw-bold d-flex flex-wrap align-items-center justify-content-between cursor-pointer w-100" for="payVNPAY">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary px-2.5 py-1 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">VNPAY</span>
                                    <span>Thanh toán online qua VNPAY</span>
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small px-2 py-0.5" style="font-size: 0.7rem;">Khuyên dùng</span>
                            </label>
                            <div class="form-text small text-muted mt-1 ps-4">
                                Quét mã VNPAY-QR, Thẻ ATM nội địa tất cả ngân hàng, Thẻ quốc tế Visa/Mastercard/JCB.
                            </div>
                        </div>

                        <!-- 2. COD -->
                        <div class="form-check p-3 rounded-3 border bg-white shadow-sm position-relative">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCOD" value="COD">
                            <label class="form-check-label fw-bold cursor-pointer" for="payCOD">
                                💵 Thanh toán khi nhận hàng (COD)
                            </label>
                            <div class="form-text small text-muted mt-1 ps-4">
                                Bạn sẽ thanh toán trực tiếp cho nhân viên giao hàng khi nhận sản phẩm.
                            </div>
                        </div>

                        <!-- 3. CHUYỂN KHOẢN -->
                        <div class="form-check p-3 rounded-3 border bg-white shadow-sm position-relative">
                            <input class="form-check-input" type="radio" name="payment_method" id="payBank" value="Chuyển khoản">
                            <label class="form-check-label fw-bold cursor-pointer" for="payBank">
                                💳 Chuyển khoản ngân hàng trực tiếp
                            </label>
                            <div class="form-text small text-muted mt-1 ps-4">
                                Chuyển khoản qua số tài khoản Vietcombank (Thông tin chi tiết hiển thị sau khi đặt hàng).
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT ĐƠN HÀNG, MÃ GIẢM GIÁ & TỔNG TIỀN -->
            <div class="col-md-5">
                <div class="card shadow-sm p-4 bg-light border-0 rounded-4 position-sticky" style="top: 85px;">
                    <h4 class="mb-3 text-dark"><i class="bi bi-bag-check me-2"></i>Đơn hàng của bạn</h4>
                    
                    <!-- Danh sách sản phẩm -->
                    <ul class="list-group mb-3 border-0">
                        <?php foreach ($cart as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm align-items-center bg-white border rounded-3 mb-2 p-2.5">
                                <div class="pe-2">
                                    <h6 class="my-0 text-dark fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($item['productname']) ?></h6>
                                    <?php if (!empty($item['variant_name'])): ?>
                                        <small class="text-primary d-block fw-semibold" style="font-size: 0.78rem;">[Phiên bản: <?= htmlspecialchars($item['variant_name']) ?>]</small>
                                    <?php endif; ?>
                                    <small class="text-muted">SL: <?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?> đ</small>
                                </div>
                                <span class="text-dark fw-bold text-nowrap"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- KHỐI MÃ GIẢM GIÁ (SHOPEE-STYLE VOUCHER) -->
                    <div class="card border rounded-3 p-3 bg-white mb-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-bold small text-dark d-flex align-items-center gap-1.5">
                                <i class="bi bi-ticket-perforated-fill text-danger fs-5"></i>
                                <span>MINISHOP Voucher</span>
                            </div>
                            <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#voucherModal">
                                Chọn Voucher <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Form nhập mã voucher -->
                        <div class="input-group mb-2">
                            <input type="text" 
                                   id="voucherInputCode" 
                                   class="form-control form-control-sm text-uppercase fw-bold" 
                                   placeholder="Nhập mã giảm giá..." 
                                   value="<?= htmlspecialchars($_SESSION['applied_coupon']['code'] ?? '') ?>"
                                   <?= !empty($_SESSION['applied_coupon']) ? 'disabled' : '' ?>>
                            
                            <button type="button" 
                                    id="btnApplyVoucher" 
                                    class="btn btn-danger btn-sm px-3 fw-semibold <?= !empty($_SESSION['applied_coupon']) ? 'd-none' : '' ?>">
                                Áp dụng
                            </button>

                            <button type="button" 
                                    id="btnRemoveVoucher" 
                                    class="btn btn-outline-secondary btn-sm px-3 fw-semibold <?= empty($_SESSION['applied_coupon']) ? 'd-none' : '' ?>">
                                Hủy mã
                            </button>
                        </div>

                        <!-- Thông báo voucher đang áp dụng -->
                        <div id="appliedVoucherNotice" class="p-2 rounded-2 bg-success-subtle border border-success-subtle small <?= !empty($_SESSION['applied_coupon']) ? '' : 'd-none' ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success me-1" id="appliedVoucherBadge">
                                        <?= htmlspecialchars($_SESSION['applied_coupon']['code'] ?? '') ?>
                                    </span>
                                    <span class="text-success fw-semibold" id="appliedVoucherDesc">
                                        <?= htmlspecialchars($_SESSION['applied_coupon']['name'] ?? '') ?>
                                    </span>
                                </div>
                                <span class="text-danger fw-bold" id="appliedVoucherSaveText">
                                    - <?= number_format($_SESSION['applied_coupon']['discount_amount'] ?? 0, 0, ',', '.') ?> đ
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php
                    $shippingFee = ($total >= 1000000) ? 0 : 30000;
                    $discountAmt = $discountAmount ?? 0;
                    $grandTotal = max(0, $total - $discountAmt + $shippingFee);
                    ?>

                    <!-- BẢNG TỔNG KẾT TIỀN (SHOPEE STYLE) -->
                    <ul class="list-group mb-3 border-0 shadow-sm rounded-3 overflow-hidden">
                        <li class="list-group-item d-flex justify-content-between bg-white py-2.5">
                            <span class="text-muted">Tổng tiền hàng:</span>
                            <span class="fw-semibold text-dark" id="displaySubtotal"><?= number_format($total, 0, ',', '.') ?> đ</span>
                        </li>
                        
                        <!-- Dòng giảm giá Voucher -->
                        <li class="list-group-item d-flex justify-content-between bg-white py-2.5 <?= $discountAmt > 0 ? '' : 'd-none' ?>" id="discountRow">
                            <span class="text-success fw-semibold">
                                <i class="bi bi-tag-fill me-1"></i>Giảm giá Voucher:
                            </span>
                            <span class="fw-bold text-success" id="displayDiscount">- <?= number_format($discountAmt, 0, ',', '.') ?> đ</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between bg-white py-2.5">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-semibold text-info" id="displayShipping">
                                <?= $shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . ' đ' : 'Miễn phí (Free Ship)' ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-white py-3 border-top">
                            <span class="fw-bold fs-5 text-dark">Tổng thanh toán:</span>
                            <strong class="text-danger fs-4" id="displayGrandTotal"><?= number_format($grandTotal, 0, ',', '.') ?> đ</strong>
                        </li>
                    </ul>

                    <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow rounded-3" id="btnConfirmOrder">
                        <i class="bi bi-check-circle me-1"></i> Xác nhận đặt hàng
                    </button>
                    <a href="<?= BASE_URL ?>cart" class="btn btn-outline-secondary w-100 mt-2 rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại giỏ hàng
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- POPUP MODAL CHỌN VOUCHER (SHOPEE-STYLE) -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 bg-light">
                <h5 class="modal-title fw-bold text-dark" id="voucherModalLabel">
                    <i class="bi bi-ticket-perforated-fill text-danger me-2"></i>Chọn MINISHOP Voucher
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <?php if (!empty($availableCoupons)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($availableCoupons as $avC): ?>
                            <?php
                            $isEligible = ($total >= $avC->minOrderAmount);
                            $isCurrentlyApplied = (!empty($_SESSION['applied_coupon']) && $_SESSION['applied_coupon']['code'] === $avC->code);
                            ?>
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between gap-2 transition-all <?= $isEligible ? 'bg-white border-danger-subtle shadow-sm' : 'bg-light opacity-75 border-secondary-subtle' ?>" style="border-left: 5px solid <?= $isEligible ? '#dc3545' : '#6c757d' ?> !important;">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-danger text-uppercase fw-bold px-2 py-1" style="font-size: 0.8rem;">
                                            <?= htmlspecialchars($avC->code) ?>
                                        </span>
                                        <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                            <?= htmlspecialchars($avC->name) ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <?= !empty($avC->description) ? htmlspecialchars($avC->description) : ($avC->discountType === 'percent' ? ('Giảm ' . $avC->discountValue . '%') : ('Giảm ' . number_format($avC->discountValue, 0, ',', '.') . ' đ')) ?>
                                    </div>
                                    <div class="small">
                                        <?php if ($avC->minOrderAmount > 0): ?>
                                            <span class="<?= $isEligible ? 'text-success' : 'text-danger' ?> fw-semibold">
                                                Đơn tối thiểu: <?= number_format($avC->minOrderAmount, 0, ',', '.') ?> đ
                                            </span>
                                        <?php else: ?>
                                            <span class="text-success fw-semibold">Áp dụng cho mọi đơn</span>
                                        <?php endif; ?>
                                        <?php if (!empty($avC->endDate)): ?>
                                            <span class="text-muted ms-2">• HSD: <?= date('d/m/Y', strtotime($avC->endDate)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="text-end text-nowrap">
                                    <?php if ($isCurrentlyApplied): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3 btn-remove-voucher-modal" data-bs-dismiss="modal">
                                            Đang dùng
                                        </button>
                                    <?php elseif ($isEligible): ?>
                                        <button type="button" class="btn btn-danger btn-sm fw-bold px-3 btn-apply-voucher-modal" data-code="<?= htmlspecialchars($avC->code) ?>" data-bs-dismiss="modal">
                                            Dùng ngay
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm disabled px-3" disabled>
                                            Chưa đủ ĐK
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-ticket-perforated fs-1 d-block mb-2 text-secondary"></i>
                        Hiện chưa có mã giảm giá nào khả dụng.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';
    const voucherInput = document.getElementById('voucherInputCode');
    const btnApply = document.getElementById('btnApplyVoucher');
    const btnRemove = document.getElementById('btnRemoveVoucher');
    const appliedNotice = document.getElementById('appliedVoucherNotice');
    const appliedBadge = document.getElementById('appliedVoucherBadge');
    const appliedDesc = document.getElementById('appliedVoucherDesc');
    const appliedSaveText = document.getElementById('appliedVoucherSaveText');
    const discountRow = document.getElementById('discountRow');
    const displayDiscount = document.getElementById('displayDiscount');
    const displayGrandTotal = document.getElementById('displayGrandTotal');

    function showToast(msg, isSuccess = true) {
        const toastEl = document.getElementById('liveToast');
        const toastMsg = document.getElementById('toastMessage');
        if (toastEl && toastMsg && typeof bootstrap !== 'undefined') {
            toastMsg.textContent = msg;
            toastEl.querySelector('.toast-header').className = isSuccess ? 'toast-header bg-success text-white' : 'toast-header bg-danger text-white';
            const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
            toast.show();
        } else {
            alert(msg);
        }
    }

    function executeApplyVoucher(code) {
        if (!code || code.trim() === '') {
            showToast('Vui lòng nhập mã giảm giá!', false);
            return;
        }

        const formData = new FormData();
        formData.append('code', code.trim().toUpperCase());
        if (typeof CSRF_TOKEN !== 'undefined') {
            formData.append('csrf_token', CSRF_TOKEN);
        }

        fetch(appBaseUrl + 'cart/applyCoupon', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (err) {
                console.error("Lỗi parse JSON apply coupon:", text);
                return { success: false, message: "Không thể áp dụng mã giảm giá. Vui lòng thử lại!" };
            }
        })
        .then(data => {
            if (data && data.success) {
                voucherInput.value = data.coupon.code;
                voucherInput.disabled = true;
                btnApply.classList.add('d-none');
                btnRemove.classList.remove('d-none');

                appliedBadge.textContent = data.coupon.code;
                appliedDesc.textContent = data.coupon.name;
                appliedSaveText.textContent = '- ' + new Intl.NumberFormat('vi-VN').format(data.discount_amount) + ' đ';
                appliedNotice.classList.remove('d-none');

                displayDiscount.textContent = '- ' + new Intl.NumberFormat('vi-VN').format(data.discount_amount) + ' đ';
                discountRow.classList.remove('d-none');

                displayGrandTotal.textContent = new Intl.NumberFormat('vi-VN').format(data.grand_total) + ' đ';
                showToast(data.message, true);
            } else {
                showToast(data.message || 'Mã giảm giá không hợp lệ!', false);
            }
        })
        .catch(err => {
            console.error('Lỗi apply coupon:', err);
            showToast('Có lỗi xảy ra khi kiểm tra mã giảm giá.', false);
        });
    }

    function executeRemoveVoucher() {
        const formData = new FormData();
        if (typeof CSRF_TOKEN !== 'undefined') {
            formData.append('csrf_token', CSRF_TOKEN);
        }

        fetch(appBaseUrl + 'cart/removeCoupon', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (err) {
                return { success: true, grand_total: 0 };
            }
        })
        .then(data => {
            voucherInput.value = '';
            voucherInput.disabled = false;
            btnApply.classList.remove('d-none');
            btnRemove.classList.add('d-none');
            appliedNotice.classList.add('d-none');
            discountRow.classList.add('d-none');

            displayGrandTotal.textContent = new Intl.NumberFormat('vi-VN').format(data.grand_total) + ' đ';
            showToast('Đã hủy áp dụng mã giảm giá.', true);
        })
        .catch(err => console.error('Lỗi remove coupon:', err));
    }

    if (btnApply) {
        btnApply.addEventListener('click', function () {
            executeApplyVoucher(voucherInput.value);
        });
    }

    if (btnRemove) {
        btnRemove.addEventListener('click', executeRemoveVoucher);
    }

    if (voucherInput) {
        voucherInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeApplyVoucher(voucherInput.value);
            }
        });
    }

    // Xử lý nút "Dùng ngay" trong modal Shopee Voucher
    document.querySelectorAll('.btn-apply-voucher-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            const code = this.dataset.code;
            if (code) {
                executeApplyVoucher(code);
            }
        });
    });

    document.querySelectorAll('.btn-remove-voucher-modal').forEach(btn => {
        btn.addEventListener('click', executeRemoveVoucher);
    });
});
</script>