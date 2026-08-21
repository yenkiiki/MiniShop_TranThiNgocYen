<?php
namespace Models;

class Review
{
    public ?int $id = null;
    public int $productId = 0;
    public ?int $userId = null;
    public ?int $orderId = null;
    public ?string $fullname = null;
    public int $rating = 5;
    public string $comment = '';
    public ?string $createdAt = null;

    // Extra join fields
    public ?string $productName = null;
    public ?string $productImage = null;
    public ?string $orderCode = null;
    public ?string $userFullName = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
            $this->userId = isset($data['user_id']) ? (int)$data['user_id'] : null;
            $this->orderId = isset($data['order_id']) ? (int)$data['order_id'] : null;
            $this->fullname = $data['fullname'] ?? ($data['user_fullname'] ?? 'Khách hàng');
            $this->rating = isset($data['rating']) ? (int)$data['rating'] : 5;
            $this->comment = $data['comment'] ?? '';
            $this->createdAt = $data['created_at'] ?? null;

            $this->productName = $data['proname'] ?? ($data['product_name'] ?? null);
            $this->productImage = $data['image'] ?? ($data['product_image'] ?? null);
            $this->orderCode = $data['order_code'] ?? null;
            $this->userFullName = $data['user_fullname'] ?? ($data['fullname'] ?? null);
        }
    }

   
    public function getDisplayName(bool $masked = false): string
    {
        $name = trim($this->userFullName ?: ($this->fullname ?: 'Khách hàng'));
        if (!$masked || mb_strlen($name, 'UTF-8') <= 2) {
            return $name;
        }
        $first = mb_substr($name, 0, 1, 'UTF-8');
        $last = mb_substr($name, -1, 1, 'UTF-8');
        return $first . '***' . $last;
    }
}
