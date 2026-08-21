<?php
namespace Models;

class Coupon
{
    public int $id = 0;
    public string $code = '';
    public string $name = '';
    public ?string $description = null;
    public string $discountType = 'fixed'; // 'fixed' (tiền cố định) hoặc 'percent' (phần trăm)
    public float $discountValue = 0.0;
    public float $minOrderAmount = 0.0;
    public ?float $maxDiscountAmount = null;
    public int $usageLimit = 100;
    public int $usedCount = 0;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $status = 1; // 1: Hoạt động, 0: Tạm khóa
    public string $createdAt = '';
    public string $updatedAt = '';

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = (int)($data['id'] ?? 0);
            $this->code = strtoupper(trim($data['code'] ?? ''));
            $this->name = $data['name'] ?? '';
            $this->description = $data['description'] ?? null;
            $this->discountType = $data['discount_type'] ?? ($data['discountType'] ?? 'fixed');
            $this->discountValue = (float)($data['discount_value'] ?? ($data['discountValue'] ?? 0));
            $this->minOrderAmount = (float)($data['min_order_amount'] ?? ($data['minOrderAmount'] ?? 0));
            $this->maxDiscountAmount = isset($data['max_discount_amount']) && $data['max_discount_amount'] !== null 
                ? (float)$data['max_discount_amount'] 
                : (isset($data['maxDiscountAmount']) && $data['maxDiscountAmount'] !== null ? (float)$data['maxDiscountAmount'] : null);
            $this->usageLimit = (int)($data['usage_limit'] ?? ($data['usageLimit'] ?? 100));
            $this->usedCount = (int)($data['used_count'] ?? ($data['usedCount'] ?? 0));
            $this->startDate = $data['start_date'] ?? ($data['startDate'] ?? null);
            $this->endDate = $data['end_date'] ?? ($data['endDate'] ?? null);
            $this->status = isset($data['status']) ? (int)$data['status'] : 1;
            $this->createdAt = $data['created_at'] ?? ($data['createdAt'] ?? date('Y-m-d H:i:s'));
            $this->updatedAt = $data['updated_at'] ?? ($data['updatedAt'] ?? date('Y-m-d H:i:s'));
        }
    }

    /**
     * Kiểm tra tính hợp lệ của mã đối với số tiền đơn hàng
     * @param float $orderSubtotal Tiền hàng chưa gồm ship
     * @return array ['valid' => bool, 'discount' => float, 'message' => string]
     */
    public function calculateDiscount(float $orderSubtotal): array
    {
        if ($this->status != 1) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Mã giảm giá này hiện đang tạm khóa hoặc không còn hiệu lực.'];
        }

        $now = date('Y-m-d H:i:s');
        if (!empty($this->startDate) && $now < $this->startDate) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Mã giảm giá chưa đến thời gian áp dụng (Bắt đầu từ ' . date('d/m/Y H:i', strtotime($this->startDate)) . ').'];
        }

        if (!empty($this->endDate) && $now > $this->endDate) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Mã giảm giá đã hết hạn sử dụng (Hạn cuối: ' . date('d/m/Y H:i', strtotime($this->endDate)) . ').'];
        }

        if ($this->usageLimit > 0 && $this->usedCount >= $this->usageLimit) {
            return ['valid' => false, 'discount' => 0, 'message' => 'Mã giảm giá này đã hết lượt sử dụng.'];
        }

        if ($orderSubtotal < $this->minOrderAmount) {
            $missing = $this->minOrderAmount - $orderSubtotal;
            return [
                'valid' => false, 
                'discount' => 0, 
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($this->minOrderAmount, 0, ',', '.') . ' đ (Cần mua thêm ' . number_format($missing, 0, ',', '.') . ' đ để áp dụng).'
            ];
        }

        // Tính toán số tiền được giảm
        $discountAmount = 0.0;
        if ($this->discountType === 'percent') {
            $discountAmount = ($orderSubtotal * $this->discountValue) / 100;
            if ($this->maxDiscountAmount !== null && $this->maxDiscountAmount > 0 && $discountAmount > $this->maxDiscountAmount) {
                $discountAmount = $this->maxDiscountAmount;
            }
        } else {
            $discountAmount = $this->discountValue;
        }

        // Không được giảm quá tổng tiền hàng
        if ($discountAmount > $orderSubtotal) {
            $discountAmount = $orderSubtotal;
        }

        return [
            'valid' => true,
            'discount' => round($discountAmount),
            'message' => 'Áp dụng mã giảm giá thành công! Tiết kiệm ' . number_format($discountAmount, 0, ',', '.') . ' đ'
        ];
    }
}
