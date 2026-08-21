<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\CategoryDAO; // Thêm DAO danh mục
use DAO\BrandDAO;    // Thêm DAO thương hiệu
use DAO\ProductVariantDAO;

class ProductController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;
    private BrandDAO $brandDAO;
    private ProductVariantDAO $variantDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
        $this->brandDAO = new BrandDAO();
        $this->variantDAO = new ProductVariantDAO();
    }

    public function detail()
    {
        $slug = $_GET['slug'] ?? '';
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $product = null;
        if (!empty($slug)) {
            $product = $this->productDAO->findBySlug($slug);
        }
        if (!$product && $id > 0) {
            $product = $this->productDAO->findById($id);
        }
        
        $productImages = [];
        $variants = [];
        $relatedProducts = [];
        $productReviews = [];
        $ratingSummary = ['average' => 5.0, 'total' => 0, 'counts' => [5=>0,4=>0,3=>0,2=>0,1=>0], 'percents' => [5=>0,4=>0,3=>0,2=>0,1=>0]];

        if ($product) {
            $productImages = $this->productDAO->getImagesByProductId($product->id);
            $variants = $this->variantDAO->getByProductId($product->id);
            $relatedProducts = $this->productDAO->getRelatedProducts($product->categoryId, $product->id, 4);
            
            $reviewDAO = new \DAO\ReviewDAO();
            $productReviews = $reviewDAO->getByProductId($product->id);
            $ratingSummary = $reviewDAO->getRatingSummary($product->id);
        }

        $pageTitle = $product ? ($product->proName . " - MINISHOP") : "Chi tiết sản phẩm";
        ob_start();
        require __DIR__ . '/../../views/client/products/detail.php';
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

    public function search()
    {
        $this->index();
    }

    public function index()
    {
        $limit = 12;
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'category_slug' => $_GET['category_slug'] ?? ($_GET['category'] ?? ($_GET['slug'] ?? '')),
            'brand_slug' => $_GET['brand_slug'] ?? ($_GET['brand'] ?? ''),
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'sort' => $_GET['sort'] ?? 'latest',
            'on_sale' => !empty($_GET['on_sale']) ? 1 : 0
        ];

        // Lấy danh sách danh mục và thương hiệu để đổ ra ô select ở View
        $categories = $this->categoryDAO->getAll(); // Hoặc tên hàm tương ứng trong CategoryDAO của bạn
        $brands = $this->brandDAO->getAll();       // Hoặc tên hàm tương ứng trong BrandDAO của bạn

        $totalProducts = $this->productDAO->countFiltered($filters);
        $totalPages = ceil($totalProducts / $limit);
        $products = $this->productDAO->getFiltered($filters, $limit, $offset);

        ob_start();
        require __DIR__ . '/../../views/client/products/index.php';
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

public function category()
    {
    
        if (isset($_GET['slug']) && empty($_GET['category_slug'])) {
            $_GET['category_slug'] = $_GET['slug'];
        }
        $this->index();
    }
public function brand()
    {
  
        if (isset($_GET['slug']) && empty($_GET['brand_slug'])) {
            $_GET['brand_slug'] = $_GET['slug'];
        }
        $this->index();
    }
}