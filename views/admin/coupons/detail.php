<?php
$pageTitle = "Chi tiết mã giảm giá #" . htmlspecialchars($coupon->code) . " - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-ticket-alt text-primary me-2"></i>Chi tiết Mã giảm giá</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/coupon">Danh sách mã giảm giá</a></li>
        <li class="breadcrumb-item active">Chi tiết #<?= htmlspecialchars($coupon->code) ?></li>
    </ol>

    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div><i class="fas fa-info-circle text-primary me-1"></i> Thông tin chi tiết: <strong class="text-primary fs-5"><?= htmlspecialchars($coupon->code) ?></strong></div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>admin/coupon/edit?id=<?= $coupon->id ?>" class="btn btn-warning text-white btn-sm px-3">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="<?= BASE_URL ?>admin/coupon" class="btn btn-secondary btn-sm px-3">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th class="bg-light" style="width: 35%;">Mã CODE</th>
                                <td>
                                    <span class="badge bg-primary fs-5 px-3 py-1.5 text-uppercase">
                                        <?= htmlspecialchars($coupon->code) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tên chương trình</th>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($coupon->name) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Loại giảm giá</th>
                                <td>
                                    <?= $coupon->discountType === 'percent' ? '<span class="badge bg-danger fs-6">Giảm theo %</span>' : '<span class="badge bg-success fs-6">Giảm tiền mặt</span>' ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Mức giảm giá</th>
                                <td class="text-danger fw-bold fs-5">
                                    <?= $coupon->discountType === 'percent' ? ($coupon->discountValue . ' %') : (number_format($coupon->discountValue, 0, ',', '.') . ' đ') ?>
                                    <?php if ($coupon->discountType === 'percent' && $coupon->maxDiscountAmount): ?>
                                        <small class="text-muted d-block font-normal fs-6 mt-1">Giảm tối đa: <?= number_format($coupon->maxDiscountAmount, 0, ',', '.') ?> đ</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Đơn hàng tối thiểu</th>
                                <td>
                                    <?= $coupon->minOrderAmount > 0 ? (number_format($coupon->minOrderAmount, 0, ',', '.') . ' đ') : '<span class="text-muted">Không yêu cầu (0 đ)</span>' ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th class="bg-light" style="width: 35%;">Trạng thái</th>
                                <td>
                                    <?php if ($coupon->status == 1): ?>
                                        <span class="badge bg-success fs-6 px-2.5 py-1">Đang kích hoạt</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-6 px-2.5 py-1">Tạm khóa</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Lượt sử dụng</th>
                                <td>
                                    <div class="fw-bold mb-1"><?= $coupon->usedCount ?> / <?= $coupon->usageLimit > 0 ? $coupon->usageLimit : 'Không giới hạn' ?> lượt</div>
                                    <?php if ($coupon->usageLimit > 0): ?>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: <?= min(100, round(($coupon->usedCount / $coupon->usageLimit) * 100)) ?>%;"></div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Ngày bắt đầu</th>
                                <td><?= !empty($coupon->startDate) ? date('d/m/Y H:i:s', strtotime($coupon->startDate)) : 'Áp dụng ngay' ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Ngày kết thúc (Hạn dùng)</th>
                                <td><?= !empty($coupon->endDate) ? date('d/m/Y H:i:s', strtotime($coupon->endDate)) : 'Vô thời hạn' ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Ngày tạo mã</th>
                                <td class="text-muted small"><?= htmlspecialchars($coupon->createdAt) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-primary me-1"></i> Mô tả / Điều kiện áp dụng:</h6>
                    <div class="p-3 bg-light rounded-3 text-muted">
                        <?= !empty($coupon->description) ? nl2br(htmlspecialchars($coupon->description)) : 'Không có mô tả chi tiết.' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
