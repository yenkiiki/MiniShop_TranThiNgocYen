<div class="container my-4">
    <h2 class="mb-4"><i class="bi bi-cart3"></i> <?= $title ?? "Giỏ hàng của bạn" ?></h2>

    <?php if (empty($cart)): ?>
        <div class="alert alert-warning text-center py-5">
            <h4 class="alert-heading mb-3">Giỏ hàng đang trống!</h4>
            <p class="text-muted">Má chưa chọn sản phẩm nào vào giỏ hàng cả.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered align-middle text-center mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 100px;">Hình ảnh</th>
                        <th class="text-start">Tên sản phẩm</th>
                        <th style="width: 150px;">Đơn giá</th>
                        <th style="width: 160px;">Số lượng</th>
                        <th style="width: 150px;">Thành tiền</th>
                        <th style="width: 100px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $id => $item): ?>
                        <tr id="row-<?= $id ?>">
                            <td>
                                <img src="<?= BASE_URL ?>uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['productname']) ?>" 
                                     class="img-thumbnail" 
                                     style="width: 70px; height: 70px; object-fit: cover;"
                                     onerror="this.src='https://placehold.co/70x70?text=SP'">
                            </td>
                            <td class="text-start fw-semibold">
                                <a href="<?= BASE_URL ?>product/detail?id=<?= $item['productid'] ?>" class="text-decoration-none text-dark d-block">
                                    <?= htmlspecialchars($item['productname']) ?>
                                </a>
                                <?php if (!empty($item['variant_name'])): ?>
                                    <div class="mt-1">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                            <i class="bi bi-tag-fill me-1"></i>Phiên bản: <?= htmlspecialchars($item['variant_name']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-danger fw-bold">
                                <?= number_format($item['price'], 0, ',', '.') ?> đ
                            </td>
                            <td>
                                <div class="input-group input-group-sm justify-content-center">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCart('<?= $id ?>', <?= $item['quantity'] - 1 ?>)">-</button>
                                    <input type="number" 
                                           name="quantity[<?= $id ?>]" 
                                           value="<?= $item['quantity'] ?>" 
                                           min="1" 
                                           class="form-control form-control-sm text-center px-1" 
                                           style="max-width: 50px;"
                                           onchange="updateCart('<?= $id ?>', parseInt(this.value))">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCart('<?= $id ?>', <?= $item['quantity'] + 1 ?>)">+</button>
                                </div>
                            </td>
                            <td class="text-success fw-bold" id="subtotal-<?= $id ?>">
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCart('<?= $id ?>')">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card mt-4 shadow-sm border-0 bg-light">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <h4 class="mb-0 text-dark">
                        Tổng tiền: <span class="text-danger fw-bold" id="cartTotal"><?= number_format($total ?? 0, 0, ',', '.') ?> đ</span>
                    </h4>
                    <a href="<?= BASE_URL ?>cart/checkout" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-bag-check"></i> Đặt hàng
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>