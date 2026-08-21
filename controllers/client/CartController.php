<?php
namespace Controllers\Client;
use DAO\ProductDAO;

class CartController
{
    private ProductDAO $productDAO;

    public function __construct()
    {
        $this->productDAO = new ProductDAO();
    }

   public function add()
    {
        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }

        // Hứng linh hoạt cả productid lẫn product_id
        $productid = $_POST["productid"] ?? $_POST["product_id"] ?? null;
        $quantity = isset($_POST["quantity"]) ? (int) $_POST["quantity"] : 1;

        if (!$productid) {
            echo json_encode(["success" => false, "message" => "Sản phẩm không hợp lệ"]);
            exit;
        }

        $product = $this->productDAO->findById($productid);
        if (!$product) {
            echo json_encode(["success" => false, "message" => "Không tìm thấy sản phẩm"]);
            exit;
        }

        $price = (isset($product->discountPrice) && $product->discountPrice > 0)
            ? $product->discountPrice
            : $product->price;

        // Đã bổ sung $product->proName vào đây để khớp với Model của bạn
        $productName = $product->proName ?? $product->productname ?? $product->name ?? $product->productName ?? $product->title ?? 'Sản phẩm';

        if (isset($_SESSION[CART_SESSION_KEY][$productid])) {
            $_SESSION[CART_SESSION_KEY][$productid]["quantity"] += $quantity;
        } else {
            $_SESSION[CART_SESSION_KEY][$productid] = [
                "productid" => $product->id,
                "productname" => $productName,
                "image" => $product->image ?? '',
                "price" => $price,
                "quantity" => $quantity
            ];
        }

        $cartCount = 0;
        foreach ($_SESSION[CART_SESSION_KEY] as $item) {
            $cartCount += $item["quantity"];
        }

        echo json_encode([
            "success" => true,
            "message" => "Đã thêm sản phẩm vào giỏ hàng",
            "cartCount" => $cartCount
        ]);
        exit;
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

    public function checkout()
    {
    }
}