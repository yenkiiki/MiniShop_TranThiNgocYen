<?php
namespace Composers;

use DAO\CategoryDAO;
use DAO\BrandDAO;
use Config\FileCache;

class HeaderComposer
{
    public static function compose()
    {
        $cacheKey = 'header_categories_brands_all';
        
        $cachedData = FileCache::get($cacheKey);
        if ($cachedData !== null) {

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