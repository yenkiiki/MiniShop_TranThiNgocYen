<?php
$pageTitle = "Thêm sản phẩm giảm giá - Mini Shop";
?>

<div class="container-fluid px-4 py-3">
    <!-- Tiêu đề trang -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Thêm sản phẩm giảm giá</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/sale" class="text-decoration-none">Sản phẩm giảm giá</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= BASE_URL ?>admin/sale" class="btn btn-outline-secondary btn-sm px-3">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border rounded shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold">Thông tin thiết lập giảm giá</h6>
        </div>
        <div class="card-body p-4">
            <form action="<?= BASE_URL ?>admin/sale/create" method="POST" id="saleForm">
                <?= csrf_field() ?>

                <div class="row g-4">
                    <!-- Cột trái: Chọn sản phẩm & Mức giảm -->
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn sản phẩm giảm giá <span class="text-danger">*</span></label>
                            <select name="product_id" id="productSelect" class="form-select" required onchange="calculateSalePrice()">
                                <option value="">-- Chọn sản phẩm cần giảm giá --</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-price="<?= (float)$p['price'] ?>" data-name="<?= htmlspecialchars($p['proname']) ?>" <?= (isset($_POST['product_id']) && $_POST['product_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['proname']) ?> (Giá gốc: <?= number_format($p['price'], 0, ',', '.') ?> đ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mức giảm giá (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="discount_percent" id="discountPercent" class="form-control" min="1" max="99" step="1" placeholder="Ví dụ: 10, 20, 50..." value="<?= htmlspecialchars($_POST['discount_percent'] ?? '10') ?>" required oninput="calculateSalePrice()">
                                <span class="input-group-text fw-bold">%</span>
                            </div>
                            <div class="form-text">Nhập tỉ lệ % muốn giảm từ 1% đến 99%.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên chương trình / Ghi chú đợt Sale</label>
                            <input type="text" name="description" class="form-control" placeholder="Ví dụ: Flash Sale Mùa Hè, Ưu đãi cuối tuần..." value="<?= htmlspecialchars($_POST['description'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- Cột phải: Tính toán thời gian & Kết quả tự động -->
                    <div class="col-lg-6">
                        <!-- Khung xem trước giá tự động tính -->
                        <div class="card bg-light border p-3 mb-3">
                            <h6 class="fw-bold text-dark mb-3">Kết quả tính giá tự động</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Giá gốc sản phẩm:</span>
                                <span class="fw-bold" id="previewOrigPrice">0 đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tỉ lệ giảm giá:</span>
                                <span class="fw-bold text-danger" id="previewPercent">0%</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Số tiền giảm (Tiết kiệm):</span>
                                <span class="fw-bold text-success" id="previewSaveAmount">0 đ</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">Giá bán sau khi giảm:</span>
                                <span class="fw-bold text-danger fs-5" id="previewSalePrice">0 đ</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Ngày bắt đầu</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Ngày kết thúc</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Trạng thái áp dụng</label>
                            <select name="status" class="form-select">
                                <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == '1') ? 'selected' : '' ?>>Kích hoạt (Áp dụng ngay vào giá bán)</option>
                                <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == '0') ? 'selected' : '' ?>>Tạm dừng (Chưa áp dụng)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">Lưu sản phẩm giảm giá</button>
                    <a href="<?= BASE_URL ?>admin/sale" class="btn btn-outline-secondary px-3">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
}

function calculateSalePrice() {
    const select = document.getElementById('productSelect');
    const percentInput = document.getElementById('discountPercent');
    const selectedOption = select.options[select.selectedIndex];
    
    let origPrice = 0;
    if (selectedOption && selectedOption.dataset.price) {
        origPrice = parseFloat(selectedOption.dataset.price) || 0;
    }
    
    let percent = parseFloat(percentInput.value) || 0;
    if (percent < 0) percent = 0;
    if (percent > 99) percent = 99;
    
    let saveAmount = origPrice * (percent / 100);
    let salePrice = origPrice - saveAmount;
    
    document.getElementById('previewOrigPrice').innerText = formatMoney(origPrice);
    document.getElementById('previewPercent').innerText = percent + '%';
    document.getElementById('previewSaveAmount').innerText = '-' + formatMoney(saveAmount);
    document.getElementById('previewSalePrice').innerText = formatMoney(salePrice);
}

document.addEventListener('DOMContentLoaded', calculateSalePrice);
</script>
