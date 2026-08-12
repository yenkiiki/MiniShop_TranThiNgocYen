<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . "/../../models/User.php";

require_once "../../config/Database.php";

// Gọi các DAO cần thiết
require_once __DIR__ . "/../../dao/BrandDAO.php";
require_once __DIR__ . "/../../dao/CategoryDAO.php";
require_once __DIR__ . "/../../dao/CustomerDAO.php";
require_once __DIR__ . "/../../dao/OrderDAO.php";
require_once __DIR__ . "/../../dao/ProductDAO.php";
require_once __DIR__ . "/../../dao/UserDAO.php";

// Khởi tạo các đối tượng DAO
$categoryDAO = new CategoryDAO();
$brandDAO = new BrandDAO();
$productDAO = new ProductDAO();
$customerDAO = new CustomerDAO();
$orderDAO = new OrderDAO();

// Lấy số liệu thống kê tổng quan
$totalCategories = $categoryDAO->countAll();
$totalBrands = $brandDAO->countAll();
$totalProducts = $productDAO->countAll();
$totalCustomers = $customerDAO->countAll();
$totalOrders = $orderDAO->countAll();

// Lấy danh sách 5 sản phẩm mới nhất và 5 đơn hàng mới nhất
$latestProducts = $productDAO->getLatest(5);
$latestOrders = $orderDAO->getLatest(5);

$pageTitle = "Dashboard - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tổng quan hệ thống quản trị Mini Shop</li>
    </ol>

    <!-- Các thẻ thống kê tổng quan (Widgets) -->
    <div class="row">
        <!-- Danh mục -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Tổng Danh Mục</div>
                            <div class="fs-4 fw-bold"><?= $totalCategories ?></div>
                        </div>
                        <i class="fas fa-list fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="categories/index.php">Chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Thương hiệu -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Tổng Thương Hiệu</div>
                            <div class="fs-4 fw-bold"><?= $totalBrands ?></div>
                        </div>
                        <i class="fas fa-tags fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="brands/index.php">Chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-black-50">Tổng Sản Phẩm</div>
                            <div class="fs-4 fw-bold"><?= $totalProducts ?></div>
                        </div>
                        <i class="fas fa-box-open fa-2x text-black-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link" href="products/index.php">Chi tiết</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Tổng Đơn Hàng</div>
                            <div class="fs-4 fw-bold"><?= $totalOrders ?></div>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="orders/index.php">Chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khách hàng thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Tổng Khách Hàng</div>
                            <div class="fs-4 fw-bold"><?= $totalCustomers ?></div>
                        </div>
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="customers/index.php">Chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng hiển thị dữ liệu mới nhất -->
    <div class="row">
        <!-- 5 Sản phẩm mới nhất -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-box me-1"></i>
                    05 Sản phẩm mới nhất
                </div>
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
                                <?php if (!empty($latestProducts)): ?>
                                    <?php foreach ($latestProducts as $p): ?>
                                        <tr>
                                            <td><?= $p->id ?></td>
                                            <td>
                                                <?php if (!empty($p->image)): ?>
                                                    <img src="<?= BASE_URL . 'uploads/products/' . $p->image ?>" alt=""
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted">No image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-start"><?= htmlspecialchars($p->proName) ?></td>
                                            <td><?= number_format($p->price, 0, ',', '.') ?> đ</td>
                                            <td><?= $p->quantity ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa có sản phẩm nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5 Đơn hàng mới nhất -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-shopping-bag me-1"></i>
                    05 Đơn hàng mới nhất
                </div>
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
                                <?php if (!empty($latestOrders)): ?>
                                    <?php foreach ($latestOrders as $o): ?>
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
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Chưa có đơn hàng nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "layouts/master.php";
?>