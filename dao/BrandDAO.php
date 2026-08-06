<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/Brand.php";

class BrandDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(): array
    {
        $list = [];
        try {
            $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands ORDER BY brandname";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $list[] = new Brand(
                    $row["id"],
                    $row["brandname"],
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
        return $list;
    }

    public function findById(int $id): ?Brand
    {
        try {
            $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return new Brand(
                    $row["id"],
                    $row["brandname"],
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

    public function insert(Brand $brand): bool
    {
        try {
            $sql = "INSERT INTO brands(brandname, slug, image, description, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);

            $brandname = $brand->getBrandname();
            $slug = $brand->getSlug();
            $image = $brand->getImage();
            $description = $brand->getDescription();
            $status = $brand->getStatus();

            $stmt->bind_param("ssssi", $brandname, $slug, $image, $description, $status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(Brand $brand): bool
    {
        try {
            $sql = "UPDATE brands SET brandname = ?, slug = ?, image = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);

            $brandname = $brand->getBrandname();
            $slug = $brand->getSlug();
            $image = $brand->getImage();
            $description = $brand->getDescription();
            $status = $brand->getStatus();
            $id = $brand->getId();

            $stmt->bind_param("ssssii", $brandname, $slug, $image, $description, $status, $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM brands WHERE id = ?";
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