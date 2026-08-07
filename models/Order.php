<?php

class Order {
    public int $id;
    public int $customerId;
    public ?int $userId;
    public string $orderCode;
    public float $totalAmount;
    public ?string $note;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct() {
        $this->totalAmount = 0.0;
        $this->status = 0;
    }
}