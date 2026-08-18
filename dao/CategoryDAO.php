<?php
namespace DAO;

use Models\Category;
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Category.php";

class CategoryDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lấy tất cả danh mục
   // Lấy tất cả danh mục (có hỗ trợ tìm kiếm theo từ khóa)
    public function getAll(string $keyword = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories";
            
            if (!empty($keyword)) {
                $sql .= " WHERE catename LIKE ? OR slug LIKE ?";
            }
            $sql .= " ORDER BY catename";

            if (!empty($keyword)) {
                $stmt = $this->prepare($sql);
                // Thêm dấu % vào từ khóa để tìm kiếm gần đúng (LIKE)
                $searchTerm = "%" . $keyword . "%";
                $stmt->bind_param("ss", $searchTerm, $searchTerm);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->executeQuery($sql);
            }

            while ($row = $result->fetch_assoc()) {
                $category = new Category();
                $category->id = (int)$row["id"];
                $category->cateName = $row["catename"];
                $category->slug = $row["slug"];
                $category->image = $row["image"];
                $category->description = $row["description"];
                $category->status = (int)$row["status"];
                $category->createdAt = $row["created_at"];
                $category->updatedAt = $row["updated_at"];
                $list[] = $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Tìm theo ID
    public function findById(int $id): ?Category
    {
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $category = new Category();
                $category->id = (int)$row["id"];
                $category->cateName = $row["catename"];
                $category->slug = $row["slug"];
                $category->image = $row["image"];
                $category->description = $row["description"];
                $category->status = (int)$row["status"];
                $category->createdAt = $row["created_at"];
                $category->updatedAt = $row["updated_at"];
                return $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    // Thêm danh mục
    public function insert(Category $category): bool
    {
        try {
            $sql = "INSERT INTO categories(catename, slug, image, description, status) VALUES(?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssi",
                $category->cateName,
                $category->slug,
                $category->image,
                $category->description,
                $category->status
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Cập nhật danh mục
    public function update(Category $category): bool
    {
        try {
            $sql = "UPDATE categories SET catename=?, slug=?, image=?, description=?, status=? WHERE id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param(
                "ssssii",
                $category->cateName,
                $category->slug,
                $category->image,
                $category->description,
                $category->status,
                $category->id
            );
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Xóa danh mục
    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM categories WHERE id=?";
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
            $sql = "SELECT COUNT(*) as total FROM categories";
            $result = $this->executeQuery($sql);
            if ($row = $result->fetch_assoc()) {
                return (int)$row["total"];
            }
        } catch (Exception $e) {
            throw $e;
        }
        return 0;
    }
    public function count(string $keyword = "", string $status = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM categories WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (catename LIKE ? OR slug LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
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

    // Lấy danh sách phân trang, tìm kiếm và lọc trạng thái
    public function getPage(int $limit, int $offset, string $keyword = "", string $status = ""): array
    {
        $list = [];
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories WHERE 1=1";
            $params = [];
            $types = "";

            if (!empty($keyword)) {
                $sql .= " AND (catename LIKE ? OR slug LIKE ?)";
                $searchTerm = "%" . $keyword . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $types .= "ss";
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
                $category = new Category();
                $category->id = (int)$row["id"];
                $category->cateName = $row["catename"];
                $category->slug = $row["slug"];
                $category->image = $row["image"];
                $category->description = $row["description"];
                $category->status = (int)$row["status"];
                $category->createdAt = $row["created_at"];
                $category->updatedAt = $row["updated_at"];
                $list[] = $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }
}