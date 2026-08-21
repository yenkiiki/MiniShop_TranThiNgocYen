<?php
use Composers\HeaderComposer;
use DAO\WishlistDAO;
$headerData = HeaderComposer::compose();
$categories = $headerData['categories'] ?? [];
$brands = $headerData['brands'] ?? [];

$wishlistDAO = new WishlistDAO();
$headerUserId = isset($_SESSION['client_user']['id']) ? (int)$_SESSION['client_user']['id'] : null;
$headerSessionId = session_id();
$wishlistCount = $wishlistDAO->getCount($headerUserId, $headerSessionId);
?>

<header class="client-glass-header sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg py-2.5">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-decoration-none header-brand-logo" href="<?= BASE_URL ?>">
                <div class="brand-icon-box shadow-sm">
                    <i class="bi bi-shop-window"></i>
                </div>
                <span class="brand-text">MINISHOP</span>
            </a>

            <!-- Mobile Toggler -->
            <button class="navbar-toggler border-0 shadow-none p-1.5" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-primary"></i>
            </button>

            <!-- Navbar Links & Actions -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Navigation Items -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-1 ps-lg-3">
                    <li class="nav-item">
                        <a class="nav-link client-nav-link" href="<?= BASE_URL ?>">
                            <i class=""></i>Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link client-nav-link" href="<?= BASE_URL ?>product">
                            <i class=""></i>Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link client-nav-link nav-link-sale" href="<?= BASE_URL ?>sale">
                            <i class="bi bi-fire text-danger me-1"></i>Flash Sale 
                            <span class="badge bg-danger rounded-pill px-2 py-0.5 ms-1 sale-pill-badge">HOT</span>
                        </a>
                    </li>

                    <!-- Dropdown Danh mục -->
                    <li class="nav-item dropdown">
                        <a class="nav-link client-nav-link dropdown-toggle" href="<?= BASE_URL ?>category" data-bs-toggle="dropdown">
                            <i class=""></i>Danh mục
                        </a>
                        <ul class="dropdown-menu glass-dropdown shadow border-0 rounded-3 mt-2">
                            <li>
                                <a class="dropdown-item fw-bold text-primary py-2 border-bottom" href="<?= BASE_URL ?>category">
                                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Xem tất cả danh mục
                                </a>
                            </li>
                            <?php foreach ($categories as $category): ?>
                                <?php
                                $slug = is_object($category) ? ($category->slug ?? '') : ($category['slug'] ?? '');
                                $catName = is_object($category) ? ($category->catename ?? $category->cateName ?? '') : ($category['catename'] ?? $category['cateName'] ?? '');
                                ?>
                                <li>
                                    <a class="dropdown-item py-1.5" href="<?= BASE_URL ?>product?category_slug=<?= $slug ?>">
                                        <?= htmlspecialchars($catName) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <!-- Dropdown Thương hiệu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link client-nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class=""></i>Thương hiệu
                        </a>
                        <ul class="dropdown-menu glass-dropdown shadow border-0 rounded-3 mt-2">
                            <?php foreach ($brands as $brand): ?>
                                <?php
                                $brandSlug = is_object($brand) ? ($brand->slug ?? '') : ($brand['slug'] ?? '');
                                $brandName = is_object($brand) ? ($brand->brandName ?? '') : ($brand['brandName'] ?? '');
                                ?>
                                <li>
                                    <a class="dropdown-item py-1.5" href="<?= BASE_URL ?>product?brand_slug=<?= $brandSlug ?>">
                                        <?= htmlspecialchars($brandName) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>

                <!-- Thanh Tìm Kiếm Nhỏ Gọn Đẹp Mắt -->
                <form class="d-flex my-2 my-lg-0 me-lg-3 header-search-form" action="<?= BASE_URL ?>product/search" method="GET">
                    <div class="header-search-box">
                        <input class="header-search-input" 
                               type="search" 
                               name="keyword" 
                               placeholder="Tìm mỹ phẩm, thương hiệu..." 
                               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                               aria-label="Search" 
                               required>
                        <button class="header-search-btn" type="submit" title="Tìm kiếm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Tài khoản, Yêu thích & Giỏ hàng -->
                <div class="d-flex align-items-center gap-2 pt-2 pt-lg-0">
                    <?php if (isset($_SESSION['client_user'])): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 user-pill-btn shadow-sm" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 text-primary"></i>
                                <span class="fw-semibold text-dark user-name-text text-truncate" style="max-width: 120px;">
                                    <?= htmlspecialchars($_SESSION['client_user']['fullname']) ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end glass-dropdown shadow border-0 rounded-3 mt-2">
                                <li>
                                    <a class="dropdown-item py-2" href="<?= BASE_URL ?>order/history">
                                        <i class="bi bi-receipt me-2 text-primary"></i>Lịch sử đơn hàng
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= BASE_URL ?>wishlist">
                                        <i class="bi bi-heart-fill me-2 text-danger"></i>Sản phẩm yêu thích
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="<?= BASE_URL ?>auth/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-1.5">
                            <a href="<?= BASE_URL ?>auth/login" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                Đăng nhập
                            </a>
                            <a href="<?= BASE_URL ?>auth/register" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-sm">
                                Đăng ký
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Sản phẩm yêu thích (Wishlist) -->
                    <a href="<?= BASE_URL ?>wishlist" class="header-wishlist-btn position-relative" title="Sản phẩm yêu thích">
                        <i class="bi bi-heart fs-5"></i>
                        <span id="wishlistCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm <?= $wishlistCount > 0 ? '' : 'd-none' ?>">
                            <?= $wishlistCount ?>
                        </span>
                    </a>

                    <!-- Giỏ hàng -->
                    <a href="<?= BASE_URL ?>cart" class="header-cart-btn position-relative" title="Xem giỏ hàng">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span id="cartCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm">
                            <?php 
                            $cartKey = defined('CART_SESSION_KEY') ? CART_SESSION_KEY : 'cart';
                            echo isset($_SESSION[$cartKey]) ? array_sum(array_column($_SESSION[$cartKey], 'quantity')) : 0; 
                            ?>
                        </span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>