<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/ProductImage.php";

class ProductDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

   public function getAll(string $keyword = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT p.*, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id";
            
            if (!empty($keyword)) {
                $sql .= " WHERE p.proname LIKE ? OR p.slug LIKE ?";
            }
            $sql .= " ORDER BY p.id DESC";

            if (!empty($keyword)) {
                $stmt = $this->prepare($sql);
                $searchTerm = "%" . $keyword . "%";
                $stmt->bind_param("ss", $searchTerm, $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->executeQuery($sql);
            }

            while ($row = $result->fetch_assoc()) {
                $product = new Product();
                $product->id = (int)$row["id"];
                $product->categoryId = (int)$row["category_id"];
                $product->brandId = (int)$row["brand_id"];
                $product->proName = $row["proname"];
                $product->slug = $row["slug"];
                $product->price = (float)$row["price"];
                $product->discountPrice = (float)$row["discount_price"];
                $product->quantity = (int)$row["quantity"];
                $product->image = $row["image"];
                $product->description = $row["description"];
                $product->status = (int)$row["status"];
                $product->createdAt = $row["created_at"];
                $product->updatedAt = $row["updated_at"];
                
                // Lấy tên danh mục và thương hiệu từ bảng JOIN
                $product->cateName = $row["catename"] ?? 'Chưa phân loại';
                $product->brandName = $row["brandname"] ?? 'Không có thương hiệu';

                $list[] = $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Product
    {
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $product = new Product();
                $product->id = (int)$row["id"];
                $product->categoryId = (int)$row["category_id"];
                $product->brandId = (int)$row["brand_id"];
                $product->proName = $row["proname"];
                $product->slug = $row["slug"];
                $product->price = (float)$row["price"];
                $product->discountPrice = (float)$row["discount_price"];
                $product->quantity = (int)$row["quantity"];
                $product->image = $row["image"];
                $product->description = $row["description"];
                $product->status = (int)$row["status"];
                $product->createdAt = $row["created_at"];
                $product->updatedAt = $row["updated_at"];
                return $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Product $product): bool
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
            return $stmt->execute();
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

    // --- Xử lý bảng product_images ---
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
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products ORDER BY id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $product = new Product();
                $product->id = (int)$row["id"];
                $product->categoryId = (int)$row["category_id"];
                $product->brandId = (int)$row["brand_id"];
                $product->proName = $row["proname"];
                $product->slug = $row["slug"];
                $product->price = (float)$row["price"];
                $product->discountPrice = (float)$row["discount_price"];
                $product->quantity = (int)$row["quantity"];
                $product->image = $row["image"];
                $product->description = $row["description"];
                $product->status = (int)$row["status"];
                $product->createdAt = $row["created_at"];
                $product->updatedAt = $row["updated_at"];
                $list[] = $product;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}