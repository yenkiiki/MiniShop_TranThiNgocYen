<header class="bg-light border-bottom sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <a class="navbar-brand fw-bold text-primary" href="/MINISHOP_TRANTHINGOCYEN/">
                <i class="bi bi-shop"></i> MINISHOP
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/category">Danh mục</a></li>
                    <li class="nav-item"><a class="nav-link" href="/brand">Thương hiệu</a></li>
                </ul>

                <form class="d-flex me-3" action="/product/search" method="GET">
                    <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm sản phẩm..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </form>

                <div class="d-flex align-items-center">
                    <a href="/login" class="nav-link text-dark me-3">
                        <i class="bi bi-person-circle fs-4"></i>
                    </a>
                    <a href="/cart" class="nav-link text-dark position-relative">
                        <i class="bi bi-cart3 fs-4"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>