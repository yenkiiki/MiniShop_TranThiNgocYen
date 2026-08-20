<div class="container my-4">
    <h2>Danh sách sản phẩm</h2>

    <!-- Form lọc và sắp xếp -->
    <form action="" method="GET" class="row g-3 mb-4 bg-light p-3 rounded">
        <input type="hidden" name="controller" value="product">
        <input type="hidden" name="action" value="index">

        <!-- Tìm kiếm -->
        <div class="col-md-2">
            <input type="text" class="form-control" name="keyword" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        </div>

        <!-- Lọc Danh mục -->
        <div class="col-md-2">
            <select name="category_slug" class="form-select">
                <option value="">-- Danh mục --</option>
                <?php if (isset($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat->slug ?>" <?= (isset($_GET['category_slug']) && $_GET['category_slug'] == $cat->slug) ? 'selected' : '' ?>>
                <?= $cat->cateName ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Lọc Thương hiệu -->
        <div class="col-md-2">
            <select name="brand_slug" class="form-select">
                <option value="">-- Thương hiệu --</option>
                <?php if (isset($brands)): ?>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand->slug ?>" <?= (isset($_GET['brand_slug']) && $_GET['brand_slug'] == $brand->slug) ? 'selected' : '' ?>>
              <?= $brand->brandName ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Khoảng giá -->
        <div class="col-md-1">
            <input type="number" class="form-control" name="min_price" placeholder="Từ" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
        </div>
        <div class="col-md-1">
            <input type="number" class="form-control" name="max_price" placeholder="Đến" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
        </div>

        <!-- Sắp xếp -->
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="latest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'latest') ? 'selected' : '' ?>>Mới nhất</option>
                <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá tăng</option>
                <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá giảm</option>
                <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
        </div>

        <!-- Nút submit -->
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Lọc / Tìm</button>
        </div>
    </form>

    <!-- Danh sách hiển thị -->
    <div class="row mt-3">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    Không tìm thấy sản phẩm nào phù hợp.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <?php require __DIR__ . '/../layouts/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Phân trang -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php 
                $queryParams = $_GET;
                for ($i = 1; $i <= $totalPages; $i++): 
                    $queryParams['page'] = $i;
                    $targetUrl = '?' . http_build_query($queryParams);
                ?>
                    <li class="page-item <?= (isset($page) && $page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $targetUrl ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>