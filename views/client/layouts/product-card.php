<div class="card h-100">
    <img src="<?= PRODUCT_IMAGE_URL . $product->image ?>" class="card-img-top" alt="<?= $product->proName ?>" style="height: 220px; object-fit: contain;">
    <div class="card-body">
        <h5><?= $product->proName ?></h5>
        <del><?= number_format($product->price) ?> đ</del>
        <p class="text-danger fw-bold">
            <?= number_format($product->discountPrice) ?> đ
        </p>
        <div class="d-flex justify-content-end gap-2">
            <a href="product/<?= $product->slug ?>" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết">
                <i class="bi bi-eye"></i>
            </a>
            <button type="button" class="btn btn-primary btn-sm btn-add-cart" data-productid="<?= $product->id ?>" title="Thêm vào giỏ hàng">
                <i class="bi bi-cart-plus"></i>
            </button>
        </div>
    </div>
</div>