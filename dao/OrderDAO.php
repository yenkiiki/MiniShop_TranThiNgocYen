<?php
namespace DAO;

use Models\Order;
use Models\OrderDetail;
use Exception;

require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Order.php";
require_once __DIR__ . "/../models/OrderDetail.php";

class OrderDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, shipping_fee, payment_method, note, status, created_at, updated_at FROM orders ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $order = new Order();
                $order->id = (int) $row["id"];
                $order->customerId = (int) $row["customer_id"];
                $order->userId = $row["user_id"] ? (int) $row["user_id"] : null;
                $order->orderCode = $row["order_code"];
                $order->totalAmount = (float) $row["total_amount"];
                $order->shippingFee = isset($row["shipping_fee"]) ? (float) $row["shipping_fee"] : 0.0;
                $order->paymentMethod = $row["payment_method"] ?? 'COD';
                $order->note = $row["note"];
                $order->status = (int) $row["status"];
                $order->createdAt = $row["created_at"];
                $order->updatedAt = $row["updated_at"];
                $list[] = $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Order
    {
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, shipping_fee, payment_method, note, status, created_at, updated_at FROM orders WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $order = new Order();
                $order->id = (int) $row["id"];
                $order->customerId = (int) $row["customer_id"];
                $order->userId = $row["user_id"] ? (int) $row["user_id"] : null;
                $order->orderCode = $row["order_code"];
                $order->totalAmount = (float) $row["total_amount"];
                $order->shippingFee = isset($row["shipping_fee"]) ? (float) $row["shipping_fee"] : 0.0;
                $order->paymentMethod = $row["payment_method"] ?? 'COD';
                $order->note = $row["note"];
                $order->status = (int) $row["status"];
                $order->createdAt = $row["created_at"];
                $order->updatedAt = $row["updated_at"];
                return $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function getInsertId(): int
    {
        return $this->conn->insert_id;
    }

    public function insert(Order $order): bool
    {
        try {
            $sql = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, shipping_fee, payment_method, note, status) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iisddssi",
                $order->customerId,
                $order->userId,
                $order->orderCode,
                $order->totalAmount,
                $order->shippingFee,
                $order->paymentMethod,
                $order->note,
                $order->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Order $order): bool
    {
        try {
            $sql = "UPDATE orders SET customer_id=?, user_id=?, order_code=?, total_amount=?, shipping_fee=?, payment_method=?, note=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iisddssii",
                $order->customerId,
                $order->userId,
                $order->orderCode,
                $order->totalAmount,
                $order->shippingFee,
                $order->paymentMethod,
                $order->note,
                $order->status,
                $order->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateOrderFullInfo(int $orderId, string $paymentMethod, float $shippingFee, float $totalAmount, ?string $note, int $status): bool
    {
        try {
            $sql = "UPDATE orders SET payment_method=?, shipping_fee=?, total_amount=?, note=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sddsii", $paymentMethod, $shippingFee, $totalAmount, $note, $status, $orderId);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM orders WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // --- Xử lý bảng order_details ---
    public function getDetailsByOrderId(int $orderId): array
    {
        $list = [];
        try {
            $sql = "SELECT od.id, od.order_id, od.product_id, od.quantity, od.price, od.subtotal, od.created_at,
                           p.proname as product_name, p.image as product_image
                    FROM order_details od
                    LEFT JOIN products p ON od.product_id = p.id
                    WHERE od.order_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $detail = new OrderDetail();
                $detail->id = (int) $row["id"];
                $detail->orderId = (int) $row["order_id"];
                $detail->productId = (int) $row["product_id"];
                $detail->quantity = (int) $row["quantity"];
                $detail->price = (float) $row["price"];
                $detail->subtotal = (float) $row["subtotal"];
                $detail->createdAt = $row["created_at"];
                // Custom properties for view convenience
                $detail->productName = $row["product_name"] ?? ('Sản phẩm #' . $row["product_id"]);
                $detail->productImage = $row["product_image"] ?? '';
                $list[] = $detail;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function calculateOrderSubtotal(int $orderId): float
    {
        try {
            $sql = "SELECT SUM(subtotal) as subtotal FROM order_details WHERE order_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return (float) ($row['subtotal'] ?? 0);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0.0;
    }

    public function insertDetail(OrderDetail $orderDetail): bool
    {
        try {
            $sql = "INSERT INTO order_details(order_id, product_id, quantity, price, subtotal) VALUES(?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iiidd",
                $orderDetail->orderId,
                $orderDetail->productId,
                $orderDetail->quantity,
                $orderDetail->price,
                $orderDetail->subtotal
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function countAll(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM orders";
            $result = $this->executeQuery($sql);
            if ($row = $result->fetch_assoc()) {
                return (int) $row["total"];
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
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, shipping_fee, payment_method, note, status, created_at, updated_at FROM orders ORDER BY id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $order = new Order();
                $order->id = (int) $row["id"];
                $order->customerId = (int) $row["customer_id"];
                $order->userId = $row["user_id"] ? (int) $row["user_id"] : null;
                $order->orderCode = $row["order_code"];
                $order->totalAmount = (float) $row["total_amount"];
                $order->shippingFee = isset($row["shipping_fee"]) ? (float) $row["shipping_fee"] : 0.0;
                $order->paymentMethod = $row["payment_method"] ?? 'COD';
                $order->note = $row["note"];
                $order->status = (int) $row["status"];
                $order->createdAt = $row["created_at"];
                $order->updatedAt = $row["updated_at"];
                $list[] = $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function updateStatus(int $id, int $status): bool
    {
        try {
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ii", $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function countSearch(string $keyword = "", $status = "", string $paymentMethod = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (o.order_code LIKE ? OR c.fullname LIKE ? OR c.phone LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            if ($status !== "" && $status !== null) {
                $sql .= " AND o.status = ?";
                $params[] = (int) $status;
                $types .= "i";
            }

            if (!empty($paymentMethod)) {
                if ($paymentMethod === 'Chuyển khoản' || $paymentMethod === 'ChuyenKhoan') {
                    $sql .= " AND (o.payment_method = 'Chuyển khoản' OR o.payment_method = 'ChuyenKhoan')";
                } else {
                    $sql .= " AND (o.payment_method = 'COD' OR o.payment_method IS NULL OR o.payment_method = '')";
                }
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

    // Lấy dữ liệu phân trang đơn hàng (có hỗ trợ tìm kiếm theo Mã đơn, Họ tên, SĐT và lọc theo TT, Payment)
    public function getPage(int $limit, int $offset, string $keyword = "", $status = "", string $paymentMethod = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT o.*, 
                           c.fullname as customer_name, 
                           c.phone as customer_phone, 
                           c.email as customer_email, 
                           c.address as customer_address, 
                           u.fullname as user_name 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE 1=1";

            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (o.order_code LIKE ? OR c.fullname LIKE ? OR c.phone LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            if ($status !== "" && $status !== null) {
                $sql .= " AND o.status = ?";
                $params[] = (int) $status;
                $types .= "i";
            }

            if (!empty($paymentMethod)) {
                if ($paymentMethod === 'Chuyển khoản' || $paymentMethod === 'ChuyenKhoan') {
                    $sql .= " AND (o.payment_method = 'Chuyển khoản' OR o.payment_method = 'ChuyenKhoan')";
                } else {
                    $sql .= " AND (o.payment_method = 'COD' OR o.payment_method IS NULL OR o.payment_method = '')";
                }
            }

            $sql .= " ORDER BY o.id DESC LIMIT ? OFFSET ?";
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
                $list[] = $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Lấy số liệu thống kê đơn hàng cho dashboard và trang danh sách
    public function getStatistics(): array
    {
        $stats = [
            'total' => 0,
            'pending' => 0,    // Chờ xác nhận (0)
            'confirmed' => 0,  // Đã xác nhận (1)
            'shipping' => 0,   // Đang giao (2)
            'completed' => 0,  // Đã giao (3)
            'cancelled' => 0,  // Đã hủy (4)
            'revenue' => 0.0   // Doanh thu (Đã giao)
        ];

        try {
            $sql = "SELECT 
                        COUNT(*) as total_orders,
                        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending_orders,
                        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as confirmed_orders,
                        SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as shipping_orders,
                        SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as completed_orders,
                        SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as cancelled_orders,
                        SUM(CASE WHEN status = 3 THEN total_amount ELSE 0 END) as total_revenue
                    FROM orders";
            $result = $this->executeQuery($sql);
            if ($row = $result->fetch_assoc()) {
                $stats['total'] = (int) ($row['total_orders'] ?? 0);
                $stats['pending'] = (int) ($row['pending_orders'] ?? 0);
                $stats['confirmed'] = (int) ($row['confirmed_orders'] ?? 0);
                $stats['shipping'] = (int) ($row['shipping_orders'] ?? 0);
                $stats['completed'] = (int) ($row['completed_orders'] ?? 0);
                $stats['cancelled'] = (int) ($row['cancelled_orders'] ?? 0);
                $stats['revenue'] = (float) ($row['total_revenue'] ?? 0);
            }
        } catch (Exception $e) {
            // ignore or log
        }

        return $stats;
    }

    public function findByOrderCode(string $orderCode): ?array
    {
        try {
            $sql = "SELECT o.*, 
                           c.fullname as customer_name, 
                           c.phone as customer_phone, 
                           c.email as customer_email, 
                           c.address as customer_address 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    WHERE o.order_code = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("s", $orderCode);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function updatePaymentStatus(string $orderCode, int $status, string $noteAppend = ''): bool
    {
        try {
            if (!empty($noteAppend)) {
                $sql = "UPDATE orders SET status = ?, note = CONCAT(IFNULL(note, ''), ' | ', ?) WHERE order_code = ?";
                $stmt = $this->prepare($sql);
                $stmt->bind_param("iss", $status, $noteAppend, $orderCode);
            } else {
                $sql = "UPDATE orders SET status = ? WHERE order_code = ?";
                $stmt = $this->prepare($sql);
                $stmt->bind_param("is", $status, $orderCode);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}