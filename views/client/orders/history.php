<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Lịch sử đơn hàng</li>
        </ol>
    </nav>

    <!-- Header Tiêu đề -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-receipt me-2 text-primary"></i>Lịch sử đơn hàng
            </h3>
            <p class="text-muted small mb-0">Theo dõi tiến trình giao hàng và gửi đánh giá sản phẩm sau khi nhận hàng</p>
        </div>
        <a href="<?= BASE_URL ?>product" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    <!-- Thông báo Alert -->
    <?php if (!empty($_SESSION['order_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($_SESSION['order_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['order_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['order_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($_SESSION['order_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['order_error']); ?>
    <?php endif; ?>

    <!-- Danh sách đơn hàng -->
    <?php if (empty($orders)): ?>
        <div class="card border-0 rounded-4 text-center py-5 shadow-sm bg-white">
            <div class="card-body py-5">
                <i class="bi bi-bag-x text-muted" style="font-size: 4.5rem;"></i>
                <h5 class="fw-bold mt-3 text-dark">Bạn chưa có đơn hàng nào</h5>
                <p class="text-muted small">Hãy khám phá các sản phẩm chính hãng và chương trình ưu đãi hấp dẫn tại MiniShop!</p>
                <a href="<?= BASE_URL ?>product" class="btn btn-primary px-4 py-2 mt-2 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-cart-plus me-1"></i> Khám phá sản phẩm ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach ($orders as $order): ?>
                <?php
                $stInfo = $statusList[(int)$order['status']] ?? ['label' => 'Không xác định', 'class' => 'bg-secondary text-white', 'icon' => 'bi-question'];
                $isDelivered = ((int)$order['status'] === 3);
                $isCancelled = ((int)$order['status'] === 4);
                ?>
                <div class="card border rounded-4 shadow-sm overflow-hidden bg-white">
                    <!-- Order Header -->
                    <div class="card-header bg-light bg-opacity-75 border-bottom d-flex flex-wrap justify-content-between align-items-center py-3 px-3 px-md-4">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-bold text-dark">
                                Đơn hàng: <span class="text-primary">#<?= htmlspecialchars($order['order_code']) ?></span>
                            </span>
                            <span class="text-muted small">|</span>
                            <span class="text-muted small">
                                <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </span>
                        </div>
                        <div class="mt-2 mt-sm-0 d-flex align-items-center gap-2">
                            <span class="badge <?= $stInfo['class'] ?> py-2 px-3 rounded-pill fw-semibold" style="font-size: 0.82rem;">
                                <i class="bi <?= $stInfo['icon'] ?> me-1"></i> <?= $stInfo['label'] ?>
                            </span>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-muted">
                                    <tr>
                                        <th class="ps-3 ps-md-4 py-2.5" style="min-width: 280px;">Sản phẩm</th>
                                        <th class="text-center py-2.5" style="width: 140px;">Đơn giá</th>
                                        <th class="text-center py-2.5" style="width: 90px;">Số lượng</th>
                                        <th class="text-end py-2.5" style="width: 140px;">Thành tiền</th>
                                        <th class="text-center py-2.5 pe-3 pe-md-4" style="width: 190px;">Đánh giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <?php 
                                        $productSlug = !empty($item['slug']) ? $item['slug'] : ('detail?id=' . $item['product_id']);
                                        $imgUrl = !empty($item['image']) ? (PRODUCT_IMAGE_URL . $item['image']) : 'https://placehold.co/100x100?text=SP';
                                        $itemSubtotal = (float)$item['price'] * (int)$item['quantity'];
                                        ?>
                                        <tr>
                                            <td class="ps-3 ps-md-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <a href="<?= BASE_URL ?>product/<?= htmlspecialchars($productSlug) ?>" class="flex-shrink-0 d-block">
                                                        <div class="rounded-3 border overflow-hidden bg-white p-1 shadow-sm" style="width: 70px; height: 70px;">
                                                            <img src="<?= $imgUrl ?>" 
                                                                 alt="<?= htmlspecialchars($item['proname'] ?? '') ?>" 
                                                                 class="w-100 h-100 object-fit-contain"
                                                                 onerror="this.src='https://placehold.co/100x100?text=SP'">
                                                        </div>
                                                    </a>
                                                    <div class="flex-grow-1">
                                                        <a href="<?= BASE_URL ?>product/<?= htmlspecialchars($productSlug) ?>" class="fw-bold text-decoration-none text-dark d-block mb-1 hover-primary" style="font-size: 0.92rem;">
                                                            <?= htmlspecialchars($item['proname'] ?? 'Sản phẩm') ?>
                                                        </a>
                                                        <div class="text-muted small">
                                                            <span>Mã SP: #<?= $item['product_id'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-semibold text-dark">
                                                <?= number_format($item['price'], 0, ',', '.') ?> đ
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold">x<?= $item['quantity'] ?></span>
                                            </td>
                                            <td class="text-end fw-bold text-dark">
                                                <?= number_format($itemSubtotal, 0, ',', '.') ?> đ
                                            </td>
                                            <td class="text-center pe-3 pe-md-4">
                                                <?php if ($isDelivered): ?>
                                                    <?php if ($item['is_reviewed']): ?>
                                                        <div class="d-inline-flex flex-column align-items-center">
                                                            <span class="badge bg-warning-subtle text-dark border border-warning px-3 py-2 rounded-pill fw-semibold small">
                                                                <i class="bi bi-star-fill text-warning me-1"></i> Đã đánh giá (<?= $item['review_data']['rating'] ?? 5 ?> ⭐)
                                                            </span>
                                                        </div>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-1.5 rounded-pill btn-open-review shadow-sm"
                                                            data-order-id="<?= $order['id'] ?>"
                                                            data-order-code="<?= htmlspecialchars($order['order_code']) ?>"
                                                            data-product-id="<?= $item['product_id'] ?>"
                                                            data-product-name="<?= htmlspecialchars($item['proname']) ?>"
                                                            data-product-image="<?= $imgUrl ?>">
                                                            <i class="bi bi-star-fill me-1"></i> Đánh giá
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($isCancelled): ?>
                                                    <span class="text-muted small fst-italic">Đơn đã hủy</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-2.5 py-1.5 small fw-normal">
                                                        <i class="bi bi-hourglass-split me-1"></i> Mở khi nhận hàng
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Footer -->
                    <div class="card-footer bg-white border-top d-flex flex-wrap justify-content-between align-items-center py-3 px-3 px-md-4 gap-2">
                        <div class="small text-muted d-flex flex-wrap align-items-center gap-3">
                            <span>Thanh toán: <strong class="text-dark"><?= htmlspecialchars($order['payment_method'] ?? 'COD') ?></strong></span>
                            <span>|</span>
                            <span>Phí vận chuyển: 
                                <?php if ((float)$order['shipping_fee'] > 0): ?>
                                    <strong class="text-dark"><?= number_format($order['shipping_fee'], 0, ',', '.') ?> đ</strong>
                                <?php else: ?>
                                    <strong class="text-success">Miễn phí (Freeship)</strong>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Tổng thanh toán:</span>
                            <span class="fs-4 fw-bold text-danger"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Đánh Giá Sản Phẩm (Shopee Style) -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow overflow-hidden">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="reviewModalLabel">
                    <i class="bi bi-star-half me-1"></i> Đánh giá sản phẩm
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>order/review" method="POST" id="reviewForm">
                <?= csrf_field() ?>
                <input type="hidden" name="order_id" id="modalOrderId">
                <input type="hidden" name="product_id" id="modalProductId">
                <input type="hidden" name="rating" id="modalRatingValue" value="5">

                <div class="modal-body p-4">
                    <!-- Product Info Preview -->
                    <div class="d-flex align-items-center mb-4 p-2 bg-light rounded-3 border">
                        <img src="" id="modalProductImg" class="rounded-3 border me-3 object-fit-contain bg-white" style="width: 65px; height: 65px;">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark" id="modalProductName"></h6>
                            <small class="text-muted">Đơn hàng: <span id="modalOrderCode" class="fw-semibold text-primary"></span></small>
                        </div>
                    </div>

                    <!-- Star Rating Selector -->
                    <div class="text-center mb-4">
                        <label class="form-label fw-bold d-block mb-2">Chất lượng sản phẩm:</label>
                        <div class="d-inline-flex gap-2" id="starContainer">
                            <i class="bi bi-star-fill text-warning fs-2 cursor-pointer star-item" data-rating="1"></i>
                            <i class="bi bi-star-fill text-warning fs-2 cursor-pointer star-item" data-rating="2"></i>
                            <i class="bi bi-star-fill text-warning fs-2 cursor-pointer star-item" data-rating="3"></i>
                            <i class="bi bi-star-fill text-warning fs-2 cursor-pointer star-item" data-rating="4"></i>
                            <i class="bi bi-star-fill text-warning fs-2 cursor-pointer star-item" data-rating="5"></i>
                        </div>
                        <div class="mt-2 fw-semibold text-warning" id="ratingText">Tuyệt vời (5/5)</div>
                    </div>

                    <!-- Review Comment -->
                    <div class="mb-3">
                        <label for="reviewComment" class="form-label fw-bold">Nhận xét của bạn <span class="text-danger">*</span>:</label>
                        <textarea name="comment" id="reviewComment" class="form-control rounded-3" rows="4" placeholder="Hãy chia sẻ trải nghiệm thực tế về sản phẩm, đóng gói, chất lượng..." required minlength="5"></textarea>
                    </div>

                    <!-- Shopee Notice -->
                    <div class="alert alert-info py-2 px-3 small mb-0 rounded-3">
                        <i class="bi bi-shield-lock-fill me-1"></i>
                        <strong>Lưu ý:</strong> Đánh giá này sẽ được hiển thị công khai trên trang sản phẩm kèm huy hiệu <strong>"Đã mua hàng"</strong> và <strong>không thể chỉnh sửa</strong> sau khi gửi.
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 rounded-pill">
                        <i class="bi bi-send-fill me-1"></i> Gửi đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
    transition: transform 0.15s ease-in-out;
}
.cursor-pointer:hover {
    transform: scale(1.2);
}
.hover-primary:hover {
    color: #0284c7 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reviewModalEl = document.getElementById('reviewModal');
    const reviewModal = new bootstrap.Modal(reviewModalEl);

    const modalOrderId = document.getElementById('modalOrderId');
    const modalProductId = document.getElementById('modalProductId');
    const modalRatingValue = document.getElementById('modalRatingValue');
    const modalProductName = document.getElementById('modalProductName');
    const modalOrderCode = document.getElementById('modalOrderCode');
    const modalProductImg = document.getElementById('modalProductImg');
    const ratingText = document.getElementById('ratingText');
    const starItems = document.querySelectorAll('.star-item');

    const ratingDescriptions = {
        1: "Rất tệ (1/5)",
        2: "Không hài lòng (2/5)",
        3: "Bình thường (3/5)",
        4: "Hài lòng (4/5)",
        5: "Tuyệt vời (5/5)"
    };

    // Open Modal
    document.querySelectorAll('.btn-open-review').forEach(btn => {
        btn.addEventListener('click', function () {
            modalOrderId.value = this.dataset.orderId;
            modalProductId.value = this.dataset.productId;
            modalProductName.textContent = this.dataset.productName;
            modalOrderCode.textContent = '#' + this.dataset.orderCode;
            modalProductImg.src = this.dataset.productImage;
            
            // Reset to 5 stars
            setStars(5);
            document.getElementById('reviewComment').value = '';

            reviewModal.show();
        });
    });

    // Star Click Handlers
    starItems.forEach(star => {
        star.addEventListener('click', function () {
            const rating = parseInt(this.dataset.rating);
            setStars(rating);
        });
    });

    function setStars(rating) {
        modalRatingValue.value = rating;
        ratingText.textContent = ratingDescriptions[rating] || (rating + "/5");

        starItems.forEach(star => {
            const r = parseInt(star.dataset.rating);
            if (r <= rating) {
                star.classList.remove('bi-star', 'text-muted');
                star.classList.add('bi-star-fill', 'text-warning');
            } else {
                star.classList.remove('bi-star-fill', 'text-warning');
                star.classList.add('bi-star', 'text-muted');
            }
        });
    }
});
</script>
