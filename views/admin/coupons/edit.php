<?php
$pageTitle = "Chỉnh sửa mã giảm giá #" . htmlspecialchars($coupon->code) . " - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-edit text-warning me-2"></i>Chỉnh sửa Mã giảm giá</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/coupon">Danh sách mã giảm giá</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa #<?= htmlspecialchars($coupon->code) ?></li>
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
            <div><i class="fas fa-ticket-alt text-primary me-1"></i> Chỉnh sửa voucher: <strong class="text-primary"><?= htmlspecialchars($coupon->code) ?></strong></div>
            <a href="<?= BASE_URL ?>admin/coupon" class="btn btn-secondary btn-sm px-3">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="<?= BASE_URL ?>admin/coupon/edit?id=<?= $coupon->id ?>" class="row g-3">
                <?= csrf_field() ?>
                <!-- Mã Code -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mã Voucher (CODE) <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="code" 
                           class="form-control text-uppercase fw-bold" 
                           placeholder="VD: SALE50K..." 
                           value="<?= htmlspecialchars($_POST['code'] ?? $coupon->code) ?>" 
                           required>
                </div>

                <!-- Tên voucher -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên chương trình voucher <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($_POST['name'] ?? $coupon->name) ?>" 
                           required>
                </div>

                <!-- Mô tả chi tiết -->
                <div class="col-12">
                    <label class="form-label fw-bold">Mô tả chi tiết / Điều kiện áp dụng</label>
                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($_POST['description'] ?? $coupon->description) ?></textarea>
                </div>

                <!-- Loại giảm giá -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Loại giảm giá <span class="text-danger">*</span></label>
                    <select name="discount_type" id="discountTypeSelect" class="form-select" required>
                        <option value="fixed" <?= ($_POST['discount_type'] ?? $coupon->discountType) === 'fixed' ? 'selected' : '' ?>>💵 Giảm số tiền cố định (VNĐ)</option>
                        <option value="percent" <?= ($_POST['discount_type'] ?? $coupon->discountType) === 'percent' ? 'selected' : '' ?>>📊 Giảm theo phần trăm (%)</option>
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
                               min="1" 
                               step="any" 
                               value="<?= htmlspecialchars($_POST['discount_value'] ?? $coupon->discountValue) ?>" 
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
                           placeholder="VD: 50000" 
                           min="0" 
                           step="1000" 
                           value="<?= htmlspecialchars($_POST['max_discount_amount'] ?? ($coupon->maxDiscountAmount ?? '')) ?>">
                </div>

                <!-- Đơn hàng tối thiểu -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" 
                           name="min_order_amount" 
                           class="form-control" 
                           min="0" 
                           step="1000" 
                           value="<?= htmlspecialchars($_POST['min_order_amount'] ?? $coupon->minOrderAmount) ?>">
                </div>

                <!-- Giới hạn lượt dùng -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giới hạn số lượt sử dụng</label>
                    <input type="number" 
                           name="usage_limit" 
                           class="form-control" 
                           min="0" 
                           value="<?= htmlspecialchars($_POST['usage_limit'] ?? $coupon->usageLimit) ?>">
                </div>

                <!-- Đã sử dụng -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Số lượt đã dùng</label>
                    <input type="number" 
                           name="used_count" 
                           class="form-control" 
                           min="0" 
                           value="<?= htmlspecialchars($_POST['used_count'] ?? $coupon->usedCount) ?>">
                </div>

                <!-- Ngày bắt đầu -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày bắt đầu</label>
                    <input type="datetime-local" 
                           name="start_date" 
                           class="form-control" 
                           value="<?= htmlspecialchars(!empty($_POST['start_date']) ? $_POST['start_date'] : (!empty($coupon->startDate) ? date('Y-m-d\TH:i', strtotime($coupon->startDate)) : '')) ?>">
                </div>

                <!-- Ngày kết thúc -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày kết thúc (Hạn dùng)</label>
                    <input type="datetime-local" 
                           name="end_date" 
                           class="form-control" 
                           value="<?= htmlspecialchars(!empty($_POST['end_date']) ? $_POST['end_date'] : (!empty($coupon->endDate) ? date('Y-m-d\TH:i', strtotime($coupon->endDate)) : '')) ?>">
                </div>

                <!-- Trạng thái -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Trạng thái kích hoạt</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($_POST['status'] ?? $coupon->status) == 1 ? 'selected' : '' ?>>✅ Kích hoạt (Mở dùng)</option>
                        <option value="0" <?= ($_POST['status'] ?? $coupon->status) == 0 ? 'selected' : '' ?>>⏸️ Tạm khóa</option>
                    </select>
                </div>

                <!-- Nút lưu -->
                <div class="col-12 mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-warning text-white px-4 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-save me-1"></i> Cập nhật thay đổi
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
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
