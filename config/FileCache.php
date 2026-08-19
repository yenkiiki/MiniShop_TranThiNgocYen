<?php
namespace Config;

class FileCache {
    private static string $cacheDir = __DIR__ . '/../storage/cache/';

    public static function get(string $key) {
        $file = self::$cacheDir . md5($key) . '.json';
        
        if (!file_exists($file)) {
            return null;
        }
        
        // Đặt tham số thứ 2 là true để trả về Mảng (Array)
        $data = json_decode(file_get_contents($file), true);
        
        if (!$data || !isset($data['expire']) || $data['expire'] < time()) {
            @unlink($file);
            return null;
        }
        
        return $data['content'] ?? null;
    }

    public static function set(string $key, $value, int $seconds = 300) {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }
        
        $file = self::$cacheDir . md5($key) . '.json';
        $data = [
            'expire' => time() + $seconds,
            'content' => $value
        ];
        
        file_put_contents($file, json_encode($data));
    }
}