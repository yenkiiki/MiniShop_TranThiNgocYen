<?php
namespace Models;

class Wishlist
{
    public int $id = 0;
    public ?int $userId = null;
    public int $productId = 0;
    public ?string $sessionId = null;
    public string $createdAt = '';

    // Thuộc tính mở rộng từ bảng products
    public ?string $productName = null;
    public ?string $productImage = null;
    public float $price = 0.0;
    public float $discountPrice = 0.0;
    public ?string $slug = null;
    public ?string $cateName = null;
    public ?string $brandName = null;
    public int $quantity = 0;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = (int)($data['wishlist_id'] ?? ($data['id'] ?? 0));
            $this->userId = isset($data['user_id']) && $data['user_id'] !== null ? (int)$data['user_id'] : null;
            $this->productId = (int)($data['product_id'] ?? ($data['productid'] ?? ($data['id'] ?? 0)));
            $this->sessionId = $data['session_id'] ?? null;
            $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
            
            $this->productName = $data['proname'] ?? ($data['product_name'] ?? null);
            $this->productImage = $data['image'] ?? ($data['product_image'] ?? null);
            $this->price = (float)($data['price'] ?? 0);
            $this->discountPrice = (float)($data['discount_price'] ?? ($data['discountPrice'] ?? 0));
            $this->slug = $data['slug'] ?? null;
            $this->cateName = $data['catename'] ?? ($data['cateName'] ?? null);
            $this->brandName = $data['brandname'] ?? ($data['brandName'] ?? null);
            $this->quantity = (int)($data['quantity'] ?? 0);
        }
    }
}
