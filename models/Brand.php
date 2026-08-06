<?php
class Brand {
    private $id;
    private $brandname;
    private $slug;
    private $image;
    private $description;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id = null, 
        $brandname = null, 
        $slug = null, 
        $image = null, 
        $description = null, 
        $status = 1, 
        $createdAt = null, 
        $updatedAt = null
    ) {
        $this->id = $id !== null ? (int)$id : null;
        $this->brandname = $brandname;
        $this->slug = $slug;
        $this->image = $image;
        $this->description = $description;
        $this->status = (int)$status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // --- GETTERS ---
    public function getId() { return $this->id; }
    public function getBrandname() { return $this->brandname; }
    public function getSlug() { return $this->slug; }
    public function getImage() { return $this->image; }
    public function getDescription() { return $this->description; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // --- SETTERS ---
    public function setId($id) { $this->id = $id !== null ? (int)$id : null; }
    public function setBrandname($brandname) { $this->brandname = $brandname; }
    public function setSlug($slug) { $this->slug = $slug; }
    public function setImage($image) { $this->image = $image; }
    public function setDescription($description) { $this->description = $description; }
    public function setStatus($status) { $this->status = (int)$status; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
    public function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
}