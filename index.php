<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Nhúng file Autoload để tự động nạp class
require_once __DIR__ . '/autoload.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// 5. Thực thi Action
$controllerObject->$action();