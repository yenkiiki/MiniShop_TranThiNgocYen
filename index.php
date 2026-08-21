<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Nạp autoload và khởi động session
require_once __DIR__ . '/autoload.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CẤU HÌNH BẢO MẬT CSRF ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $reqUri = strtolower($_SERVER['REQUEST_URI'] ?? '');
            if (strpos($reqUri, 'cart/add') !== false || strpos($reqUri, 'cart/update') !== false || strpos($reqUri, 'cart/remove') !== false) {
                return;
            }
            $submittedToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
            $sessionToken = $_SESSION['csrf_token'] ?? '';

            if (empty($sessionToken) || empty($submittedToken) || !hash_equals($sessionToken, $submittedToken)) {
                $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(["success" => false, "message" => "CSRF Token không hợp lệ!"]);
                    exit;
                }
                die("Lỗi bảo mật: CSRF Token không hợp lệ! Vui lòng làm mới trang (F5) và thử lại.");
            }
        }
    }
}

// --- XỬ LÝ URL THÂN THIỆN (.HTACCESS) VÀ QUERY STRING ---
$rawUri = $_SERVER['REQUEST_URI'] ?? '';
$path = $_GET['path'] ?? '';
$area = $_GET['area'] ?? null;
$controller = $_GET['controller'] ?? null;
$action = $_GET['action'] ?? null;

// Tự động nhận diện area=admin nếu URL chứa /admin
if (!$area) {
    if (stripos($rawUri, '/admin') !== false || stripos($path, 'admin') === 0) {
        $area = "admin";
    } else {
        $area = "client";
    }
}

// Nếu có path từ .htaccess
if (!empty($path)) {
    // Làm sạch path, loại bỏ index.php nếu vô tình bị ghép vào URL
    $cleanPath = preg_replace('/(\/)?index\.php.*/i', '', $path);
    $cleanPath = trim($cleanPath, '/');
    $segments = $cleanPath !== '' ? explode('/', $cleanPath) : [];

    if (!empty($segments)) {
        if ($segments[0] === 'admin' || $segments[0] === 'client') {
            $area = $segments[0];
            array_shift($segments);
        }

        if (isset($segments[0]) && $segments[0] !== '') {
            if ($area === 'admin' && $segments[0] === 'login') {
                $controller = 'auth';
                $action = 'login';
            } elseif ($area === 'admin' && $segments[0] === 'logout') {
                $controller = 'auth';
                $action = 'logout';
            } else {
                $controller = $segments[0];
                array_shift($segments);

                if (isset($segments[0]) && $segments[0] !== '') {
                    $action = $segments[0];
                    array_shift($segments);

                    if (isset($segments[0]) && is_numeric($segments[0])) {
                        $_GET['id'] = $segments[0];
                    }
                }
            }
        }
    }
}

// Thiết lập mặc định nếu chưa xác định
if (!$controller) {
    $controller = ($area === 'admin') ? "dashboard" : "home";
}
if (!$action) {
    $action = "index";
}

// Tự động chuyển area sang admin nếu action thuộc về chức năng quản trị
if ($area === 'client' && in_array(strtolower($action), ['create', 'edit', 'delete']) && in_array(strtolower($controller), ['category', 'brand', 'user', 'order', 'coupon', 'sale'])) {
    $area = 'admin';
}

// --- KIỂM TRA ĐĂNG NHẬP TRANG ADMIN ---
if ($area === 'admin') {
    $isLoginRoute = (strtolower($controller) === 'auth' && in_array(strtolower($action), ['login', 'logout']));

    if (!$isLoginRoute) {
        if (!isset($_SESSION['user']) && !isset($_SESSION['admin_logged'])) {
            header("Location: " . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . "/admin/login");
            exit();
        }
    }
}

// --- GỌI CONTROLLER VÀ ACTION TƯƠNG ỨNG ---
$namespace = ($area === 'admin') ? "Controllers\\Admin\\" : "Controllers\\Client\\";
$controllerClass = $namespace . ucfirst($controller) . "Controller";

if (!class_exists($controllerClass)) {
    die("Không tìm thấy Controller: " . htmlspecialchars($controllerClass));
}

$controllerObject = new $controllerClass();

if (!method_exists($controllerObject, $action)) {
    // Thử chuyển kebab-case hoặc snake_case sang camelCase (VD: apply-coupon -> applyCoupon)
    $camelAction = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $action))));
    if (method_exists($controllerObject, $camelAction)) {
        $action = $camelAction;
    } elseif ($area !== 'admin' && strtolower($controller) === 'product') {
        $_GET['slug'] = $action;
        $action = 'detail';
    } else {
        die("Action không tồn tại: " . htmlspecialchars($action));
    }
}

// Xác thực CSRF trước khi nhận request POST
verify_csrf();

// Chạy hàm (action) trong Controller
$controllerObject->$action();