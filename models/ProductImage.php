<?php
class ProductImage {
    private $id;
    private $productId;
    private $image;
    private $sortOrder;
    private $createdAt;

    public function __construct($id = null, $productId = null, $image = null, $sortOrder = 0, $createdAt = null) {
        $this->id = $id;
        $this->productId = $productId;
        $this->image = $image;
        $this->sortOrder = $sortOrder;
        $this->createdAt = $createdAt;
    }

    public function getId() { return $this->id; }
    public function getProductId() { return $this->productId; }
    public function getImage() { return $this->image; }
    public function getSortOrder() { return $this->sortOrder; }
    public function getCreatedAt() { return $this->createdAt; }

    public function setId($id) { $this->id = $id; }
    public function setProductId($productId) { $this->productId = $productId; }
    public function setImage($image) { $this->image = $image; }
    public function setSortOrder($sortOrder) { $this->sortOrder = $sortOrder; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
}