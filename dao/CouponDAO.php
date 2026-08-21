<?php
namespace DAO;

use Models\Coupon;
use Exception;

require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Coupon.php";

class CouponDAO extends BaseDAO
{
    private static bool $tableChecked = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableAndColumnsExist();
    }

    /**
     * Tự động kiểm tra và tạo bảng coupons, cập nhật cột vào bảng orders
     */
    private function ensureTableAndColumnsExist(): void
    {
        if (self::$tableChecked) {
            return;
        }

        try {
            // 1. Tạo bảng coupons nếu chưa có
            $sqlCoupons = "CREATE TABLE IF NOT EXISTS `coupons` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `code` varchar(50) NOT NULL UNIQUE,
                `name` varchar(150) NOT NULL,
                `description` text DEFAULT NULL,
                `discount_type` enum('fixed','percent') NOT NULL DEFAULT 'fixed',
                `discount_value` decimal(12,2) NOT NULL,
                `min_order_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
                `max_discount_amount` decimal(12,2) DEFAULT NULL,
                `usage_limit` int(11) NOT NULL DEFAULT 100,
                `used_count` int(11) NOT NULL DEFAULT 0,
                `start_date` datetime DEFAULT NULL,
                `end_date` datetime DEFAULT NULL,
                `status` tinyint(4) NOT NULL DEFAULT 1,
                `created_at` datetime DEFAULT current_timestamp(),
                `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_code` (`code`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            $this->conn->query($sqlCoupons);

            // 2. Thêm cột coupon_code và discount_amount vào bảng orders nếu chưa có
            $ordersCols = $this->conn->query("SHOW COLUMNS FROM `orders` LIKE 'coupon_code'");
            if ($ordersCols && $ordersCols->num_rows === 0) {
                $this->conn->query("ALTER TABLE `orders` ADD COLUMN `coupon_code` VARCHAR(50) DEFAULT NULL AFTER `payment_method`, ADD COLUMN `discount_amount` DECIMAL(12,2) DEFAULT 0.00 AFTER `coupon_code`");
            }

            // 3. Khởi tạo mã giảm giá mẫu nếu bảng rỗng
            $countRes = $this->conn->query("SELECT COUNT(*) as total FROM `coupons`");
            if ($countRes && $r = $countRes->fetch_assoc()) {
                if ((int)$r['total'] === 0) {
                    $this->seedInitialCoupons();
                }
            }

            self::$tableChecked = true;
        } catch (Exception $e) {
            error_log("Lỗi tạo bảng coupons: " . $e->getMessage());
        }
    }

    /**
     * Dữ liệu mã giảm giá mẫu Shopee-style
     */
    private function seedInitialCoupons(): void
    {
        $samples = [
            [
                'code' => 'MINISHOP20K',
                'name' => 'Giảm 20.000 đ cho khách hàng mới',
                'description' => 'Áp dụng cho mọi đơn hàng từ 150.000 đ trên toàn hệ thống MINISHOP.',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'min_order_amount' => 150000,
                'max_discount_amount' => null,
                'usage_limit' => 200,
                'used_count' => 5,
                'start_date' => date('Y-m-d 00:00:00'),
                'end_date' => date('Y-m-d 23:59:59', strtotime('+60 days')),
                'status' => 1
            ],
            [
                'code' => 'FREESHIP30K',
                'name' => 'Mã miễn phí vận chuyển 30.000 đ',
                'description' => 'Giảm ngay 30.000 đ phí ship cho đơn hàng từ 300.000 đ.',
                'discount_type' => 'fixed',
                'discount_value' => 30000,
                'min_order_amount' => 300000,
                'max_discount_amount' => null,
                'usage_limit' => 500,
                'used_count' => 12,
                'start_date' => date('Y-m-d 00:00:00'),
                'end_date' => date('Y-m-d 23:59:59', strtotime('+90 days')),
                'status' => 1
            ],
            [
                'code' => 'SALE10',
                'name' => 'Voucher giảm 10% tối đa 50.000 đ',
                'description' => 'Ưu đãi 10% cho đơn hàng từ 250.000 đ, mức giảm tối đa 50k.',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_amount' => 250000,
                'max_discount_amount' => 50000,
                'usage_limit' => 150,
                'used_count' => 8,
                'start_date' => date('Y-m-d 00:00:00'),
                'end_date' => date('Y-m-d 23:59:59', strtotime('+45 days')),
                'status' => 1
            ],
            [
                'code' => 'VIP50K',
                'name' => 'Voucher VIP giảm 50.000 đ',
                'description' => 'Giảm 50.000 đ trực tiếp cho đơn hàng giá trị từ 500.000 đ.',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_amount' => 500000,
                'max_discount_amount' => null,
                'usage_limit' => 100,
                'used_count' => 3,
                'start_date' => date('Y-m-d 00:00:00'),
                'end_date' => date('Y-m-d 23:59:59', strtotime('+30 days')),
                'status' => 1
            ]
        ];

        foreach ($samples as $s) {
            $stmt = $this->conn->prepare("INSERT INTO `coupons` (code, name, description, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, used_count, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdddiissi", $s['code'], $s['name'], $s['description'], $s['discount_type'], $s['discount_value'], $s['min_order_amount'], $s['max_discount_amount'], $s['usage_limit'], $s['used_count'], $s['start_date'], $s['end_date'], $s['status']);
            $stmt->execute();
        }
    }

    /**
     * Lấy toàn bộ danh sách mã giảm giá
     */
    public function getAll(): array
    {
        $coupons = [];
        $sql = "SELECT * FROM `coupons` ORDER BY `id` DESC";
        $res = $this->conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $coupons[] = new Coupon($row);
            }
        }
        return $coupons;
    }

    /**
     * Lấy danh sách phân trang và tìm kiếm
     */
    public function getPage(int $limit, int $offset, string $keyword = ''): array
    {
        $coupons = [];
        $sql = "SELECT * FROM `coupons`";
        if (!empty($keyword)) {
            $kw = "%" . $this->conn->real_escape_string($keyword) . "%";
            $sql .= " WHERE `code` LIKE ? OR `name` LIKE ? OR `description` LIKE ?";
        }
        $sql .= " ORDER BY `id` DESC LIMIT ? OFFSET ?";

        $stmt = $this->prepare($sql);
        if (!empty($keyword)) {
            $stmt->bind_param("sssii", $kw, $kw, $kw, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $coupons[] = new Coupon($row);
        }
        return $coupons;
    }

    /**
     * Đếm tổng số bản ghi phục vụ phân trang
     */
    public function count(string $keyword = ''): int
    {
        $sql = "SELECT COUNT(*) as total FROM `coupons`";
        if (!empty($keyword)) {
            $kw = "%" . $this->conn->real_escape_string($keyword) . "%";
            $sql .= " WHERE `code` LIKE ? OR `name` LIKE ? OR `description` LIKE ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sss", $kw, $kw, $kw);
            $stmt->execute();
            $res = $stmt->get_result();
        } else {
            $res = $this->conn->query($sql);
        }

        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }

    /**
     * Tìm mã giảm giá theo ID
     */
    public function findById(int $id): ?Coupon
    {
        $stmt = $this->prepare("SELECT * FROM `coupons` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return new Coupon($row);
        }
        return null;
    }

    /**
     * Tìm mã giảm giá theo Mã Code (không phân biệt chữ hoa/thường)
     */
    public function findByCode(string $code): ?Coupon
    {
        $codeClean = strtoupper(trim($code));
        $stmt = $this->prepare("SELECT * FROM `coupons` WHERE UPPER(`code`) = ?");
        $stmt->bind_param("s", $codeClean);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return new Coupon($row);
        }
        return null;
    }

    /**
     * Lấy các mã giảm giá đang kích hoạt còn hạn dùng (để hiển thị popup Shopee Voucher)
     */
    public function getActiveCoupons(): array
    {
        $coupons = [];
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM `coupons` 
                WHERE `status` = 1 
                  AND (`start_date` IS NULL OR `start_date` <= ?)
                  AND (`end_date` IS NULL OR `end_date` >= ?)
                  AND (`usage_limit` = 0 OR `used_count` < `usage_limit`)
                ORDER BY `discount_value` DESC";

        $stmt = $this->prepare($sql);
        $stmt->bind_param("ss", $now, $now);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $coupons[] = new Coupon($row);
        }
        return $coupons;
    }

    /**
     * Thêm mới mã giảm giá
     */
    public function insert(Coupon $c): int|false
    {
        try {
            $sql = "INSERT INTO `coupons` (`code`, `name`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->prepare($sql);
            $code = strtoupper(trim($c->code));
            $stmt->bind_param("ssssdddiissi", 
                $code, 
                $c->name, 
                $c->description, 
                $c->discountType, 
                $c->discountValue, 
                $c->minOrderAmount, 
                $c->maxDiscountAmount, 
                $c->usageLimit, 
                $c->usedCount, 
                $c->startDate, 
                $c->endDate, 
                $c->status
            );
            if ($stmt->execute()) {
                return $stmt->insert_id;
            }
        } catch (Exception $e) {
            error_log("Lỗi insert Coupon: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Cập nhật mã giảm giá
     */
    public function update(Coupon $c): bool
    {
        try {
            $sql = "UPDATE `coupons` SET 
                        `code` = ?, 
                        `name` = ?, 
                        `description` = ?, 
                        `discount_type` = ?, 
                        `discount_value` = ?, 
                        `min_order_amount` = ?, 
                        `max_discount_amount` = ?, 
                        `usage_limit` = ?, 
                        `used_count` = ?, 
                        `start_date` = ?, 
                        `end_date` = ?, 
                        `status` = ?, 
                        `updated_at` = NOW() 
                    WHERE `id` = ?";
            $stmt = $this->prepare($sql);
            $code = strtoupper(trim($c->code));
            $stmt->bind_param("ssssdddiissii", 
                $code, 
                $c->name, 
                $c->description, 
                $c->discountType, 
                $c->discountValue, 
                $c->minOrderAmount, 
                $c->maxDiscountAmount, 
                $c->usageLimit, 
                $c->usedCount, 
                $c->startDate, 
                $c->endDate, 
                $c->status,
                $c->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Lỗi update Coupon: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Xóa mã giảm giá
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->prepare("DELETE FROM `coupons` WHERE `id` = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Tăng số lượt đã sử dụng của coupon lên 1
     */
    public function incrementUsedCount(string $code): void
    {
        try {
            $codeClean = strtoupper(trim($code));
            $stmt = $this->prepare("UPDATE `coupons` SET `used_count` = `used_count` + 1 WHERE UPPER(`code`) = ?");
            $stmt->bind_param("s", $codeClean);
            $stmt->execute();
        } catch (Exception $e) {}
    }
}
