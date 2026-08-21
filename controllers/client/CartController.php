<?php
namespace Controllers\Client;

use DAO\ProductDAO;
use DAO\OrderDAO;
use Services\VNPayService;
use Services\EmailService;

class CartController
{
    private ProductDAO $productDAO;
    private OrderDAO $orderDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
        $this->orderDAO = new OrderDAO();
    }

    public function add()
    {
        $isLoggedIn = isset($_SESSION['client_user']);
        $productid = $_POST["productid"] ?? $_POST["product_id"] ?? null;
        $quantity = isset($_POST["quantity"]) ? max(1, (int) $_POST["quantity"]) : 1;

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                || isset($_POST['is_ajax']);

        if (!$isLoggedIn) {
            if ($productid) {
                $_SESSION['pending_cart_add'] = [
                    'product_id' => (int)$productid,
                    'quantity' => $quantity
                ];
            }
            $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'product');
            $_SESSION['login_notice'] = "Bạn cần phải đăng nhập để thêm sản phẩm vào giỏ hàng và tiếp tục mua hàng!";

            if ($isAjax || !isset($_POST['traditional_form_post'])) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    "success" => false,
                    "require_login" => true,
                    "redirect" => BASE_URL . "auth/login",
                    "message" => "Bạn cần phải đăng nhập"
                ]);
                exit;
            } else {
                header("Location: " . BASE_URL . "auth/login");
                exit;
            }
        }

        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }

        if (!$productid) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["success" => false, "message" => "Sản phẩm không hợp lệ"]);
                exit;
            } else {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL));
                exit;
            }
        }

        $product = $this->productDAO->findById($productid);
        if (!$product) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["success" => false, "message" => "Không tìm thấy sản phẩm"]);
                exit;
            } else {
                header("Location: " . ($_SERVER['HTTP_REFERER'] ?? BASE_URL));
                exit;
            }
        }

        $variantId = isset($_POST["variant_id"]) ? (int)$_POST["variant_id"] : 0;
        $variant = null;
        if ($variantId > 0) {
            require_once __DIR__ . '/../../dao/ProductVariantDAO.php';
            $variantDAO = new \DAO\ProductVariantDAO();
            $variant = $variantDAO->findById($variantId);
        }

        $cartKey = ($variant && $variant->id > 0) ? ($product->id . '_' . $variant->id) : (string)$product->id;

        $productName = $product->proName ?? $product->productname ?? $product->name ?? $product->productName ?? $product->title ?? 'Sản phẩm';
        $variantName = $variant ? $variant->variantName : null;
        $image = ($variant && !empty($variant->image)) ? $variant->image : ($product->image ?? '');

        // Tính giá của biến thể
        if ($variant && $variant->price !== null && $variant->price > 0) {
            $price = ($variant->discountPrice && $variant->discountPrice > 0 && $variant->discountPrice < $variant->price) ? $variant->discountPrice : $variant->price;
        } else {
            $price = (isset($product->discountPrice) && $product->discountPrice > 0) ? $product->discountPrice : $product->price;
        }

        if (isset($_SESSION[CART_SESSION_KEY][$cartKey])) {
            $_SESSION[CART_SESSION_KEY][$cartKey]["quantity"] += $quantity;
        } else {
            $_SESSION[CART_SESSION_KEY][$cartKey] = [
                "productid" => $product->id,
                "variant_id" => $variant ? $variant->id : null,
                "variant_name" => $variantName,
                "productname" => $productName,
                "image" => $image,
                "price" => $price,
                "quantity" => $quantity
            ];
        }

        $cartCount = 0;
        foreach ($_SESSION[CART_SESSION_KEY] as $item) {
            $cartCount += $item["quantity"];
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                "success" => true,
                "message" => "Đã thêm sản phẩm vào giỏ hàng",
                "cartCount" => $cartCount
            ]);
            exit;
        } else {
            $_SESSION['cart_message'] = "Đã thêm '" . htmlspecialchars($productName) . "' vào giỏ hàng!";
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . "product")));
            exit;
        }
    }

    public function index()
    {
        $cart = $_SESSION[CART_SESSION_KEY] ?? [];
        $total = 0;
        foreach ($cart as $item) {
            $total += $item["price"] * $item["quantity"];
        }

        $pageTitle = "Giỏ hàng";
        ob_start();
        require __DIR__ . "/../../views/client/cart/index.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

    public function update()
    {
        $productid = $_POST["productid"] ?? null;
        $quantity = isset($_POST["quantity"]) ? (int) $_POST["quantity"] : 0;

        if (!$productid || !isset($_SESSION[CART_SESSION_KEY][$productid])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["success" => false, "message" => "Sản phẩm không tồn tại trong giỏ hàng"]);
            exit;
        }

        if ($quantity <= 0) {
            unset($_SESSION[CART_SESSION_KEY][$productid]);
            $newQuantity = 0;
            $itemSubtotal = 0;
        } else {
            $_SESSION[CART_SESSION_KEY][$productid]["quantity"] = $quantity;
            $newQuantity = $quantity;
            $itemSubtotal = $_SESSION[CART_SESSION_KEY][$productid]["price"] * $newQuantity;
        }

        $total = 0;
        $cartCount = 0;
        foreach ($_SESSION[CART_SESSION_KEY] as $item) {
            $total += $item["price"] * $item["quantity"];
            $cartCount += $item["quantity"];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "success" => true,
            "newQuantity" => $newQuantity,
            "itemSubtotal" => $itemSubtotal,
            "total" => $total,
            "cartCount" => $cartCount
        ]);
        exit;
    }

    public function remove()
    {
        $productid = $_POST["productid"] ?? null;

        if (!$productid || !isset($_SESSION[CART_SESSION_KEY][$productid])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["success" => false, "message" => "Sản phẩm không tồn tại trong giỏ hàng"]);
            exit;
        }

        unset($_SESSION[CART_SESSION_KEY][$productid]);

        $total = 0;
        $cartCount = 0;
        foreach ($_SESSION[CART_SESSION_KEY] as $item) {
            $total += $item["price"] * $item["quantity"];
            $cartCount += $item["quantity"];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "success" => true,
            "message" => "Đã xóa sản phẩm khỏi giỏ hàng",
            "total" => $total,
            "cartCount" => $cartCount,
            "isCartEmpty" => empty($_SESSION[CART_SESSION_KEY])
        ]);
        exit;
    }

    public function count()
    {
    }

    /**
     * AJAX: Áp dụng mã giảm giá (Shopee-style)
     */
    public function applyCoupon()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? ''));
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit;
        }

        $cart = $_SESSION[CART_SESSION_KEY] ?? [];
        if (empty($cart)) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống.']);
            exit;
        }

        $cartSubtotal = 0;
        foreach ($cart as $item) {
            $cartSubtotal += $item['price'] * $item['quantity'];
        }

        require_once __DIR__ . '/../../dao/CouponDAO.php';
        $couponDAO = new \DAO\CouponDAO();
        $coupon = $couponDAO->findByCode($code);

        if (!$coupon) {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã bị xóa.']);
            exit;
        }

        $calc = $coupon->calculateDiscount($cartSubtotal);
        if (!$calc['valid']) {
            echo json_encode(['success' => false, 'message' => $calc['message']]);
            exit;
        }

        $discountAmount = $calc['discount'];
        $shippingFee = ($cartSubtotal >= 1000000) ? 0.0 : 30000.0;
        $grandTotal = max(0, $cartSubtotal - $discountAmount + $shippingFee);

        $_SESSION['applied_coupon'] = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'discount_type' => $coupon->discountType,
            'discount_value' => $coupon->discountValue,
            'discount_amount' => $discountAmount
        ];

        echo json_encode([
            'success' => true,
            'message' => $calc['message'],
            'coupon' => $_SESSION['applied_coupon'],
            'cart_subtotal' => $cartSubtotal,
            'discount_amount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'grand_total' => $grandTotal
        ]);
        exit;
    }

    /**
     * Alias methods để tương thích định dạng snake_case
     */
    public function apply_coupon() { $this->applyCoupon(); }
    public function remove_coupon() { $this->removeCoupon(); }

    public function checkout()
    {
        $isLoggedIn = isset($_SESSION['client_user']);
        if (!$isLoggedIn) {
            $_SESSION['redirect_after_login'] = BASE_URL . "cart/checkout";
            $_SESSION['login_notice'] = "Bạn cần phải đăng nhập để tiến hành thanh toán đơn hàng!";
            header("Location: " . BASE_URL . "auth/login");
            exit;
        }

        $cart = $_SESSION[CART_SESSION_KEY] ?? [];
        if (empty($cart)) {
            header("Location: " . BASE_URL . "cart");
            exit;
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item["price"] * $item["quantity"];
        }

        require_once __DIR__ . '/../../dao/CouponDAO.php';
        $couponDAO = new \DAO\CouponDAO();

        // Kiểm tra và tính toán mã giảm giá đang áp dụng (nếu có)
        $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
        $discountAmount = 0.0;
        $couponCode = null;

        if (!empty($appliedCoupon)) {
            $coupon = $couponDAO->findByCode($appliedCoupon['code']);
            if ($coupon) {
                $calc = $coupon->calculateDiscount($total);
                if ($calc['valid']) {
                    $discountAmount = $calc['discount'];
                    $couponCode = $coupon->code;
                    $_SESSION['applied_coupon']['discount_amount'] = $discountAmount;
                } else {
                    unset($_SESSION['applied_coupon']);
                    $appliedCoupon = null;
                }
            } else {
                unset($_SESSION['applied_coupon']);
                $appliedCoupon = null;
            }
        }

        // Lấy danh sách voucher khả dụng để hiển thị popup Shopee Voucher
        $availableCoupons = $couponDAO->getActiveCoupons();

        $currentUser = $_SESSION['client_user'] ?? ($_SESSION['user'] ?? null);
        $userId = $currentUser ? (int)$currentUser['id'] : null;

        $error = $_SESSION['checkout_error'] ?? null;
        unset($_SESSION['checkout_error']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $note = trim($_POST['note'] ?? '');
            $paymentMethod = trim($_POST['payment_method'] ?? 'VNPAY');

            if (empty($fullname) || empty($phone) || empty($address)) {
                $error = "Vui lòng điền đầy đủ các thông tin bắt buộc (*).";
            } elseif (!preg_match('/^(0|\+84)[0-9]{9}$/', $phone)) {
                $error = "Số điện thoại không hợp lệ! Vui lòng nhập định dạng 10 chữ số.";
            } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Địa chỉ email không đúng định dạng!";
            } else {
                $shippingFee = ($total >= 1000000) ? 0.0 : 30000.0;
                $totalAmount = max(0.0, $total - $discountAmount + $shippingFee);
                $orderCode = "DH" . strtoupper(uniqid());
                $fullNote = "Người nhận: $fullname - SĐT: $phone - Địa chỉ: $address. Ghi chú: $note";
                if (!empty($couponCode)) {
                    $fullNote .= " (Mã giảm giá: $couponCode - Giảm: " . number_format($discountAmount, 0, ',', '.') . " đ)";
                }

                require_once __DIR__ . "/../../dao/BaseDAO.php";
                require_once __DIR__ . "/../../services/VNPayService.php";
                require_once __DIR__ . "/../../services/EmailService.php";

                $db = new class extends \DAO\BaseDAO {
                    public function findCustomerByPhone(string $phone): ?int {
                        $sql = "SELECT id FROM customers WHERE phone = ?";
                        $stmt = $this->prepare($sql);
                        $stmt->bind_param("s", $phone);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()) return (int)$row['id'];
                        return null;
                    }
                    public function createCustomer(string $fullname, string $phone, string $email, string $address, string $note): int|false {
                        $sql = "INSERT INTO customers(fullname, phone, email, address, note, status) VALUES(?, ?, ?, ?, ?, 1)";
                        $stmt = $this->prepare($sql);
                        $stmt->bind_param("sssss", $fullname, $phone, $email, $address, $note);
                        if ($stmt->execute()) return $stmt->insert_id;
                        return false;
                    }
                    public function updateCustomerEmail(int $id, string $email, string $address): void {
                        $sql = "UPDATE customers SET email = IF(email IS NULL OR email = '', ?, email), address = ? WHERE id = ?";
                        $stmt = $this->prepare($sql);
                        $stmt->bind_param("ssi", $email, $address, $id);
                        $stmt->execute();
                    }
                    public function createOrder(int $customerId, ?int $userId, string $orderCode, float $totalAmount, float $shippingFee, string $paymentMethod, ?string $couponCode, float $discountAmount, string $note, int $status = 0): int|false {
                        $sql = "INSERT INTO orders(customer_id, user_id, order_code, total_amount, shipping_fee, payment_method, coupon_code, discount_amount, note, status) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $this->prepare($sql);
                        $stmt->bind_param("iisdssddsi", $customerId, $userId, $orderCode, $totalAmount, $shippingFee, $paymentMethod, $couponCode, $discountAmount, $note, $status);
                        if ($stmt->execute()) return $stmt->insert_id;
                        return false;
                    }
                    public function createOrderDetail(int $orderId, int $productId, int $quantity, float $price, float $subtotal): bool {
                        $sql = "INSERT INTO order_details(order_id, product_id, quantity, price, subtotal) VALUES(?, ?, ?, ?, ?)";
                        $stmt = $this->prepare($sql);
                        $stmt->bind_param("iiidd", $orderId, $productId, $quantity, $price, $subtotal);
                        return $stmt->execute();
                    }
                    public function beginTx(): void { $this->beginTransaction(); }
                    public function commitTx(): void { $this->commit(); }
                    public function rollbackTx(): void { $this->rollback(); }
                };

                try {
                    $db->beginTx();
                    $customerId = $db->findCustomerByPhone($phone);
                    if (!$customerId) {
                        $customerId = $db->createCustomer($fullname, $phone, $email, $address, $fullNote);
                        if (!$customerId) throw new \Exception("Không thể tạo khách hàng mới.");
                    } else {
                        $db->updateCustomerEmail($customerId, $email, $address);
                    }

                    $orderStatus = 0; // Chờ xác nhận hoặc chờ thanh toán VNPay
                    $orderId = $db->createOrder($customerId, $userId, $orderCode, $totalAmount, $shippingFee, $paymentMethod, $couponCode, $discountAmount, $fullNote, $orderStatus);
                    if (!$orderId) throw new \Exception("Không thể tạo đơn hàng.");

                    $orderDetailsForMail = [];
                    foreach ($cart as $item) {
                        $productId = $item['productid'];
                        $quantity = $item['quantity'];
                        $price = $item['price'];
                        $subtotal = $price * $quantity;
                        if (!$db->createOrderDetail($orderId, $productId, $quantity, $price, $subtotal)) {
                            throw new \Exception("Không thể lưu chi tiết sản phẩm.");
                        }
                        $orderDetailsForMail[] = [
                            'proname' => $item['productname'],
                            'quantity' => $quantity,
                            'price' => $price,
                            'subtotal' => $subtotal
                        ];
                    }

                    // Tăng số lượt đã sử dụng của coupon nếu có
                    if (!empty($couponCode)) {
                        $couponDAO->incrementUsedCount($couponCode);
                    }

                    $db->commitTx();

                    // NẾU CHỌN THANH TOÁN VNPAY: Chuyển hướng sang cổng VNPAY Sandbox
                    if ($paymentMethod === 'VNPAY') {
                        $orderInfo = "Thanh toan don hang " . $orderCode;
                        $vnpayUrl = \Services\VNPayService::createPaymentUrl($orderCode, $totalAmount, $orderInfo);
                        
                        // Lưu thông tin tạm để dùng khi callback nếu cần
                        $_SESSION['vnpay_pending_order'] = [
                            'order_code' => $orderCode,
                            'email' => $email,
                            'fullname' => $fullname,
                            'phone' => $phone,
                            'address' => $address,
                            'note' => $note,
                            'coupon_code' => $couponCode,
                            'discount_amount' => $discountAmount
                        ];

                        unset($_SESSION['applied_coupon']);
                        header("Location: " . $vnpayUrl);
                        exit;
                    }

                    // NẾU CHỌN COD HOẶC CHUYỂN KHOẢN: Gửi email ngay và hoàn tất
                    $orderData = [
                        'order_code' => $orderCode,
                        'total_amount' => $totalAmount,
                        'shipping_fee' => $shippingFee,
                        'discount_amount' => $discountAmount,
                        'coupon_code' => $couponCode,
                        'payment_method' => $paymentMethod,
                        'note' => $note,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $customerData = [
                        'fullname' => $fullname,
                        'phone' => $phone,
                        'email' => $email,
                        'address' => $address
                    ];
                    \Services\EmailService::sendOrderConfirmation($orderData, $customerData, $orderDetailsForMail);

                    unset($_SESSION[CART_SESSION_KEY]);
                    unset($_SESSION['applied_coupon']);
                    header("Location: " . BASE_URL . "cart/success?code=" . $orderCode);
                    exit;
                } catch (\Throwable $e) {
                    $db->rollbackTx();
                    $error = "Thanh toán thất bại: " . $e->getMessage();
                }
            }
        }

        $pageTitle = "Thanh toán đơn hàng";
        ob_start();
        require __DIR__ . "/../../views/client/cart/checkout.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

    /**
     * Xử lý kết quả trả về từ cổng thanh toán VNPay
     */
    public function vnpayReturn()
    {
        require_once __DIR__ . "/../../services/VNPayService.php";
        require_once __DIR__ . "/../../services/EmailService.php";

        $verifyResult = \Services\VNPayService::verifyReturn($_GET);
        $orderCode = $verifyResult['orderCode'] ?? '';

        if (empty($orderCode)) {
            $_SESSION['checkout_error'] = "Không tìm thấy thông tin đơn hàng từ VNPay.";
            header("Location: " . BASE_URL . "cart/checkout");
            exit;
        }

        $order = $this->orderDAO->findByOrderCode($orderCode);
        if (!$order) {
            $_SESSION['checkout_error'] = "Đơn hàng #{$orderCode} không tồn tại trên hệ thống.";
            header("Location: " . BASE_URL . "cart/checkout");
            exit;
        }

        if ($verifyResult['isSuccess']) {
            // Cập nhật trạng thái đơn hàng: 1 (Đã xác nhận / Đã thanh toán VNPAY)
            $transNo = $verifyResult['transNo'];
            $bankCode = $verifyResult['bankCode'];
            $noteAppend = "[VNPAY: Mã GD {$transNo}, Ngân hàng {$bankCode}, Số tiền " . number_format($verifyResult['amount'], 0, ',', '.') . "đ]";
            
            $this->orderDAO->updatePaymentStatus($orderCode, 1, $noteAppend);

            // Lấy chi tiết đơn hàng để gửi email
            $orderDetails = $this->orderDAO->getDetailsByOrderId((int)$order['id']);
            $orderDetailsForMail = [];
            foreach ($orderDetails as $item) {
                $orderDetailsForMail[] = [
                    'proname' => $item->productName ?? ('Sản phẩm #' . $item->productId),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal
                ];
            }

            $orderData = [
                'order_code' => $order['order_code'],
                'total_amount' => (float)$order['total_amount'],
                'shipping_fee' => (float)$order['shipping_fee'],
                'payment_method' => 'VNPAY',
                'note' => $order['note'],
                'created_at' => $order['created_at']
            ];

            $customerData = [
                'fullname' => $order['customer_name'] ?? 'Quý khách',
                'phone' => $order['customer_phone'] ?? '',
                'email' => $order['customer_email'] ?? '',
                'address' => $order['customer_address'] ?? ''
            ];

            // Gửi email xác nhận kèm thông tin giao dịch VNPay tới Khách hàng và Admin (tranngocyen280905@gmail.com)
            \Services\EmailService::sendOrderConfirmation($orderData, $customerData, $orderDetailsForMail, $verifyResult);

            // Xóa giỏ hàng sau khi thanh toán thành công
            unset($_SESSION[CART_SESSION_KEY]);
            unset($_SESSION['vnpay_pending_order']);

            // Chuyển hướng tới màn hình thành công
            header("Location: " . BASE_URL . "cart/success?code=" . urlencode($orderCode) . "&vnpay=1&trans_no=" . urlencode($transNo) . "&bank=" . urlencode($bankCode));
            exit;
        } else {
            // Thanh toán thất bại hoặc khách hủy
            $noteAppend = "[VNPAY THẤT BẠI: " . $verifyResult['message'] . "]";
            $this->orderDAO->updatePaymentStatus($orderCode, 0, $noteAppend);

            $_SESSION['checkout_error'] = "Giao dịch VNPAY không thành công: " . $verifyResult['message'];
            header("Location: " . BASE_URL . "cart/checkout");
            exit;
        }
    }

    public function success()
    {
        $orderCode = $_GET['code'] ?? '';
        $pageTitle = "Đặt hàng thành công";
        ob_start();
        require __DIR__ . "/../../views/client/cart/checkout_success.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }
}