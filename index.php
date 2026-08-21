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

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function verify_csrf() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (strpos($_SERVER['REQUEST_URI'], 'cart/add') !== false) {
            return;
        }
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Lỗi bảo mật: CSRF Token không hợp lệ!");
        }
    }
}

// --- XỬ LÝ URL THÂN THIỆN (.HTACCESS) ---
$area = $_GET['area'] ?? "client";
$controller = $_GET['controller'] ?? null;
$action = $_GET['action'] ?? null;

if (!$controller || !$action) {
    $path = $_GET['path'] ?? '';
    $path = str_replace('index.php', '', $path);
    $path = trim($path, '/');
    $segments = $path !== '' ? explode('/', $path) : [];

    $controller = "home";
    $action = "index";

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
    die("Action không tồn tại: " . htmlspecialchars($action));
}

// Xác thực CSRF trước khi nhận request POST
verify_csrf();

// Chạy hàm (action) trong Controller
$controllerObject->$action();