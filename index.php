<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Nhúng file Autoload để tự động nạp class
require_once __DIR__ . '/autoload.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- BẮT ĐẦU CẤU HÌNH BẢO MẬT CSRF TỰ ĐỘNG ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Hàm sinh input ẩn cho các file View (create, edit,...)
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

// Hàm tự động xác thực token khi có request POST gửi lên hệ thống
function verify_csrf() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Lỗi bảo mật: CSRF Token không hợp lệ hoặc đã hết hạn! Vui lòng quay lại thao tác.");
        }
    }
}
// --- KẾT THÚC CẤU HÌNH BẢO MẬT CSRF ---

// --- BẮT ĐẦU XỬ LÝ URL THÂN THIỆN (.HTACCESS) ---
$path = $_GET['path'] ?? '';
$path = str_replace('index.php', '', $path);
$path = trim($path, '/');
$segments = $path !== '' ? explode('/', $path) : [];

// Mặc định ban đầu
$area = "admin";
$controller = "dashboard";
$action = "index";

if (!empty($segments)) {
    // 1. Lấy area (admin hoặc client) nếu có ở đoạn đầu tiên
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
            
            // 3. Lấy action nếu có
            if (isset($segments[0]) && $segments[0] !== '') {
                $action = $segments[0];
                array_shift($segments);
                
                // 4. Phần còn lại nếu là số thì gán vào $_GET['id'] (ví dụ: /admin/product/edit/5)
                if (isset($segments[0]) && is_numeric($segments[0])) {
                    $_GET['id'] = $segments[0];
                }
            }
        }
    }
}

// --- KIỂM TRA AUTHENTICATION / MIDDLEWARE CHO KHU VỰC ADMIN ---
if ($area === 'admin') {
    // Cho phép truy cập trang login và logout mà không cần đăng nhập
    $isLoginRoute = (strtolower($controller) === 'auth' && in_array(strtolower($action), ['login', 'logout']));

    if (!$isLoginRoute) {
        // Kiểm tra xem session đăng nhập có tồn tại hay không 
        if (!isset($_SESSION['user']) && !isset($_SESSION['admin_logged'])) {
            // Chưa đăng nhập -> Chuyển hướng về trang login của admin
            header("Location: " . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . "/admin/login");
            exit();
        }
    }
}
// --- KẾT THÚC KIỂM TRA MIDDLEWARE ---

// Đồng bộ ngược lại các biến để khớp với hệ thống cũ của cậu
$_GET['area'] = $area;
$_GET['controller'] = $controller;
$_GET['action'] = $action;
// --- KẾT THÚC XỬ LÝ URL THÂN THIỆN ---

// Xây dựng Namespace linh hoạt
$namespace = ($area === 'admin') ? "Controllers\\Admin\\" : "Controllers\\";
$controllerClass = $namespace . ucfirst($controller) . "Controller";

// 3. Kiểm tra class có tồn tại hay không
if (!class_exists($controllerClass)) {
    die("Không tìm thấy Controller: " . htmlspecialchars($controllerClass));
}

$controllerObject = new $controllerClass();

// 4. Kiểm tra action có tồn tại không
if (!method_exists($controllerObject, $action)) {
    die("Action không tồn tại: " . htmlspecialchars($action));
}

// --- TỰ ĐỘNG CHẶN VÀ KIỂM TRA CSRF CHO TẤT CẢ REQUEST POST ---
verify_csrf();

// 5. Thực thi Action
$controllerObject->$action();