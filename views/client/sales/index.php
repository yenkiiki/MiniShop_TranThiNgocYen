<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-dark">Trang chủ</a></li>
            <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Flash Sale Giảm Giá</li>
        </ol>
    </nav>
    <div class="card border-0 rounded-4 overflow-hidden shadow-sm text-white mb-4 position-relative sale-hero-banner">
        <div class="card-body p-4 p-md-5 position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase rounded-pill mb-3 shadow-sm" style="font-size: 0.85rem;">
                        <i class="bi bi-fire text-danger me-1"></i> ƯU ĐÃI ĐỘC QUYỀN HÔM NAY
                    </span>
                    <h2 class="display-6 fw-bold mb-2 text-white">ĐẠI TIỆC FLASH SALE</h2>
                    <p class="lead text-white-50 mb-4" style="font-size: 1.1rem;">
                        Săn ngay mỹ phẩm CeraVe, son 3CE và tinh chất dưỡng da chính hãng giảm giá cực sốc lên đến 50%!
                    </p>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="small text-white-50 me-2 text-uppercase fw-semibold"><i class="bi bi-alarm me-1"></i> Kết thúc sau:</span>
                        <div class="timer-box" id="saleHours">08</div>
                        <span class="fw-bold">:</span>
                        <div class="timer-box" id="saleMinutes">45</div>
                        <span class="fw-bold">:</span>
                        <div class="timer-box" id="saleSeconds">20</div>
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end d-none d-lg-block">
                    <i class="bi bi-tags-fill display-1 text-white opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="card border rounded-3 p-3 bg-white shadow-sm mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Danh Sách Deal Hot
                    <span class="badge bg-danger ms-2"><?= $totalSales ?> Sản phẩm</span>
                </h5>
            </div>
            <form method="GET" action="<?= BASE_URL ?>sale" class="d-flex align-items-center gap-2">
                <label for="sortSelect" class="small text-muted text-nowrap fw-semibold mb-0">Sắp xếp:</label>
                <select name="sort" id="sortSelect" class="form-select form-select-sm" style="min-width: 190px;" onchange="this.form.submit()">
                    <option value="discount_desc" <?= $sort === 'discount_desc' ? 'selected' : '' ?>>Giảm giá nhiều nhất (%)</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá sale: Thấp đến cao</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá sale: Cao đến thấp</option>
                    <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Mới cập nhật</option>
                </select>
            </form>
        </div>
    </div>
    <?php if (empty($sales)): ?>
        <div class="card border rounded text-center py-5 shadow-sm">
            <div class="card-body">
                <i class="bi bi-emoji-smile text-muted" style="font-size: 4rem;"></i>
                <h4 class="fw-bold mt-3">Hiện chưa có chương trình Flash Sale</h4>
                <p class="text-muted">Các chương trình ưu đãi mới nhất đang được cập nhật. Hãy quay lại sau bạn nhé!</p>
                <a href="<?= BASE_URL ?>product" class="btn btn-primary px-4 py-2 mt-2">
                    <i class="bi bi-grid me-1"></i> Xem tất cả sản phẩm
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php
        $wlDAO = new \DAO\WishlistDAO();
        $uId = isset($_SESSION['client_user']['id']) ? (int)$_SESSION['client_user']['id'] : null;
        $favoriteProductIds = $wlDAO->getFavoriteProductIds($uId, session_id());
        ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
            <?php foreach ($sales as $sale): ?>
                <?php
                $discountPercent = (int)$sale->discountPercent;
                $originalPrice = (float)$sale->productPrice;
                $salePrice = (float)$sale->salePrice;
                $savedAmount = max(0, $originalPrice - $salePrice);
                $productSlug = !empty($sale->slug) ? $sale->slug : ('detail?id=' . $sale->productId);
                $imgSrc = !empty($sale->productImage) ? (PRODUCT_IMAGE_URL . $sale->productImage) : 'https://placehold.co/220x220?text=SALE';
                $isFavSale = in_array((int)$sale->productId, $favoriteProductIds);
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border rounded-3 position-relative sale-item-card overflow-hidden bg-white">
                        <!-- Nút Yêu Thích -->
                        <button type="button" 
                                class="btn-wishlist-toggle position-absolute top-0 start-0 m-2 z-3 <?= $isFavSale ? 'active' : '' ?>" 
                                data-product-id="<?= $sale->productId ?>" 
                                title="<?= $isFavSale ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' ?>">
                            <i class="bi <?= $isFavSale ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                        </button>

                        <span class="badge bg-danger position-absolute top-0 end-0 m-2 px-2.5 py-1 shadow-sm fs-6 z-2">
                            -<?= $discountPercent ?>%
                        </span>
                        <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-center p-3 d-block overflow-hidden bg-white">
                            <img src="<?= $imgSrc ?>" class="card-img-top sale-img" alt="<?= htmlspecialchars($sale->productName) ?>" style="height: 190px; object-fit: contain;" onerror="this.src='https://placehold.co/200x200?text=SP'">
                        </a>
                        <div class="card-body d-flex flex-column pt-0">
                            <div class="small text-muted mb-1 text-truncate">
                                <?= htmlspecialchars($sale->brandName ?: ($sale->categoryName ?: 'Mỹ phẩm')) ?>
                            </div>
                            <h6 class="card-title fw-bold mb-2">
                                <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-decoration-none text-dark text-truncate d-block" title="<?= htmlspecialchars($sale->productName) ?>">
                                    <?= htmlspecialchars($sale->productName) ?>
                                </a>
                            </h6>
                            <div class="mb-2 mt-auto">
                                <div class="text-muted small text-decoration-line-through">
                                    <?= number_format($originalPrice, 0, ',', '.') ?> đ
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="text-danger fw-bold fs-5">
                                        <?= number_format($salePrice, 0, ',', '.') ?> đ
                                    </span>
                                </div>
                                <?php if ($savedAmount > 0): ?>
                                    <small class="text-success fw-semibold" style="font-size: 0.75rem;">
                                        Tiết kiệm: <?= number_format($savedAmount, 0, ',', '.') ?> đ
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <div class="progress" style="height: 14px; border-radius: 10px; background-color: #ffcccc;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 82%;" aria-valuenow="82" aria-valuemin="0" aria-valuemax="100">
                                        <small style="font-size: 9px; font-weight: bold;">Đang cháy hàng</small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between gap-2">
                                <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="btn btn-outline-secondary btn-sm flex-fill" title="Xem chi tiết">
                                    Chi tiết
                                </a>
                                <button type="button" class="btn btn-danger btn-sm btn-add-cart flex-fill fw-semibold" data-productid="<?= $sale->productId ?>" title="Thêm vào giỏ hàng">
                                    <i class="bi bi-cart-plus me-1"></i> Thêm giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Phân trang Flash Sale" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_URL ?>sale?page=<?= $page - 1 ?>&sort=<?= $sort ?>"><i class="bi bi-chevron-left"></i> Trước</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= BASE_URL ?>sale?page=<?= $i ?>&sort=<?= $sort ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= BASE_URL ?>sale?page=<?= $page + 1 ?>&sort=<?= $sort ?>">Sau <i class="bi bi-chevron-right"></i></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let hours = 8;
    let minutes = 45;
    let seconds = 20;
    const elH = document.getElementById('saleHours');
    const elM = document.getElementById('saleMinutes');
    const elS = document.getElementById('saleSeconds');
    if (elH && elM && elS) {
        setInterval(function () {
            if (seconds > 0) {
                seconds--;
            } else {
                seconds = 59;
                if (minutes > 0) {
                    minutes--;
                } else {
                    minutes = 59;
                    if (hours > 0) hours--;
                }
            }
            elH.textContent = String(hours).padStart(2, '0');
            elM.textContent = String(minutes).padStart(2, '0');
            elS.textContent = String(seconds).padStart(2, '0');
        }, 1000);
    }
});
</script>