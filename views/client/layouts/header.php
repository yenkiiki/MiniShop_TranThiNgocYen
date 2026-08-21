<?php
use Composers\HeaderComposer;
$headerData = HeaderComposer::compose();
$categories = $headerData['categories'] ?? [];
$brands = $headerData['brands'] ?? [];
?>

<header class="bg-light border-bottom sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <a class="navbar-brand fw-bold text-primary" href="<?= BASE_URL ?>">
                <i class="bi bi-shop"></i> MINISHOP
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">Trang chủ</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Danh mục</a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categories as $category): ?>
                                <?php
                                $slug = is_object($category) ? ($category->slug ?? '') : ($category['slug'] ?? '');
                                $catName = is_object($category) ? ($category->catename ?? $category->cateName ?? '') : ($category['catename'] ?? $category['cateName'] ?? '');
                                ?>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>product?category_slug=<?= $slug ?>">
                                        <?= htmlspecialchars($catName) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Thương hiệu</a>
                        <ul class="dropdown-menu">
                            <?php foreach ($brands as $brand): ?>
                                <?php
                                $brandSlug = is_object($brand) ? ($brand->slug ?? '') : ($brand['slug'] ?? '');
                                $brandName = is_object($brand) ? ($brand->brandName ?? '') : ($brand['brandName'] ?? '');
                                ?>
                                <li>
                                    <a class="dropdown-item" href="<?= BASE_URL ?>product?brand_slug=<?= $brandSlug ?>">
                                        <?= htmlspecialchars($brandName) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>

                <form class="d-flex me-3" action="<?= BASE_URL ?>product/search" method="GET">
                    <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm sản phẩm..."
                        aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </form>

                <div class="d-flex align-items-center">
                    <a href="<?= BASE_URL ?>login" class="nav-link text-dark me-3">
                        <i class="bi bi-person-circle fs-4"></i>
                    </a>
                    <a href="<?= BASE_URL ?>cart" class="nav-link text-dark position-relative">
                        <i class="bi bi-cart3 fs-4"></i>
                        <span id="cartCount"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?>
                        </span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>