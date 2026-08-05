<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/User.php";

class UserDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $list[] = new User(
                    $row["id"],
                    $row["fullname"],
                    $row["username"],
                    $row["password"],
                    $row["email"],
                    $row["phone"],
                    $row["address"],
                    $row["role"],
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

    public function findById(int $id): ?User
    {
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new User(
                    $row["id"],
                    $row["fullname"],
                    $row["username"],
                    $row["password"],
                    $row["email"],
                    $row["phone"],
                    $row["address"],
                    $row["role"],
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

    public function insert(User $user): bool
    {
        try {
            $sql = "INSERT INTO users(fullname, username, password, email, phone, address, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);

            $fullname = $user->getFullname();
            $username = $user->getUsername();
            $password = $user->getPassword();
            $email = $user->getEmail();
            $phone = $user->getPhone();
            $address = $user->getAddress();
            $role = $user->getRole();
            $status = $user->getStatus();

            $stmt->bind_param("ssssssii", $fullname, $username, $password, $email, $phone, $address, $role, $status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(User $user): bool
    {
        try {
            $sql = "UPDATE users SET fullname = ?, username = ?, password = ?, email = ?, phone = ?, address = ?, role = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);

            $fullname = $user->getFullname();
            $username = $user->getUsername();
            $password = $user->getPassword();
            $email = $user->getEmail();
            $phone = $user->getPhone();
            $address = $user->getAddress();
            $role = $user->getRole();
            $status = $user->getStatus();
            $id = $user->getId();

            $stmt->bind_param("ssssssiii", $fullname, $username, $password, $email, $phone, $address, $role, $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}