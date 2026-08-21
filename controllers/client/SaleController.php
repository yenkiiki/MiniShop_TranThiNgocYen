<?php
namespace Controllers\Client;

use DAO\SaleDAO;
use DAO\CategoryDAO;
use DAO\BrandDAO;

class SaleController
{
    private SaleDAO $saleDAO;

    public function __construct()
    {
        $this->saleDAO = new SaleDAO();
    }

    /**
     * Hiển thị trang Flash Sale / Săn Deal Giảm Giá dành cho khách hàng
     */
    public function index()
    {
        $pageTitle = "🔥 Săn Deal Flash Sale - Giảm Giá Đến 50% | MINISHOP";
        
        $sort = $_GET['sort'] ?? 'discount_desc';
        if (!in_array($sort, ['discount_desc', 'price_asc', 'price_desc', 'latest'])) {
            $sort = 'discount_desc';
        }

        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $sales = $this->saleDAO->getActiveSales($sort, $limit, $offset);
        $totalSales = $this->saleDAO->countActiveSales();
        $totalPages = ceil($totalSales / $limit);

        $categoryDAO = new CategoryDAO();
        $categories = $categoryDAO->getAll();

        $brandDAO = new BrandDAO();
        $brands = $brandDAO->getAll();

        ob_start();
        require __DIR__ . "/../../views/client/sales/index.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }
}
