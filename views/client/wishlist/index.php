<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Sản phẩm yêu thích</li>
        </ol>
    </nav>

    <!-- Header Tiêu đề -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-heart-fill text-danger me-2"></i>Sản phẩm yêu thích
            </h3>
            <p class="text-muted small mb-0">Lưu lại các sản phẩm bạn quan tâm để dễ dàng theo dõi giá và đặt mua nhanh chóng</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold" id="wishlistPageBadge">
                <span id="wishlistPageCount"><?= $totalWishlist ?></span> Sản phẩm
            </span>
            <a href="<?= BASE_URL ?>product" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
            </a>
        </div>
    </div>

    <!-- Thông báo nếu có -->
    <?php if (!empty($_SESSION['wishlist_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['wishlist_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['wishlist_message']); ?>
    <?php endif; ?>

    <!-- Trạng thái trống (Empty Wishlist) -->
    <div id="wishlistEmptyState" class="card border-0 rounded-4 text-center py-5 shadow-sm bg-white <?= $totalWishlist === 0 ? '' : 'd-none' ?>">
        <div class="card-body py-5">
            <div class="wishlist-empty-icon mb-3">
                <i class="bi bi-heart text-danger opacity-50" style="font-size: 4.5rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Danh sách yêu thích của bạn đang trống</h4>
            <p class="text-muted small mx-auto" style="max-width: 480px;">
                Hãy bấm vào biểu tượng trái tim <i class="bi bi-heart text-danger"></i> ở bất kỳ sản phẩm nào để lưu lại và theo dõi các chương trình khuyến mãi độc quyền!
            </p>
            <a href="<?= BASE_URL ?>product" class="btn btn-primary px-4 py-2 mt-2 rounded-pill fw-semibold shadow-sm">
                <i class="bi bi-bag-plus me-1"></i> Khám phá sản phẩm ngay
            </a>
        </div>
    </div>

    <!-- Grid danh sách sản phẩm yêu thích -->
    <?php if ($totalWishlist > 0): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4" id="wishlistGrid">
            <?php foreach ($wishlistItems as $item): ?>
                <?php
                $hasDiscount = !empty($item->discountPrice) && $item->discountPrice > 0 && $item->discountPrice < $item->price;
                $finalPrice = $hasDiscount ? $item->discountPrice : $item->price;
                $discountPercent = $hasDiscount ? round((($item->price - $item->discountPrice) / $item->price) * 100) : 0;
                $productSlug = !empty($item->slug) ? $item->slug : ('detail?id=' . $item->productId);
                $imgSrc = !empty($item->productImage) ? (PRODUCT_IMAGE_URL . $item->productImage) : 'https://placehold.co/400x400?text=SP';
                ?>
                <div class="col wishlist-item-col" id="wishlist-item-<?= $item->productId ?>">
                    <div class="card h-100 modern-product-card border rounded-3 overflow-hidden shadow-sm bg-white position-relative">
                        <!-- Nút Xóa Khỏi Wishlist -->
                        <button type="button" 
                                class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm btn-remove-wishlist z-3"
                                data-product-id="<?= $item->productId ?>"
                                title="Bỏ yêu thích">
                            <i class="bi bi-trash3-fill text-danger"></i>
                        </button>

                        <!-- Badge giảm giá -->
                        <?php if ($hasDiscount && $discountPercent > 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2.5 py-1 fw-bold shadow-sm z-2" style="font-size: 0.72rem;">
                                -<?= $discountPercent ?>%
                            </span>
                        <?php endif; ?>

                        <!-- Ảnh sản phẩm -->
                        <div class="modern-card-img-wrapper position-relative text-center p-3">
                            <a href="<?= BASE_URL ?>product/<?= htmlspecialchars($productSlug) ?>" class="d-block w-100 h-100">
                                <img src="<?= $imgSrc ?>" 
                                     class="modern-card-img img-fluid" 
                                     style="height: 190px; object-fit: contain;" 
                                     alt="<?= htmlspecialchars($item->productName) ?>"
                                     onerror="this.src='https://placehold.co/400x400?text=SP'">
                            </a>
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">
                                    <?= htmlspecialchars($item->brandName ?: ($item->cateName ?: 'Chính hãng')) ?>
                                </small>
                                <h6 class="card-title fw-bold mb-2" style="font-size: 0.92rem;">
                                    <a href="<?= BASE_URL ?>product/<?= htmlspecialchars($productSlug) ?>" class="text-decoration-none text-dark text-truncate d-block" title="<?= htmlspecialchars($item->productName) ?>">
                                        <?= htmlspecialchars($item->productName) ?>
                                    </a>
                                </h6>
                            </div>

                            <div class="mt-2">
                                <div class="d-flex align-items-baseline gap-2 mb-3">
                                    <span class="text-danger fw-bold fs-5 mb-0">
                                        <?= number_format($finalPrice, 0, ',', '.') ?> đ
                                    </span>
                                    <?php if ($hasDiscount): ?>
                                        <span class="text-muted small text-decoration-line-through">
                                            <?= number_format($item->price, 0, ',', '.') ?> đ
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>product/<?= htmlspecialchars($productSlug) ?>" class="btn btn-outline-secondary btn-sm flex-fill py-1.5 rounded-2 fw-semibold" style="font-size: 0.78rem;">
                                        Chi tiết
                                    </a>
                                    <button type="button" class="btn btn-primary btn-sm btn-add-cart flex-fill py-1.5 rounded-2 fw-semibold modern-new-btn-add" data-productid="<?= $item->productId ?>" style="font-size: 0.78rem;">
                                        <i class="bi bi-cart-plus me-1"></i> Thêm giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Xử lý nút xóa sản phẩm khỏi Wishlist
    document.querySelectorAll('.btn-remove-wishlist').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            if (!productId) return;

            const appBaseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '/MiniShop_TranThiNgocYen/';
            const formData = new FormData();
            formData.append('product_id', productId);
            if (typeof CSRF_TOKEN !== 'undefined') {
                formData.append('csrf_token', CSRF_TOKEN);
            }

            fetch(appBaseUrl + 'wishlist/remove', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    // Xóa thẻ sản phẩm với hiệu ứng mờ dần
                    const itemCol = document.getElementById('wishlist-item-' + productId);
                    if (itemCol) {
                        itemCol.style.transition = 'all 0.35s ease';
                        itemCol.style.opacity = '0';
                        itemCol.style.transform = 'scale(0.85)';
                        setTimeout(() => {
                            itemCol.remove();
                            
                            // Cập nhật số lượng
                            const count = data.count || 0;
                            const countEl = document.getElementById('wishlistPageCount');
                            const headerBadge = document.getElementById('wishlistCount');

                            if (countEl) countEl.textContent = count;
                            if (headerBadge) {
                                headerBadge.textContent = count;
                                if (count > 0) {
                                    headerBadge.classList.remove('d-none');
                                } else {
                                    headerBadge.classList.add('d-none');
                                }
                            }

                            // Nếu hết sản phẩm thì hiện Empty State
                            if (count === 0) {
                                const grid = document.getElementById('wishlistGrid');
                                const emptyState = document.getElementById('wishlistEmptyState');
                                if (grid) grid.remove();
                                if (emptyState) emptyState.classList.remove('d-none');
                            }
                        }, 350);
                    }

                    // Thông báo Toast
                    showToast(data.message || 'Đã xóa khỏi danh sách yêu thích!');
                }
            })
            .catch(err => console.error('Lỗi xóa wishlist:', err));
        });
    });

    function showToast(msg) {
        const toastEl = document.getElementById('liveToast');
        const toastMsg = document.getElementById('toastMessage');
        if (toastEl && toastMsg && typeof bootstrap !== 'undefined') {
            toastMsg.textContent = msg;
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }
    }
});
</script>
