<?php
namespace DAO;

use Models\Sale;
use Exception;

class SaleDAO extends BaseDAO
{
    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT s.*, p.proname, p.image, p.price, c.catename, b.brandname FROM sales s JOIN products p ON s.product_id = p.id LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id ORDER BY s.id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $list[] = new Sale($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function countSearch(string $keyword = "", $status = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM sales s JOIN products p ON s.product_id = p.id WHERE 1=1";
            $params = [];
            $types = "";
            if (!empty($keyword)) {
                $sql .= " AND (p.proname LIKE ? OR s.description LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }
            if ($status !== "" && $status !== null) {
                $sql .= " AND s.status = ?";
                $params[] = (int) $status;
                $types .= "i";
            }
            $stmt = $this->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return (int) $row["total"];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0;
    }

    public function getPage(int $limit, int $offset, string $keyword = "", $status = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT s.*, p.proname, p.image, p.price, c.catename, b.brandname FROM sales s JOIN products p ON s.product_id = p.id LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE 1=1";
            $params = [];
            $types = "";
            if (!empty($keyword)) {
                $sql .= " AND (p.proname LIKE ? OR s.description LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }
            if ($status !== "" && $status !== null) {
                $sql .= " AND s.status = ?";
                $params[] = (int) $status;
                $types .= "i";
            }
            $sql .= " ORDER BY s.id DESC LIMIT ? OFFSET ?";
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
                $list[] = new Sale($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Sale
    {
        try {
            $sql = "SELECT s.*, p.proname, p.image, p.price, c.catename, b.brandname FROM sales s JOIN products p ON s.product_id = p.id LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id WHERE s.id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Sale($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function findByProductId(int $productId): ?Sale
    {
        try {
            $sql = "SELECT * FROM sales WHERE product_id = ? AND status = 1 LIMIT 1";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Sale($row);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function create(Sale $sale): int|false
    {
        try {
            $pSql = "SELECT price FROM products WHERE id = ?";
            $pStmt = $this->prepare($pSql);
            $pStmt->bind_param("i", $sale->productId);
            $pStmt->execute();
            $pRes = $pStmt->get_result();
            $pRow = $pRes->fetch_assoc();
            if (!$pRow) {
                return false;
            }
            $originalPrice = (float)$pRow['price'];
            $discountPercent = max(0, min(100, (int)$sale->discountPercent));
            $salePrice = $originalPrice * (1 - ($discountPercent / 100));
            $sale->salePrice = $salePrice;
            $sql = "INSERT INTO sales (product_id, discount_percent, sale_price, start_date, end_date, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("iidsssi", $sale->productId, $discountPercent, $salePrice, $sale->startDate, $sale->endDate, $sale->description, $sale->status);
            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                if ($sale->status == 1) {
                    $updSql = "UPDATE products SET discount_price = ? WHERE id = ?";
                    $updStmt = $this->prepare($updSql);
                    $updStmt->bind_param("di", $salePrice, $sale->productId);
                    $updStmt->execute();
                }
                return $newId;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return false;
    }

    public function update(Sale $sale): bool
    {
        try {
            $pSql = "SELECT price FROM products WHERE id = ?";
            $pStmt = $this->prepare($pSql);
            $pStmt->bind_param("i", $sale->productId);
            $pStmt->execute();
            $pRes = $pStmt->get_result();
            $pRow = $pRes->fetch_assoc();
            if (!$pRow) {
                return false;
            }
            $originalPrice = (float)$pRow['price'];
            $discountPercent = max(0, min(100, (int)$sale->discountPercent));
            $salePrice = $originalPrice * (1 - ($discountPercent / 100));
            $sale->salePrice = $salePrice;
            $sql = "UPDATE sales SET product_id = ?, discount_percent = ?, sale_price = ?, start_date = ?, end_date = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("iidsssii", $sale->productId, $discountPercent, $salePrice, $sale->startDate, $sale->endDate, $sale->description, $sale->status, $sale->id);
            if ($stmt->execute()) {
                $effectiveDiscountPrice = ($sale->status == 1) ? $salePrice : 0.0;
                $updSql = "UPDATE products SET discount_price = ? WHERE id = ?";
                $updStmt = $this->prepare($updSql);
                $updStmt->bind_param("di", $effectiveDiscountPrice, $sale->productId);
                $updStmt->execute();
                return true;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return false;
    }

    public function delete(int $id): bool
    {
        try {
            $sale = $this->findById($id);
            if ($sale) {
                $sql = "DELETE FROM sales WHERE id = ?";
                $stmt = $this->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $updSql = "UPDATE products SET discount_price = 0 WHERE id = ?";
                    $updStmt = $this->prepare($updSql);
                    $updStmt->bind_param("i", $sale->productId);
                    $updStmt->execute();
                    return true;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        return false;
    }

    public function toggleStatus(int $id): bool
    {
        try {
            $sale = $this->findById($id);
            if ($sale) {
                $newStatus = ($sale->status == 1) ? 0 : 1;
                $sql = "UPDATE sales SET status = ? WHERE id = ?";
                $stmt = $this->prepare($sql);
                $stmt->bind_param("ii", $newStatus, $id);
                if ($stmt->execute()) {
                    $effectivePrice = ($newStatus == 1) ? $sale->salePrice : 0.0;
                    $updSql = "UPDATE products SET discount_price = ? WHERE id = ?";
                    $updStmt = $this->prepare($updSql);
                    $updStmt->bind_param("di", $effectivePrice, $sale->productId);
                    $updStmt->execute();
                    return true;
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
        return false;
    }

    public function getStatistics(): array
    {
        $stats = ['total' => 0, 'active' => 0, 'inactive' => 0, 'avg_discount' => 0];
        try {
            $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive, AVG(discount_percent) as avg_discount FROM sales";
            $res = $this->executeQuery($sql);
            if ($row = $res->fetch_assoc()) {
                $stats['total'] = (int)($row['total'] ?? 0);
                $stats['active'] = (int)($row['active'] ?? 0);
                $stats['inactive'] = (int)($row['inactive'] ?? 0);
                $stats['avg_discount'] = round((float)($row['avg_discount'] ?? 0), 1);
            }
        } catch (Exception $e) {}
        return $stats;
    }

    public function getActiveSales(string $sort = "discount_desc", int $limit = 24, int $offset = 0): array
    {
        $list = [];
        try {
            $orderBy = "s.discount_percent DESC";
            if ($sort === "price_asc") {
                $orderBy = "s.sale_price ASC";
            } elseif ($sort === "price_desc") {
                $orderBy = "s.sale_price DESC";
            } elseif ($sort === "latest") {
                $orderBy = "s.id DESC";
            }

            $sql = "SELECT s.*, p.proname, p.image, p.price, p.slug, c.catename, b.brandname 
                    FROM sales s 
                    JOIN products p ON s.product_id = p.id 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    WHERE s.status = 1 
                      AND (s.start_date IS NULL OR s.start_date <= CURDATE()) 
                      AND (s.end_date IS NULL OR s.end_date >= CURDATE())
                    ORDER BY {$orderBy} 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $sale = new Sale($row);
                $sale->slug = $row['slug'] ?? '';
                $list[] = $sale;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function countActiveSales(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM sales s 
                    JOIN products p ON s.product_id = p.id 
                    WHERE s.status = 1 
                    AND (s.start_date IS NULL OR s.start_date <= CURDATE()) 
                    AND (s.end_date IS NULL OR s.end_date >= CURDATE())";
            $res = $this->executeQuery($sql);
            if ($row = $res->fetch_assoc()) {
                return (int)$row['total'];
            }
        } catch (Exception $e) {}
        return 0;
    }

    /**
     * Lấy danh sách các sản phẩm để hiển thị trong form tạo/sửa chương trình giảm giá
     */
    public function getAvailableProductsForSale(int $excludeSaleId = 0): array
    {
        $list = [];
        try {
            $sql = "SELECT p.id, p.proname, p.price, p.image, c.catename, b.brandname 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    LEFT JOIN brands b ON p.brand_id = b.id 
                    ORDER BY p.id DESC";
            $res = $this->executeQuery($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $list[] = $row;
                }
            }
        } catch (Exception $e) {
            error_log("Lỗi getAvailableProductsForSale: " . $e->getMessage());
        }
        return $list;
    }
}