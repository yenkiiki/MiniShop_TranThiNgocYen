<?php
// views/admin/orders/index.php
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý đơn hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh sách đơn hàng</li>
    </ol>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- FORM TÌM KIẾM VÀ BỘ LỌC -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-search me-1"></i> Tìm kiếm & Lọc đơn hàng</div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Giữ lại router controller và action -->
                <input type="hidden" name="controller" value="order">
                <input type="hidden" name="action" value="index">
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <div class="col-md-5">
                    <input type="text" name="keyword" class="form-control" placeholder="Mã đơn hàng hoặc tên khách hàng..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-md-4">
                    <select name="search_status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($statusList as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($searchStatus !== "" && $searchStatus == $key) ? 'selected' : '' ?>><?= $value['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i> Tìm</button>
                    <a href="index.php?controller=order&action=index" class="btn btn-secondary"><i class="fas fa-redo"></i> Làm mới</a>
                </div>
            </form>
        </div>
    </div>

    <!-- BẢNG HIỂN THỊ ĐƠN HÀNG -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-shopping-cart me-1"></i> Danh sách đơn hàng
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th class="text-start">Khách hàng</th>
                            <th class="text-start">Nhân viên xử lý</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($orders as $od): ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($od['order_code']) ?></td>
                                    <td class="text-start">
                                        <?= htmlspecialchars($od['customer_name'] ?? 'Khách lẻ / Khách vãng lai') ?>
                                    </td>
                                    <td class="text-start"><?= htmlspecialchars($od['user_name'] ?? 'Chưa phân công') ?></td>
                                    <td><?= $od['created_at'] ?></td>
                                    <td><span class="text-danger fw-bold"><?= number_format($od['total_amount'], 0, ',', '.') ?> đ</span></td>
                                    <td>
                                        <?php
                                        $sttKey = (int) $od['status'];
                                        $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="index.php?controller=order&action=detail&id=<?= $od['id'] ?>" class="btn btn-info text-white btn-sm" title="Chi tiết">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>

                                        <button type="button" class="btn btn-warning text-white btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#statusModal<?= $od['id'] ?>" title="Cập nhật trạng thái">
                                            <i class="fas fa-edit"></i> Trạng thái
                                        </button>

                                        <!-- Modal Cập nhật Trạng Thái -->
                                        <div class="modal fade text-start" id="statusModal<?= $od['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="index.php?controller=order&action=index" method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cập nhật trạng thái đơn: <?= htmlspecialchars($od['order_code']) ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $od['id'] ?>">
                                                            <div class="mb-3 p-2 bg-light border rounded">
                                                                <span class="fw-bold">Trạng thái hiện tại:</span>
                                                                <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Chọn trạng thái mới:</label>
                                                                <select name="status" class="form-select">
                                                                    <?php foreach ($statusList as $key => $value): ?>
                                                                        <option value="<?= $key ?>" <?= $od['status'] == $key ? 'selected' : '' ?>>
                                                                            <?= $value['label'] ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" name="btnUpdateStatus" class="btn btn-primary btn-sm">Lưu thay đổi</button>
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
                                <td colspan="8" class="text-center text-danger fw-bold py-4">Không tìm thấy dữ liệu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- THANH PHÂN TRANG VÀ CHỌN SỐ LƯỢNG HIỂN THỊ -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <label class="me-2">Hiển thị:</label>
                    <form method="GET">
                        <input type="hidden" name="controller" value="order">
                        <input type="hidden" name="action" value="index">
                        <?php if (!empty($keyword)): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>
                        <?php if ($searchStatus !== ""): ?>
                            <input type="hidden" name="search_status" value="<?= $searchStatus ?>">
                        <?php endif; ?>
                        <select name="limit" class="form-select" onchange="this.form.submit()">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                            <option value="30" <?= $limit == 30 ? 'selected' : '' ?>>30</option>
                        </select>
                    </form>
                </div>

                <!-- Thanh phân trang (Pagination) -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mb-0">
                        <ul class="pagination mb-0">
                            <!-- Nút Trước -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?controller=order&action=index&limit=<?= $limit ?>&page=<?= $page - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Trước</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?controller=order&action=index&limit=<?= $limit ?>&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Nút Sau -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?controller=order&action=index&limit=<?= $limit ?>&page=<?= $page + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Sau</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>