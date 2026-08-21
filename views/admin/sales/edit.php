<?php
$pageTitle = "Hiệu chỉnh sản phẩm giảm giá - Mini Shop";
?>

<div class="container-fluid px-4 py-3">
    <!-- Tiêu đề trang -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Hiệu chỉnh sản phẩm giảm giá</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/sale" class="text-decoration-none">Sản phẩm giảm giá</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hiệu chỉnh #<?= $sale->id ?></li>
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
            <h6 class="m-0 fw-bold">Thông tin chương trình giảm giá: <?= htmlspecialchars($sale->productName ?? '') ?></h6>
        </div>
        <div class="card-body p-4">
            <form action="<?= BASE_URL ?>admin/sale/edit?id=<?= $sale->id ?>" method="POST" id="saleForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $sale->id ?>">

                <div class="row g-4">
                    <!-- Cột trái: Chọn sản phẩm & Mức giảm -->
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sản phẩm áp dụng <span class="text-danger">*</span></label>
                            <select name="product_id" id="productSelect" class="form-select" required onchange="calculateSalePrice()">
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-price="<?= (float)$p['price'] ?>" data-name="<?= htmlspecialchars($p['proname']) ?>" <?= ($sale->productId == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['proname']) ?> (Giá gốc: <?= number_format($p['price'], 0, ',', '.') ?> đ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mức giảm giá (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="discount_percent" id="discountPercent" class="form-control" min="1" max="99" step="1" value="<?= (int)$sale->discountPercent ?>" required oninput="calculateSalePrice()">
                                <span class="input-group-text fw-bold">%</span>
                            </div>
                            <div class="form-text">Nhập tỉ lệ % muốn giảm từ 1% đến 99%.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên chương trình / Ghi chú đợt Sale</label>
                            <input type="text" name="description" class="form-control" placeholder="Ví dụ: Flash Sale Mùa Hè, Ưu đãi cuối tuần..." value="<?= htmlspecialchars($sale->description ?? '') ?>">
                        </div>
                    </div>

                    <!-- Cột phải: Tính toán thời gian & Kết quả tự động -->
                    <div class="col-lg-6">
                        <!-- Khung xem trước giá tự động tính -->
                        <div class="card bg-light border p-3 mb-3">
                            <h6 class="fw-bold text-dark mb-3">Kết quả tính giá tự động</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Giá gốc sản phẩm:</span>
                                <span class="fw-bold" id="previewOrigPrice"><?= number_format($sale->productPrice, 0, ',', '.') ?> đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tỉ lệ giảm giá:</span>
                                <span class="fw-bold text-danger" id="previewPercent"><?= (int)$sale->discountPercent ?>%</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Số tiền giảm (Tiết kiệm):</span>
                                <span class="fw-bold text-success" id="previewSaveAmount">-<?= number_format($sale->productPrice - $sale->salePrice, 0, ',', '.') ?> đ</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">Giá bán sau khi giảm:</span>
                                <span class="fw-bold text-danger fs-5" id="previewSalePrice"><?= number_format($sale->salePrice, 0, ',', '.') ?> đ</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Ngày bắt đầu</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= !empty($sale->startDate) ? date('Y-m-d', strtotime($sale->startDate)) : '' ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Ngày kết thúc</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= !empty($sale->endDate) ? date('Y-m-d', strtotime($sale->endDate)) : '' ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Trạng thái áp dụng</label>
                            <select name="status" class="form-select">
                                <option value="1" <?= $sale->status == 1 ? 'selected' : '' ?>>Kích hoạt (Áp dụng ngay vào giá bán)</option>
                                <option value="0" <?= $sale->status == 0 ? 'selected' : '' ?>>Tạm dừng (Chưa áp dụng)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">Cập nhật thay đổi</button>
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
