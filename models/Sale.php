<?php
namespace Models;

class Sale
{
    public ?int $id = null;
    public int $productId = 0;
    public int $discountPercent = 0;
    public float $salePrice = 0.0;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $description = null;
    public int $status = 1;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    // Các thuộc tính mở rộng từ bảng products
    public ?string $productName = null;
    public ?string $productImage = null;
    public float $productPrice = 0.0;
    public ?string $categoryName = null;
    public ?string $brandName = null;
    public ?string $slug = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
            $this->discountPercent = isset($data['discount_percent']) ? (int)$data['discount_percent'] : 0;
            $this->salePrice = isset($data['sale_price']) ? (float)$data['sale_price'] : 0.0;
            $this->startDate = $data['start_date'] ?? null;
            $this->endDate = $data['end_date'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->status = isset($data['status']) ? (int)$data['status'] : 1;
            $this->createdAt = $data['created_at'] ?? null;
            $this->updatedAt = $data['updated_at'] ?? null;

            // Extra product details
            $this->productName = $data['proname'] ?? ($data['product_name'] ?? null);
            $this->productImage = $data['image'] ?? ($data['product_image'] ?? null);
            $this->productPrice = isset($data['price']) ? (float)$data['price'] : (isset($data['product_price']) ? (float)$data['product_price'] : 0.0);
            $this->categoryName = $data['catename'] ?? null;
            $this->brandName = $data['brandname'] ?? null;
            $this->slug = $data['slug'] ?? null;
        }
    }
}
