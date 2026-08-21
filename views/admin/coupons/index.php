<?php
$pageTitle = "Quản lý Mã giảm giá (Vouchers) - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fas fa-ticket-alt text-primary me-2"></i>Quản lý Mã giảm giá (Vouchers)</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh sách mã giảm giá</li>
    </ol>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center py-3 gap-2">
            <div>
                <i class="fas fa-table me-1 text-primary"></i> Danh sách mã giảm giá trong hệ thống (<strong><?= $totalRecords ?? 0 ?></strong> mã)
            </div>
            <div>
                <a href="<?= BASE_URL ?>admin/coupon/create" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tạo mã giảm giá mới
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Form Tìm kiếm & Giới hạn -->
            <form method="GET" action="<?= BASE_URL ?>admin/coupon/index" class="row g-3 mb-4">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo mã CODE hoặc tên voucher..." value="<?= htmlspecialchars($keyword ?? '') ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                        <?php if (!empty($keyword)): ?>
                            <a href="<?= BASE_URL ?>admin/coupon/index" class="btn btn-outline-secondary" title="Xóa tìm kiếm"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3 ms-auto text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <label class="small text-muted mb-0">Hiển thị:</label>
                        <select name="limit" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="10" <?= ($limit ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= ($limit ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                            <option value="30" <?= ($limit ?? 10) == 30 ? 'selected' : '' ?>>30</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center mb-0">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th class="text-start" style="min-width: 140px;">Mã Voucher (CODE)</th>
                            <th class="text-start" style="min-width: 200px;">Tên & Mô tả chương trình</th>
                            <th>Mức giảm</th>
                            <th>Đơn tối thiểu</th>
                            <th>Lượt dùng</th>
                            <th>Thời hạn</th>
                            <th>Trạng thái</th>
                            <th style="width: 160px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($coupons)): ?>
                            <?php foreach ($coupons as $idx => $c): ?>
                                <?php
                                $now = date('Y-m-d H:i:s');
                                $isExpired = !empty($c->endDate) && $now > $c->endDate;
                                $isUpcoming = !empty($c->startDate) && $now < $c->startDate;
                                $isExhausted = ($c->usageLimit > 0 && $c->usedCount >= $c->usageLimit);
                                ?>
                                <tr>
                                    <td class="fw-bold text-muted"><?= $offset + $idx + 1 ?></td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-2.5 py-1 text-uppercase fw-bold">
                                                <?= htmlspecialchars($c->code) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($c->name) ?></div>
                                        <?php if (!empty($c->description)): ?>
                                            <small class="text-muted text-truncate d-block" style="max-width: 260px;">
                                                <?= htmlspecialchars($c->description) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c->discountType === 'percent'): ?>
                                            <span class="badge bg-danger fs-6 px-2.5 py-1">-<?= $c->discountValue ?>%</span>
                                            <?php if ($c->maxDiscountAmount): ?>
                                                <small class="text-muted d-block mt-1">Tối đa <?= number_format($c->maxDiscountAmount, 0, ',', '.') ?> đ</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success fs-6 px-2.5 py-1">-<?= number_format($c->discountValue, 0, ',', '.') ?> đ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c->minOrderAmount > 0): ?>
                                            <span class="fw-semibold text-dark"><?= number_format($c->minOrderAmount, 0, ',', '.') ?> đ</span>
                                        <?php else: ?>
                                            <span class="text-muted small">0 đ (Mọi đơn)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold small mb-1"><?= $c->usedCount ?> / <?= $c->usageLimit > 0 ? $c->usageLimit : '∞' ?></div>
                                        <?php if ($c->usageLimit > 0): ?>
                                            <?php $pctUsed = min(100, round(($c->usedCount / $c->usageLimit) * 100)); ?>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar <?= $pctUsed >= 100 ? 'bg-danger' : 'bg-info' ?>" style="width: <?= $pctUsed ?>%;"></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <?php if ($c->startDate): ?>
                                                <div><i class="far fa-calendar-alt text-muted me-1"></i><?= date('d/m/Y', strtotime($c->startDate)) ?></div>
                                            <?php endif; ?>
                                            <?php if ($c->endDate): ?>
                                                <div class="<?= $isExpired ? 'text-danger fw-bold' : 'text-muted' ?>">
                                                    ➔ <?= date('d/m/Y', strtotime($c->endDate)) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-success small">Vô thời hạn</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($c->status != 1): ?>
                                            <span class="badge bg-secondary">Tạm khóa</span>
                                        <?php elseif ($isExpired): ?>
                                            <span class="badge bg-danger">Hết hạn</span>
                                        <?php elseif ($isUpcoming): ?>
                                            <span class="badge bg-warning text-dark">Chưa bắt đầu</span>
                                        <?php elseif ($isExhausted): ?>
                                            <span class="badge bg-dark">Hết lượt dùng</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>admin/coupon/detail?id=<?= $c->id ?>" class="btn btn-info btn-sm text-white" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>admin/coupon/edit?id=<?= $c->id ?>" class="btn btn-warning btn-sm text-white" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $c->id ?>" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal Xác nhận xóa -->
                                        <div class="modal fade" id="deleteModal<?= $c->id ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $c->id ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content text-start">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel<?= $c->id ?>"><i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa Voucher</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body py-4">
                                                        Bạn có chắc chắn muốn xóa mã giảm giá <strong class="text-danger"><?= htmlspecialchars($c->code) ?></strong> (<?= htmlspecialchars($c->name) ?>)?
                                                        <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy bỏ</button>
                                                        <form method="POST" action="<?= BASE_URL ?>admin/coupon/index">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="id" value="<?= $c->id ?>">
                                                            <button type="submit" name="btnDelete" class="btn btn-danger btn-sm px-3">Xác nhận xóa</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-muted py-4">Không tìm thấy mã giảm giá nào phù hợp.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>admin/coupon/index?page=<?= $page - 1 ?>&limit=<?= $limit ?>&keyword=<?= urlencode($keyword) ?>">Trước</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>admin/coupon/index?page=<?= $i ?>&limit=<?= $limit ?>&keyword=<?= urlencode($keyword) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>admin/coupon/index?page=<?= $page + 1 ?>&limit=<?= $limit ?>&keyword=<?= urlencode($keyword) ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>
