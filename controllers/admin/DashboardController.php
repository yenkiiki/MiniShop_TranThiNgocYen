<?php
namespace Controllers\Admin;

use Middleware\AuthMiddleware;
use DAO\CategoryDAO; 
use DAO\ProductDAO;
use DAO\BrandDAO;
use DAO\CustomerDAO;
use DAO\OrderDAO;

class DashboardController {
    public function index() {
        // 1. Kiểm tra đăng nhập đầu tiên
        AuthMiddleware::handle();

        // 2. Lấy tất cả dữ liệu thống kê từ DAO trước
        $totalCategories = (new CategoryDAO())->countAll();
        $totalBrands = (new BrandDAO())->countAll();
        $totalProducts = (new ProductDAO())->countAll();
        $totalCustomers = (new CustomerDAO())->countAll();
        $totalOrders = (new OrderDAO())->countAll();

        $latestProducts = (new ProductDAO())->getLatest(5);
        $latestOrders = (new OrderDAO())->getLatest(5);

        $pageTitle = "Dashboard - Mini Shop";

        // 3. Sau khi đã có đủ biến dữ liệu, mới gọi view để hiển thị
        require_once __DIR__ . "/../../views/admin/dashboard.php";
    }
}