<?php
namespace DAO;

use Models\Review;
use Exception;

class ReviewDAO extends BaseDAO
{
    /**
     * Kiểm tra xem người dùng có đủ điều kiện đánh giá sản phẩm trong đơn hàng này không
     * Điều kiện bắt buộc:
     * 1. Đơn hàng phải thuộc về user_id này
     * 2. Đơn hàng phải ở trạng thái "Đã giao" (status = 3)
     * 3. Sản phẩm phải nằm trong chi tiết đơn hàng
     * 4. Chưa từng đánh giá sản phẩm này trong đơn hàng đó (mỗi đơn hàng được đánh giá 1 lần)
     */
    public function canUserReview(int $userId, int $orderId, int $productId): array
    {
        if ($userId <= 0 || $orderId <= 0 || $productId <= 0) {
            return ['can_review' => false, 'reason' => 'Thông tin không hợp lệ.'];
        }

        // 1. Kiểm tra đơn hàng có thuộc về user và trạng thái = 3 (Đã giao)
        $sqlOrder = "SELECT o.id, o.status, o.order_code FROM orders o WHERE o.id = ? AND o.user_id = ?";
        $stmtOrder = $this->prepare($sqlOrder);
        $stmtOrder->bind_param("ii", $orderId, $userId);
        $stmtOrder->execute();
        $orderRes = $stmtOrder->get_result();
        $order = $orderRes->fetch_assoc();

        if (!$order) {
            return ['can_review' => false, 'reason' => 'Đơn hàng không tồn tại hoặc không thuộc tài khoản của bạn.'];
        }

        if ((int)$order['status'] !== 3) {
            return ['can_review' => false, 'reason' => 'Đơn hàng phải ở trạng thái Giao hàng thành công (Đã giao) mới được phép đánh giá.'];
        }

        // 2. Kiểm tra sản phẩm có trong đơn hàng này không
        $sqlDetail = "SELECT id FROM order_details WHERE order_id = ? AND product_id = ?";
        $stmtDetail = $this->prepare($sqlDetail);
        $stmtDetail->bind_param("ii", $orderId, $productId);
        $stmtDetail->execute();
        $detailRes = $stmtDetail->get_result();
        if ($detailRes->num_rows === 0) {
            return ['can_review' => false, 'reason' => 'Sản phẩm không có trong đơn hàng này.'];
        }

        // 3. Kiểm tra xem đã đánh giá sản phẩm này trong đơn hàng này chưa
        $sqlCheck = "SELECT id, rating FROM product_reviews WHERE user_id = ? AND order_id = ? AND product_id = ?";
        $stmtCheck = $this->prepare($sqlCheck);
        $stmtCheck->bind_param("iii", $userId, $orderId, $productId);
        $stmtCheck->execute();
        $checkRes = $stmtCheck->get_result();
        if ($existing = $checkRes->fetch_assoc()) {
            return [
                'can_review' => false,
                'reason' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng ' . $order['order_code'] . '.',
                'already_reviewed' => true,
                'rating' => (int)$existing['rating']
            ];
        }

        return ['can_review' => true, 'reason' => 'Đủ điều kiện đánh giá.'];
    }

    /**
     * Tạo mới một đánh giá sản phẩm (Bất biến - Không cho phép chỉnh sửa sau khi gửi)
     */
    public function createReview(int $userId, int $orderId, int $productId, int $rating, string $comment, string $fullname = ''): bool
    {
        $check = $this->canUserReview($userId, $orderId, $productId);
        if (!$check['can_review']) {
            throw new Exception($check['reason']);
        }

        $rating = max(1, min(5, $rating));
        $comment = trim($comment);
        if (empty($comment)) {
            throw new Exception("Vui lòng nhập nội dung đánh giá sản phẩm.");
        }

        if (empty($fullname)) {
            // Lấy tên user từ DB
            $sqlU = "SELECT fullname FROM users WHERE id = ?";
            $stU = $this->prepare($sqlU);
            $stU->bind_param("i", $userId);
            $stU->execute();
            $resU = $stU->get_result();
            if ($rowU = $resU->fetch_assoc()) {
                $fullname = $rowU['fullname'];
            }
        }

        $sql = "INSERT INTO product_reviews (product_id, user_id, order_id, fullname, rating, comment, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("iiisis", $productId, $userId, $orderId, $fullname, $rating, $comment);
        return $stmt->execute();
    }

    /**
     * Lấy tất cả đánh giá công khai của một sản phẩm
     */
    public function getByProductId(int $productId): array
    {
        $sql = "SELECT r.*, u.fullname AS user_fullname, p.proname, p.image, o.order_code 
                FROM product_reviews r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN orders o ON r.order_id = o.id
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = new Review($row);
        }
        return $reviews;
    }

    /**
     * Tính toán tổng quan đánh giá (Điểm trung bình, số lượng đánh giá từng mức sao)
     */
    public function getRatingSummary(int $productId): array
    {
        $sql = "SELECT rating, COUNT(*) as count FROM product_reviews WHERE product_id = ? GROUP BY rating";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $totalReviews = 0;
        $totalPoints = 0;

        while ($row = $result->fetch_assoc()) {
            $r = (int)$row['rating'];
            $c = (int)$row['count'];
            if (isset($counts[$r])) {
                $counts[$r] = $c;
            }
            $totalReviews += $c;
            $totalPoints += ($r * $c);
        }

        $average = $totalReviews > 0 ? round($totalPoints / $totalReviews, 1) : 5.0;

        $percents = [];
        foreach ($counts as $star => $count) {
            $percents[$star] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        }

        return [
            'average' => $average,
            'total' => $totalReviews,
            'counts' => $counts,
            'percents' => $percents
        ];
    }

    /**
     * Lấy danh sách ID các sản phẩm đã được đánh giá trong đơn hàng của user
     */
    public function getReviewedProductsMap(int $userId, int $orderId): array
    {
        $sql = "SELECT product_id, rating, comment, created_at FROM product_reviews WHERE user_id = ? AND order_id = ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ii", $userId, $orderId);
        $stmt->execute();
        $result = $stmt->get_result();

        $map = [];
        while ($row = $result->fetch_assoc()) {
            $map[(int)$row['product_id']] = [
                'rating' => (int)$row['rating'],
                'comment' => $row['comment'],
                'created_at' => $row['created_at']
            ];
        }
        return $map;
    }

    /**
     * Lấy lịch sử tất cả đơn hàng của một người dùng kèm thông tin chi tiết và trạng thái đánh giá
     */
    public function getUserOrderHistory(int $userId): array
    {
        $sql = "SELECT o.*, c.fullname AS customer_fullname, c.phone AS customer_phone, c.address AS customer_address 
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC, o.id DESC";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        $orders = [];
        while ($order = $res->fetch_assoc()) {
            $orderId = (int)$order['id'];

            // Lấy danh sách sản phẩm trong đơn
            $sqlDet = "SELECT od.*, p.proname, p.image, p.slug 
                       FROM order_details od
                       LEFT JOIN products p ON od.product_id = p.id
                       WHERE od.order_id = ?";
            $stDet = $this->prepare($sqlDet);
            $stDet->bind_param("i", $orderId);
            $stDet->execute();
            $resDet = $stDet->get_result();

            $items = [];
            while ($item = $resDet->fetch_assoc()) {
                $items[] = $item;
            }

            // Lấy map các sản phẩm đã đánh giá trong đơn này
            $reviewedMap = $this->getReviewedProductsMap($userId, $orderId);

            foreach ($items as &$it) {
                $pId = (int)$it['product_id'];
                $it['is_reviewed'] = isset($reviewedMap[$pId]);
                $it['review_data'] = $reviewedMap[$pId] ?? null;
                $it['can_review'] = ((int)$order['status'] === 3) && !$it['is_reviewed'];
            }
            unset($it);

            $order['items'] = $items;
            $orders[] = $order;
        }

        return $orders;
    }
}
