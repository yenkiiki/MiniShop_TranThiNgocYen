<?php
namespace Models;

class ReviewModel extends \Config\Database {
    
    // Lấy danh sách đánh giá theo ID sản phẩm
    public function getReviewsByProductId($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM product_reviews WHERE product_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(\MYSQLI_OBJ);
    }

    // Thêm đánh giá mới vào cơ sở dữ liệu
    public function addReview($product_id, $fullname, $rating, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO product_reviews (product_id, fullname, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $product_id, $fullname, $rating, $comment);
        return $stmt->execute();
    }
}