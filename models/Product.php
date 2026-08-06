<?php
class Product {
    private $id;
    private $categoryId;
    private $brandId;
    private $proname;
    private $slug;
    private $price;
    private $discountPrice;
    private $quantity;
    private $image;
    private $description;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct($id = null, $categoryId = null, $brandId = null, $proname = null, $slug = null, $price = 0, $discountPrice = 0, $quantity = 0, $image = null, $description = null, $status = 1, $createdAt = null, $updatedAt = null) {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->proname = $proname;
        $this->slug = $slug;
        $this->price = $price;
        $this->discountPrice = $discountPrice;
        $this->quantity = $quantity;
        $this->image = $image;
        $this->description = $description;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId() { return $this->id; }
    public function getCategoryId() { return $this->categoryId; }
    public function getBrandId() { return $this->brandId; }
    public function getProname() { return $this->proname; }
    public function getSlug() { return $this->slug; }
    public function getPrice() { return $this->price; }
    public function getDiscountPrice() { return $this->discountPrice; }
    public function getQuantity() { return $this->quantity; }
    public function getImage() { return $this->image; }
    public function getDescription() { return $this->description; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    public function setId($id) { $this->id = $id; }
    public function setCategoryId($categoryId) { $this->categoryId = $categoryId; }
    public function setBrandId($brandId) { $this->brandId = $brandId; }
    public function setProname($proname) { $this->proname = $proname; }
    public function setSlug($slug) { $this->slug = $slug; }
    public function setPrice($price) { $this->price = $price; }
    public function setDiscountPrice($discountPrice) { $this->discountPrice = $discountPrice; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }
    public function setImage($image) { $this->image = $image; }
    public function setDescription($description) { $this->description = $description; }
    public function setStatus($status) { $this->status = $status; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
}