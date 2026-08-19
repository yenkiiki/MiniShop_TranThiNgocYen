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