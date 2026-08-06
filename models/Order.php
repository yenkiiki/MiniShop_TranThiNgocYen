<?php
class Order {
    // 1. Khai báo HẰNG SỐ cho trạng thái (Khớp với TINYINT trong CSDL)
    public const STATUS_PENDING   = 0; // Chờ xử lý
    public const STATUS_COMPLETED = 1; // Hoàn thành
    public const STATUS_CANCELLED = 2; // Hủy

    private $id;
    private $customerId;
    private $userId;
    private $orderCode;
    private $totalAmount;
    private $note;
    private $status;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        $id = null, 
        $customerId = null, 
        $userId = null, 
        $orderCode = null, 
        $totalAmount = 0.00, 
        $note = null, 
        $status = self::STATUS_PENDING, 
        $createdAt = null, 
        $updatedAt = null
    ) {
        $this->id = $id !== null ? (int)$id : null;
        $this->customerId = $customerId !== null ? (int)$customerId : null;
        $this->userId = $userId !== null ? (int)$userId : null;
        $this->orderCode = $orderCode;
        // Ép kiểu float & làm tròn 2 chữ số thập phân chuẩn DECIMAL(12,2)
        $this->totalAmount = round((float)$totalAmount, 2);
        $this->note = $note;
        $this->status = (int)$status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // --- GETTERS ---
    public function getId(): ?int { return $this->id; }
    public function getCustomerId(): ?int { return $this->customerId; }
    public function getUserId(): ?int { return $this->userId; }
    public function getOrderCode(): ?string { return $this->orderCode; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function getNote(): ?string { return $this->note; }
    public function getStatus(): int { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }

    // --- SETTERS ---
    public function setId($id): void { $this->id = $id !== null ? (int)$id : null; }
    public function setCustomerId($customerId): void { $this->customerId = $customerId !== null ? (int)$customerId : null; }
    public function setUserId($userId): void { $this->userId = $userId !== null ? (int)$userId : null; }
    public function setOrderCode(?string $orderCode): void { $this->orderCode = $orderCode; }
    
    public function setTotalAmount($totalAmount): void { 
        $this->totalAmount = round((float)$totalAmount, 2); 
    }
    
    public function setNote(?string $note): void { $this->note = $note; }
    public function setStatus($status): void { $this->status = (int)$status; }
    public function setCreatedAt(?string $createdAt): void { $this->createdAt = $createdAt; }
    public function setUpdatedAt(?string $updatedAt): void { $this->updatedAt = $updatedAt; }

    // --- HÀM BỔ SUNG TIỆN ÍCH CHO GIAO DIỆN ADMIN/CLIENT ---

    // 1. Chuyển status dạng số (0, 1, 2) sang Chữ để hiển thị
    public function getStatusText(): string {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return 'Hoàn thành';
            case self::STATUS_CANCELLED:
                return 'Đã hủy';
            case self::STATUS_PENDING:
            default:
                return 'Chờ xử lý';
        }
    }

    // 2. Trả về Badge màu sắc Bootstrap 5 sẵn cho bảng Admin
    public function getStatusBadge(): string {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return '<span class="badge bg-success">Hoàn thành</span>';
            case self::STATUS_CANCELLED:
                return '<span class="badge bg-danger">Đã hủy</span>';
            case self::STATUS_PENDING:
            default:
                return '<span class="badge bg-warning text-dark">Chờ xử lý</span>';
        }
    }

    // 3. Định dạng tổng tiền dạng VNĐ dễ nhìn (Ví dụ: 150.000 VNĐ)
    public function getFormattedTotalAmount(): string {
        return number_format($this->totalAmount, 0, ',', '.') . ' VNĐ';
    }
}