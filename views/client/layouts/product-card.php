<?php
$hasDiscount = isset($product->discountPrice) && $product->discountPrice > 0 && $product->discountPrice < $product->price;
$discountPercent = $hasDiscount ? round((($product->price - $product->discountPrice) / $product->price) * 100) : 0;
$productSlug = !empty($product->slug) ? $product->slug : ('detail?id=' . $product->id);
$imgSrc = !empty($product->image) ? (PRODUCT_IMAGE_URL . $product->image) : 'https://placehold.co/220x220?text=SP';

// Kiểm tra trạng thái yêu thích
if (!isset($favoriteProductIds)) {
    global $favoriteProductIds;
    if (!isset($favoriteProductIds)) {
        $wlDAO = new \DAO\WishlistDAO();
        $uId = isset($_SESSION['client_user']['id']) ? (int)$_SESSION['client_user']['id'] : null;
        $favoriteProductIds = $wlDAO->getFavoriteProductIds($uId, session_id());
    }
}
$isFav = in_array((int)$product->id, $favoriteProductIds ?? []);
?>
<div class="card h-100 shadow-sm position-relative modern-product-card">
    <!-- Nút Yêu Thích (Wishlist) -->
    <button type="button" 
            class="btn-wishlist-toggle position-absolute top-0 start-0 m-2 z-3 <?= $isFav ? 'active' : '' ?>" 
            data-product-id="<?= $product->id ?>" 
            title="<?= $isFav ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' ?>">
        <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
    </button>

    <!-- Badge giảm giá -->
    <?php if ($hasDiscount && $discountPercent > 0): ?>
        <span class="badge bg-danger position-absolute top-0 end-0 m-2 px-2 py-1 shadow-sm fs-6 z-2">
            -<?= $discountPercent ?>%
        </span>
    <?php endif; ?>
    
    <a href="<?= BASE_URL ?>product/<?= $productSlug ?>">
        <img src="<?= $imgSrc ?>" class="card-img-top p-2" alt="<?= htmlspecialchars($product->proName) ?>" style="height: 200px; object-fit: contain;" onerror="this.src='https://placehold.co/200x200?text=SP'">
    </a>
    
    <div class="card-body d-flex flex-column">
        <h6 class="card-title fw-bold mb-2">
            <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="text-decoration-none text-dark text-truncate d-block" title="<?= htmlspecialchars($product->proName) ?>">
                <?= htmlspecialchars($product->proName) ?>
            </a>
        </h6>
        
        <div class="mb-3 mt-auto">
            <?php if ($hasDiscount): ?>
                <div class="text-muted small text-decoration-line-through">
                    <?= number_format($product->price, 0, ',', '.') ?> đ
                </div>
                <div class="text-danger fw-bold fs-5">
                    <?= number_format($product->discountPrice, 0, ',', '.') ?> đ
                </div>
            <?php else: ?>
                <div class="fw-bold fs-5 text-dark">
                    <?= number_format($product->price, 0, ',', '.') ?> đ
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-between gap-2">
            <a href="<?= BASE_URL ?>product/<?= $productSlug ?>" class="btn btn-outline-secondary btn-sm flex-fill" title="Xem chi tiết">
                Chi tiết
            </a>
            <button type="button" class="btn btn-primary btn-sm btn-add-cart flex-fill" data-productid="<?= $product->id ?>" title="Thêm vào giỏ hàng">
                Thêm giỏ
            </button>
        </div>
    </div>
</div>