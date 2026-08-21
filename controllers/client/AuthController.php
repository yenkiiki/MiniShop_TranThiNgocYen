<?php
namespace Controllers\Client;

use DAO\UserDAO;
use DAO\ProductDAO;
use Models\User;

class AuthController
{
    public function login()
    {
        if (isset($_SESSION['client_user'])) {
            header("Location: " . BASE_URL);
            exit();
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            if (empty($username) || empty($password)) {
                $error = "Vui lòng điền đầy đủ tên đăng nhập và mật khẩu.";
            } else {
                $userDAO = new UserDAO();
                $user = $userDAO->findByUsername($username);
                if ($user && $user->status === 1 && password_verify($password, $user->password)) {
                    $_SESSION['client_user'] = [
                        'id' => $user->id,
                        'fullname' => $user->fullName,
                        'username' => $user->userName,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '',
                        'address' => $user->address ?? ''
                    ];

                    // Đồng bộ wishlist khách sang tài khoản đã đăng nhập
                    try {
                        $wishlistDAO = new \DAO\WishlistDAO();
                        $wishlistDAO->syncSessionToUser(session_id(), (int)$user->id);
                    } catch (\Exception $e) {}

                    // Nếu trước đó có sản phẩm đang chờ thêm vào giỏ hàng
                    if (!empty($_SESSION['pending_cart_add'])) {
                        $pending = $_SESSION['pending_cart_add'];
                        $pId = (int)($pending['product_id'] ?? 0);
                        $pQty = max(1, (int)($pending['quantity'] ?? 1));

                        if ($pId > 0) {
                            $productDAO = new ProductDAO();
                            $product = $productDAO->findById($pId);
                            if ($product) {
                                $cartKey = defined('CART_SESSION_KEY') ? CART_SESSION_KEY : 'cart';
                                if (!isset($_SESSION[$cartKey])) {
                                    $_SESSION[$cartKey] = [];
                                }
                                $price = (isset($product->discountPrice) && $product->discountPrice > 0)
                                    ? $product->discountPrice
                                    : $product->price;
                                $productName = $product->proName ?? 'Sản phẩm';

                                if (isset($_SESSION[$cartKey][$pId])) {
                                    $_SESSION[$cartKey][$pId]["quantity"] += $pQty;
                                } else {
                                    $_SESSION[$cartKey][$pId] = [
                                        "productid" => $product->id,
                                        "productname" => $productName,
                                        "image" => $product->image ?? '',
                                        "price" => $price,
                                        "quantity" => $pQty
                                    ];
                                }
                                $_SESSION['cart_message'] = "Đăng nhập thành công và đã thêm '" . htmlspecialchars($productName) . "' vào giỏ hàng!";
                            }
                        }
                        unset($_SESSION['pending_cart_add']);
                    } else {
                        $_SESSION['cart_message'] = "Đăng nhập thành công! Chào mừng " . htmlspecialchars($user->fullName);
                    }

                    // Chuyển tiếp về trang đang đứng trước đó
                    $targetUrl = BASE_URL;
                    if (!empty($_SESSION['redirect_after_login'])) {
                        $targetUrl = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                    } elseif (!empty($_POST['redirect'])) {
                        $targetUrl = $_POST['redirect'];
                    }

                    header("Location: " . $targetUrl);
                    exit();
                } else {
                    $error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                }
            }
        }
        $pageTitle = "Đăng nhập tài khoản";
        ob_start();
        require __DIR__ . "/../../views/client/auth/login.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

    public function register()
    {
        if (isset($_SESSION['client_user'])) {
            header("Location: " . BASE_URL);
            exit();
        }
        $error = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
                $error = "Vui lòng điền đầy đủ các thông tin bắt buộc.";
            } else {
                $userDAO = new UserDAO();
                $existingUser = $userDAO->findByUsername($username);
                if ($existingUser) {
                    $error = "Tên đăng nhập đã tồn tại.";
                } else {
                    $newUser = new User();
                    $newUser->fullName = $fullname;
                    $newUser->userName = $username;
                    $newUser->email = $email;
                    $newUser->password = password_hash($password, PASSWORD_DEFAULT);
                    $newUser->phone = $phone;
                    $newUser->address = $address;
                    $newUser->role = 0; 
                    $newUser->status = 1;

                    if ($userDAO->insert($newUser)) {
                        $createdUser = $userDAO->findByUsername($username);
                        if ($createdUser) {
                            $_SESSION['client_user'] = [
                                'id' => $createdUser->id,
                                'fullname' => $createdUser->fullName,
                                'username' => $createdUser->userName,
                                'email' => $createdUser->email,
                                'phone' => $createdUser->phone ?? '',
                                'address' => $createdUser->address ?? ''
                            ];

                            // Nếu có sản phẩm chờ thêm giỏ hàng
                            if (!empty($_SESSION['pending_cart_add'])) {
                                $pending = $_SESSION['pending_cart_add'];
                                $pId = (int)($pending['product_id'] ?? 0);
                                $pQty = max(1, (int)($pending['quantity'] ?? 1));

                                if ($pId > 0) {
                                    $productDAO = new ProductDAO();
                                    $product = $productDAO->findById($pId);
                                    if ($product) {
                                        $cartKey = defined('CART_SESSION_KEY') ? CART_SESSION_KEY : 'cart';
                                        if (!isset($_SESSION[$cartKey])) {
                                            $_SESSION[$cartKey] = [];
                                        }
                                        $price = (isset($product->discountPrice) && $product->discountPrice > 0)
                                            ? $product->discountPrice
                                            : $product->price;
                                        $productName = $product->proName ?? 'Sản phẩm';

                                        if (isset($_SESSION[$cartKey][$pId])) {
                                            $_SESSION[$cartKey][$pId]["quantity"] += $pQty;
                                        } else {
                                            $_SESSION[$cartKey][$pId] = [
                                                "productid" => $product->id,
                                                "productname" => $productName,
                                                "image" => $product->image ?? '',
                                                "price" => $price,
                                                "quantity" => $pQty
                                            ];
                                        }
                                        $_SESSION['cart_message'] = "Đăng ký thành công! Đã tự động thêm '" . htmlspecialchars($productName) . "' vào giỏ hàng!";
                                    }
                                }
                                unset($_SESSION['pending_cart_add']);
                            } else {
                                $_SESSION['cart_message'] = "Đăng ký tài khoản thành công! Chào mừng " . htmlspecialchars($createdUser->fullName);
                            }

                            $targetUrl = BASE_URL;
                            if (!empty($_SESSION['redirect_after_login'])) {
                                $targetUrl = $_SESSION['redirect_after_login'];
                                unset($_SESSION['redirect_after_login']);
                            } elseif (!empty($_POST['redirect'])) {
                                $targetUrl = $_POST['redirect'];
                            }

                            header("Location: " . $targetUrl);
                            exit();
                        } else {
                            $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay.";
                        }
                    } else {
                        $error = "Đăng ký thất bại, vui lòng thử lại sau.";
                    }
                }
            }
        }
        $pageTitle = "Đăng ký tài khoản";
        ob_start();
        require __DIR__ . "/../../views/client/auth/register.php";
        $content = ob_get_clean();
        require __DIR__ . "/../../views/client/layouts/master.php";
    }

    public function logout()
    {
        if (isset($_SESSION['client_user'])) {
            unset($_SESSION['client_user']);
        }
        header("Location: " . BASE_URL);
        exit();
    }
}