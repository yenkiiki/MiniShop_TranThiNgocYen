<div class="container my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.3px;">TẤT CẢ SẢN PHẨM</h3>
            <span class="text-muted small">
                Hiển thị <strong><?= count($products ?? []) ?></strong> trên tổng số <strong><?= $totalProducts ?? 0 ?></strong> sản phẩm
            </span>
        </div>
        <div class="small text-muted mt-2 mt-sm-0">
            <a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a> / 
            <span class="text-primary fw-semibold">Sản phẩm</span>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-3">
            <form action="<?= BASE_URL ?>product" method="GET" id="filterForm">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="index">
                <div class="card product-filter-sidebar rounded-4 border-0 shadow-sm overflow-hidden mb-4">
                    <div class="filter-sidebar-header p-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-white mb-0" style="letter-spacing: 0.5px; font-size: 0.95rem;">BỘ LỌC TÌM KIẾM</span>
                        <a href="<?= BASE_URL ?>product" class="filter-reset-btn text-white text-decoration-none small">Xóa lọc</a>
                    </div>
                    <div class="card-body p-3 p-xl-4 d-flex flex-column gap-4 bg-white">
                        <div class="filter-group">
                            <label class="filter-group-title fw-bold text-dark mb-2 d-block">TỪ KHÓA</label>
                            <input type="text" class="form-control filter-input-custom" name="keyword" placeholder="Nhập tên sản phẩm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                        </div>
                        <div class="filter-group">
                            <label class="filter-group-title fw-bold text-dark mb-2 d-block">DANH MỤC</label>
                            <div class="filter-list-container custom-scrollbar">
                                <label class="filter-pill-item <?= empty($_GET['category_slug']) ? 'active' : '' ?>">
                                    <input type="radio" name="category_slug" value="" <?= empty($_GET['category_slug']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <span>Tất cả danh mục</span>
                                </label>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <?php 
                                        $catSlug = is_object($cat) ? ($cat->slug ?? '') : ($cat['slug'] ?? '');
                                        $catName = is_object($cat) ? ($cat->catename ?? $cat->cateName ?? '') : ($cat['catename'] ?? $cat['cateName'] ?? '');
                                        $isSelected = (isset($_GET['category_slug']) && $_GET['category_slug'] === $catSlug);
                                        ?>
                                        <label class="filter-pill-item <?= $isSelected ? 'active' : '' ?>">
                                            <input type="radio" name="category_slug" value="<?= $catSlug ?>" <?= $isSelected ? 'checked' : '' ?> onchange="this.form.submit()">
                                            <span class="text-truncate"><?= htmlspecialchars($catName) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-group-title fw-bold text-dark mb-2 d-block">THƯƠNG HIỆU</label>
                            <div class="filter-list-container custom-scrollbar">
                                <label class="filter-pill-item <?= empty($_GET['brand_slug']) ? 'active' : '' ?>">
                                    <input type="radio" name="brand_slug" value="" <?= empty($_GET['brand_slug']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <span>Tất cả thương hiệu</span>
                                </label>
                                <?php if (!empty($brands)): ?>
                                    <?php foreach ($brands as $brand): ?>
                                        <?php 
                                        $bSlug = is_object($brand) ? ($brand->slug ?? '') : ($brand['slug'] ?? '');
                                        $bName = is_object($brand) ? ($brand->brandName ?? $brand->brandname ?? '') : ($brand['brandName'] ?? $brand['brandname'] ?? '');
                                        $isSelected = (isset($_GET['brand_slug']) && $_GET['brand_slug'] === $bSlug);
                                        ?>
                                        <label class="filter-pill-item <?= $isSelected ? 'active' : '' ?>">
                                            <input type="radio" name="brand_slug" value="<?= $bSlug ?>" <?= $isSelected ? 'checked' : '' ?> onchange="this.form.submit()">
                                            <span class="text-truncate"><?= htmlspecialchars($bName) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-group-title fw-bold text-dark mb-2 d-block">MỨC GIÁ (VNĐ)</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <input type="number" class="form-control filter-input-custom" name="min_price" placeholder="Từ" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                                <span class="text-muted small">-</span>
                                <input type="number" class="form-control filter-input-custom" name="max_price" placeholder="Đến" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-group-title fw-bold text-dark mb-2 d-block">ƯU ĐÃI</label>
                            <label class="filter-sale-toggle d-flex align-items-center justify-content-between p-2 px-3 rounded-3 border">
                                <span class="fw-semibold text-danger" style="font-size: 0.85rem;">Đang giảm giá</span>
                                <input type="checkbox" name="on_sale" value="1" class="form-check-input mt-0" <?= !empty($_GET['on_sale']) ? 'checked' : '' ?> onchange="this.form.submit()">
                            </label>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold filter-apply-btn">Áp dụng bộ lọc</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center p-3 mb-4 rounded-3 bg-white border shadow-sm gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small fw-semibold">Sắp xếp theo:</span>
                    <select name="sort" class="form-select form-select-sm filter-sort-select" onchange="document.getElementById('filterForm').elements['sort'].value = this.value; document.getElementById('filterForm').submit();">
                        <option value="latest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'latest') ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Tên A - Z</option>
                    </select>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                        Trang <?= $page ?? 1 ?> / <?= max($totalPages ?? 1, 1) ?>
                    </span>
                </div>
            </div>
            <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-2 g-md-3">
                <?php if (empty($products)): ?>
                    <div class="col-12">
                        <div class="alert alert-light border border-info-subtle text-center py-5 rounded-4 shadow-sm">
                            <h5 class="fw-bold text-dark mb-2">Không tìm thấy sản phẩm phù hợp</h5>
                            <p class="text-muted small mb-3">Vui lòng thử điều chỉnh hoặc xóa bộ lọc để xem các sản phẩm khác.</p>
                            <a href="<?= BASE_URL ?>product" class="btn btn-outline-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold">Xem tất cả sản phẩm</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $hasDiscount = isset($product->discountPrice) && $product->discountPrice > 0 && $product->discountPrice < $product->price;
                        $discountPercent = $hasDiscount ? round((($product->price - $product->discountPrice) / $product->price) * 100) : 0;
                        $productSlug = !empty($product->slug) ? $product->slug : ('detail?id=' . $product->id);
                        $imgSrc = !empty($product->image) ? (PRODUCT_IMAGE_URL . $product->image) : 'https://placehold.co/400x400?text=SP';
                        $descText = !empty($product->description) ? trim(strip_tags($product->description)) : 'Sản phẩm chính hãng chất lượng cao.';
                        ?>
                        <div class="col">
                            <div class="card h-100 pro-grid-card border rounded-3 overflow-hidden shadow-sm">
                                <div class="pro-grid-img-box position-relative">
                                    <?php if ($hasDiscount && $discountPercent > 0): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-1.5 px-2 py-0.5 fw-bold rounded-pill shadow-sm" style="font-size: 0.65rem;">
                                            -<?= $discountPercent ?>%
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="d-block w-100 h-100">
                                        <img src="<?= $imgSrc ?>" class="pro-grid-img w-100 h-100" alt="<?= htmlspecialchars($product->proName) ?>">
                                    </a>
                                </div>
                                <div class="card-body p-2 p-md-2.5 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.4px;">
                                                <?= htmlspecialchars($product->cateName ?? 'Sản phẩm') ?>
                                            </small>
                                            <?php if (!empty($product->brandName)): ?>
                                                <small class="text-primary fw-semibold" style="font-size: 0.65rem;">
                                                    <?= htmlspecialchars($product->brandName) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="card-title fw-bold mb-1 pro-grid-title">
                                            <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-decoration-none text-dark" title="<?= htmlspecialchars($product->proName) ?>">
                                                <?= htmlspecialchars($product->proName) ?>
                                            </a>
                                        </h6>
                                        <p class="pro-grid-desc text-muted mb-2" title="<?= htmlspecialchars($descText) ?>">
                                            <?= htmlspecialchars(mb_strimwidth($descText, 0, 60, '...')) ?>
                                        </p>
                                    </div>
                                    <div class="mt-2">
                                        <div class="d-flex align-items-baseline gap-1 mb-2">
                                            <?php if ($hasDiscount): ?>
                                                <span class="text-danger fw-bold pro-grid-price mb-0">
                                                    <?= number_format($product->discountPrice, 0, ',', '.') ?> đ
                                                </span>
                                                <span class="text-muted text-decoration-line-through pro-grid-old-price">
                                                    <?= number_format($product->price, 0, ',', '.') ?> đ
                                                </span>
                                            <?php else: ?>
                                                <span class="text-primary fw-bold pro-grid-price mb-0">
                                                    <?= number_format($product->price, 0, ',', '.') ?> đ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="btn btn-outline-secondary btn-sm flex-fill py-1 px-1 rounded-2 fw-semibold pro-btn-action" style="font-size: 0.72rem;">Chi tiết</a>
                                            <button type="button" class="btn btn-primary btn-sm btn-add-cart flex-fill py-1 px-1 rounded-2 fw-semibold pro-grid-btn-add pro-btn-action" data-productid="<?= $product->id ?>" style="font-size: 0.72rem;">Thêm giỏ</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <nav class="mt-5 mb-4">
                    <ul class="pagination justify-content-center gap-1">
                        <?php 
                        $queryParams = $_GET;
                        unset($queryParams['path']);
                        for ($i = 1; $i <= $totalPages; $i++): 
                            $queryParams['page'] = $i;
                            $targetUrl = BASE_URL . 'product?' . http_build_query($queryParams);
                            $isActive = (isset($page) && $page == $i);
                        ?>
                            <li class="page-item <?= $isActive ? 'active' : '' ?>">
                                <a class="page-link modern-page-link rounded-3 px-3 py-2 <?= $isActive ? 'fw-bold' : '' ?>" href="<?= $targetUrl ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>