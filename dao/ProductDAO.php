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

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $list[] = new Product(
                    $row["id"],
                    $row["category_id"],
                    $row["brand_id"],
                    $row["proname"],
                    $row["slug"],
                    $row["price"],
                    $row["discount_price"],
                    $row["quantity"],
                    $row["image"],
                    $row["description"],
                    $row["status"],
                    $row["created_at"],
                    $row["updated_at"]
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Product
    {
        try {
            $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at FROM products WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Product(
                    $row["id"],
                    $row["category_id"],
                    $row["brand_id"],
                    $row["proname"],
                    $row["slug"],
                    $row["price"],
                    $row["discount_price"],
                    $row["quantity"],
                    $row["image"],
                    $row["description"],
                    $row["status"],
                    $row["created_at"],
                    $row["updated_at"]
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Product $product): bool
    {
        try {
            $sql = "INSERT INTO products(category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);

            $categoryId = $product->getCategoryId();
            $brandId = $product->getBrandId();
            $proname = $product->getProname();
            $slug = $product->getSlug();
            $price = $product->getPrice();
            $discountPrice = $product->getDiscountPrice();
            $quantity = $product->getQuantity();
            $image = $product->getImage();
            $description = $product->getDescription();
            $status = $product->getStatus();

            $stmt->bind_param("iissddissi", $categoryId, $brandId, $proname, $slug, $price, $discountPrice, $quantity, $image, $description, $status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Product $product): bool
    {
        try {
            $sql = "UPDATE products SET category_id = ?, brand_id = ?, proname = ?, slug = ?, price = ?, discount_price = ?, quantity = ?, image = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);

            $categoryId = $product->getCategoryId();
            $brandId = $product->getBrandId();
            $proname = $product->getProname();
            $slug = $product->getSlug();
            $price = $product->getPrice();
            $discountPrice = $product->getDiscountPrice();
            $quantity = $product->getQuantity();
            $image = $product->getImage();
            $description = $product->getDescription();
            $status = $product->getStatus();
            $id = $product->getId();

            $stmt->bind_param("iissddissii", $categoryId, $brandId, $proname, $slug, $price, $discountPrice, $quantity, $image, $description, $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Các phương thức quản lý Album ảnh phụ (product_images)
    public function getImagesByProductId(int $productId): array
    {
        $images = [];
        try {
            $sql = "SELECT id, product_id, image, sort_order, created_at FROM product_images WHERE product_id = ? ORDER BY sort_order ASC";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $images[] = new ProductImage(
                    $row["id"],
                    $row["product_id"],
                    $row["image"],
                    $row["sort_order"],
                    $row["created_at"]
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $images;
    }

    public function addImage(ProductImage $productImage): bool
    {
        try {
            $sql = "INSERT INTO product_images(product_id, image, sort_order) VALUES (?, ?, ?)";
            $stmt = $this->prepare($sql);

            $productId = $productImage->getProductId();
            $image = $productImage->getImage();
            $sortOrder = $productImage->getSortOrder();

            $stmt->bind_param("isi", $productId, $image, $sortOrder);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteImage(int $imageId): bool
    {
        try {
            $sql = "DELETE FROM product_images WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $imageId);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    // Đếm tổng số sản phẩm
public function count(): int
{
    try {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $this->executeQuery($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        throw $e;
    }
}

// Lấy 5 sản phẩm mới nhất
public function getLatest(int $limit = 5): array
{
    $list = [];
    try {
        $sql = "SELECT id, category_id, brand_id, proname, slug, price, discount_price, quantity, image, description, status, created_at, updated_at 
                FROM products ORDER BY id DESC LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = new Product(
                $row["id"],
                $row["category_id"],
                $row["brand_id"],
                $row["proname"],
                $row["slug"],
                $row["price"],
                $row["discount_price"],
                $row["quantity"],
                $row["image"],
                $row["description"],
                $row["status"],
                $row["created_at"],
                $row["updated_at"]
            );
        }
    } catch (Exception $e) {
        throw $e;
    }
    return $list;
}
}