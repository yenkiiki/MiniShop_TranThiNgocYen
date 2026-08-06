<?php
// 1. Đặt tiêu đề trang (Header sẽ nhận biến này)
$pageTitle = "Bảng Điều Khiển Tổng Quan";

// 2. Bắt đầu bộ nhớ đệm
ob_start();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h3 class="fw-bold text-primary"><i class="fa-solid fa-gauge me-2"></i>Bảng Điều Khiển Tổng Quan</h3>
    <span class="text-muted"><i class="fa-regular fa-calendar-alt me-1"></i>Hôm nay: <?= date('d/m/Y') ?></span>
</div>

<!-- THỐNG KÊ TỔNG QUAN (CARDS) -->
<div class="row g-3 mb-4">
    <!-- Danh mục -->
    <div class="col-md-4 col-lg-2">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase fw-semibold">Danh mục</small>
                        <h3 class="mb-0 fw-bold mt-1"><?= number_format($totalCategories ?? 0) ?></h3>
                    </div>
                    <i class="fa-solid fa-layer-group fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Thương hiệu -->
    <div class="col-md-4 col-lg-2">
        <div class="card bg-secondary text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase fw-semibold">Thương hiệu</small>
                        <h3 class="mb-0 fw-bold mt-1"><?= number_format($totalBrands ?? 0) ?></h3>
                    </div>
                    <i class="fa-solid fa-copyright fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Sản phẩm -->
    <div class="col-md-4 col-lg-2">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase fw-semibold">Sản phẩm</small>
                        <h3 class="mb-0 fw-bold mt-1"><?= number_format($totalProducts ?? 0) ?></h3>
                    </div>
                    <i class="fa-solid fa-box-open fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Khách hàng -->
    <div class="col-md-4 col-lg-2">
        <div class="card bg-info text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase fw-semibold">Khách hàng</small>
                        <h3 class="mb-0 fw-bold mt-1"><?= number_format($totalCustomers ?? 0) ?></h3>
                    </div>
                    <i class="fa-solid fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Đơn hàng -->
    <div class="col-md-4 col-lg-2">
        <div class="card bg-warning text-dark shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase fw-semibold">Đơn hàng</small>
                        <h3 class="mb-0 fw-bold mt-1"><?= number_format($totalOrders ?? 0) ?></h3>
                    </div>
                    <i class="fa-solid fa-file-invoice-dollar fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Doanh thu -->
    <div class="col-md-4 col-lg-2">
        <div class="card bg-danger text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase fw-semibold">Doanh thu</small>
                        <h4 class="mb-0 fw-bold mt-1"><?= number_format($totalRevenue ?? 0, 0, ',', '.') ?>đ</h4>
                    </div>
                    <i class="fa-solid fa-sack-dollar fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DỮ LIỆU MỚI NHẤT (TABLES) -->
<div class="row g-4">
    <!-- Top 5 Sản phẩm mới nhất -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-box text-success me-2"></i>Sản phẩm mới nhất</h5>
                <a href="index.php?view=products" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá bán</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($latestProducts)): ?>
                                <?php foreach ($latestProducts as $p): ?>
                                    <tr>
                                        <td>
                                            <img src="../../uploads/products/<?= htmlspecialchars($p->getImage()) ?>" 
                                                 class="rounded border" 
                                                 width="40" 
                                                 height="40" 
                                                 style="object-fit: cover;" 
                                                 alt="img" 
                                                 onerror="this.onerror=null; this.src='https://placehold.co/40x40?text=No+Img';">
                                        </td>
                                        <td class="fw-semibold text-truncate" style="max-width: 180px;">
                                            <?= htmlspecialchars($p->getProname()) ?>
                                        </td>
                                        <td class="text-danger fw-bold"><?= number_format($p->getPrice(), 0, ',', '.') ?>đ</td>
                                        <td><span class="badge bg-secondary"><?= $p->getQuantity() ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">Chưa có sản phẩm nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Đơn hàng mới nhất -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-cart-shopping text-warning me-2"></i>Đơn hàng mới nhất</h5>
                <a href="index.php?view=orders" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($latestOrders)): ?>
                                <?php foreach ($latestOrders as $o): ?>
                                    <tr>
                                        <td><span class="badge bg-dark"><?= htmlspecialchars($o->getOrderCode()) ?></span></td>
                                        <td class="text-success fw-bold"><?= number_format($o->getTotalAmount(), 0, ',', '.') ?>đ</td>
                                        <td>
                                            <?php
                                            $st = $o->getStatus();
                                            if ($st == 0) echo '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
                                            elseif ($st == 1) echo '<span class="badge bg-success">Hoàn thành</span>';
                                            else echo '<span class="badge bg-danger">Đã hủy</span>';
                                            ?>
                                        </td>
                                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($o->getCreatedAt())) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">Chưa có đơn hàng nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// 3. Lấy nội dung gán vào $content và xả đệm
$content = ob_get_clean();

// 4. Nhúng master layout
include __DIR__ . '/layouts/master.php';
?>