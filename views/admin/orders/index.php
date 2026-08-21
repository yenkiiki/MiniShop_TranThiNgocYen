<?php
$pageTitle = "Quản lý đơn hàng - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4 py-3">
    <!-- Tiêu đề trang -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Quản lý đơn hàng</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Danh sách đơn hàng</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Thông báo -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Thẻ thống kê đơn giản -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Tổng đơn hàng</div>
                <div class="fs-4 fw-bold text-primary mt-1"><?= number_format($statistics['total'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Chờ xác nhận</div>
                <div class="fs-4 fw-bold text-warning mt-1"><?= number_format($statistics['pending'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Đang giao hàng</div>
                <div class="fs-4 fw-bold text-info mt-1"><?= number_format($statistics['shipping'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Doanh thu đã giao</div>
                <div class="fs-4 fw-bold text-success mt-1"><?= number_format($statistics['revenue'] ?? 0, 0, ',', '.') ?> đ</div>
            </div>
        </div>
    </div>

    <!-- Khối tìm kiếm & Lọc đơn hàng -->
    <div class="card border rounded shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>admin/order" class="row g-2 align-items-center">
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <div class="col-lg-4 col-md-6">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo mã đơn, họ tên, số điện thoại..." value="<?= htmlspecialchars($keyword) ?>">
                </div>

                <div class="col-lg-3 col-md-6">
                    <select name="search_status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($statusList as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($searchStatus !== "" && (string)$searchStatus === (string)$key) ? 'selected' : '' ?>>
                                <?= $value['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <select name="search_payment" class="form-select">
                        <option value="">-- Tất cả hình thức thanh toán --</option>
                        <option value="COD" <?= ($searchPayment === "COD") ? 'selected' : '' ?>>Thanh toán khi nhận hàng (COD)</option>
                        <option value="Chuyển khoản" <?= ($searchPayment === "Chuyển khoản" || $searchPayment === "ChuyenKhoan") ? 'selected' : '' ?>>Chuyển khoản ngân hàng</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Lọc đơn</button>
                    <a href="<?= BASE_URL ?>admin/order" class="btn btn-outline-secondary">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách đơn hàng -->
    <div class="card border rounded shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold">Danh sách đơn hàng (<?= $totalRecords ?> đơn)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th style="width: 50px;">STT</th>
                            <th>Mã đơn hàng</th>
                            <th class="text-start">Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Ngày đặt</th>
                            <th>Hình thức thanh toán</th>
                            <th>Phí ship</th>
                            <th class="text-end">Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th style="width: 170px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($orders as $od): ?>
                                <?php
                                $sttKey = (int) $od['status'];
                                $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary text-white'];
                                $rawPayment = $od['payment_method'] ?? 'COD';
                                if (empty($rawPayment)) {
                                    $rawPayment = (stripos($od['note'] ?? '', 'chuyển khoản') !== false) ? 'Chuyển khoản' : 'COD';
                                }
                                $isBank = ($rawPayment === 'ChuyenKhoan' || $rawPayment === 'Chuyển khoản' || stripos($rawPayment, 'khoản') !== false || stripos($rawPayment, 'bank') !== false);
                                ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>admin/order/detail/<?= $od['id'] ?>" class="fw-bold text-decoration-none">
                                            <?= htmlspecialchars($od['order_code']) ?>
                                        </a>
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?= htmlspecialchars($od['customer_name'] ?? 'Khách lẻ') ?></div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($od['customer_phone'] ?? '-') ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d/m/Y H:i', strtotime($od['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($isBank): ?>
                                            <span class="badge bg-primary text-white px-2 py-1">
                                                Chuyển khoản ngân hàng
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success text-white px-2 py-1">
                                                Thanh toán khi nhận hàng (COD)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $fee = (float)($od['shipping_fee'] ?? 0); ?>
                                        <?= $fee > 0 ? number_format($fee, 0, ',', '.') . ' đ' : '0 đ' ?>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        <?= number_format($od['total_amount'], 0, ',', '.') ?> đ
                                    </td>
                                    <td>
                                        <span class="badge <?= $badgeInfo['class'] ?> px-2 py-1">
                                            <?= $badgeInfo['label'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= BASE_URL ?>admin/order/detail/<?= $od['id'] ?>" class="btn btn-outline-info">
                                                Xem
                                            </a>
                                            <a href="<?= BASE_URL ?>admin/order/edit/<?= $od['id'] ?>" class="btn btn-outline-warning text-dark">
                                                Sửa
                                            </a>
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statusModal<?= $od['id'] ?>">
                                                Đổi TT
                                            </button>
                                        </div>

                                        <!-- Modal Cập Nhật Trạng Thái Nhanh -->
                                        <div class="modal fade text-start" id="statusModal<?= $od['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="<?= BASE_URL ?>admin/order/updateStatus" method="POST">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id" value="<?= $od['id'] ?>">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title fs-6 fw-bold">
                                                                Cập nhật trạng thái: <?= htmlspecialchars($od['order_code']) ?>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="mb-3 p-3 bg-light rounded border">
                                                                <div class="small mb-1">Khách hàng: <strong><?= htmlspecialchars($od['customer_name'] ?? 'Khách lẻ') ?></strong> (<?= htmlspecialchars($od['customer_phone'] ?? '-') ?>)</div>
                                                                <div class="small mb-1">
                                                                    Hình thức thanh toán: 
                                                                    <?php if ($isBank): ?>
                                                                        <span class="badge bg-primary text-white">Chuyển khoản ngân hàng</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success text-white">Thanh toán khi nhận hàng (COD)</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="small mb-1">Tổng tiền: <strong class="text-danger"><?= number_format($od['total_amount'], 0, ',', '.') ?> đ</strong></div>
                                                                <div class="small">Trạng thái hiện tại: <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span></div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small">Chọn trạng thái mới:</label>
                                                                <select name="status" class="form-select">
                                                                    <?php foreach ($statusList as $key => $value): ?>
                                                                        <option value="<?= $key ?>" <?= $od['status'] == $key ? 'selected' : '' ?>>
                                                                            <?= $value['label'] ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>

                                                            <?php if (!empty($od['customer_email'])): ?>
                                                                <div class="form-check form-switch mb-2">
                                                                    <input class="form-check-input" type="checkbox" name="send_email_notify" id="emailCheck<?= $od['id'] ?>" value="1" checked>
                                                                    <label class="form-check-label small" for="emailCheck<?= $od['id'] ?>">
                                                                        Gửi email cập nhật tới: <strong class="text-primary"><?= htmlspecialchars($od['customer_email']) ?></strong>
                                                                    </label>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-primary btn-sm px-3">Lưu thay đổi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    Không tìm thấy đơn hàng nào phù hợp với điều kiện tìm kiếm.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân trang & tùy chọn số dòng -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center g-2">
                <div class="col-md-4 d-flex align-items-center">
                    <label class="me-2 small text-muted text-nowrap">Hiển thị:</label>
                    <form method="GET" action="<?= BASE_URL ?>admin/order" class="d-inline-block">
                        <?php if (!empty($keyword)): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>
                        <?php if ($searchStatus !== ""): ?>
                            <input type="hidden" name="search_status" value="<?= $searchStatus ?>">
                        <?php endif; ?>
                        <?php if ($searchPayment !== ""): ?>
                            <input type="hidden" name="search_payment" value="<?= htmlspecialchars($searchPayment) ?>">
                        <?php endif; ?>
                        <select name="limit" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                            <option value="30" <?= $limit == 30 ? 'selected' : '' ?>>30</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </form>
                    <span class="ms-2 small text-muted">/ <?= $totalRecords ?> đơn</span>
                </div>

                <div class="col-md-8 d-flex justify-content-md-end justify-content-center">
                    <?php 
                    $pageParams = "";
                    if (!empty($keyword)) $pageParams .= '&keyword=' . urlencode($keyword);
                    if ($searchStatus !== "") $pageParams .= '&search_status=' . $searchStatus;
                    if ($searchPayment !== "") $pageParams .= '&search_payment=' . urlencode($searchPayment);
                    ?>
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>admin/order?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= $pageParams ?>">
                                        Trước
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= BASE_URL ?>admin/order?limit=<?= $limit ?>&page=<?= $i ?><?= $pageParams ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>admin/order?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= $pageParams ?>">
                                        Sau
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>