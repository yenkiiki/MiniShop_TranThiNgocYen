<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse py-3 border-end">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item mb-1">
                <a class="nav-link text-dark active" href="<?= BASE_URL ?>views/admin/dashboard.php">
                    📊 Tổng quan (Dashboard)
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-dark" href="<?= BASE_URL ?>views/admin/categories/index.php">
                    📁 Quản lý Danh mục
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-dark" href="<?= BASE_URL ?>views/admin/brands/index.php">
                    🏷️ Quản lý Thương hiệu
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-dark" href="<?= BASE_URL ?>views/admin/products/index.php">
                    📦 Quản lý Sản phẩm
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-dark" href="<?= BASE_URL ?>views/admin/customers/index.php">
                    👥 Quản lý Khách hàng
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-dark" href="<?= BASE_URL ?>views/admin/users/index.php">
                    🔒 Quản lý Tài khoản
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link text-dark" href="<?= BASE_URL ?>views/admin/orders/index.php">
                    🛒 Quản lý Đơn hàng
                </a>
            </li>

        </ul>

        <hr class="my-3">

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= BASE_URL ?>index.php" target="_blank">
                    🌐 Xem trang chủ Client
                </a>
            </li>
        </ul>
    </div>
</nav>