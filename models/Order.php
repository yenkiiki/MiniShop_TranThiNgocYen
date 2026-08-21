<?php
namespace Models;
class Order {
    public int $id;
    public int $customerId;
    public ?int $userId;
    public string $orderCode;
    public float $totalAmount;
    public float $shippingFee;
    public string $paymentMethod;
    public ?string $note;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct() {
        $this->totalAmount = 0.0;
        $this->shippingFee = 0.0;
        $this->paymentMethod = 'COD';
        $this->status = 0;
    }
}