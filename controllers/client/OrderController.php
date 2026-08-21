<?php
namespace Controllers\Client;

use DAO\ReviewDAO;
use DAO\OrderDAO;
use Exception;

class OrderController
{
    private ReviewDAO $reviewDAO;

    public function __construct()
    {
        $this->reviewDAO = new ReviewDAO();
    }

    /**
     * Xem lịch sử đơn hàng của khách hàng đã đăng nhập
     */
    public function history()
    {
        if (!isset($_SESSION['client_user'])) {
            $_SESSION['redirect_after_login'] = BASE_URL . "order/history";
            $_SESSION['login_notice'] = "Vui lòng đăng nhập để xem lịch sử đơn hàng của bạn!";
            header("Location: " . BASE_URL . "auth/login");
            exit;
        }

        $userId = (int)$_SESSION['client_user']['id'];
        $orders = $this->reviewDAO->getUserOrderHistory($userId);

        $statusList = [
            0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark', 'icon' => 'bi-clock'],
            1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark', 'icon' => 'bi-check2-circle'],
            2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white', 'icon' => 'bi-truck'],
            3 => ['label' => 'Giao thành công', 'class' => 'bg-success text-white', 'icon' => 'bi-check-circle-fill'],
            4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white', 'icon' => 'bi-x-circle']
        ];

        $pageTitle = "Lịch sử đơn hàng";
        ob_start();
        require __DIR__ . "/../../views/client/orders/history.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

    /**
     * Gửi đánh giá sản phẩm (Chỉ áp dụng cho đơn hàng đã giao thành công)
     */
    public function review()
    {
        if (!isset($_SESSION['client_user'])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["success" => false, "message" => "Vui lòng đăng nhập để đánh giá."]);
                exit;
            }
            header("Location: " . BASE_URL . "auth/login");
            exit;
        }

        $userId = (int)$_SESSION['client_user']['id'];
        $orderId = (int)($_POST['order_id'] ?? 0);
        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        $fullname = $_SESSION['client_user']['fullname'] ?? '';

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        try {
            $success = $this->reviewDAO->createReview($userId, $orderId, $productId, $rating, $comment, $fullname);
            if ($success) {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        "success" => true,
                        "message" => "Đánh giá sản phẩm thành công! Cảm ơn bạn đã phản hồi."
                    ]);
                    exit;
                } else {
                    $_SESSION['order_message'] = "Đánh giá sản phẩm thành công!";
                    header("Location: " . BASE_URL . "order/history");
                    exit;
                }
            } else {
                throw new Exception("Không thể lưu đánh giá. Vui lòng thử lại.");
            }
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    "success" => false,
                    "message" => $e->getMessage()
                ]);
                exit;
            } else {
                $_SESSION['order_error'] = $e->getMessage();
                header("Location: " . BASE_URL . "order/history");
                exit;
            }
        }
    }
}
