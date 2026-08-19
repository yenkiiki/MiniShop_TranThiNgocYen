<?php
namespace Composers;

use DAO\CategoryDAO;
use DAO\BrandDAO;
use Config\FileCache;

class HeaderComposer
{
    // Bỏ kiểu bắt buộc ": array" hoặc ép kiểu dữ liệu khi trả về
    public static function compose()
    {
        $cacheKey = 'header_categories_brands_all';
        
        $cachedData = FileCache::get($cacheKey);
        if ($cachedData !== null) {
            // Ép kiểu đối tượng stdClass từ cache thành mảng nếu cần, 
            // hoặc nếu FileCache ở Cách 1 trả về mảng thì giữ nguyên dòng dưới:
            return (array)$cachedData; 
        }

        $categoryDAO = new CategoryDAO();
        $brandDAO = new BrandDAO(); 
        
        $data = [
            'categories' => $categoryDAO->getAll(),
            'brands' => $brandDAO->getAll()
        ];

        FileCache::set($cacheKey, $data, 300);

        return $data;
    }
}