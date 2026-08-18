<?php
namespace DAO;

use Models\Order;
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
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $order = new Order();
                $order->id = (int)$row["id"];
                $order->customerId = (int)$row["customer_id"];
                $order->userId = $row["user_id"] ? (int)$row["user_id"] : null;
                $order->orderCode = $row["order_code"];
                $order->totalAmount = (float)$row["total_amount"];
                $order->note = $row["note"];
                $order->status = (int)$row["status"];
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
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $order = new Order();
                $order->id = (int)$row["id"];
                $order->customerId = (int)$row["customer_id"];
                $order->userId = $row["user_id"] ? (int)$row["user_id"] : null;
                $order->orderCode = $row["order_code"];
                $order->totalAmount = (float)$row["total_amount"];
                $order->note = $row["note"];
                $order->status = (int)$row["status"];
                $order->createdAt = $row["created_at"];
                $order->updatedAt = $row["updated_at"];
                return $order;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Order $order): bool
    {
        try {
            $sql = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, note, status) VALUES(?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iisiisi",
                $order->customerId,
                $order->userId,
                $order->orderCode,
                $order->totalAmount,
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
            $sql = "UPDATE orders SET customer_id=?, user_id=?, order_code=?, total_amount=?, note=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "iisdsii",
                $order->customerId,
                $order->userId,
                $order->orderCode,
                $order->totalAmount,
                $order->note,
                $order->status,
                $order->id
            );
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
            $sql = "SELECT id, order_id, product_id, quantity, price, subtotal, created_at FROM order_details WHERE order_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $detail = new OrderDetail();
                $detail->id = (int)$row["id"];
                $detail->orderId = (int)$row["order_id"];
                $detail->productId = (int)$row["product_id"];
                $detail->quantity = (int)$row["quantity"];
                $detail->price = (float)$row["price"];
                $detail->subtotal = (float)$row["subtotal"];
                $detail->createdAt = $row["created_at"];
                $list[] = $detail;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
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
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders ORDER BY id DESC LIMIT ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $order = new Order();
                $order->id = (int)$row["id"];
                $order->customerId = (int)$row["customer_id"];
                $order->userId = $row["user_id"] ? (int)$row["user_id"] : null;
                $order->orderCode = $row["order_code"];
                $order->totalAmount = (float)$row["total_amount"];
                $order->note = $row["note"];
                $order->status = (int)$row["status"];
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
   public function countSearch(string $keyword, $status): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (o.order_code LIKE ? OR c.fullname LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }

            if ($status !== "") {
                $sql .= " AND o.status = ?";
                $params[] = $status;
                $types .= "i";
            }

            $stmt = $this->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
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

    // Lấy dữ liệu phân trang đơn hàng (có hỗ trợ tìm kiếm)
    public function getPage(int $limit, int $offset, string $keyword, $status): array
    {
        $list = [];
        try {
            $sql = "SELECT o.*, c.fullname as customer_name, u.fullname as user_name 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE 1=1";
            
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (o.order_code LIKE ? OR c.fullname LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
            }

            if ($status !== "") {
                $sql .= " AND o.status = ?";
                $params[] = $status;
                $types .= "i";
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
}