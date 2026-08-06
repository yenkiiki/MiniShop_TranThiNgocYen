<?php
class OrderDetail {
    private $id;
    private $orderId;
    private $productId;
    private $quantity;
    private $price;
    private $subtotal;
    private $createdAt;

    public function __construct($id = null, $orderId = null, $productId = null, $quantity = 1, $price = 0.00, $subtotal = 0.00, $createdAt = null) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = (int)$quantity;
        // Bắt buộc ép kiểu float và làm tròn 2 chữ số thập phân (tương đương DECIMAL(10,2))
        $this->price = round((float)$price, 2);
        
        // Tự động tính subtotal nếu chưa truyền hoặc ép kiểu chuẩn
        if ($subtotal == 0 && $price > 0) {
            $this->subtotal = round($this->price * $this->quantity, 2);
        } else {
            $this->subtotal = round((float)$subtotal, 2);
        }
        
        $this->createdAt = $createdAt;
    }

    public function getId() { return $this->id; }
    public function getOrderId() { return $this->orderId; }
    public function getProductId() { return $this->productId; }
    public function getQuantity() { return $this->quantity; }
    public function getPrice() { return $this->price; }
    public function getSubtotal() { return $this->subtotal; }
    public function getCreatedAt() { return $this->createdAt; }

    public function setId($id) { $this->id = $id; }
    public function setOrderId($orderId) { $this->orderId = $orderId; }
    public function setProductId($productId) { $this->productId = $productId; }
    public function setQuantity($quantity) { 
        $this->quantity = (int)$quantity; 
        // Tự động tính lại thành tiền khi thay đổi số lượng
        $this->subtotal = round($this->price * $this->quantity, 2);
    }
    
    // Gán price đảm bảo đúng định dạng Decimal(10,2)
    public function setPrice($price) { 
        $this->price = round((float)$price, 2); 
        $this->subtotal = round($this->price * $this->quantity, 2);
    }
    
    public function setSubtotal($subtotal) { 
        $this->subtotal = round((float)$subtotal, 2); 
    }
    
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }

    // Hàm bổ sung: Định dạng hiển thị tiền tệ dạng VND hoặc số có 2 chữ số thập phân
    public function getFormattedPrice() {
        return number_format($this->price, 2, '.', '');
    }
    
    public function getFormattedSubtotal() {
        return number_format($this->subtotal, 2, '.', '');
    }
}