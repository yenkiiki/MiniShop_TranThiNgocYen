<?php
namespace Models;
class OrderDetail {
    public int $id;
    public int $orderId;
    public int $productId;
    public int $quantity;
    public float $price;
    public float $subtotal;
    public string $createdAt;

    public function __construct() {
        $this->quantity = 1;
        $this->price = 0.0;
        $this->subtotal = 0.0;
    }
}