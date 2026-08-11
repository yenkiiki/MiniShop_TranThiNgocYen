<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Customer.php";

class CustomerDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $customer = new Customer();
                $customer->id = (int)$row["id"];
                $customer->fullName = $row["fullname"];
                $customer->phone = $row["phone"];
                $customer->email = $row["email"];
                $customer->address = $row["address"];
                $customer->note = $row["note"];
                $customer->status = (int)$row["status"];
                $customer->createdAt = $row["created_at"];
                $customer->updatedAt = $row["updated_at"];
                $list[] = $customer;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?Customer
    {
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $customer = new Customer();
                $customer->id = (int)$row["id"];
                $customer->fullName = $row["fullname"];
                $customer->phone = $row["phone"];
                $customer->email = $row["email"];
                $customer->address = $row["address"];
                $customer->note = $row["note"];
                $customer->status = (int)$row["status"];
                $customer->createdAt = $row["created_at"];
                $customer->updatedAt = $row["updated_at"];
                return $customer;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(Customer $customer): bool
    {
        try {
            $sql = "INSERT INTO customers(fullname, phone, email, address, note, status) VALUES(?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "sssssi",
                $customer->fullName,
                $customer->phone,
                $customer->email,
                $customer->address,
                $customer->note,
                $customer->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Customer $customer): bool
    {
        try {
            $sql = "UPDATE customers SET fullname=?, phone=?, email=?, address=?, note=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "sssssii",
                $customer->fullName,
                $customer->phone,
                $customer->email,
                $customer->address,
                $customer->note,
                $customer->status,
                $customer->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM customers WHERE id=?";
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
            $sql = "SELECT COUNT(*) as total FROM customers";
            $result = $this->executeQuery($sql);
            if ($row = $result->fetch_assoc()) {
                return (int)$row["total"];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0;
    }
    public function count(string $keyword = "", $status = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM customers WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (fullname LIKE ? OR phone LIKE ? OR email LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            if ($status !== "") {
                $sql .= " AND status = ?";
                $params[] = (int)$status;
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

    // Lấy danh sách khách hàng phân trang kết hợp tìm kiếm và lọc trạng thái
    public function getPage(int $limit, int $offset, string $keyword = "", $status = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (fullname LIKE ? OR phone LIKE ? OR email LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "sss";
            }

            if ($status !== "") {
                $sql .= " AND status = ?";
                $params[] = (int)$status;
                $types .= "i";
            }

            $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";

            $stmt = $this->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $customer = new Customer();
                $customer->id = (int)$row["id"];
                $customer->fullName = $row["fullname"];
                $customer->phone = $row["phone"];
                $customer->email = $row["email"];
                $customer->address = $row["address"];
                $customer->note = $row["note"];
                $customer->status = (int)$row["status"];
                $customer->createdAt = $row["created_at"];
                $customer->updatedAt = $row["updated_at"];
                $list[] = $customer;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}