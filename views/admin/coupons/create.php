<?php
$pageTitle = "Tạo mã giảm giá mới - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-plus-circle text-primary me-2"></i>Thêm Mã giảm giá mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/coupon">Danh sách mã giảm giá</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Vui lòng kiểm tra lại các thông tin sau:</h6>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div><i class="fas fa-ticket-alt text-primary me-1"></i> Thông tin mã giảm giá (Voucher)</div>
            <a href="<?= BASE_URL ?>admin/coupon" class="btn btn-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="<?= BASE_URL ?>admin/coupon/create" class="row g-3">
                <?= csrf_field() ?>
                <!-- Mã Code & Nút Random -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mã Voucher (CODE) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" 
                               name="code" 
                               id="couponCodeInput" 
                               class="form-control text-uppercase fw-bold" 
                               placeholder="VD: SALE50K, FREESHIP..." 
                               value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" 
                               required>
                        <button type="button" class="btn btn-outline-primary" id="btnGenCode">
                            <i class="fas fa-magic me-1"></i> Tạo ngẫu nhiên
                        </button>
                    </div>
                    <small class="text-muted">Mã khách hàng sẽ nhập khi thanh toán (tự động chuyển chữ hoa).</small>
                </div>

                <!-- Tên voucher -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên chương trình voucher <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           class="form-control" 
                           placeholder="VD: Giảm 50.000 đ cho đơn hàng từ 300k" 
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                           required>
                </div>

                <!-- Mô tả chi tiết -->
                <div class="col-12">
                    <label class="form-label fw-bold">Mô tả chi tiết / Điều kiện áp dụng</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="VD: Áp dụng cho toàn bộ danh mục mỹ phẩm, không áp dụng cùng lúc với voucher khác..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <!-- Loại giảm giá -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Loại giảm giá <span class="text-danger">*</span></label>
                    <select name="discount_type" id="discountTypeSelect" class="form-select" required>
                        <option value="fixed" <?= ($_POST['discount_type'] ?? 'fixed') === 'fixed' ? 'selected' : '' ?>>💵 Giảm số tiền cố định (VNĐ)</option>
                        <option value="percent" <?= ($_POST['discount_type'] ?? '') === 'percent' ? 'selected' : '' ?>>📊 Giảm theo phần trăm (%)</option>
                    </select>
                </div>

                <!-- Giá trị giảm -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Mức giảm giá <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="discount_value" 
                               id="discountValueInput" 
                               class="form-control fw-bold text-danger" 
                               placeholder="VD: 50000 hoặc 15" 
                               min="1" 
                               step="any" 
                               value="<?= htmlspecialchars($_POST['discount_value'] ?? '') ?>" 
                               required>
                        <span class="input-group-text" id="discountValueUnit">VNĐ</span>
                    </div>
                </div>

                <!-- Giảm tối đa nếu là % -->
                <div class="col-md-4" id="maxDiscountBox" style="display: none;">
                    <label class="form-label fw-bold">Giảm tối đa (VNĐ)</label>
                    <input type="number" 
                           name="max_discount_amount" 
                           class="form-control" 
                           placeholder="VD: 50000 (Để trống nếu không giới hạn)" 
                           min="0" 
                           step="1000" 
                           value="<?= htmlspecialchars($_POST['max_discount_amount'] ?? '') ?>">
                </div>

                <!-- Đơn hàng tối thiểu -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giá trị đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" 
                           name="min_order_amount" 
                           class="form-control" 
                           placeholder="VD: 200000 (Để 0 nếu áp dụng mọi đơn)" 
                           min="0" 
                           step="1000" 
                           value="<?= htmlspecialchars($_POST['min_order_amount'] ?? '0') ?>">
                    <small class="text-muted">Khách phải mua đạt số tiền này thì mới áp dụng được voucher.</small>
                </div>

                <!-- Số lượt sử dụng tối đa -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giới hạn số lượt sử dụng</label>
                    <input type="number" 
                           name="usage_limit" 
                           class="form-control" 
                           placeholder="VD: 100 (Để 0 nếu không giới hạn)" 
                           min="0" 
                           value="<?= htmlspecialchars($_POST['usage_limit'] ?? '100') ?>">
                    <small class="text-muted">Tổng số lần mã có thể được sử dụng trong toàn hệ thống.</small>
                </div>

                <!-- Ngày bắt đầu -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày bắt đầu</label>
                    <input type="datetime-local" 
                           name="start_date" 
                           class="form-control" 
                           value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d\TH:i')) ?>">
                </div>

                <!-- Ngày kết thúc -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày kết thúc (Hạn dùng)</label>
                    <input type="datetime-local" 
                           name="end_date" 
                           class="form-control" 
                           value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d\T23:59', strtotime('+30 days'))) ?>">
                </div>

                <!-- Trạng thái -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Trạng thái kích hoạt</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($_POST['status'] ?? 1) == 1 ? 'selected' : '' ?>>✅ Kích hoạt (Mở dùng ngay)</option>
                        <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>⏸️ Tạm khóa</option>
                    </select>
                </div>

                <!-- Nút lưu -->
                <div class="col-12 mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1"></i> Lưu mã giảm giá
                    </button>
                    <a href="<?= BASE_URL ?>admin/coupon" class="btn btn-outline-secondary px-4 py-2">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('discountTypeSelect');
    const unitSpan = document.getElementById('discountValueUnit');
    const maxBox = document.getElementById('maxDiscountBox');
    const codeInput = document.getElementById('couponCodeInput');
    const btnGen = document.getElementById('btnGenCode');

    function updateTypeUI() {
        if (typeSelect.value === 'percent') {
            unitSpan.textContent = '%';
            maxBox.style.display = 'block';
        } else {
            unitSpan.textContent = 'VNĐ';
            maxBox.style.display = 'none';
        }
    }

    typeSelect.addEventListener('change', updateTypeUI);
    updateTypeUI();

    btnGen.addEventListener('click', function () {
        const prefix = ['MINI', 'SALE', 'VIP', 'VOUCHER', 'DISCOUNT'];
        const p = prefix[Math.floor(Math.random() * prefix.length)];
        const num = Math.floor(1000 + Math.random() * 9000);
        codeInput.value = p + num;
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
