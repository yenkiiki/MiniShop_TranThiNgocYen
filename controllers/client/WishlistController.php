<?php
namespace Controllers\Client;

use DAO\WishlistDAO;
use Exception;

class WishlistController
{
    private WishlistDAO $wishlistDAO;

    public function __construct()
    {
        $this->wishlistDAO = new WishlistDAO();
    }

    /**
     * Lấy userId và sessionId hiện tại
     */
    private function getUserContext(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = isset($_SESSION['client_user']['id']) ? (int)$_SESSION['client_user']['id'] : null;
        $sessionId = session_id();
        return [$userId, $sessionId];
    }

    /**
     * Trang hiển thị danh sách sản phẩm yêu thích
     */
    public function index()
    {
        [$userId, $sessionId] = $this->getUserContext();
        $wishlistItems = $this->wishlistDAO->getItems($userId, $sessionId);
        $totalWishlist = count($wishlistItems);

        $pageTitle = "❤️ Danh sách sản phẩm yêu thích - MINISHOP";
        ob_start();
        require_once __DIR__ . '/../../views/client/wishlist/index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../../views/client/layouts/master.php';
    }

    /**
     * AJAX Toggle (Thêm hoặc Bỏ yêu thích)
     */
    public function toggle()
    {
        header('Content-Type: application/json; charset=utf-8');

        $productId = (int)($_POST['product_id'] ?? ($_POST['productid'] ?? 0));
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Mã sản phẩm không hợp lệ!']);
            exit;
        }

        [$userId, $sessionId] = $this->getUserContext();
        $result = $this->wishlistDAO->toggle($productId, $userId, $sessionId);

        echo json_encode($result);
        exit;
    }

    /**
     * Xóa sản phẩm khỏi danh sách yêu thích
     */
    public function remove()
    {
        $productId = (int)($_POST['product_id'] ?? ($_POST['productid'] ?? ($_GET['id'] ?? 0)));
        [$userId, $sessionId] = $this->getUserContext();

        $success = $this->wishlistDAO->remove($productId, $userId, $sessionId);
        $count = $this->wishlistDAO->getCount($userId, $sessionId);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => $success,
                'count' => $count,
                'message' => $success ? 'Đã xóa sản phẩm khỏi danh sách yêu thích!' : 'Không thể xóa sản phẩm.'
            ]);
            exit;
        }

        $_SESSION['wishlist_message'] = 'Đã xóa sản phẩm khỏi danh sách yêu thích!';
        header('Location: ' . BASE_URL . 'wishlist');
        exit;
    }

    /**
     * Trả về số lượng sản phẩm yêu thích hiện tại (JSON)
     */
    public function count()
    {
        header('Content-Type: application/json; charset=utf-8');
        [$userId, $sessionId] = $this->getUserContext();
        $count = $this->wishlistDAO->getCount($userId, $sessionId);
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
    }
}
