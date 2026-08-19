<?php
define('BASE_URL', '/MiniShop_TranThiNgocYen/');
define('PRODUCT_IMAGE_URL', BASE_URL . 'uploads/products/');

spl_autoload_register(function ($className) {
    $prefixes = [
        'Controllers\\Admin\\' => __DIR__ . '/controllers/admin/',
        'Controllers\\' => __DIR__ . '/controllers/',
        'DAO\\' => __DIR__ . '/dao/',
        'Models\\' => __DIR__ . '/models/',
        'Middleware\\' => __DIR__ . '/middleware/',
        'Config\\' => __DIR__ . '/config/',
        'Composers\\' => __DIR__ . '/app/Composers/',
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            continue;
        }
        $relative_class = substr($className, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});