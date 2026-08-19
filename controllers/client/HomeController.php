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
        $pageTitle = "Trang chủ - MINISHOP";
        
        $categories = $this->categoryDAO->getAll();
        $discountProducts = $this->productDAO->getDiscountProducts(8);
        $newProducts = $this->productDAO->getNewProducts(4);

        ob_start();
        require __DIR__ . "/../../views/client/home/index.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }
}