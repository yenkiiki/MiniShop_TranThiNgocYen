<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\CategoryDAO;

class HomeController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
    }

    public function index()
    {
        $pageTitle = "MINISHOP - Cửa hàng phụ kiện & thiết bị công nghệ chính hãng";
        
        $categories = $this->categoryDAO->getAll();
        $discountProducts = $this->productDAO->getDiscountProducts(8);
        $newProducts = $this->productDAO->getNewProducts(8);

        // Lấy thông tin và số lượng sản phẩm cho từng danh mục
        $categoryRows = [];
        foreach ($categories as $category) {
            $totalInCat = $this->productDAO->countFiltered(['category_id' => $category->id]);
            $categoryRows[] = [
                'category' => $category,
                'totalCount' => $totalInCat
            ];
        }

        require_once __DIR__ . "/../../dao/BrandDAO.php";
        $brandDAO = new \DAO\BrandDAO();
        $brands = $brandDAO->getAll();

        require_once __DIR__ . "/../../dao/ReviewDAO.php";
        $reviewDAO = new \DAO\ReviewDAO();
        $featuredReviews = $reviewDAO->getByProductId(0); // If 0 returns all, or we can fetch top reviews

        ob_start();
        require __DIR__ . "/../../views/client/home/index.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }
}