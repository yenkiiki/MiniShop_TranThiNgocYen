<?php
namespace DAO;

use Models\ProductVariant;
use Exception;

require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/ProductVariant.php";

class ProductVariantDAO extends BaseDAO
{
    private static bool $tableChecked = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    /**
     * Tự động kiểm tra và tạo bảng product_variants nếu chưa tồn tại
     */
    private function ensureTableExists(): void
    {
        if (self::$tableChecked) {
            return;
        }

        try {
            $sql = "CREATE TABLE IF NOT EXISTS `product_variants` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `product_id` int(11) NOT NULL,
                `variant_name` varchar(150) NOT NULL,
                `sku` varchar(100) DEFAULT NULL,
                `price` decimal(10,0) DEFAULT NULL,
                `discount_price` decimal(10,0) DEFAULT NULL,
                `quantity` int(11) NOT NULL DEFAULT 10,
                `image` varchar(255) DEFAULT NULL,
                `sort_order` int(11) NOT NULL DEFAULT 0,
                `status` tinyint(4) NOT NULL DEFAULT 1,
                `created_at` datetime DEFAULT current_timestamp(),
                `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `fk_product_variants_products` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            $this->conn->query($sql);

            // Kiểm tra xem đã có dữ liệu mẫu chưa, nếu chưa có thì gán dữ liệu mẫu
            $checkCountSql = "SELECT COUNT(*) as total FROM `product_variants`";
            $res = $this->conn->query($checkCountSql);
            if ($res && $row = $res->fetch_assoc()) {
                if ((int)$row['total'] === 0) {
                    $this->seedInitialVariants();
                }
            }

            self::$tableChecked = true;
        } catch (Exception $e) {
            error_log("Lỗi tạo bảng product_variants: " . $e->getMessage());
        }
    }

    /**
     * Khởi tạo biến thể mẫu liên kết ảnh chính và các ảnh phụ cho sản phẩm mẫu
     */
    public function seedInitialVariants(): void
    {
        try {
            // Lấy danh sách toàn bộ sản phẩm hiện có
            $productsRes = $this->conn->query("SELECT id, proname, price, discount_price, quantity, image FROM products");
            if (!$productsRes) {
                return;
            }

            while ($p = $productsRes->fetch_assoc()) {
                $pId = (int)$p['id'];
                $pName = $p['proname'];
                $mainImg = $p['image'];
                $price = (float)$p['price'];
                $discPrice = (float)$p['discount_price'];

                // Kiểm tra xem sản phẩm đã có biến thể chưa
                $checkP = $this->conn->query("SELECT COUNT(*) as c FROM product_variants WHERE product_id = $pId");
                if ($checkP && $r = $checkP->fetch_assoc()) {
                    if ((int)$r['c'] > 0) {
                        continue;
                    }
                }

                // Lấy các ảnh phụ của sản phẩm
                $galleryRes = $this->conn->query("SELECT image FROM product_images WHERE product_id = $pId ORDER BY sort_order, id");
                $gallery = [];
                if ($galleryRes) {
                    while ($g = $galleryRes->fetch_assoc()) {
                        $gallery[] = $g['image'];
                    }
                }

                $stmt = $this->conn->prepare("INSERT INTO product_variants (product_id, variant_name, sku, price, discount_price, quantity, image, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stt1 = 1;

                // 1. Biến thể 1 gắn với Ảnh chính
                $v1Name = "Phiên bản Tiêu chuẩn (Màu Đen / Bản Gốc)";
                if (stripos($pName, 'áo') !== false) $v1Name = "Size M - Màu Tiêu Chuẩn";
                elseif (stripos($pName, 'quần') !== false) $v1Name = "Size 30 - Màu Đen";
                elseif (stripos($pName, 'giày') !== false) $v1Name = "Size 40 - Màu Trắng";
                elseif (stripos($pName, 'ví') !== false || stripos($pName, 'thắt lưng') !== false) $v1Name = "Da Bò Đen Nhám";
                elseif (stripos($pName, 'chuột') !== false) $v1Name = "Màu Đen Nhám (Black)";
                elseif (stripos($pName, 'bàn phím') !== false) $v1Name = "Green Switch (Clicky)";

                $sku1 = "SKU-" . $pId . "-01";
                $qty1 = max(10, (int)$p['quantity']);
                $sort1 = 0;
                $stmt->bind_param("issddisii", $pId, $v1Name, $sku1, $price, $discPrice, $qty1, $mainImg, $sort1, $stt1);
                $stmt->execute();

                // 2. Biến thể 2 gắn với Ảnh phụ 1 (nếu có, hoặc biến thể thứ 2)
                $v2Img = !empty($gallery[0]) ? $gallery[0] : $mainImg;
                $v2Name = "Phiên bản Nâng cấp (Màu Trắng / Bản Pro)";
                if (stripos($pName, 'áo') !== false) $v2Name = "Size L - Màu Trắng";
                elseif (stripos($pName, 'quần') !== false) $v2Name = "Size 31 - Màu Xanh Đậm";
                elseif (stripos($pName, 'giày') !== false) $v2Name = "Size 41 - Màu Đen";
                elseif (stripos($pName, 'ví') !== false || stripos($pName, 'thắt lưng') !== false) $v2Name = "Da Bò Nâu Cà Phê";
                elseif (stripos($pName, 'chuột') !== false) $v2Name = "Màu Trắng Ngọc (White)";
                elseif (stripos($pName, 'bàn phím') !== false) $v2Name = "Yellow Switch (Linear Silent)";

                $sku2 = "SKU-" . $pId . "-02";
                $price2 = $price > 0 ? ($price + 50000) : $price;
                $disc2 = $discPrice > 0 ? ($discPrice + 50000) : 0;
                $qty2 = 12;
                $sort2 = 1;
                $stmt->bind_param("issddisii", $pId, $v2Name, $sku2, $price2, $disc2, $qty2, $v2Img, $sort2, $stt1);
                $stmt->execute();

                // 3. Biến thể 3 gắn với Ảnh phụ 2 (nếu có)
                if (!empty($gallery[1])) {
                    $v3Name = "Phiên bản Đặc biệt (Màu Hồng / Cao cấp)";
                    if (stripos($pName, 'áo') !== false) $v3Name = "Size XL - Màu Xanh";
                    elseif (stripos($pName, 'quần') !== false) $v3Name = "Size 32 - Màu Xám Khói";
                    elseif (stripos($pName, 'chuột') !== false) $v3Name = "Màu Hồng Magenta";

                    $sku3 = "SKU-" . $pId . "-03";
                    $price3 = $price > 0 ? ($price + 100000) : $price;
                    $disc3 = $discPrice > 0 ? ($discPrice + 100000) : 0;
                    $qty3 = 8;
                    $sort3 = 2;
                    $stmt->bind_param("issddisii", $pId, $v3Name, $sku3, $price3, $disc3, $qty3, $gallery[1], $sort3, $stt1);
                    $stmt->execute();
                }
            }
        } catch (Exception $e) {
            error_log("Lỗi seedInitialVariants: " . $e->getMessage());
        }
    }

    private function mapRowToVariant(array $row): ProductVariant
    {
        $v = new ProductVariant();
        $v->id = (int)$row["id"];
        $v->productId = (int)$row["product_id"];
        $v->variantName = $row["variant_name"] ?? '';
        $v->sku = $row["sku"] ?? null;
        $v->price = isset($row["price"]) ? (float)$row["price"] : null;
        $v->discountPrice = isset($row["discount_price"]) ? (float)$row["discount_price"] : null;
        $v->quantity = (int)($row["quantity"] ?? 0);
        $v->image = $row["image"] ?? null;
        $v->sortOrder = (int)($row["sort_order"] ?? 0);
        $v->status = (int)($row["status"] ?? 1);
        $v->createdAt = $row["created_at"] ?? null;
        $v->updatedAt = $row["updated_at"] ?? null;
        return $v;
    }

    /**
     * Lấy danh sách biến thể theo ID sản phẩm
     */
    public function getByProductId(int $productId): array
    {
        $list = [];
        try {
            $sql = "SELECT * FROM product_variants WHERE product_id = ? ORDER BY sort_order ASC, id ASC";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $list[] = $this->mapRowToVariant($row);
            }
        } catch (Exception $e) {
            error_log("Lỗi getByProductId: " . $e->getMessage());
        }
        return $list;
    }

    /**
     * Tìm biến thể theo ID
     */
    public function findById(int $id): ?ProductVariant
    {
        try {
            $sql = "SELECT * FROM product_variants WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $this->mapRowToVariant($row);
            }
        } catch (Exception $e) {
            error_log("Lỗi findById variant: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Thêm mới 1 biến thể
     */
    public function insert(ProductVariant $variant): int
    {
        try {
            $sql = "INSERT INTO product_variants (product_id, variant_name, sku, price, discount_price, quantity, image, sort_order, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "issddisii",
                $variant->productId,
                $variant->variantName,
                $variant->sku,
                $variant->price,
                $variant->discountPrice,
                $variant->quantity,
                $variant->image,
                $variant->sortOrder,
                $variant->status
            );
            if ($stmt->execute()) {
                return $stmt->insert_id;
            }
        } catch (Exception $e) {
            error_log("Lỗi insert variant: " . $e->getMessage());
        }
        return 0;
    }

    /**
     * Cập nhật 1 biến thể
     */
    public function update(ProductVariant $variant): bool
    {
        try {
            $sql = "UPDATE product_variants 
                    SET variant_name=?, sku=?, price=?, discount_price=?, quantity=?, image=?, sort_order=?, status=? 
                    WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssddisiii",
                $variant->variantName,
                $variant->sku,
                $variant->price,
                $variant->discountPrice,
                $variant->quantity,
                $variant->image,
                $variant->sortOrder,
                $variant->status,
                $variant->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Lỗi update variant: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Xóa biến thể theo ID
     */
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM product_variants WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Lỗi delete variant: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Xóa tất cả biến thể của 1 sản phẩm
     */
    public function deleteByProductId(int $productId): bool
    {
        try {
            $sql = "DELETE FROM product_variants WHERE product_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $productId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Lỗi deleteByProductId: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Đồng bộ danh sách biến thể từ Form Admin (Thêm/Sửa sản phẩm)
     * Quy tắc liên kết hình ảnh theo đúng yêu cầu:
     * - Biến thể 1 (Index 0) -> Gắn với Ảnh chính ($mainImage) nếu không chỉ định ảnh riêng
     * - Biến thể 2 (Index 1) -> Gắn với Ảnh phụ 1 ($galleryImages[0]) nếu không chỉ định ảnh riêng
     * - Biến thể 3 (Index 2) -> Gắn với Ảnh phụ 2 ($galleryImages[1]) nếu không chỉ định ảnh riêng
     * - ...
     */
    public function syncVariants(int $productId, array $variantsInput, ?string $mainImage = null, array $galleryImages = []): void
    {
        if (empty($variantsInput)) {
            return;
        }

        // Lấy danh sách biến thể cũ để biết id nào bị xóa
        $existing = $this->getByProductId($productId);
        $existingMap = [];
        foreach ($existing as $ex) {
            $existingMap[$ex->id] = $ex;
        }

        $keptIds = [];

        foreach ($variantsInput as $index => $item) {
            $variantName = trim($item['variant_name'] ?? $item['name'] ?? '');
            if (empty($variantName)) {
                continue;
            }

            $vId = isset($item['id']) ? (int)$item['id'] : 0;
            $sku = !empty($item['sku']) ? trim($item['sku']) : ("SKU-" . $productId . "-" . ($index + 1));
            $price = isset($item['price']) && $item['price'] !== '' ? (float)$item['price'] : null;
            $discountPrice = isset($item['discount_price']) && $item['discount_price'] !== '' ? (float)$item['discount_price'] : null;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 10;
            $status = isset($item['status']) ? (int)$item['status'] : 1;

            // Xử lý ảnh liên kết cho biến thể:
            // 1. Nếu có file ảnh riêng hoặc chuỗi ảnh được chọn
            $image = !empty($item['image']) ? trim($item['image']) : null;

            // 2. Nếu chưa có ảnh, tự động map theo quy tắc:
            // Biến thể 1 -> Ảnh chính
            // Biến thể 2 -> Ảnh phụ 1
            // Biến thể 3 -> Ảnh phụ 2, v.v.
            if (empty($image)) {
                if ($index === 0 && !empty($mainImage)) {
                    $image = $mainImage;
                } elseif ($index > 0 && isset($galleryImages[$index - 1])) {
                    $image = $galleryImages[$index - 1];
                } elseif (!empty($mainImage)) {
                    $image = $mainImage;
                }
            }

            $variant = new ProductVariant();
            $variant->productId = $productId;
            $variant->variantName = $variantName;
            $variant->sku = $sku;
            $variant->price = $price;
            $variant->discountPrice = $discountPrice;
            $variant->quantity = $quantity;
            $variant->image = $image;
            $variant->sortOrder = $index;
            $variant->status = $status;

            if ($vId > 0 && isset($existingMap[$vId])) {
                $variant->id = $vId;
                $this->update($variant);
                $keptIds[] = $vId;
            } else {
                $newId = $this->insert($variant);
                if ($newId > 0) {
                    $keptIds[] = $newId;
                }
            }
        }

        // Xóa các biến thể không còn trong danh sách gửi lên
        foreach ($existingMap as $oldId => $oldObj) {
            if (!in_array($oldId, $keptIds)) {
                $this->delete($oldId);
            }
        }
    }
}
