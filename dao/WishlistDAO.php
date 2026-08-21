<?php
namespace DAO;

use Models\Wishlist;
use Exception;

require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Wishlist.php";

class WishlistDAO extends BaseDAO
{
    private static bool $tableChecked = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    /**
     * Tự động kiểm tra và tạo bảng wishlists nếu chưa tồn tại
     */
    private function ensureTableExists(): void
    {
        if (self::$tableChecked) {
            return;
        }

        try {
            $sql = "CREATE TABLE IF NOT EXISTS `wishlists` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) DEFAULT NULL,
                `session_id` varchar(100) DEFAULT NULL,
                `product_id` int(11) NOT NULL,
                `created_at` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_user` (`user_id`),
                KEY `idx_session` (`session_id`),
                KEY `idx_product` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

            $this->conn->query($sql);
            self::$tableChecked = true;
        } catch (Exception $e) {
            error_log("Lỗi tạo bảng wishlists: " . $e->getMessage());
        }
    }

    /**
     * Thêm hoặc xóa sản phẩm khỏi danh sách yêu thích (Toggle)
     */
    public function toggle(int $productId, ?int $userId = null, string $sessionId = ''): array
    {
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Sản phẩm không hợp lệ.'];
        }

        try {
            // Kiểm tra xem đã tồn tại chưa
            $exists = false;
            $existId = 0;

            if ($userId !== null && $userId > 0) {
                $checkStmt = $this->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
                $checkStmt->bind_param("ii", $userId, $productId);
            } else {
                $checkStmt = $this->prepare("SELECT id FROM wishlists WHERE session_id = ? AND product_id = ?");
                $checkStmt->bind_param("si", $sessionId, $productId);
            }

            $checkStmt->execute();
            $res = $checkStmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $exists = true;
                $existId = (int)$row['id'];
            }

            if ($exists) {
                // Đã có -> Xóa khỏi wishlist
                $delStmt = $this->prepare("DELETE FROM wishlists WHERE id = ?");
                $delStmt->bind_param("i", $existId);
                $delStmt->execute();

                $totalCount = $this->getCount($userId, $sessionId);
                return [
                    'success' => true,
                    'status' => 'removed',
                    'is_favorite' => false,
                    'count' => $totalCount,
                    'message' => 'Đã bỏ sản phẩm khỏi danh sách yêu thích.'
                ];
            } else {
                // Chưa có -> Thêm mới vào wishlist
                if ($userId !== null && $userId > 0) {
                    $addStmt = $this->prepare("INSERT INTO wishlists (user_id, session_id, product_id, created_at) VALUES (?, ?, ?, NOW())");
                    $addStmt->bind_param("isi", $userId, $sessionId, $productId);
                } else {
                    $addStmt = $this->prepare("INSERT INTO wishlists (session_id, product_id, created_at) VALUES (?, ?, NOW())");
                    $addStmt->bind_param("si", $sessionId, $productId);
                }
                $addStmt->execute();

                $totalCount = $this->getCount($userId, $sessionId);
                return [
                    'success' => true,
                    'status' => 'added',
                    'is_favorite' => true,
                    'count' => $totalCount,
                    'message' => 'Đã thêm sản phẩm vào danh sách yêu thích! ❤️'
                ];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa sản phẩm khỏi wishlist
     */
    public function remove(int $productId, ?int $userId = null, string $sessionId = ''): bool
    {
        try {
            if ($userId !== null && $userId > 0) {
                $stmt = $this->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $userId, $productId);
            } else {
                $stmt = $this->prepare("DELETE FROM wishlists WHERE session_id = ? AND product_id = ?");
                $stmt->bind_param("si", $sessionId, $productId);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Đếm tổng số sản phẩm trong wishlist
     */
    public function getCount(?int $userId = null, string $sessionId = ''): int
    {
        try {
            if ($userId !== null && $userId > 0) {
                $stmt = $this->prepare("SELECT COUNT(DISTINCT product_id) as total FROM wishlists WHERE user_id = ?");
                $stmt->bind_param("i", $userId);
            } else {
                $stmt = $this->prepare("SELECT COUNT(DISTINCT product_id) as total FROM wishlists WHERE session_id = ?");
                $stmt->bind_param("s", $sessionId);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                return (int)$row['total'];
            }
        } catch (Exception $e) {}
        return 0;
    }

    /**
     * Lấy danh sách ID các sản phẩm yêu thích (dùng để kiểm tra hiển thị tim đỏ ở mọi nơi)
     */
    public function getFavoriteProductIds(?int $userId = null, string $sessionId = ''): array
    {
        $ids = [];
        try {
            if ($userId !== null && $userId > 0) {
                $stmt = $this->prepare("SELECT DISTINCT product_id FROM wishlists WHERE user_id = ?");
                $stmt->bind_param("i", $userId);
            } else {
                $stmt = $this->prepare("SELECT DISTINCT product_id FROM wishlists WHERE session_id = ?");
                $stmt->bind_param("s", $sessionId);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $ids[] = (int)$row['product_id'];
            }
        } catch (Exception $e) {}
        return $ids;
    }

    /**
     * Lấy danh sách sản phẩm đầy đủ trong wishlist
     */
    public function getItems(?int $userId = null, string $sessionId = ''): array
    {
        $items = [];
        try {
            $sql = "SELECT w.id as wishlist_id, w.created_at as added_at, 
                           p.*, c.catename, b.brandname 
                    FROM wishlists w
                    JOIN products p ON w.product_id = p.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN brands b ON p.brand_id = b.id
                    WHERE " . ($userId !== null && $userId > 0 ? "w.user_id = ?" : "w.session_id = ?") . "
                    GROUP BY p.id
                    ORDER BY w.id DESC";

            $stmt = $this->prepare($sql);
            if ($userId !== null && $userId > 0) {
                $stmt->bind_param("i", $userId);
            } else {
                $stmt->bind_param("s", $sessionId);
            }

            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $items[] = new Wishlist($row);
            }
        } catch (Exception $e) {
            error_log("Lỗi getItems Wishlist: " . $e->getMessage());
        }
        return $items;
    }

    /**
     * Đồng bộ wishlist từ guest session sang user sau khi đăng nhập
     */
    public function syncSessionToUser(string $sessionId, int $userId): void
    {
        if (empty($sessionId) || $userId <= 0) {
            return;
        }

        try {
            $stmt = $this->prepare("SELECT product_id FROM wishlists WHERE session_id = ?");
            $stmt->bind_param("s", $sessionId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $pId = (int)$row['product_id'];
                
                // Kiểm tra xem user đã có chưa
                $ch = $this->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
                $ch->bind_param("ii", $userId, $pId);
                $ch->execute();
                if ($ch->get_result()->num_rows === 0) {
                    $ins = $this->prepare("INSERT INTO wishlists (user_id, session_id, product_id, created_at) VALUES (?, ?, ?, NOW())");
                    $ins->bind_param("isi", $userId, $sessionId, $pId);
                    $ins->execute();
                }
            }

            // Xóa session_id cũ
            $del = $this->prepare("DELETE FROM wishlists WHERE session_id = ? AND (user_id IS NULL OR user_id = 0)");
            $del->bind_param("s", $sessionId);
            $del->execute();
        } catch (Exception $e) {}
    }
}
