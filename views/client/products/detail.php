<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

<div class="container my-4">
    <?php if (empty($product)): ?>
        <div class="alert alert-danger text-center py-5">
            <h4>Sản phẩm không tồn tại hoặc đã bị xóa!</h4>
            <a href="<?= BASE_URL ?>product" class="btn btn-primary mt-3">Quay lại danh sách sản phẩm</a>
        </div>
    <?php else: ?>
        <?php
        // Chuẩn bị danh sách ảnh đầy đủ
        $galleryList = [];
        $galleryList[0] = [
            'file' => $product->image,
            'url' => PRODUCT_IMAGE_URL . $product->image
        ];

        if (!empty($productImages)) {
            foreach ($productImages as $pIdx => $imgObj) {
                $galleryList[$pIdx + 1] = [
                    'file' => $imgObj->image,
                    'url' => PRODUCT_IMAGE_URL . $imgObj->image
                ];
            }
        }

        // Chuẩn bị danh sách biến thể liên kết trực tiếp với từng hình ảnh và mức giá
        $variantItems = [];
        if (!empty($variants)) {
            foreach ($variants as $vIdx => $v) {
                $slideIdx = $vIdx;
                $imgUrl = isset($galleryList[$vIdx]) ? $galleryList[$vIdx]['url'] : (PRODUCT_IMAGE_URL . ($v->image ?: $product->image));
                $imgFile = isset($galleryList[$vIdx]) ? $galleryList[$vIdx]['file'] : ($v->image ?: $product->image);

                // Giá riêng của từng biến thể
                $vBasePrice = ($v->price !== null && $v->price > 0) ? (float)$v->price : ($product->price + ($vIdx * 50000));
                $vDiscPrice = ($v->discountPrice !== null && $v->discountPrice > 0) 
                    ? (float)$v->discountPrice 
                    : (($product->discountPrice > 0) ? ($product->discountPrice + ($vIdx * 50000)) : 0);

                $vFinalPrice = ($vDiscPrice > 0 && $vDiscPrice < $vBasePrice) ? $vDiscPrice : $vBasePrice;
                $hasDisc = ($vDiscPrice > 0 && $vDiscPrice < $vBasePrice);
                $pct = $hasDisc ? round((($vBasePrice - $vDiscPrice) / $vBasePrice) * 100) : 0;

                $variantItems[] = [
                    'id' => $v->id,
                    'name' => $v->variantName ?: ('Phân loại ' . ($vIdx + 1)),
                    'slide_index' => $slideIdx,
                    'image_url' => $imgUrl,
                    'image_file' => $imgFile,
                    'price' => $vBasePrice,
                    'discount_price' => $vDiscPrice,
                    'final_price' => $vFinalPrice,
                    'has_discount' => $hasDisc,
                    'discount_pct' => $pct,
                    'quantity' => $v->quantity > 0 ? $v->quantity : $product->quantity,
                ];
            }
        } elseif (count($galleryList) > 1) {
            foreach ($galleryList as $sIdx => $gItem) {
                $vBasePrice = $product->price + ($sIdx * 50000);
                $vDiscPrice = ($product->discountPrice > 0) ? ($product->discountPrice + ($sIdx * 50000)) : 0;
                $vFinalPrice = ($vDiscPrice > 0 && $vDiscPrice < $vBasePrice) ? $vDiscPrice : $vBasePrice;
                $hasDisc = ($vDiscPrice > 0 && $vDiscPrice < $vBasePrice);
                $pct = $hasDisc ? round((($vBasePrice - $vDiscPrice) / $vBasePrice) * 100) : 0;

                $vName = ($sIdx === 0) ? 'Bản Tiêu chuẩn' : ('Bản ' . ($sIdx === 1 ? 'Nâng cấp' : 'Đặc biệt ' . $sIdx));

                $variantItems[] = [
                    'id' => 0,
                    'name' => $vName,
                    'slide_index' => $sIdx,
                    'image_url' => $gItem['url'],
                    'image_file' => $gItem['file'],
                    'price' => $vBasePrice,
                    'discount_price' => $vDiscPrice,
                    'final_price' => $vFinalPrice,
                    'has_discount' => $hasDisc,
                    'discount_pct' => $pct,
                    'quantity' => $product->quantity,
                ];
            }
        }

        // Biến thể mặc định đầu tiên
        $initialVariant = !empty($variantItems) ? $variantItems[0] : null;
        $initialFinalPrice = $initialVariant ? $initialVariant['final_price'] : ($product->discountPrice > 0 && $product->discountPrice < $product->price ? $product->discountPrice : $product->price);
        $initialBasePrice = $initialVariant ? $initialVariant['price'] : $product->price;
        $initialHasDisc = $initialVariant ? $initialVariant['has_discount'] : ($product->discountPrice > 0 && $product->discountPrice < $product->price);
        $initialPct = $initialVariant ? $initialVariant['discount_pct'] : ($initialHasDisc ? round((($product->price - $product->discountPrice) / $product->price) * 100) : 0);
        $initialName = $initialVariant ? $initialVariant['name'] : 'Mặc định';
        $initialVariantId = $initialVariant ? $initialVariant['id'] : 0;
        ?>

        <!-- BREADCRUMB -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>product" class="text-decoration-none text-muted">Sản phẩm</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars($product->proName) ?></li>
            </ol>
        </nav>

        <!-- KHỐI CHI TIẾT SẢN PHẨM CHÍNH -->
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 bg-white mb-4">
            <div class="row g-4 g-lg-5">
                <!-- 1. CỘT HÌNH ẢNH SẢN PHẨM -->
                <div class="col-md-5">
                    <!-- Ảnh lớn -->
                    <div class="product-slider mb-3 border rounded-3 bg-white overflow-hidden">
                        <?php foreach ($galleryList as $gIdx => $g): ?>
                            <div class="text-center p-2">
                                <img src="<?= $g['url'] ?>" class="img-fluid w-100" style="height: 380px; object-fit: contain;" alt="<?= htmlspecialchars($product->proName) ?>" onerror="this.src='https://placehold.co/400x400?text=SP'">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Thumbnails -->
                    <div class="product-nav">
                        <?php foreach ($galleryList as $gIdx => $g): ?>
                            <div class="px-1" style="cursor: pointer;">
                                <div class="border rounded-2 p-1 bg-white thumb-box <?= $gIdx === 0 ? 'border-primary' : '' ?>">
                                    <img src="<?= $g['url'] ?>" class="w-100 rounded" style="height: 65px; object-fit: cover;" alt="" onerror="this.src='https://placehold.co/70x70?text=SP'">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. CỘT THÔNG TIN VÀ ĐẶT HÀNG -->
                <div class="col-md-7 d-flex flex-column">
                    <!-- Tên sản phẩm -->
                    <h3 class="fw-bold text-dark mb-2"><?= htmlspecialchars($product->proName) ?></h3>

                    <!-- Đánh giá sao & Tình trạng kho -->
                    <div class="d-flex align-items-center gap-3 small text-muted mb-3 pb-2 border-bottom">
                        <div class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="text-dark fw-bold ms-1">5.0</span>
                        </div>
                        <span>|</span>
                        <span class="text-muted"><?= $ratingSummary['total'] ?? 0 ?> Đánh giá</span>
                        <span>|</span>
                        <span id="stock-text-status" class="<?= $product->quantity > 0 ? 'text-success fw-semibold' : 'text-danger fw-semibold' ?>">
                            <?= $product->quantity > 0 ? ('Còn ' . $product->quantity . ' sản phẩm') : 'Hết hàng' ?>
                        </span>
                    </div>

                    <!-- KHỐI GIÁ BÁN -->
                    <div class="p-3 bg-light rounded-3 mb-4" id="price-display-box">
                        <div class="d-flex align-items-baseline flex-wrap gap-2">
                            <span class="text-danger fs-2 fw-bold" id="current-price-val"><?= number_format($initialFinalPrice, 0, ',', '.') ?> đ</span>
                            <del class="text-muted fs-5 <?= $initialHasDisc ? '' : 'd-none' ?>" id="original-price-val"><?= number_format($initialBasePrice, 0, ',', '.') ?> đ</del>
                            <span class="badge bg-danger fs-6 align-middle <?= $initialHasDisc ? '' : 'd-none' ?>" id="discount-pct-badge">-<?= $initialPct ?>%</span>
                        </div>
                    </div>

                    <!-- DANH SÁCH BIẾN THỂ -->
                    <?php if (!empty($variantItems)): ?>
                        <div class="mb-4">
                            <div class="fw-bold text-dark small mb-2">Phân loại:</div>
                            <div class="d-flex flex-wrap gap-2" id="variant-selector-group">
                                <?php foreach ($variantItems as $idx => $vItem): ?>
                                    <button type="button" 
                                            class="btn variant-option-btn text-start p-2 d-flex align-items-center gap-2 <?= $idx === 0 ? 'btn-primary active' : 'btn-outline-secondary text-dark bg-light' ?>"
                                            data-variant-id="<?= $vItem['id'] ?>"
                                            data-variant-name="<?= htmlspecialchars($vItem['name']) ?>"
                                            data-slide-index="<?= $vItem['slide_index'] ?>"
                                            data-price="<?= $vItem['price'] ?>"
                                            data-discount-price="<?= $vItem['discount_price'] ?>"
                                            data-final-price="<?= $vItem['final_price'] ?>"
                                            data-has-discount="<?= $vItem['has_discount'] ? '1' : '0' ?>"
                                            data-discount-pct="<?= $vItem['discount_pct'] ?>"
                                            data-quantity="<?= $vItem['quantity'] ?>"
                                            style="border-radius: 8px; font-size: 0.88rem; transition: all 0.2s ease;">
                                        <img src="<?= $vItem['image_url'] ?>" class="rounded border" width="36" height="36" style="object-fit: cover;" alt="">
                                        <div>
                                            <div class="fw-semibold text-truncate" style="max-width: 140px;"><?= htmlspecialchars($vItem['name']) ?></div>
                                            <div class="small fw-bold variant-btn-price <?= $idx === 0 ? 'text-white' : 'text-danger' ?>">
                                                <?= number_format($vItem['final_price'], 0, ',', '.') ?> đ
                                            </div>
                                        </div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- FORM ĐẶT HÀNG & CHỌN SỐ LƯỢNG -->
                    <form action="<?= BASE_URL ?>cart/add" method="POST" class="mb-4" id="productDetailCartForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $product->id ?>">
                        <input type="hidden" name="variant_id" id="hidden_variant_id" value="<?= $initialVariantId ?>">
                        <input type="hidden" name="variant_name" id="hidden_variant_name" value="<?= htmlspecialchars($initialName) ?>">
                        
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="fw-bold text-dark small">Số lượng:</span>
                            <div class="input-group" style="width: 130px;">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="decrease-btn">-</button>
                                <input type="number" name="quantity" id="quantity-input" class="form-control form-control-sm text-center fw-bold border-secondary" value="1" min="1" max="<?= $product->quantity > 0 ? $product->quantity : 1 ?>" <?= $product->quantity <= 0 ? 'disabled' : '' ?>>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="increase-btn">+</button>
                            </div>
                        </div>

                        <?php
                        $detailWlDAO = new \DAO\WishlistDAO();
                        $detailUId = isset($_SESSION['client_user']['id']) ? (int)$_SESSION['client_user']['id'] : null;
                        $detailFavIds = $detailWlDAO->getFavoriteProductIds($detailUId, session_id());
                        $isDetailFav = in_array((int)$product->id, $detailFavIds);
                        ?>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <?php if ($product->quantity > 0): ?>
                                <button type="submit" class="btn btn-primary btn-lg px-4 fw-semibold rounded-3 shadow-sm" id="btn-submit-cart">
                                    <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-lg px-4 rounded-3 fw-semibold" id="btn-submit-cart" disabled>Hết hàng</button>
                            <?php endif; ?>
                            
                            <!-- Nút Yêu Thích -->
                            <button type="button" 
                                    class="btn btn-outline-danger btn-lg px-3 rounded-3 btn-wishlist-toggle <?= $isDetailFav ? 'active' : '' ?>" 
                                    data-product-id="<?= $product->id ?>" 
                                    title="<?= $isDetailFav ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' ?>"
                                    style="width: 50px; height: 48px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="bi <?= $isDetailFav ? 'bi-heart-fill' : 'bi-heart' ?> fs-5"></i>
                            </button>

                            <a href="<?= BASE_URL ?>product" class="btn btn-outline-secondary btn-lg px-4 rounded-3">
                                Tiếp tục xem
                            </a>
                        </div>
                    </form>

                    <!-- CAM KẾT DỊCH VỤ -->
                    <div class="mt-auto pt-3 border-top d-flex flex-wrap gap-4 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-primary fs-5"></i>
                            <span>100% Chính hãng</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-truck text-primary fs-5"></i>
                            <span>Giao hàng toàn quốc</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-repeat text-primary fs-5"></i>
                            <span>Đổi trả trong 7 ngày</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MÔ TẢ SẢN PHẨM -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">Mô tả sản phẩm</h5>
            <div class="text-muted" style="line-height: 1.8;">
                <?= nl2br(htmlspecialchars($product->description)) ?>
            </div>
        </div>

        <!-- SẢN PHẨM LIÊN QUAN -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold mb-4 text-dark border-bottom pb-2">Sản phẩm liên quan</h5>
                <div class="row g-3">
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="col-6 col-md-3">
                            <?php 
                                $product = $related; 
                                require __DIR__ . '/../layouts/product-card.php'; 
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- ĐÁNH GIÁ SẢN PHẨM -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h5 class="fw-bold mb-0 text-dark">Đánh giá sản phẩm</h5>
                <span class="badge bg-light text-muted border px-3 py-2 small">
                    Đánh giá từ người mua thực tế
                </span>
            </div>

            <!-- Tóm tắt sao -->
            <div class="p-3 bg-light rounded-3 mb-4">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-3 text-center border-end-md py-2">
                        <div class="display-5 fw-bold text-danger mb-0">
                            <?= number_format($ratingSummary['average'] ?? 5.0, 1) ?>
                        </div>
                        <div class="text-warning my-1">
                            <?php 
                            $avgStars = round($ratingSummary['average'] ?? 5.0);
                            for ($s = 1; $s <= 5; $s++): ?>
                                <i class="bi <?= $s <= $avgStars ? 'bi-star-fill' : 'bi-star' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="text-muted small">
                            (<?= $ratingSummary['total'] ?? 0 ?> đánh giá)
                        </div>
                    </div>
                    <div class="col-md-9 ps-md-4 pt-3 pt-md-0">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-danger px-3 py-2">Tất cả (<?= $ratingSummary['total'] ?? 0 ?>)</span>
                            <span class="badge bg-white text-dark border px-3 py-2">5 Sao (<?= $ratingSummary['counts'][5] ?? 0 ?>)</span>
                            <span class="badge bg-white text-dark border px-3 py-2">4 Sao (<?= $ratingSummary['counts'][4] ?? 0 ?>)</span>
                            <span class="badge bg-white text-dark border px-3 py-2">3 Sao (<?= $ratingSummary['counts'][3] ?? 0 ?>)</span>
                            <span class="badge bg-white text-dark border px-3 py-2">2 Sao (<?= $ratingSummary['counts'][2] ?? 0 ?>)</span>
                            <span class="badge bg-white text-dark border px-3 py-2">1 Sao (<?= $ratingSummary['counts'][1] ?? 0 ?>)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách đánh giá -->
            <?php if (empty($productReviews)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots" style="font-size: 2.5rem;"></i>
                    <p class="mt-2 mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($productReviews as $rev): ?>
                        <div class="border-bottom pb-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <span class="fw-bold text-dark me-2"><?= htmlspecialchars($rev->fullname ?: 'Khách hàng') ?></span>
                                    <div class="text-warning small d-inline-block">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi <?= $i <= $rev->rating ? 'bi-star-fill' : 'bi-star text-muted' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?= $rev->createdAt ? date('d/m/Y', strtotime($rev->createdAt)) : '' ?>
                                </small>
                            </div>
                            <p class="mb-0 text-dark small" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($rev->comment)) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script>
    $(document).ready(function(){
        // Khởi tạo slider ảnh lớn
        $('.product-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.product-nav'
        });

        // Khởi tạo thumbnails ảnh phụ
        $('.product-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-slider',
            focusOnSelect: true,
            arrows: true,
            responsive: [
                { breakpoint: 768, settings: { slidesToShow: 3 } },
                { breakpoint: 480, settings: { slidesToShow: 2 } }
            ]
        });

        // Hàm chọn biến thể và cập nhật giao diện 2 chiều
        function selectVariantBySlideIndex(slideIdx) {
            const $btn = $('.variant-option-btn[data-slide-index="' + slideIdx + '"]');
            if ($btn.length === 0) return;

            // Đổi class nút biến thể
            $('.variant-option-btn').removeClass('btn-primary active text-white').addClass('btn-outline-secondary text-dark bg-light');
            $('.variant-option-btn .variant-btn-price').removeClass('text-white').addClass('text-danger');

            $btn.removeClass('btn-outline-secondary text-dark bg-light').addClass('btn-primary active text-white');
            $btn.find('.variant-btn-price').removeClass('text-danger').addClass('text-white');

            // Cập nhật thumbnail viền
            $('.product-nav .thumb-box').removeClass('border-primary');
            $('.product-nav .slick-slide[data-slick-index="' + slideIdx + '"] .thumb-box').addClass('border-primary');

            // Lấy dữ liệu biến thể
            const variantId = $btn.data('variant-id');
            const variantName = $btn.data('variant-name');
            const price = parseFloat($btn.data('price')) || 0;
            const discountPrice = parseFloat($btn.data('discount-price')) || 0;
            const finalPrice = parseFloat($btn.data('final-price')) || price;
            const hasDiscount = $btn.data('has-discount') == '1';
            const discountPct = parseInt($btn.data('discount-pct')) || 0;
            const quantity = parseInt($btn.data('quantity')) || 0;

            // Cập nhật giá bán hiển thị
            const formatter = new Intl.NumberFormat('vi-VN');
            $('#current-price-val').text(formatter.format(finalPrice) + ' đ');
            
            if (hasDiscount && discountPrice > 0 && discountPrice < price) {
                $('#original-price-val').text(formatter.format(price) + ' đ').removeClass('d-none');
                $('#discount-pct-badge').text('-' + discountPct + '%').removeClass('d-none');
            } else {
                $('#original-price-val').addClass('d-none');
                $('#discount-pct-badge').addClass('d-none');
            }

            // Cập nhật tồn kho
            if (quantity > 0) {
                $('#stock-text-status').removeClass('text-danger').addClass('text-success fw-semibold').text('Còn ' + quantity + ' sản phẩm');
                $('#quantity-input').prop('disabled', false).attr('max', quantity);
                $('#btn-submit-cart').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary').html('<i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng');
            } else {
                $('#stock-text-status').removeClass('text-success').addClass('text-danger fw-semibold').text('Hết hàng');
                $('#quantity-input').prop('disabled', true).val(1);
                $('#btn-submit-cart').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary').text('Hết hàng');
            }

            // Cập nhật form hidden fields
            $('#hidden_variant_id').val(variantId);
            $('#hidden_variant_name').val(variantName);
        }

        // 1. Khi click nút Biến thể -> Chuyển slider ảnh sang slide tương ứng & cập nhật giá
        $('.variant-option-btn').click(function(){
            const slideIdx = parseInt($(this).data('slide-index')) || 0;
            if ($('.product-slider').hasClass('slick-initialized')) {
                $('.product-slider').slick('slickGoTo', slideIdx);
            }
            selectVariantBySlideIndex(slideIdx);
        });

        // 2. Khi bấm vào thumbnail ảnh phụ hoặc lướt slider -> Tự động chọn biến thể và cập nhật giá tương ứng
        $('.product-slider').on('afterChange', function(event, slick, currentSlide){
            selectVariantBySlideIndex(currentSlide);
        });

        // 3. Tăng / Giảm số lượng
        $('#increase-btn').click(function(){
            let input = $('#quantity-input');
            let max = parseInt(input.attr('max')) || 999;
            let val = parseInt(input.val()) || 1;
            if(val < max) {
                input.val(val + 1);
            }
        });

        $('#decrease-btn').click(function(){
            let input = $('#quantity-input');
            let val = parseInt(input.val()) || 1;
            if(val > 1) {
                input.val(val - 1);
            }
        });
    });
</script>