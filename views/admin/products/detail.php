<?php
$pageTitle = "Chi tiết sản phẩm - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/product">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Chi tiết sản phẩm</li>
    </ol>

    <div class="card mb-4 shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <div>
                <i class="fas fa-info-circle text-primary me-1"></i> Chi tiết sản phẩm: <strong class="text-dark"><?= htmlspecialchars($product->proName ?? '') ?></strong>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>admin/product/edit?id=<?= $product->id ?>" class="btn btn-warning text-white btn-sm px-3">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="<?= BASE_URL ?>admin/product" class="btn btn-secondary btn-sm px-3">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- CỘT HÌNH ẢNH CHÍNH & ẢNH PHỤ -->
                <div class="col-md-4 text-center">
                    <div class="border rounded-3 p-3 bg-white shadow-sm mb-3">
                        <div class="fw-bold small text-muted text-start mb-2">
                            <i class="fas fa-image text-primary me-1"></i> Ảnh đại diện chính:
                        </div>
                        <?php if (!empty($product->image)): ?>
                            <a href="<?= PRODUCT_IMAGE_URL . htmlspecialchars($product->image) ?>" target="_blank" title="Xem ảnh gốc">
                                <img src="<?= PRODUCT_IMAGE_URL . htmlspecialchars($product->image) ?>" 
                                     alt="<?= htmlspecialchars($product->proName) ?>" 
                                     class="img-fluid rounded-3" 
                                     style="max-height: 280px; width: 100%; object-fit: contain;"
                                     onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                            </a>
                        <?php else: ?>
                            <div class="py-5 text-muted bg-light rounded">Không có hình ảnh chính</div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($productImages)): ?>
                        <div class="border rounded-3 p-3 bg-white shadow-sm text-start">
                            <h6 class="fw-bold mb-2 small text-muted">
                                <i class="fas fa-images text-success me-1"></i> Bộ sưu tập ảnh phụ (<?= count($productImages) ?>):
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($productImages as $pIdx => $img): ?>
                                    <div class="position-relative">
                                        <a href="<?= PRODUCT_IMAGE_URL . htmlspecialchars($img->image) ?>" target="_blank" title="Xem ảnh phụ <?= $pIdx + 1 ?>">
                                            <img src="<?= PRODUCT_IMAGE_URL . htmlspecialchars($img->image) ?>" 
                                                 class="rounded-3 border shadow-sm" 
                                                 width="65" height="65" 
                                                 style="object-fit: cover;"
                                                 onerror="this.src='https://placehold.co/65x65?text=Img'">
                                        </a>
                                        <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 start-0 m-1 px-1 py-0.5" style="font-size: 0.65rem;">
                                            #<?= $pIdx + 1 ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CỘT THÔNG TIN CƠ BẢN -->
                <div class="col-md-8">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th style="width: 25%;" class="bg-light">Mã sản phẩm (ID)</th>
                                <td class="fw-bold text-primary">#<?= $product->id ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tên sản phẩm</th>
                                <td class="fw-bold text-dark fs-5"><?= htmlspecialchars($product->proName) ?></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Slug</th>
                                <td><code><?= htmlspecialchars($product->slug ?? '') ?></code></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Danh mục</th>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1"><?= htmlspecialchars($cateName ?? 'Chưa phân loại') ?></span></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Thương hiệu</th>
                                <td><span class="badge bg-info-subtle text-dark border border-info-subtle px-2.5 py-1"><?= htmlspecialchars($brandName ?? 'Không có thương hiệu') ?></span></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Giá bán niêm yết</th>
                                <td><span class="text-danger fw-bold fs-5"><?= number_format($product->price ?? 0, 0, ',', '.') ?> đ</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Giá khuyến mãi</th>
                                <td>
                                    <?php if (!empty($product->discountPrice) && $product->discountPrice > 0): ?>
                                        <span class="text-success fw-bold fs-5"><?= number_format($product->discountPrice, 0, ',', '.') ?> đ</span>
                                        <span class="badge bg-danger ms-2">-<?= round((($product->price - $product->discountPrice) / $product->price) * 100) ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">Không áp dụng</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tổng tồn kho</th>
                                <td>
                                    <span class="badge <?= ($product->quantity ?? 0) > 0 ? 'bg-success' : 'bg-danger' ?> px-2.5 py-1.5 fs-6">
                                        <?= ($product->quantity ?? 0) > 0 ? ($product->quantity . ' sản phẩm') : 'Hết hàng' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Trạng thái</th>
                                <td>
                                    <?php if (isset($product->status) && $product->status == 1): ?>
                                        <span class="badge bg-success">Đang mở bán</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tạm ẩn</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Thời gian tạo</th>
                                <td class="text-muted small"><?= htmlspecialchars($product->createdAt ?? 'Chưa cập nhật') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BẢNG BIẾN THỂ SẢN PHẨM & HÌNH ẢNH LIÊN KẾT -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border rounded-3 overflow-hidden shadow-sm">
                        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3 gap-2">
                            <div>
                                <h6 class="fw-bold text-primary mb-0">
                                    <i class="fas fa-layer-group me-1"></i> Danh sách Biến thể & Hình ảnh liên kết
                                </h6>
                                <small class="text-muted">Mỗi biến thể được liên kết trực tiếp với hình ảnh và mức giá bán riêng</small>
                            </div>
                            <span class="badge bg-primary px-3 py-2 rounded-pill">
                                <?= !empty($variants) ? count($variants) : (count($productImages) + 1) ?> Phiên bản
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center align-middle mb-0">
                                <thead class="table-light small text-muted">
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th style="width: 90px;">Ảnh biến thể</th>
                                        <th class="text-start" style="min-width: 180px;">Tên phiên bản / Biến thể</th>
                                        <th>Mã SKU</th>
                                        <th>Giá gốc riêng</th>
                                        <th>Giá khuyến mãi</th>
                                        <th>Tồn kho</th>
                                        <th>Liên kết hình ảnh</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $displayVariants = $variants;
                                    if (empty($displayVariants)) {
                                        // Tự động map theo ảnh chính và các ảnh phụ
                                        $displayVariants = [];
                                        $displayVariants[] = (object)[
                                            'id' => 0,
                                            'variantName' => 'Phiên bản Tiêu chuẩn',
                                            'sku' => 'SKU-' . $product->id . '-STD',
                                            'price' => $product->price,
                                            'discountPrice' => $product->discountPrice,
                                            'quantity' => $product->quantity,
                                            'image' => $product->image,
                                            'status' => 1
                                        ];
                                        foreach ($productImages as $piIdx => $pImg) {
                                            $imgName = is_object($pImg) ? ($pImg->image ?? '') : ($pImg['image'] ?? '');
                                            $displayVariants[] = (object)[
                                                'id' => 0,
                                                'variantName' => 'Phiên bản Nâng cấp ' . ($piIdx + 1),
                                                'sku' => 'SKU-' . $product->id . '-V' . ($piIdx + 1),
                                                'price' => $product->price + (($piIdx + 1) * 50000),
                                                'discountPrice' => $product->discountPrice > 0 ? ($product->discountPrice + (($piIdx + 1) * 50000)) : 0,
                                                'quantity' => $product->quantity,
                                                'image' => $imgName,
                                                'status' => 1
                                            ];
                                        }
                                    }
                                    ?>
                                    <?php foreach ($displayVariants as $idx => $v): ?>
                                        <?php
                                        // Xác định tên file ảnh biến thể
                                        if (!empty($v->image)) {
                                            $vImg = $v->image;
                                        } elseif ($idx === 0) {
                                            $vImg = $product->image;
                                        } elseif (isset($productImages[$idx - 1])) {
                                            $vImg = is_object($productImages[$idx - 1]) ? $productImages[$idx - 1]->image : $productImages[$idx - 1]['image'];
                                        } else {
                                            $vImg = $product->image;
                                        }
                                        $vImgUrl = !empty($vImg) ? (PRODUCT_IMAGE_URL . $vImg) : 'https://placehold.co/80x80?text=SP';
                                        ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?= $idx + 1 ?></td>
                                            <td>
                                                <a href="<?= $vImgUrl ?>" target="_blank" title="Bấm để xem ảnh gốc">
                                                    <img src="<?= $vImgUrl ?>" 
                                                         class="rounded-3 border shadow-sm" 
                                                         width="60" height="60" 
                                                         style="object-fit: cover; background: #fff;" 
                                                         alt="<?= htmlspecialchars($v->variantName) ?>"
                                                         onerror="this.src='https://placehold.co/60x60?text=SP'">
                                                </a>
                                            </td>
                                            <td class="text-start">
                                                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($v->variantName) ?></div>
                                                <small class="text-muted">Biến thể số #<?= $idx + 1 ?></small>
                                            </td>
                                            <td><code><?= htmlspecialchars($v->sku ?? 'Tự động') ?></code></td>
                                            <td>
                                                <span class="text-danger fw-bold fs-6">
                                                    <?= number_format($v->price ?? $product->price, 0, ',', '.') ?> đ
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($v->discountPrice) && $v->discountPrice > 0): ?>
                                                    <span class="text-success fw-bold"><?= number_format($v->discountPrice, 0, ',', '.') ?> đ</span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= ($v->quantity ?? 0) > 0 ? 'bg-info text-dark' : 'bg-danger text-white' ?> px-2.5 py-1.5 fs-6">
                                                    <?= $v->quantity ?? 0 ?> sp
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($idx === 0): ?>
                                                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                                                        <i class="fas fa-image me-1"></i> Ảnh chính
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                                        <i class="fas fa-images me-1"></i> Ảnh phụ <?= $idx ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= ($v->status ?? 1) == 1 ? 'bg-success' : 'bg-secondary' ?> px-2 py-1">
                                                    <?= ($v->status ?? 1) == 1 ? 'Hiển thị' : 'Ẩn' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MÔ TẢ CHI TIẾT SẢN PHẨM -->
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-primary me-1"></i> Mô tả chi tiết sản phẩm:</h6>
                    <div class="border rounded-3 p-3 bg-light text-muted" style="line-height: 1.8;">
                        <?php if (!empty($product->description)): ?>
                            <?= nl2br(htmlspecialchars($product->description)) ?>
                        <?php else: ?>
                            <span class="text-muted">Chưa có mô tả cho sản phẩm này.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>