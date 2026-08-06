<?php

class Product {
    private ?int $id;
    private ?int $categoryId;
    private ?int $brandId;
    private string $proname;
    private ?string $slug;
    private float $price;
    private float $discountPrice;
    private int $quantity;
    private ?string $image;
    private ?string $description;
    private int $status;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id = null,
        ?int $categoryId = null,
        ?int $brandId = null,
        string $proname = '',
        ?string $slug = null,
        float $price = 0.0,
        float $discountPrice = 0.0,
        int $quantity = 0,
        ?string $image = null,
        ?string $description = null,
        int $status = 1,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
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

    // ==================== GETTERS ====================
    
    public function getId(): ?int { 
        return $this->id; 
    }

    public function getCategoryId(): ?int { 
        return $this->categoryId; 
    }

    public function getBrandId(): ?int { 
        return $this->brandId; 
    }

    public function getProname(): string { 
        return $this->proname; 
    }

    public function getSlug(): ?string { 
        return $this->slug; 
    }

    public function getPrice(): float { 
        return $this->price; 
    }

    public function getDiscountPrice(): float { 
        return $this->discountPrice; 
    }

    public function getQuantity(): int { 
        return $this->quantity; 
    }

    public function getImage(): ?string { 
        return $this->image; 
    }

    public function getDescription(): ?string { 
        return $this->description; 
    }

    public function getStatus(): int { 
        return $this->status; 
    }

    public function getCreatedAt(): ?string { 
        return $this->createdAt; 
    }

    public function getUpdatedAt(): ?string { 
        return $this->updatedAt; 
    }

    // ==================== SETTERS ====================

    public function setId(?int $id): self { 
        $this->id = $id; 
        return $this; 
    }

    public function setCategoryId(?int $categoryId): self { 
        $this->categoryId = $categoryId; 
        return $this; 
    }

    public function setBrandId(?int $brandId): self { 
        $this->brandId = $brandId; 
        return $this; 
    }

    public function setProname(string $proname): self { 
        $this->proname = $proname; 
        return $this; 
    }

    public function setSlug(?string $slug): self { 
        $this->slug = $slug; 
        return $this; 
    }

    public function setPrice(float $price): self { 
        $this->price = $price; 
        return $this; 
    }

    public function setDiscountPrice(float $discountPrice): self { 
        $this->discountPrice = $discountPrice; 
        return $this; 
    }

    public function setQuantity(int $quantity): self { 
        $this->quantity = $quantity; 
        return $this; 
    }

    public function setImage(?string $image): self { 
        $this->image = $image; 
        return $this; 
    }

    public function setDescription(?string $description): self { 
        $this->description = $description; 
        return $this; 
    }

    public function setStatus(int $status): self { 
        $this->status = $status; 
        return $this; 
    }

    public function setCreatedAt(?string $createdAt): self { 
        $this->createdAt = $createdAt; 
        return $this; 
    }

    public function setUpdatedAt(?string $updatedAt): self { 
        $this->updatedAt = $updatedAt; 
        return $this; 
    }

    // ==================== HELPER METHODS ====================

    /**
     * Khởi tạo đối tượng từ mảng dữ liệu lấy từ Database (FETCH_ASSOC)
     */
    public static function fromArray(array $data): self {
        return new self(
            isset($data['id']) ? (int)$data['id'] : null,
            isset($data['category_id']) ? (int)$data['category_id'] : null,
            isset($data['brand_id']) ? (int)$data['brand_id'] : null,
            $data['proname'] ?? '',
            $data['slug'] ?? null,
            isset($data['price']) ? (float)$data['price'] : 0.0,
            isset($data['discount_price']) ? (float)$data['discount_price'] : 0.0,
            isset($data['quantity']) ? (int)$data['quantity'] : 0,
            $data['image'] ?? null,
            $data['description'] ?? null,
            isset($data['status']) ? (int)$data['status'] : 1,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    /**
     * Chuyển đổi đối tượng thành mảng dữ liệu tương ứng tên cột Database
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'brand_id' => $this->brandId,
            'proname' => $this->proname,
            'slug' => $this->slug,
            'price' => $this->price,
            'discount_price' => $this->discountPrice,
            'quantity' => $this->quantity,
            'image' => $this->image,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}