<?php
$pageTitle = "Quản lý sản phẩm giảm giá - Mini Shop";
?>

<div class="container-fluid px-4 py-3">
    <!-- Tiêu đề trang -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Quản lý sản phẩm giảm giá (Sale)</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sản phẩm giảm giá</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= BASE_URL ?>admin/sale/create" class="btn btn-primary btn-sm px-3">
                + Thêm sản phẩm giảm giá
            </a>
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
                <div class="text-muted small text-uppercase fw-semibold">Tổng sản phẩm Sale</div>
                <div class="fs-4 fw-bold text-primary mt-1"><?= number_format($statistics['total'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Đang áp dụng</div>
                <div class="fs-4 fw-bold text-success mt-1"><?= number_format($statistics['active'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Tạm dừng</div>
                <div class="fs-4 fw-bold text-secondary mt-1"><?= number_format($statistics['inactive'] ?? 0) ?></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border rounded shadow-sm p-3">
                <div class="text-muted small text-uppercase fw-semibold">Mức giảm TB</div>
                <div class="fs-4 fw-bold text-danger mt-1"><?= $statistics['avg_discount'] ?? 0 ?>%</div>
            </div>
        </div>
    </div>

    <!-- Khối tìm kiếm & Lọc -->
    <div class="card border rounded shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>admin/sale" class="row g-2 align-items-center">
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <div class="col-lg-6 col-md-6">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm hoặc tên đợt sale..." value="<?= htmlspecialchars($keyword) ?>">
                </div>

                <div class="col-lg-4 col-md-6">
                    <select name="search_status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="1" <?= ($searchStatus !== "" && (string)$searchStatus === "1") ? 'selected' : '' ?>>Đang áp dụng</option>
                        <option value="0" <?= ($searchStatus !== "" && (string)$searchStatus === "0") ? 'selected' : '' ?>>Tạm dừng</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Lọc</button>
                    <a href="<?= BASE_URL ?>admin/sale" class="btn btn-outline-secondary">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách sản phẩm giảm giá -->
    <div class="card border rounded shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold">Danh sách chương trình giảm giá (<?= $totalRecords ?> mục)</h6>
            <a href="<?= BASE_URL ?>admin/sale/create" class="btn btn-primary btn-sm">
                + Thêm mới
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="table-light small text-muted">
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th style="width: 70px;">Ảnh</th>
                            <th class="text-start">Tên sản phẩm</th>
                            <th>Giá gốc</th>
                            <th>Mức giảm</th>
                            <th>Giá sau giảm</th>
                            <th>Tiết kiệm</th>
                            <th>Thời gian áp dụng</th>
                            <th>Trạng thái</th>
                            <th style="width: 170px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sales)): ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($sales as $s): ?>
                                <?php
                                $imgSrc = !empty($s->productImage) ? BASE_URL . "uploads/products/" . $s->productImage : "https://placehold.co/60x60?text=SP";
                                $saveAmount = $s->productPrice - $s->salePrice;
                                ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td>
                                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($s->productName ?? '') ?>" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://placehold.co/50x50?text=SP'">
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold"><?= htmlspecialchars($s->productName ?? 'Sản phẩm #' . $s->productId) ?></div>
                                        <?php if (!empty($s->description)): ?>
                                            <small class="text-muted d-block"><?= htmlspecialchars($s->description) ?></small>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($s->categoryName ?? '') ?> <?= !empty($s->brandName) ? ' | ' . htmlspecialchars($s->brandName) : '' ?>
                                        </small>
                                    </td>
                                    <td class="text-muted text-decoration-line-through">
                                        <?= number_format($s->productPrice, 0, ',', '.') ?> đ
                                    </td>
                                    <td>
                                        <span class="badge bg-danger text-white fs-6 px-2 py-1">
                                            -<?= (int)$s->discountPercent ?>%
                                        </span>
                                    </td>
                                    <td class="fw-bold text-danger fs-6">
                                        <?= number_format($s->salePrice, 0, ',', '.') ?> đ
                                    </td>
                                    <td class="text-success small fw-semibold">
                                        -<?= number_format($saveAmount, 0, ',', '.') ?> đ
                                    </td>
                                    <td class="small text-muted">
                                        <?php if (!empty($s->startDate) || !empty($s->endDate)): ?>
                                            <?= !empty($s->startDate) ? date('d/m/Y', strtotime($s->startDate)) : '...' ?> 
                                            đến 
                                            <?= !empty($s->endDate) ? date('d/m/Y', strtotime($s->endDate)) : '...' ?>
                                        <?php else: ?>
                                            Không giới hạn
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($s->status == 1): ?>
                                            <span class="badge bg-success text-white px-2 py-1">Đang áp dụng</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white px-2 py-1">Tạm dừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= BASE_URL ?>admin/sale/toggleStatus?id=<?= $s->id ?>" class="btn <?= $s->status == 1 ? 'btn-outline-secondary' : 'btn-outline-success' ?>" title="<?= $s->status == 1 ? 'Tạm dừng sale' : 'Kích hoạt sale' ?>">
                                                <?= $s->status == 1 ? 'Tắt' : 'Bật' ?>
                                            </a>
                                            <a href="<?= BASE_URL ?>admin/sale/edit?id=<?= $s->id ?>" class="btn btn-outline-warning text-dark">
                                                Sửa
                                            </a>
                                            <a href="<?= BASE_URL ?>admin/sale/delete?id=<?= $s->id ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa chương trình giảm giá cho sản phẩm này? Giá bán sẽ được khôi phục về giá gốc.')">
                                                Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    Không tìm thấy chương trình giảm giá nào.
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
                    <form method="GET" action="<?= BASE_URL ?>admin/sale" class="d-inline-block">
                        <?php if (!empty($keyword)): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>
                        <?php if ($searchStatus !== ""): ?>
                            <input type="hidden" name="search_status" value="<?= $searchStatus ?>">
                        <?php endif; ?>
                        <select name="limit" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                            <option value="30" <?= $limit == 30 ? 'selected' : '' ?>>30</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </form>
                    <span class="ms-2 small text-muted">/ <?= $totalRecords ?> mục</span>
                </div>

                <div class="col-md-8 d-flex justify-content-md-end justify-content-center">
                    <?php 
                    $pageParams = "";
                    if (!empty($keyword)) $pageParams .= '&keyword=' . urlencode($keyword);
                    if ($searchStatus !== "") $pageParams .= '&search_status=' . $searchStatus;
                    ?>
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>admin/sale?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= $pageParams ?>">
                                        Trước
                                    </a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= BASE_URL ?>admin/sale?limit=<?= $limit ?>&page=<?= $i ?><?= $pageParams ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>admin/sale?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= $pageParams ?>">
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
