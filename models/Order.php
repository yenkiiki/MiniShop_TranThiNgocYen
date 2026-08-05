<?php
class Order {
    private $id;
    private $customerId;
    private $userId;
    private $orderCode;
    private $totalAmount;
    private $note;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct($id = null, $customerId = null, $userId = null, $orderCode = null, $totalAmount = 0, $note = null, $status = 0, $createdAt = null, $updatedAt = null) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->userId = $userId;
        $this->orderCode = $orderCode;
        $this->totalAmount = $totalAmount;
        $this->note = $note;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getCustomerId() { return $this->customerId; }
    public function getUserId() { return $this->userId; }
    public function getOrderCode() { return $this->orderCode; }
    public function getTotalAmount() { return $this->totalAmount; }
    public function getNote() { return $this->note; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    public function setId($id) { $this->id = $id; }
    public function setCustomerId($customerId) { $this->customerId = $customerId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setOrderCode($orderCode) { $this->orderCode = $orderCode; }
    public function setTotalAmount($totalAmount) { $this->totalAmount = $totalAmount; }
    public function setNote($note) { $this->note = $note; }
    public function setStatus($status) { $this->status = $status; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
}