<?php
namespace DAO;

use Models\ProductImage;
use Models\Product;
use Exception;

require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/ProductImage.php";

class ProductDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    private function mapRowToProduct(array $row): Product
    {
        $product = new Product();
        $product->id = (int)($row["id"] ?? 0);
        $product->categoryId = (int)($row["category_id"] ?? 0);
        $product->brandId = (int)($row["brand_id"] ?? 0);
        $product->proName = $row["proname"] ?? '';
        $product->slug = $row["slug"] ?? '';
        $product->price = (float)($row["price"] ?? 0);
        $product->discountPrice = (float)($row["discount_price"] ?? 0);
        $product->quantity = (int)($row["quantity"] ?? 0);
        $product->image = $row["image"] ?? '';
        $product->description = $row["description"] ?? '';
        $product->status = (int)($row["status"] ?? 0);
        $product->createdAt = $row["created_at"] ?? null;
        $product->updatedAt = $row["updated_at"] ?? null;
        
        $product->cateName = $row["catename"] ?? 'Chưa phân loại';
        $product->brandName = $row["brandname"] ?? 'Không có thương hiệu';

        return $product;
    }

    public function getAll(string $keyword = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id";
            
            if (!empty(trim($keyword))) {
                $sql .= " WHERE p.proname LIKE ? OR p.slug LIKE ?";
            }
            $sql .= " ORDER BY p.id DESC";

            if (!empty(trim($keyword))) {
                $stmt = $this->prepare($sql);
                $searchTerm = "%" . trim($keyword) . "%";
                $stmt->bind_param("ss", $searchTerm, $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->executeQuery($sql);
            }

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Product
    {
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE p.id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Product $product): int
    {
        try {
            $sql = "INSERT INTO products(category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iissddissi",
                $product->categoryId,
                $product->brandId,
                $product->proName,
                $product->slug,
                $product->price,
                $product->discountPrice,
                $product->quantity,
                $product->image,
                $product->description,
                $product->status
            );
            if ($stmt->execute()) {
                return $stmt->insert_id;
            }
            return 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Product $product): bool
    {
        try {
            $sql = "UPDATE products SET category_id=?, brand_id=?, proname=?, slug=?, price=?, discount_price=?, quantity=?, image=?, description=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iissddissii",
                $product->categoryId,
                $product->brandId,
                $product->proName,
                $product->slug,
                $product->price,
                $product->discountPrice,
                $product->quantity,
                $product->image,
                $product->description,
                $product->status,
                $product->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM products WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getImagesByProductId(int $productId): array
    {
        $list = [];
        try {
            $sql = "SELECT id, product_id, image, sort_order, created_at FROM product_images WHERE product_id = ? ORDER BY sort_order";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $img = new ProductImage();
                $img->id = (int)$row["id"];
                $img->productId = (int)$row["product_id"];
                $img->image = $row["image"];
                $img->sortOrder = (int)$row["sort_order"];
                $img->createdAt = $row["created_at"];
                $list[] = $img;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function insertImage(ProductImage $productImage): bool
    {
        try {
            $sql = "INSERT INTO product_images(product_id, image, sort_order) VALUES(?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("isi", $productImage->productId, $productImage->image, $productImage->sortOrder);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteImage(int $id): bool
    {
        try {
            $sql = "DELETE FROM product_images WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function countAll(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM products";
            $result = $this->executeQuery($sql);
            if ($row = $result->fetch_assoc()) {
                return (int)$row["total"];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0;
    }

    public function getLatest(int $limit = 5): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    ORDER BY p.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function getPage(int $limit, int $offset, string $keyword = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id";
            
            if (!empty(trim($keyword))) {
                $sql .= " WHERE p.proname LIKE ? OR p.slug LIKE ?";
            }
            
            $sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";
            
            $stmt = $this->prepare($sql);
            
            if (!empty(trim($keyword))) {
                $searchTerm = "%" . trim($keyword) . "%";
                $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
            } else {
                $stmt->bind_param("ii", $limit, $offset);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function count(string $table = "products", string $column = "proname", string $keyword = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM products p";
            
            if (!empty(trim($keyword))) {
                $sql .= " WHERE p.proname LIKE ? OR p.slug LIKE ?";
                $stmt = $this->prepare($sql);
                $searchTerm = "%" . trim($keyword) . "%";
                $stmt->bind_param("ss", $searchTerm, $searchTerm);
            } else {
                $stmt = $this->prepare($sql);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return (int)$row["total"];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0;
    }

    public function getDiscountProducts(int $limit = 8): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE p.discount_price > 0 AND p.discount_price < p.price 
                    ORDER BY p.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function getNewProducts(int $limit = 4): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    ORDER BY p.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function getByCategory(string $slug): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE c.slug = ? 
                    ORDER BY p.id DESC";
            
            $stmt = $this->prepare($sql);
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function getByBrand(string $slug): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE b.slug = ? 
                    ORDER BY p.id DESC";
            
            $stmt = $this->prepare($sql);
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findBySlug(string $slug): ?Product
    {
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE p.slug = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function getRelatedProducts(int $categoryId, int $currentId, int $limit = 4): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE p.category_id = ? AND p.id != ? 
                    ORDER BY p.id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("iii", $categoryId, $currentId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function addReview(int $productId, string $fullname, int $rating, string $comment): bool
    {
        try {
            $sql = "INSERT INTO product_reviews (product_id, fullname, rating, comment) VALUES (?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("isis", $productId, $fullname, $rating, $comment);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getReviewsByProductId(int $productId): array
    {
        $list = [];
        try {
            $sql = "SELECT id, product_id, fullname, rating, comment, created_at FROM product_reviews WHERE product_id = ? ORDER BY id DESC";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_object()) { 
                $list[] = $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function getFiltered(array $filters = [], int $limit = 8, int $offset = 0): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty(trim($filters['keyword'] ?? ''))) {
                $sql .= " AND (p.proname LIKE ? OR p.slug LIKE ?)";
                $searchTerm = "%" . trim($filters['keyword']) . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }

            if (!empty($filters['category_id'])) {
                $sql .= " AND p.category_id = ?";
                $params[] = (int)$filters['category_id'];
                $types .= "i";
            } elseif (!empty(trim($filters['category_slug'] ?? ''))) {
                $sql .= " AND c.slug = ?";
                $params[] = trim($filters['category_slug']);
                $types .= "s";
            }

            if (!empty($filters['brand_id'])) {
                $sql .= " AND p.brand_id = ?";
                $params[] = (int)$filters['brand_id'];
                $types .= "i";
            } elseif (!empty(trim($filters['brand_slug'] ?? ''))) {
                $sql .= " AND b.slug = ?";
                $params[] = trim($filters['brand_slug']);
                $types .= "s";
            }

            if (isset($filters['min_price']) && $filters['min_price'] !== '' && is_numeric($filters['min_price'])) {
                $sql .= " AND p.price >= ?";
                $params[] = (float)$filters['min_price'];
                $types .= "d";
            }

            if (isset($filters['max_price']) && $filters['max_price'] !== '' && is_numeric($filters['max_price'])) {
                $sql .= " AND p.price <= ?";
                $params[] = (float)$filters['max_price'];
                $types .= "d";
            }

            $sort = $filters['sort'] ?? 'latest';
            if ($sort === 'price_asc') {
                $sql .= " ORDER BY p.price ASC";
            } elseif ($sort === 'price_desc') {
                $sql .= " ORDER BY p.price DESC";
            } elseif ($sort === 'name_asc') {
                $sql .= " ORDER BY p.proname ASC";
            } else {
                $sql .= " ORDER BY p.id DESC";
            }

            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";

            $stmt = $this->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToProduct($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function countFiltered(array $filters = []): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty(trim($filters['keyword'] ?? ''))) {
                $sql .= " AND (p.proname LIKE ? OR p.slug LIKE ?)";
                $searchTerm = "%" . trim($filters['keyword']) . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }

            if (!empty($filters['category_id'])) {
                $sql .= " AND p.category_id = ?";
                $params[] = (int)$filters['category_id'];
                $types .= "i";
            } elseif (!empty(trim($filters['category_slug'] ?? ''))) {
                $sql .= " AND c.slug = ?";
                $params[] = trim($filters['category_slug']);
                $types .= "s";
            }

            if (!empty($filters['brand_id'])) {
                $sql .= " AND p.brand_id = ?";
                $params[] = (int)$filters['brand_id'];
                $types .= "i";
            } elseif (!empty(trim($filters['brand_slug'] ?? ''))) {
                $sql .= " AND b.slug = ?";
                $params[] = trim($filters['brand_slug']);
                $types .= "s";
            }

            if (isset($filters['min_price']) && $filters['min_price'] !== '' && is_numeric($filters['min_price'])) {
                $sql .= " AND p.price >= ?";
                $params[] = (float)$filters['min_price'];
                $types .= "d";
            }

            if (isset($filters['max_price']) && $filters['max_price'] !== '' && is_numeric($filters['max_price'])) {
                $sql .= " AND p.price <= ?";
                $params[] = (float)$filters['max_price'];
                $types .= "d";
            }

            $stmt = $this->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return (int)$row['total'];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0;
    }
}