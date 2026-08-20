<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

<div class="container my-4">
    <?php if (empty($product)): ?>
        <div class="alert alert-danger text-center py-5">
            <h4>Sản phẩm không tồn tại hoặc đã bị xóa!</h4>
            <a href="<?= BASE_URL ?>product" class="btn btn-primary mt-3">Quay lại danh sách sản phẩm</a>
        </div>
    <?php else: ?>
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>product">Sản phẩm</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $product->proName ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-5">
                <div class="product-slider mb-3">
                    <div><img src="<?= PRODUCT_IMAGE_URL . $product->image ?>" class="img-fluid rounded border w-100" style="height: 350px; object-fit: contain;"></div>
                    <?php foreach ($productImages as $img): ?>
                        <div><img src="<?= PRODUCT_IMAGE_URL . $img->image ?>" class="img-fluid rounded border w-100" style="height: 350px; object-fit: contain;"></div>
                    <?php endforeach; ?>
                </div>

                <div class="product-nav">
                    <div class="px-1" style="cursor: pointer;"><img src="<?= PRODUCT_IMAGE_URL . $product->image ?>" class="img-thumbnail w-100" style="height: 70px; object-fit: cover;"></div>
                    <?php foreach ($productImages as $img): ?>
                        <div class="px-1" style="cursor: pointer;"><img src="<?= PRODUCT_IMAGE_URL . $img->image ?>" class="img-thumbnail w-100" style="height: 70px; object-fit: cover;"></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-7">
                <h2><?= $product->proName ?></h2>
                <p class="text-muted mb-2">Danh mục: <strong><?= $product->cateName ?></strong> | Thương hiệu: <strong><?= $product->brandName ?></strong></p>
                
                <div class="my-3">
                    <?php if ($product->discountPrice > 0 && $product->discountPrice < $product->price): ?>
                        <del class="text-muted fs-5"><?= number_format($product->price) ?> đ</del>
                        <span class="text-danger fs-3 fw-bold ms-2"><?= number_format($product->discountPrice) ?> đ</span>
                    <?php else: ?>
                        <span class="text-danger fs-3 fw-bold"><?= number_format($product->price) ?> đ</span>
                    <?php endif; ?>
                </div>

                <p class="mb-3"><strong>Tình trạng:</strong> <?= $product->quantity > 0 ? '<span class="text-success">Còn hàng (' . $product->quantity . ' sản phẩm)</span>' : '<span class="text-danger">Hết hàng</span>' ?></p>
                
                <!-- FORM THÊM VÀO GIỎ HÀNG VÀ CHỌN SỐ LƯỢNG -->
                <form action="<?= BASE_URL ?>cart/add" method="POST" class="mb-4">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    
                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-bold">Số lượng:</label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" id="decrease-btn">-</button>
                                <input type="number" name="quantity" id="quantity-input" class="form-control text-center" value="1" min="1" max="<?= $product->quantity > 0 ? $product->quantity : 1 ?>" <?= $product->quantity <= 0 ? 'disabled' : '' ?>>
                                <button type="button" class="btn btn-outline-secondary" id="increase-btn">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <?php if ($product->quantity > 0): ?>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-lg px-4" disabled>Hết hàng</button>
                        <?php endif; ?>
                        
                        <a href="<?= BASE_URL ?>product" class="btn btn-outline-secondary btn-lg">Tiếp tục mua sắm</a>
                    </div>
                </form>

                <div class="mb-4">
                    <h5>Mô tả sản phẩm:</h5>
                    <p><?= nl2br($product->description) ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($relatedProducts)): ?>
            <hr class="my-5">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
            <div class="row">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="col-md-3 mb-4">
                        <?php 
                            // Gán tạm biến để dùng lại product-card nếu cần
                            $product = $related; 
                            require __DIR__ . '/../layouts/product-card.php'; 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <hr class="my-5">
        <div class="row">
            <div class="col-md-8">
                <h3 class="mb-4">Đánh giá sản phẩm</h3>

                <?php if (empty($productReviews)): ?>
                    <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên đánh giá!</p>
                <?php else: ?>
                    <div class="list-group mb-4">
                        <?php foreach ($productReviews as $rev): ?>
                            <div class="list-group-item p-3 mb-3 border rounded shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold text-primary"><?= htmlspecialchars($rev->fullname) ?></h6>
                                    <small class="text-muted"><?= $rev->created_at ?></small>
                                </div>
                                <div class="text-warning mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= $rev->rating ? '' : 'text-secondary' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($rev->comment)) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="card bg-light p-4 border-0 shadow-sm">
                    <h5 class="mb-3">Viết đánh giá của bạn</h5>
                    <form action="" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="product_id" value="<?= $product->id ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Họ và tên:</label>
                            <input type="text" name="fullname" class="form-control" required placeholder="Nhập tên của bạn...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mức độ hài lòng (Số sao):</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">5 sao - Tuyệt vời</option>
                                <option value="4">4 sao - Hài lòng</option>
                                <option value="3">3 sao - Bình thường</option>
                                <option value="2">2 sao - Tạm được</option>
                                <option value="1">1 sao - Không tệ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nội dung đánh giá:</label>
                            <textarea name="comment" rows="3" class="form-control" required placeholder="Sản phẩm dùng thế nào, bạn có thích không?"></textarea>
                        </div>

                        <button type="submit" name="submit_review" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script>
    $(document).ready(function(){
        // Slider cho ảnh sản phẩm
        $('.product-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.product-nav'
        });
        $('.product-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-slider',
            focusOnSelect: true,
            arrows: true
        });

        // Xử lý nút tăng giảm số lượng
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