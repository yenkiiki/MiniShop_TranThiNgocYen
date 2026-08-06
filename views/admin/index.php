<?php
// Bật hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Nhúng tất cả DAO từ thư mục /dao gốc
require_once __DIR__ . '/../../dao/CategoryDAO.php';
require_once __DIR__ . '/../../dao/BrandDAO.php';
require_once __DIR__ . '/../../dao/ProductDAO.php';
require_once __DIR__ . '/../../dao/CustomerDAO.php';
require_once __DIR__ . '/../../dao/UserDAO.php';
require_once __DIR__ . '/../../dao/OrderDAO.php';

// 2. Lấy tham số 'view'
$view = $_GET['view'] ?? 'dashboard';

try {
    switch ($view) {
        case 'categories':
            $categoryDAO = new CategoryDAO();
            $categories = $categoryDAO->getAll();
            require_once __DIR__ . '/categories/index.php';
            break;

        case 'brands':
            $brandDAO = new BrandDAO();
            $brands = $brandDAO->getAll();
            require_once __DIR__ . '/brands/index.php';
            break;

        case 'products':
            $productDAO = new ProductDAO();
            $products = $productDAO->getAll();
            require_once __DIR__ . '/products/index.php';
            break;

        case 'customers':
            $customerDAO = new CustomerDAO();
            $customers = $customerDAO->getAll();
            require_once __DIR__ . '/customers/index.php';
            break;

        case 'users':
            $userDAO = new UserDAO();
            $users = $userDAO->getAll();
            require_once __DIR__ . '/users/index.php';
            break;

        case 'orders':
            $orderDAO = new OrderDAO();
            $orders = $orderDAO->getAll();
            require_once __DIR__ . '/orders/index.php';
            break;

        case 'dashboard':
        default:
            $categoryDAO = new CategoryDAO();
            $brandDAO = new BrandDAO();
            $productDAO = new ProductDAO();
            $customerDAO = new CustomerDAO();
            $orderDAO = new OrderDAO();

            $totalCategories = $categoryDAO->count();
            $totalBrands = $brandDAO->count();
            $totalProducts = $productDAO->count();
            $totalCustomers = $customerDAO->count();
            $totalOrders = $orderDAO->count();
            $totalRevenue = $orderDAO->getTotalRevenue();

            $latestProducts = $productDAO->getLatest(5);
            $latestOrders = $orderDAO->getLatest(5);

            require_once __DIR__ . '/dashboard.php';
            break;
    }
} catch (Exception $e) {
    echo "<div style='color:red; padding:20px;'>Lỗi kết nối hoặc truy vấn CSDL: " . $e->getMessage() . "</div>";
    exit();
}