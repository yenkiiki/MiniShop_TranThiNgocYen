<?php if (isset($_GET['keyword'])): ?>
    <div class="container my-5">
        <h2 class="mb-4">Kết quả tìm kiếm cho từ khóa: <span class="text-primary">"<?= htmlspecialchars($keyword) ?>"</span></h2>
        <?php if (empty($keyword)): ?>
            <div class="alert alert-warning text-center py-4">
                <h4>Vui lòng nhập từ khóa để tìm kiếm sản phẩm!</h4>
            </div>
        <?php elseif (empty($products)): ?>
            <div class="alert alert-danger text-center py-4">
                <h4>Không tìm thấy sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>"!</h4>
            </div>
        <?php else: ?>
            <p class="text-muted mb-4">Tìm thấy <strong><?= count($products) ?></strong> sản phẩm.</p>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <?php require __DIR__ . '/../layouts/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- ĐOẠN CODE TRANG CHỦ CỦA BẠN SẼ NẰM Ở ĐÂY -->
    <div class="text-center py-5">
        <h1 class="display-4 fw-bold">Chào mừng đến với MINISHOP</h1>
        <p class="lead text-muted mb-4">Website bán hàng trực tuyến uy tín và chất lượng hàng đầu</p>
        <a href="<?= BASE_URL ?>product" class="btn btn-primary btn-lg">
            <i class="bi bi-bag-check me-2"></i> Xem sản phẩm
        </a>
    </div>

    <div class="container my-4">
        <h2 class="mb-4">Danh mục sản phẩm</h2>
        <div class="row">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 text-center">
                            <?php if (!empty($category->image)): ?>
                                <img src="<?= $category->image ?>" class="card-img-top" alt="<?= $category->cateName ?>" style="height: 150px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= $category->cateName ?></h5>
                                <a href="<?= BASE_URL ?>product?category_id=<?= $category->id ?>" class="btn btn-outline-primary mt-auto">Xem danh mục</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Không có danh mục nào.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container my-4">
        <h2 class="mb-4">Sản phẩm giảm giá</h2>
        <div class="row">
            <?php if (!empty($discountProducts)): ?>
                <?php foreach ($discountProducts as $product): ?>
                    <div class="col-md-3 mb-4">
                        <?php include __DIR__ . '/../layouts/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Không có sản phẩm giảm giá nào.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container my-4">
        <h2 class="mb-4">Sản phẩm mới</h2>
        <div class="row">
            <?php if (!empty($newProducts)): ?>
                <?php foreach ($newProducts as $product): ?>
                    <div class="col-md-3 mb-4">
                        <?php include __DIR__ . '/../layouts/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Không có sản phẩm mới nào.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>