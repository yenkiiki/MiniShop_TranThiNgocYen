<?php
namespace Controllers\Client;

use DAO\CategoryDAO;
use DAO\ProductDAO;
use DAO\BrandDAO;

class CategoryController
{
    private CategoryDAO $categoryDAO;
    private ProductDAO $productDAO;
    private BrandDAO $brandDAO;

    public function __construct()
    {
        $this->categoryDAO = new CategoryDAO();
        $this->productDAO = new ProductDAO();
        $this->brandDAO = new BrandDAO();
    }

    public function index()
    {
        $pageTitle = "Danh Mục Sản Phẩm - MINISHOP";
        $keyword = trim($_GET['keyword'] ?? '');

        // Lấy tất cả danh mục
        $allCategories = $this->categoryDAO->getAll($keyword);

        $categoryRows = [];
        foreach ($allCategories as $category) {
            // Lọc sản phẩm theo từng danh mục
            $filters = [
                'category_id' => $category->id,
                'keyword' => $keyword
            ];
            
            $totalInCat = $this->productDAO->countFiltered($filters);
            $products = $this->productDAO->getFiltered($filters, 8, 0);

            $categoryRows[] = [
                'category' => $category,
                'products' => $products,
                'totalCount' => $totalInCat
            ];
        }

        ob_start();
        require __DIR__ . '/../../views/client/categories/index.php';
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }
}
