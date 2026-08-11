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
                $user = new User();
                $user->id = (int)$row["id"];
                $user->fullName = $row["fullname"];
                $user->userName = $row["username"];
                $user->password = $row["password"];
                $user->email = $row["email"];
                $user->phone = $row["phone"];
                $user->address = $row["address"];
                $user->role = (int)$row["role"];
                $user->status = (int)$row["status"];
                $user->createdAt = $row["created_at"];
                $user->updatedAt = $row["updated_at"];
                $list[] = $user;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?User
    {
        try {
            $sql = "SELECT id, fullname, username, password, email, phone, address, role, status, created_at, updated_at FROM users WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $user = new User();
                $user->id = (int)$row["id"];
                $user->fullName = $row["fullname"];
                $user->userName = $row["username"];
                $user->password = $row["password"];
                $user->email = $row["email"];
                $user->phone = $row["phone"];
                $user->address = $row["address"];
                $user->role = (int)$row["role"];
                $user->status = (int)$row["status"];
                $user->createdAt = $row["created_at"];
                $user->updatedAt = $row["updated_at"];
                return $user;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(User $user): bool
    {
        try {
            $sql = "INSERT INTO users(fullname, username, password, email, phone, address, role, status) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssssii",
                $user->fullName,
                $user->userName,
                $user->password,
                $user->email,
                $user->phone,
                $user->address,
                $user->role,
                $user->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(User $user): bool
    {
        try {
            $sql = "UPDATE users SET fullname=?, username=?, password=?, email=?, phone=?, address=?, role=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssssiii",
                $user->fullName,
                $user->userName,
                $user->password,
                $user->email,
                $user->phone,
                $user->address,
                $user->role,
                $user->status,
                $user->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function countSearch(string $keyword, $role, $status): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (fullname LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ssss";
            }

            if ($role !== "") {
                $sql .= " AND role = ?";
                $params[] = $role;
                $types .= "i";
            }

            if ($status !== "") {
                $sql .= " AND status = ?";
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

    // Lấy dữ liệu phân trang (có hỗ trợ tìm kiếm và lọc role, status)
    public function getPage(int $limit, int $offset, string $keyword, $role, $status): array
    {
        $list = [];
        try {
            $sql = "SELECT id, fullname, username, email, phone, address, role, status, created_at, updated_at FROM users WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (fullname LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ssss";
            }

            if ($role !== "") {
                $sql .= " AND role = ?";
                $params[] = $role;
                $types .= "i";
            }

            if ($status !== "") {
                $sql .= " AND status = ?";
                $params[] = $status;
                $types .= "i";
            }

            $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
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
                $user = new User();
                $user->id = (int)$row["id"];
                $user->fullName = $row["fullname"];
                $user->userName = $row["username"];
                $user->email = $row["email"];
                $user->phone = $row["phone"] ?? '';
                $user->address = $row["address"] ?? '';
                $user->role = (int)$row["role"];
                $user->status = (int)$row["status"];
                $user->createdAt = $row["created_at"];
                $user->updatedAt = $row["updated_at"];
                $list[] = $user;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}