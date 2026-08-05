<?php
// 1. Nhúng các file DAO từ thư mục gốc (lùi ra 2 cấp thư mục ../../)
require_once __DIR__ . '/../../dao/CategoryDAO.php';
require_once __DIR__ . '/../../dao/BrandDAO.php';
require_once __DIR__ . '/../../dao/ProductDAO.php';
require_once __DIR__ . '/../../dao/CustomerDAO.php';
require_once __DIR__ . '/../../dao/UserDAO.php';
require_once __DIR__ . '/../../dao/OrderDAO.php';

// 2. Lấy tham số 'view' từ URL (mặc định là 'dashboard')
$view = $_GET['view'] ?? 'dashboard';

try {
    switch ($view) {
        case 'categories':
            $categoryDAO = new CategoryDAO();
            $categories = $categoryDAO->getAll();
            $contentView = __DIR__ . '/categories/index.php';
            break;

        case 'brands':
            $brandDAO = new BrandDAO();
            $brands = $brandDAO->getAll();
            $contentView = __DIR__ . '/brands/index.php';
            break;

        case 'products':
            $productDAO = new ProductDAO();
            $products = $productDAO->getAll();
            $contentView = __DIR__ . '/products/index.php';
            break;

        case 'customers':
            $customerDAO = new CustomerDAO();
            $customers = $customerDAO->getAll();
            $contentView = __DIR__ . '/customers/index.php';
            break;

        case 'users':
            $userDAO = new UserDAO();
            $users = $userDAO->getAll();
            $contentView = __DIR__ . '/users/index.php';
            break;

        case 'orders':
            $orderDAO = new OrderDAO();
            $orders = $orderDAO->getAll();
            $contentView = __DIR__ . '/orders/index.php';
            break;

        case 'dashboard':
        default:
            // Khởi tạo các DAO phục vụ dữ liệu Dashboard
            $categoryDAO = new CategoryDAO();
            $brandDAO = new BrandDAO();
            $productDAO = new ProductDAO();
            $customerDAO = new CustomerDAO();
            $orderDAO = new OrderDAO();

            // 1. Thống kê số lượng & Doanh thu
            $totalCategories = $categoryDAO->count();
            $totalBrands = $brandDAO->count();
            $totalProducts = $productDAO->count();
            $totalCustomers = $customerDAO->count();
            $totalOrders = $orderDAO->count();
            $totalRevenue = $orderDAO->getTotalRevenue();

            // 2. Lấy danh sách 5 sản phẩm & 5 đơn hàng mới nhất
            $latestProducts = $productDAO->getLatest(5);
            $latestOrders = $orderDAO->getLatest(5);

            $contentView = __DIR__ . '/dashboard.php';
            break;
    }
} catch (Exception $e) {
    echo "<div style='color:red; padding:20px;'>Lỗi kết nối hoặc truy vấn CSDL: " . $e->getMessage() . "</div>";
    exit();
}

// 3. Nhúng duy nhất Master Layout của Admin ở đây
require_once __DIR__ . '/layouts/master.php';