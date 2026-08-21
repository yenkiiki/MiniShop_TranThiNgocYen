<div class="category-page-wrapper">
    <div class="category-hero-section py-4 px-3 px-md-4 rounded-4 mb-4 shadow-sm position-relative overflow-hidden">
        <div class="hero-bg-shapes">
            <span class="shape shape-1"></span>
            <span class="shape shape-2"></span>
            <span class="shape shape-3"></span>
        </div>
        <div class="container position-relative z-1">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <span class="badge bg-white text-primary border border-info-subtle px-3 py-2 rounded-pill shadow-sm mb-2 fw-bold" style="font-size: 0.8rem;">
                        <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i> BỘ SƯU TẬP THEO DANH MỤC
                    </span>
                    <h1 class="fw-bold text-dark mb-2 display-6">Danh Mục Sản Phẩm</h1>
                    <p class="text-secondary mb-0" style="font-size: 1.05rem;">
                        Khám phá đầy đủ thiết bị & phụ kiện chính hãng được phân loại trực quan, dễ dàng mua sắm.
                    </p>
                </div>
                <div class="col-lg-5">
                    <form action="<?= BASE_URL ?>category" method="GET" class="category-search-form d-flex gap-2 p-2 bg-white rounded-pill shadow-sm border">
                        <input type="text" name="keyword" class="form-control border-0 rounded-pill ps-3 shadow-none" placeholder="Tìm danh mục hoặc sản phẩm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-search me-1"></i> Tìm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($categoryRows)): ?>
        <div class="category-chips-nav mb-4 sticky-top py-2 bg-body bg-opacity-95" style="z-index: 1020; backdrop-filter: blur(10px);">
            <div class="container">
                <div class="d-flex align-items-center gap-2 overflow-auto pb-2 custom-scrollbar">
                    <span class="text-muted small fw-bold text-nowrap me-2"><i class="bi bi-compass me-1 text-primary"></i> Nhảy nhanh:</span>
                    <?php foreach ($categoryRows as $row): ?>
                        <?php 
                        $cat = $row['category'];
                        $targetId = 'cat-row-' . $cat->id;
                        ?>
                        <a href="#<?= $targetId ?>" class="category-chip text-decoration-none">
                            <span class="chip-icon"><i class="bi bi-folder2-open"></i></span>
                            <span class="chip-text"><?= htmlspecialchars($cat->cateName) ?></span>
                            <span class="chip-count"><?= $row['totalCount'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <?php if (empty($categoryRows)): ?>
            <div class="text-center py-5 bg-light rounded-4 border p-4 my-4">
                <i class="bi bi-search text-primary opacity-50" style="font-size: 3.5rem;"></i>
                <h4 class="mt-3 fw-bold text-secondary">Không tìm thấy danh mục hoặc sản phẩm phù hợp!</h4>
                <p class="text-muted">Vui lòng thử lại với từ khóa khác.</p>
                <a href="<?= BASE_URL ?>category" class="btn btn-primary rounded-pill px-4 mt-2">Xem tất cả danh mục</a>
            </div>
        <?php else: ?>
            <?php foreach ($categoryRows as $rowIndex => $row): ?>
                <?php 
                $cat = $row['category'];
                $products = $row['products'];
                $totalInCat = $row['totalCount'];
                $catSlug = !empty($cat->slug) ? $cat->slug : ('category_' . $cat->id);
                $rowAnchorId = 'cat-row-' . $cat->id;
                $catImg = !empty($cat->image) ? (BASE_URL . 'uploads/categories/' . $cat->image) : null;
                ?>
                <div class="category-row-card mb-5 rounded-4 overflow-hidden" id="<?= $rowAnchorId ?>">
                    <div class="category-row-header p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="category-emblem shadow-sm">
                                <?php if ($catImg): ?>
                                    <img src="<?= $catImg ?>" alt="" class="cat-emblem-img" onerror="this.parentElement.innerHTML='<i class=\'bi bi-tag-fill\'></i>'">
                                <?php else: ?>
                                    <i class="bi bi-box-seam-fill"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h3 class="category-title mb-0 fw-bold">
                                        <a href="<?= BASE_URL ?>product?category_slug=<?= $catSlug ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($cat->cateName) ?>
                                        </a>
                                    </h3>
                                    <span class="category-count-badge">
                                        <?= $totalInCat ?> sản phẩm
                                    </span>
                                </div>
                                <?php if (!empty($cat->description)): ?>
                                    <p class="text-muted small mb-0 mt-1 d-none d-md-block text-truncate" style="max-width: 550px;">
                                        <?= htmlspecialchars($cat->description) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <a href="<?= BASE_URL ?>product?category_slug=<?= $catSlug ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold category-view-all-btn">
                                Xem tất cả <?= htmlspecialchars($cat->cateName) ?> <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="category-row-body p-3 p-md-4">
                        <?php if (empty($products)): ?>
                            <div class="text-center py-4 bg-white rounded-3 border border-info-subtle p-3">
                                <i class="bi bi-inbox text-info fs-2 d-block mb-1"></i>
                                <span class="text-muted small">Hiện chưa có sản phẩm nào trong danh mục này.</span>
                            </div>
                        <?php else: ?>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $hasDiscount = isset($product->discountPrice) && $product->discountPrice > 0 && $product->discountPrice < $product->price;
                                    $discountPercent = $hasDiscount ? round((($product->price - $product->discountPrice) / $product->price) * 100) : 0;
                                    $productSlug = !empty($product->slug) ? $product->slug : ('detail?id=' . $product->id);
                                    $imgSrc = !empty($product->image) ? (PRODUCT_IMAGE_URL . $product->image) : 'https://placehold.co/240x240?text=SP';
                                    ?>
                                    <div class="col">
                                        <div class="card h-100 product-grid-card shadow-sm position-relative border">
                                            <?php if ($hasDiscount && $discountPercent > 0): ?>
                                                <span class="badge bg-danger position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm fs-6 rounded-pill z-2">
                                                    -<?= $discountPercent ?>%
                                                </span>
                                            <?php endif; ?>
                                            <div class="product-thumb-container p-3 text-center position-relative overflow-hidden bg-white">
                                                <a href="<?= BASE_URL ?>product/<?= $productSlug ?>">
                                                    <img src="<?= $imgSrc ?>" class="card-img-top product-thumb-img" alt="<?= htmlspecialchars($product->proName) ?>" onerror="this.src='https://placehold.co/200x200?text=SP'">
                                                </a>
                                            </div>
                                            <div class="card-body d-flex flex-column p-3">
                                                <?php if (!empty($product->brandName) && $product->brandName !== 'Không có thương hiệu'): ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle align-self-start mb-2 px-2 py-1" style="font-size: 0.7rem;">
                                                        <?= htmlspecialchars($product->brandName) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <h6 class="card-title fw-bold mb-2">
                                                    <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-decoration-none text-dark product-name-link text-truncate d-block" title="<?= htmlspecialchars($product->proName) ?>">
                                                        <?= htmlspecialchars($product->proName) ?>
                                                    </a>
                                                </h6>
                                                <div class="small text-warning mb-2">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <span class="text-muted ms-1" style="font-size: 0.75rem;">(5.0)</span>
                                                </div>
                                                <div class="mb-3 mt-auto">
                                                    <?php if ($hasDiscount): ?>
                                                        <div class="text-muted small text-decoration-line-through">
                                                            <?= number_format($product->price, 0, ',', '.') ?> đ
                                                        </div>
                                                        <div class="text-danger fw-bold fs-5">
                                                            <?= number_format($product->discountPrice, 0, ',', '.') ?> đ
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="fw-bold fs-5 text-primary">
                                                            <?= number_format($product->price, 0, ',', '.') ?> đ
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex justify-content-between gap-2">
                                                    <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="btn btn-outline-secondary btn-sm flex-fill rounded-pill" title="Xem chi tiết">
                                                        Chi tiết
                                                    </a>
                                                    <button type="button" class="btn btn-primary btn-sm btn-add-cart flex-fill rounded-pill" data-productid="<?= $product->id ?>" title="Thêm vào giỏ hàng">
                                                        <i class="bi bi-cart-plus me-1"></i> Thêm giỏ
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>