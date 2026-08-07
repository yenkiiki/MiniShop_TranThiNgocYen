<?php

class ProductImage {
    public int $id;
    public int $productId;
    public string $image;
    public int $sortOrder;
    public string $createdAt;

    public function __construct() {
        $this->sortOrder = 0;
    }
}