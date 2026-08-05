<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Category.php";

class CategoryDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lấy tất cả danh mục
    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories ORDER BY catename";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $category = new Category(
                    $row["id"],
                    $row["catename"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
                    $row["status"],
                    $row["created_at"],
                    $row["updated_at"]
                );
                $list[] = $category;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    // Tìm danh mục theo ID
    public function findById(int $id): ?Category
    {
        try {
            $sql = "SELECT id, catename, slug, image, description, status, created_at, updated_at FROM categories WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Category(
                    $row["id"],
                    $row["catename"],
                    $row["slug"],
                    $row["image"],
                    $row["description"],
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

    // Thêm danh mục
    public function insert(Category $category): bool
    {
        try {
            $sql = "INSERT INTO categories(catename, slug, image, description, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            
            $catename = $category->getCatename();
            $slug = $category->getSlug();
            $image = $category->getImage();
            $description = $category->getDescription();
            $status = $category->getStatus();

            $stmt->bind_param(
                "ssssi",
                $catename,
                $slug,
                $image,
                $description,
                $status
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
            $sql = "UPDATE categories SET catename = ?, slug = ?, image = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);

            $catename = $category->getCatename();
            $slug = $category->getSlug();
            $image = $category->getImage();
            $description = $category->getDescription();
            $status = $category->getStatus();
            $id = $category->getId();

            $stmt->bind_param(
                "ssssii",
                $catename,
                $slug,
                $image,
                $description,
                $status,
                $id
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
            $sql = "DELETE FROM categories WHERE id = ?";
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
        $sql = "SELECT COUNT(*) as total FROM categories";
        $result = $this->executeQuery($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    } catch (Exception $e) {
        throw $e;
    }
}
}