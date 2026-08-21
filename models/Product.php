<?php
namespace Models;
class Product {
    public int $id;
    public int $categoryId;
    public int $brandId;
    public string $proName;
    public ?string $slug;
    public float $price;
    public float $discountPrice;
    public int $quantity;
    public ?string $image;
    public ?string $description;
    public int $status;
    public string $createdAt;
    public string $updatedAt;
    public ?string $cateName = null;
    public ?string $brandName = null;
    public array $variants = [];

    public function __construct() {
        $this->quantity = 0;
        $this->status = 1;
        $this->variants = [];
    }
}