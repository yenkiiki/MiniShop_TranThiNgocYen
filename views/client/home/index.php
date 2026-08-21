<?php
$banners = [
    [
        'image' => BASE_URL . 'uploads/banner_cerave.jpg',
        'title' => 'CERAVE - DƯỢC MỸ PHẨM HÀNG ĐẦU',
        'subtitle' => 'Sữa rửa mặt & kem dưỡng ẩm phục hồi hàng rào bảo vệ da chuyên sâu',
        'btn_text' => 'Khám phá CeraVe',
        'link' => BASE_URL . 'product?brand_slug=cerave'
    ],
    [
        'image' => BASE_URL . 'uploads/banner_3ce.jpg',
        'title' => '3CE STYLENANDA KOREA',
        'subtitle' => 'Bộ sưu tập son kem lì velvet, son thỏi & phấn mắt chuẩn phong cách Hàn Quốc',
        'btn_text' => 'Xem son 3CE',
        'link' => BASE_URL . 'product?brand_slug=3ce'
    ],
    [
        'image' => BASE_URL . 'uploads/banner_skincare.jpg',
        'title' => 'TINH CHẤT DƯỠNG DA CHUYÊN SÂU',
        'subtitle' => 'Serum Vitamin C, Kem chống nắng & Tinh dầu Organic cho làn da căng bóng',
        'btn_text' => 'Mua ngay ưu đãi',
        'link' => BASE_URL . 'product'
    ]
];

$homeWlDAO = new \DAO\WishlistDAO();
$homeUId = isset($_SESSION['client_user']['id']) ? (int)$_SESSION['client_user']['id'] : null;
$homeFavIds = $homeWlDAO->getFavoriteProductIds($homeUId, session_id());
?>

<?php if (isset($_GET['keyword'])): ?>
    <div class="container my-4">
        <h3 class="mb-4 fw-bold">Kết quả tìm kiếm cho: <span class="text-primary">"<?= htmlspecialchars($keyword ?? '') ?>"</span></h3>
        <?php if (empty($keyword)): ?>
            <div class="alert alert-warning text-center py-4">
                <h5>Vui lòng nhập từ khóa để tìm kiếm sản phẩm!</h5>
            </div>
        <?php elseif (empty($products)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-search text-muted fs-1 mb-3 d-block"></i>
                <h5>Không tìm thấy sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>".</h5>
                <a href="<?= BASE_URL ?>product" class="btn btn-primary mt-2">Xem tất cả sản phẩm</a>
            </div>
        <?php else: ?>
            <p class="text-muted mb-4">Tìm thấy <strong><?= count($products) ?></strong> sản phẩm phù hợp:</p>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <?php require __DIR__ . '/../layouts/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>

    <div class="container mb-4">
        <div id="homeBannerCarousel" class="carousel slide rounded-4 overflow-hidden shadow-sm" data-bs-ride="carousel" data-bs-interval="4500">
            <div class="carousel-indicators">
                <?php foreach ($banners as $index => $banner): ?>
                    <button type="button" data-bs-target="#homeBannerCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>"></button>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-inner">
                <?php foreach ($banners as $index => $banner): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="position-relative banner-wrapper">
                            <img src="<?= htmlspecialchars($banner['image']) ?>" class="d-block w-100 home-banner-img" alt="<?= htmlspecialchars($banner['title']) ?>" onerror="this.src='https://placehold.co/1200x480?text=MINISHOP+COSMETICS'">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center banner-overlay">
                                <div class="container ps-4 ps-md-5 text-white">
                                    <div style="max-width: 580px;">
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 mb-2 text-uppercase fw-bold shadow-sm bg-white" style="font-size: 0.8rem;">
                                            <i class="bi bi-stars me-1"></i> Bộ sưu tập mỹ phẩm chính hãng
                                        </span>
                                        <h2 class="display-6 fw-bold text-white mb-2 text-shadow"><?= htmlspecialchars($banner['title']) ?></h2>
                                        <p class="lead text-white-50 mb-3 d-none d-sm-block text-shadow" style="font-size: 1.05rem;"><?= htmlspecialchars($banner['subtitle']) ?></p>
                                        <a href="<?= htmlspecialchars($banner['link']) ?>" class="btn btn-primary fw-bold px-4 py-2 shadow rounded-pill">
                                            <?= htmlspecialchars($banner['btn_text']) ?> <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#homeBannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon p-3 bg-dark bg-opacity-50 rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Trước</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homeBannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon p-3 bg-dark bg-opacity-50 rounded-circle" aria-hidden="true"></span>
                <span class="visually-hidden">Sau</span>
            </button>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <a href="<?= BASE_URL ?>product" class="text-decoration-none">
                    <div class="jelly-card jelly-ocean h-100 p-3 p-md-4 rounded-4 position-relative overflow-hidden d-flex align-items-center justify-content-between">
                        <div class="bubble-container">
                            <span class="bubble bubble-1"></span>
                            <span class="bubble bubble-2"></span>
                            <span class="bubble bubble-3"></span>
                            <span class="bubble bubble-4"></span>
                            <span class="bubble bubble-5"></span>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="jelly-badge jelly-badge-ocean">Chính Hãng</span>
                                <h5 class="fw-bold text-dark mb-0">Tất Cả Sản Phẩm</h5>
                            </div>
                            <p class="text-muted small mb-0">Khám phá toàn bộ mỹ phẩm & chăm sóc da</p>
                        </div>
                        <div class="position-relative z-1 ps-2">
                            <span class="jelly-btn jelly-btn-ocean">Khám phá <i class="bi bi-arrow-right-short fs-5"></i></span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6">
                <a href="<?= BASE_URL ?>sale" class="text-decoration-none">
                    <div class="jelly-card jelly-rose h-100 p-3 p-md-4 rounded-4 position-relative overflow-hidden d-flex align-items-center justify-content-between">
                        <div class="bubble-container">
                            <span class="bubble bubble-1"></span>
                            <span class="bubble bubble-2"></span>
                            <span class="bubble bubble-3"></span>
                            <span class="bubble bubble-4"></span>
                            <span class="bubble bubble-5"></span>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="jelly-badge jelly-badge-rose">Flash Deal</span>
                                <h5 class="fw-bold text-danger mb-0">Sản Phẩm Giảm Giá</h5>
                            </div>
                            <p class="text-muted small mb-0">Ưu đãi giảm giá độc quyền đến 50%</p>
                        </div>
                        <div class="position-relative z-1 ps-2">
                            <span class="jelly-btn jelly-btn-rose">Săn deal <i class="bi bi-arrow-right-short fs-5"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="card h-100 border rounded p-3 text-center bg-white shadow-sm">
                    <div class="fs-2 text-primary mb-1"><i class="bi bi-truck"></i></div>
                    <h6 class="fw-bold mb-1">Giao hàng nhanh 24h</h6>
                    <small class="text-muted">Miễn phí ship đơn từ 1.000.000đ</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border rounded p-3 text-center bg-white shadow-sm">
                    <div class="fs-2 text-success mb-1"><i class="bi bi-shield-check"></i></div>
                    <h6 class="fw-bold mb-1">Chính hãng 100%</h6>
                    <small class="text-muted">Cam kết bảo hành chu đáo</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border rounded p-3 text-center bg-white shadow-sm">
                    <div class="fs-2 text-warning mb-1"><i class="bi bi-arrow-repeat"></i></div>
                    <h6 class="fw-bold mb-1">Đổi trả 7 ngày</h6>
                    <small class="text-muted">Đổi mới nếu phát sinh lỗi</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border rounded p-3 text-center bg-white shadow-sm">
                    <div class="fs-2 text-danger mb-1"><i class="bi bi-headset"></i></div>
                    <h6 class="fw-bold mb-1">Hỗ trợ 24/7</h6>
                    <small class="text-muted">Tư vấn tận tình, nhanh chóng</small>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> DANH MỤC SẢN PHẨM
                </span>
                <h5 class="fw-bold mb-0 text-dark">BỘ SƯU TẬP THEO DANH MỤC</h5>
            </div>
            <a href="<?= BASE_URL ?>category" class="text-primary text-decoration-none small fw-semibold">
                Xem tất cả danh mục <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="d-flex flex-column gap-2">
            <?php if (!empty($categoryRows)): ?>
                <?php foreach ($categoryRows as $row): ?>
                    <?php 
                    $cat = $row['category'];
                    $totalInCat = $row['totalCount'];
                    $slug = is_object($cat) ? ($cat->slug ?? '') : ($cat['slug'] ?? '');
                    $catName = is_object($cat) ? ($cat->catename ?? $cat->cateName ?? '') : ($cat['catename'] ?? $cat['cateName'] ?? '');
                    $catImg = is_object($cat) ? ($cat->image ?? '') : ($cat['image'] ?? '');
                    $catDesc = is_object($cat) ? ($cat->description ?? '') : ($cat['description'] ?? '');
                    
                    $imgUrl = !empty($catImg) ? (BASE_URL . 'uploads/categories/' . $catImg) : '';
                    if (empty($catImg)) {
                        $imgUrl = 'https://placehold.co/80x80?text=' . urlencode(mb_substr($catName, 0, 4));
                    }
                    ?>
                    <a href="<?= BASE_URL ?>product?category_slug=<?= $slug ?>" class="home-cat-full-row d-flex align-items-center justify-content-between p-2 px-3 rounded-3 border text-decoration-none shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <div class="home-cat-img-box rounded-3 overflow-hidden border">
                                <img src="<?= $imgUrl ?>" class="home-cat-img" alt="<?= htmlspecialchars($catName) ?>">
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 home-cat-name"><?= htmlspecialchars($catName) ?></h6>
                                <?php if (!empty($catDesc)): ?>
                                    <small class="text-muted d-none d-md-block text-truncate mt-1" style="max-width: 500px; font-size: 0.78rem;">
                                        <?= htmlspecialchars($catDesc) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill" style="font-size: 0.75rem;">
                                <?= $totalInCat ?> sản phẩm
                            </span>
                            <span class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-none d-sm-inline-block" style="font-size: 0.78rem;">
                                Xem danh mục <i class="bi bi-chevron-right ms-1"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-muted small py-3 text-center">Đang cập nhật danh mục...</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container mb-5">
        <div class="card modern-sale-card rounded-4 overflow-hidden border shadow-sm">
            <div class="modern-sale-header p-3 px-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge modern-sale-badge px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Flash Sale
                    </span>
                    <h4 class="fw-bold mb-0 text-dark">ƯU ĐÃI GIỜ VÀNG</h4>
                    
                    <div class="d-none d-md-flex align-items-center gap-1 ms-2">
                        <small class="text-muted fw-semibold me-1">Kết thúc sau:</small>
                        <span class="modern-timer-box" id="saleTimerHours">08</span>
                        <span class="text-danger fw-bold">:</span>
                        <span class="modern-timer-box" id="saleTimerMinutes">45</span>
                        <span class="text-danger fw-bold">:</span>
                        <span class="modern-timer-box" id="saleTimerSeconds">30</span>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>sale" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1.5 fw-bold">
                    Xem tất cả ưu đãi <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            
            <div class="card-body p-3 p-md-4 bg-light-subtle">
                <?php if (!empty($discountProducts)): ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
                        <?php foreach ($discountProducts as $product): ?>
                            <?php
                            $hasDiscount = isset($product->discountPrice) && $product->discountPrice > 0 && $product->discountPrice < $product->price;
                            $discountPercent = $hasDiscount ? round((($product->price - $product->discountPrice) / $product->price) * 100) : 0;
                            $productSlug = !empty($product->slug) ? $product->slug : ('detail?id=' . $product->id);
                            $imgSrc = !empty($product->image) ? (PRODUCT_IMAGE_URL . $product->image) : 'https://placehold.co/400x400?text=Sale';
                            $descText = !empty($product->description) ? trim(strip_tags($product->description)) : 'Sản phẩm chính hãng với mức giá ưu đãi đặc biệt.';
                            $isFavHome = in_array((int)$product->id, $homeFavIds);
                            ?>
                            <div class="col">
                                <div class="card h-100 modern-product-card border rounded-3 overflow-hidden shadow-sm position-relative">
                                    <!-- Nút Yêu Thích -->
                                    <button type="button" 
                                            class="btn-wishlist-toggle position-absolute top-0 end-0 m-2 z-3 <?= $isFavHome ? 'active' : '' ?>" 
                                            data-product-id="<?= $product->id ?>" 
                                            title="<?= $isFavHome ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' ?>">
                                        <i class="bi <?= $isFavHome ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                    </button>

                                    <div class="modern-card-img-wrapper position-relative">
                                        <?php if ($hasDiscount && $discountPercent > 0): ?>
                                            <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2.5 py-1 fw-bold shadow-sm z-2" style="font-size: 0.72rem;">
                                                -<?= $discountPercent ?>% GIẢM
                                            </span>
                                        <?php endif; ?>
                                        <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="d-block w-100 h-100">
                                            <img src="<?= $imgSrc ?>" class="modern-card-img w-100 h-100" alt="<?= htmlspecialchars($product->proName) ?>">
                                        </a>
                                    </div>
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;"><?= htmlspecialchars($product->cateName ?? 'Sản phẩm') ?></small>
                                            </div>
                                            <h6 class="card-title fw-bold mb-1" style="font-size: 0.92rem;">
                                                <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($product->proName) ?></a>
                                            </h6>
                                            <p class="modern-prod-desc text-muted mb-2"><?= htmlspecialchars(mb_strimwidth($descText, 0, 75, '...')) ?></p>
                                        </div>
                                        <div class="mt-2">
                                            <div class="d-flex align-items-baseline gap-2 mb-2">
                                                <?php if ($hasDiscount): ?>
                                                    <span class="text-danger fw-bold fs-5 mb-0"><?= number_format($product->discountPrice, 0, ',', '.') ?> đ</span>
                                                    <span class="text-muted small text-decoration-line-through"><?= number_format($product->price, 0, ',', '.') ?> đ</span>
                                                <?php else: ?>
                                                    <span class="text-dark fw-bold fs-5 mb-0"><?= number_format($product->price, 0, ',', '.') ?> đ</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="btn btn-outline-secondary btn-sm flex-fill py-1.5 px-2 rounded-2 fw-semibold" style="font-size: 0.78rem;">Chi tiết</a>
                                                <button type="button" class="btn btn-danger btn-sm btn-add-cart flex-fill py-1.5 px-2 rounded-2 fw-semibold modern-btn-buy" data-productid="<?= $product->id ?>" style="font-size: 0.78rem;">Thêm giỏ</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">Hiện chưa có chương trình giảm giá nào đang diễn ra.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h4 class="fw-bold mb-0 text-dark">SẢN PHẨM MỚI NHẤT</h4>
                <small class="text-muted" style="font-size: 0.8rem;">Cập nhật các sản phẩm mới và thịnh hành nhất</small>
            </div>
            <a href="<?= BASE_URL ?>product" class="text-decoration-none small fw-semibold text-primary">Xem thêm sản phẩm &rarr;</a>
        </div>
        
        <?php if (!empty($newProducts)): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
                <?php foreach ($newProducts as $product): ?>
                    <?php
                    $hasDiscount = isset($product->discountPrice) && $product->discountPrice > 0 && $product->discountPrice < $product->price;
                    $discountPercent = $hasDiscount ? round((($product->price - $product->discountPrice) / $product->price) * 100) : 0;
                    $productSlug = !empty($product->slug) ? $product->slug : ('detail?id=' . $product->id);
                    $imgSrc = !empty($product->image) ? (PRODUCT_IMAGE_URL . $product->image) : 'https://placehold.co/400x400?text=New';
                    $descText = !empty($product->description) ? trim(strip_tags($product->description)) : 'Sản phẩm mới chính hãng, chất lượng đảm bảo.';
                    $isFavNew = in_array((int)$product->id, $homeFavIds);
                    ?>
                    <div class="col">
                        <div class="card h-100 modern-new-card border rounded-3 overflow-hidden shadow-sm position-relative">
                            <!-- Nút Yêu Thích -->
                            <button type="button" 
                                    class="btn-wishlist-toggle position-absolute top-0 end-0 m-2 z-3 <?= $isFavNew ? 'active' : '' ?>" 
                                    data-product-id="<?= $product->id ?>" 
                                    title="<?= $isFavNew ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' ?>">
                                <i class="bi <?= $isFavNew ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </button>

                            <div class="modern-new-img-box position-relative">
                                <span class="badge bg-primary position-absolute top-0 start-0 m-2 px-2.5 py-1 fw-bold rounded-pill shadow-sm" style="font-size: 0.72rem;">MỚI</span>
                                <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="d-block w-100 h-100">
                                    <img src="<?= $imgSrc ?>" class="modern-new-img w-100 h-100" alt="<?= htmlspecialchars($product->proName) ?>">
                                </a>
                            </div>
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;"><?= htmlspecialchars($product->cateName ?? 'Sản phẩm') ?></small>
                                    </div>
                                    <h6 class="card-title fw-bold mb-1" style="font-size: 0.92rem;">
                                        <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($product->proName) ?></a>
                                    </h6>
                                    <p class="modern-new-desc text-muted mb-2"><?= htmlspecialchars(mb_strimwidth($descText, 0, 75, '...')) ?></p>
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex align-items-baseline gap-2 mb-3">
                                        <?php if ($hasDiscount): ?>
                                            <span class="text-danger fw-bold fs-5 mb-0"><?= number_format($product->discountPrice, 0, ',', '.') ?> đ</span>
                                            <span class="text-muted small text-decoration-line-through"><?= number_format($product->price, 0, ',', '.') ?> đ</span>
                                        <?php else: ?>
                                            <span class="text-primary fw-bold fs-5 mb-0"><?= number_format($product->price, 0, ',', '.') ?> đ</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="btn btn-outline-secondary btn-sm flex-fill py-1.5 px-2 rounded-2 fw-semibold" style="font-size: 0.78rem;">Chi tiết</a>
                                        <button type="button" class="btn btn-primary btn-sm btn-add-cart flex-fill py-1.5 px-2 rounded-2 fw-semibold modern-new-btn-add" data-productid="<?= $product->id ?>" style="font-size: 0.78rem;">Thêm vào giỏ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted py-3 text-center">Chưa có sản phẩm mới nào.</p>
        <?php endif; ?>
    </div>

<?php if (!empty($brands)): ?>
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-0 text-dark" style="letter-spacing:0.5px;">THƯƠNG HIỆU</h4>
            <small class="text-muted" style="font-size: 0.8rem;">Các đối tác & thương hiệu chính hãng hàng đầu</small>
        </div>
        <a href="<?= BASE_URL ?>product" class="text-decoration-none small fw-semibold text-primary">Xem tất cả &rarr;</a>
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
        <?php foreach ($brands as $brand): ?>
            <?php 
            $bSlug = is_object($brand) ? ($brand->slug ?? '') : ($brand['slug'] ?? '');
            $bName = is_object($brand) ? ($brand->brandName ?? '') : ($brand['brandName'] ?? '');
            $bImg = is_object($brand) ? ($brand->image ?? '') : ($brand['image'] ?? '');
            $bImgUrl = !empty($bImg) ? (BASE_URL . 'uploads/brands/' . $bImg) : 'https://placehold.co/200x100?text=' . urlencode($bName);
            ?>
            <div class="col">
                <a href="<?= BASE_URL ?>product?brand_slug=<?= $bSlug ?>" class="text-decoration-none brand-card-link d-block" title="<?= htmlspecialchars($bName) ?>">
                    <div class="home-brand-card rounded-3 border bg-white shadow-sm d-flex align-items-center justify-content-center overflow-hidden">
                        <img src="<?= $bImgUrl ?>" 
                             alt="<?= htmlspecialchars($bName) ?>" 
                             class="home-brand-logo"
                             onerror="this.src='https://placehold.co/200x100?text=Brand'">
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var hoursEl = document.getElementById('saleTimerHours');
    var minutesEl = document.getElementById('saleTimerMinutes');
    var secondsEl = document.getElementById('saleTimerSeconds');

    if (hoursEl && minutesEl && secondsEl) {
        var totalSecs = 8 * 3600 + 45 * 60 + 30;
        setInterval(function () {
            if (totalSecs > 0) {
                totalSecs--;
                var h = Math.floor(totalSecs / 3600);
                var m = Math.floor((totalSecs % 3600) / 60);
                var s = totalSecs % 60;
                hoursEl.textContent = (h < 10 ? '0' : '') + h;
                minutesEl.textContent = (m < 10 ? '0' : '') + m;
                secondsEl.textContent = (s < 10 ? '0' : '') + s;
            }
        }, 1000);
    }
});
</script>
