<?php
namespace Models;
class Category {
    public int $id;
    public string $cateName;
    public ?string $slug;
    public ?string $image;
    public ?string $description;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct() {
        $this->status = 1;
    }
}