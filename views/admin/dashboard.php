<?php
ob_start();
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tổng quan hệ thống quản trị Mini Shop</li>
    </ol>

    <div class="row">
        <!-- Tổng Danh Mục -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="small text-white-50">Tổng Danh Mục</div>
                    <div class="fs-4 fw-bold"><?= $totalCategories ?? 0 ?></div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/MINISHOP_TRANTHINGOCYEN/admin/category">Chi
                        tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Tổng Thương Hiệu -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="small text-white-50">Tổng Thương Hiệu</div>
                    <div class="fs-4 fw-bold"><?= $totalBrands ?? 0 ?></div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/MINISHOP_TRANTHINGOCYEN/admin/brand">Chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Tổng Sản Phẩm -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark mb-4">
                <div class="card-body">
                    <div class="small text-black-50">Tổng Sản Phẩm</div>
                    <div class="fs-4 fw-bold"><?= $totalProducts ?? 0 ?></div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link" href="/MINISHOP_TRANTHINGOCYEN/admin/product">Chi tiết</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Tổng Đơn Hàng -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="small text-white-50">Tổng Đơn Hàng</div>
                    <div class="fs-4 fw-bold"><?= $totalOrders ?? 0 ?></div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/MINISHOP_TRANTHINGOCYEN/admin/order">Chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Tổng Khách Hàng -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="small text-white-50">Tổng Khách Hàng</div>
                    <div class="fs-4 fw-bold"><?= $totalCustomers ?? 0 ?></div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/MINISHOP_TRANTHINGOCYEN/admin/customer">Chi
                        tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bảng sản phẩm mới nhất -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-box me-1"></i> 05 Sản phẩm mới nhất</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($latestProducts)):
                                    foreach ($latestProducts as $p): ?>
                                        <tr>
                                            <td><?= $p->id ?></td>
                                            <td>
                                                <?php if (!empty($p->image)): ?>
                                                    <img src="<?= BASE_URL . 'uploads/products/' . $p->image ?>"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted">No image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-start"><?= htmlspecialchars($p->proName) ?></td>
                                            <td><?= number_format($p->price, 0, ',', '.') ?> đ</td>
                                            <td><?= $p->quantity ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="5">Chưa có sản phẩm nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng đơn hàng mới nhất -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-shopping-bag me-1"></i> 05 Đơn hàng mới nhất</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($latestOrders)):
                                    foreach ($latestOrders as $o): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($o->orderCode) ?></strong></td>
                                            <td><?= number_format($o->totalAmount, 0, ',', '.') ?> đ</td>
                                            <td>
                                                <?php if ($o->status == 1): ?>
                                                    <span class="badge bg-success">Đã xử lý</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $o->createdAt ?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="4">Chưa có đơn hàng nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        include __DIR__ . "/layouts/master.php";
        ?>