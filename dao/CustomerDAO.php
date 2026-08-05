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
                $list[] = new Customer(
                    $row["id"],
                    $row["fullname"],
                    $row["phone"],
                    $row["email"],
                    $row["address"],
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

    public function findById(int $id): ?Customer
    {
        try {
            $sql = "SELECT id, fullname, phone, email, address, note, status, created_at, updated_at FROM customers WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Customer(
                    $row["id"],
                    $row["fullname"],
                    $row["phone"],
                    $row["email"],
                    $row["address"],
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

    public function insert(Customer $customer): bool
    {
        try {
            $sql = "INSERT INTO customers(fullname, phone, email, address, note, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);

            $fullname = $customer->getFullname();
            $phone = $customer->getPhone();
            $email = $customer->getEmail();
            $address = $customer->getAddress();
            $note = $customer->getNote();
            $status = $customer->getStatus();

            $stmt->bind_param("sssssi", $fullname, $phone, $email, $address, $note, $status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Customer $customer): bool
    {
        try {
            $sql = "UPDATE customers SET fullname = ?, phone = ?, email = ?, address = ?, note = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);

            $fullname = $customer->getFullname();
            $phone = $customer->getPhone();
            $email = $customer->getEmail();
            $address = $customer->getAddress();
            $note = $customer->getNote();
            $status = $customer->getStatus();
            $id = $customer->getId();

            $stmt->bind_param("sssssii", $fullname, $phone, $email, $address, $note, $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM customers WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function count(): int
{
    try {
        $sql = "SELECT COUNT(*) as total FROM brands";
        $result = $this->executeQuery($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        throw $e;
    }
}
}