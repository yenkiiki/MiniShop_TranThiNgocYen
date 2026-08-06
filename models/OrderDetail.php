<?php
class OrderDetail {
    private $id;
    private $orderId;
    private $productId;
    private $quantity;
    private $price;
    private $subtotal;
    private $createdAt;

    public function __construct($id = null, $orderId = null, $productId = null, $quantity = 1, $price = 0, $subtotal = 0, $createdAt = null) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->subtotal = $subtotal;
        $this->createdAt = $createdAt;
    }

    public function getId() { return $this->id; }
    public function getOrderId() { return $this->orderId; }
    public function getProductId() { return $this->productId; }
    public function getQuantity() { return $this->quantity; }
    public function getPrice() { return $this->price; }
    public function getSubtotal() { return $this->subtotal; }
    public function getCreatedAt() { return $this->createdAt; }

    public function setId($id) { $this->id = $id; }
    public function setOrderId($orderId) { $this->orderId = $orderId; }
    public function setProductId($productId) { $this->productId = $productId; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    public function setPrice($price) { $this->price = $price; }
    public function setSubtotal($subtotal) { $this->subtotal = $subtotal; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
}