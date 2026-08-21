<?php
namespace Models;

class ProductVariant
{
    public int $id;
    public int $productId;
    public string $variantName;
    public ?string $sku = null;
    public ?float $price = null;
    public ?float $discountPrice = null;
    public int $quantity;
    public ?string $image = null;
    public int $sortOrder = 0;
    public int $status = 1;
    public ?string $createdAt = null;
    public ?string $updatedAt = null;

    public function __construct()
    {
        $this->quantity = 10;
        $this->status = 1;
        $this->sortOrder = 0;
    }
}
