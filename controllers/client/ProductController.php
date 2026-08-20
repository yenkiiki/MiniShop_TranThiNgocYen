<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\CategoryDAO; // Thêm DAO danh mục
use DAO\BrandDAO;    // Thêm DAO thương hiệu

class ProductController
{
    private ProductDAO $productDAO;
    private CategoryDAO $categoryDAO;
    private BrandDAO $brandDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->categoryDAO = new CategoryDAO();
        $this->brandDAO = new BrandDAO();
    }

    public function detail()
    {
        $slug = $_GET['slug'] ?? '';
        $product = $this->productDAO->findBySlug($slug);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
            $productId = $_POST['product_id'] ?? 0;
            $fullname = trim($_POST['fullname'] ?? '');
            $rating = intval($_POST['rating'] ?? 5);
            $comment = trim($_POST['comment'] ?? '');
            if (!empty($fullname) && !empty($comment)) {
                $this->productDAO->addReview($productId, $fullname, $rating, $comment);
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        $productImages = [];
        $relatedProducts = [];
        $productReviews = [];
        if ($product) {
            $productImages = $this->productDAO->getImagesByProductId($product->id);
            $relatedProducts = $this->productDAO->getRelatedProducts($product->categoryId, $product->id, 4);
            $productReviews = $this->productDAO->getReviewsByProductId($product->id);
        }
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
        $limit = 8;
        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $offset = ($page - 1) * $limit;

        // Đồng bộ lại tên tham số cho khớp với form HTML (category_slug và brand_slug)
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'category_slug' => $_GET['category_slug'] ?? ($_GET['category'] ?? ($_GET['slug'] ?? '')),
            'brand_slug' => $_GET['brand_slug'] ?? ($_GET['brand'] ?? ''),
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'sort' => $_GET['sort'] ?? 'latest'
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