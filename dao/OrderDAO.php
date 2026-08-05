<?php
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
                $list[] = new Order(
                    $row["id"],
                    $row["customer_id"],
                    $row["user_id"],
                    $row["order_code"],
                    $row["total_amount"],
                    $row["note"],
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

    public function findById(int $id): ?Order
    {
        try {
            $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at FROM orders WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Order(
                    $row["id"],
                    $row["customer_id"],
                    $row["user_id"],
                    $row["order_code"],
                    $row["total_amount"],
                    $row["note"],
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

    public function insert(Order $order): bool
    {
        try {
            $sql = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, note, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);

            $customerId = $order->getCustomerId();
            $userId = $order->getUserId();
            $orderCode = $order->getOrderCode();
            $totalAmount = $order->getTotalAmount();
            $note = $order->getNote();
            $status = $order->getStatus();

            $stmt->bind_param("iisdsi", $customerId, $userId, $orderCode, $totalAmount, $note, $status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Order $order): bool
    {
        try {
            $sql = "UPDATE orders SET customer_id = ?, user_id = ?, order_code = ?, total_amount = ?, note = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);

            $customerId = $order->getCustomerId();
            $userId = $order->getUserId();
            $orderCode = $order->getOrderCode();
            $totalAmount = $order->getTotalAmount();
            $note = $order->getNote();
            $status = $order->getStatus();
            $id = $order->getId();

            $stmt->bind_param("iisdsii", $customerId, $userId, $orderCode, $totalAmount, $note, $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM orders WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Lấy chi tiết các sản phẩm trong đơn hàng
    public function getDetailsByOrderId(int $orderId): array
    {
        $details = [];
        try {
            $sql = "SELECT id, order_id, product_id, quantity, price, subtotal, created_at FROM order_details WHERE order_id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $details[] = new OrderDetail(
                    $row["id"],
                    $row["order_id"],
                    $row["product_id"],
                    $row["quantity"],
                    $row["price"],
                    $row["subtotal"],
                    $row["created_at"]
                );
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $details;
    }

    // Thêm đơn hàng kèm danh sách sản phẩm (sử dụng Transaction)
    public function createOrderWithDetails(Order $order, array $orderDetails): bool
    {
        try {
            $this->beginTransaction();

            // 1. Thêm đơn hàng
            $sqlOrder = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, note, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtOrder = $this->prepare($sqlOrder);

            $customerId = $order->getCustomerId();
            $userId = $order->getUserId();
            $orderCode = $order->getOrderCode();
            $totalAmount = $order->getTotalAmount();
            $note = $order->getNote();
            $status = $order->getStatus();

            $stmtOrder->bind_param("iisdsi", $customerId, $userId, $orderCode, $totalAmount, $note, $status);
            $stmtOrder->execute();

            $orderId = $this->conn->insert_id;

            // 2. Thêm các chi tiết đơn hàng
            $sqlDetail = "INSERT INTO order_details(order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmtDetail = $this->prepare($sqlDetail);

            foreach ($orderDetails as $detail) {
                $productId = $detail->getProductId();
                $quantity = $detail->getQuantity();
                $price = $detail->getPrice();
                $subtotal = $detail->getSubtotal();

                $stmtDetail->bind_param("iiidd", $orderId, $productId, $quantity, $price, $subtotal);
                $stmtDetail->execute();
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    // Đếm tổng số đơn hàng
public function count(): int
{
    try {
        $sql = "SELECT COUNT(*) as total FROM orders";
        $result = $this->executeQuery($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        throw $e;
    }
}

// Tính tổng doanh thu của các đơn hàng thành công (status = 1)
public function getTotalRevenue(): float
{
    try {
        $sql = "SELECT SUM(total_amount) as total FROM orders WHERE status = 1";
        $result = $this->executeQuery($sql);
        $row = $result->fetch_assoc();
        return (float)($row['total'] ?? 0);
    } catch (Exception $e) {
        throw $e;
    }
}

// Lấy 5 đơn hàng mới nhất
public function getLatest(int $limit = 5): array
{
    $list = [];
    try {
        $sql = "SELECT id, customer_id, user_id, order_code, total_amount, note, status, created_at, updated_at 
                FROM orders ORDER BY id DESC LIMIT ?";
        $stmt = $this->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $list[] = new Order(
                $row["id"],
                $row["customer_id"],
                $row["user_id"],
                $row["order_code"],
                $row["total_amount"],
                $row["note"],
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