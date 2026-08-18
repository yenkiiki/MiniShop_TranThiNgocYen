<?php
namespace Models;
class Brand {
    public int $id;
    public string $brandName;
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