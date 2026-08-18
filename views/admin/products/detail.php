<?php
$pageTitle = "Chi tiết sản phẩm - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard/index">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/product/index">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Chi tiết sản phẩm</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-eye me-1"></i> Thông tin chi tiết sản phẩm: <strong><?= htmlspecialchars($product->proName ?? '') ?></strong>
            </div>
            <div>
                <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/edit?id=<?= $product->id ?>" class="btn btn-warning text-white btn-sm">
                    <i class="fas fa-edit"></i> Sửa
                </a>
                <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/index" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <div class="border rounded p-3 bg-light">
                        <?php if (!empty($product->image)): ?>
                            <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($product->image) ?>" 
                                 alt="<?= htmlspecialchars($product->proName) ?>" 
                                 class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: contain;">
                        <?php else: ?>
                            <div class="py-5 text-muted">Không có hình ảnh chính</div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($productImages)): ?>
                        <div class="mt-3 text-start">
                            <h6 class="fw-bold">Hình ảnh phụ (<?= count($productImages) ?>):</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($productImages as $img): ?>
                                    <a href="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($img->image) ?>" target="_blank" title="Xem ảnh lớn">
                                        <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($img->image) ?>" 
                                             class="rounded border" width="70" height="70" style="object-fit: cover;">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 25%;">ID sản phẩm</th>
                                <td>#<?= $product->id ?></td>
                            </tr>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($product->proName) ?></td>
                            </tr>
                            <tr>
                                <th>Slug</th>
                                <td class="text-muted"><?= htmlspecialchars($product->slug ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Danh mục</th>
                                <td><?= htmlspecialchars($cateName ?? 'Chưa phân loại') ?></td>
                            </tr>
                            <tr>
                                <th>Thương hiệu</th>
                                <td><?= htmlspecialchars($brandName ?? 'Không có thương hiệu') ?></td>
                            </tr>
                            <tr>
                                <th>Giá gốc</th>
                                <td><span class="text-danger fw-bold"><?= number_format($product->price ?? 0, 0, ',', '.') ?> đ</span></td>
                            </tr>
                            <tr>
                                <th>Giá khuyến mãi</th>
                                <td>
                                    <?php if (!empty($product->discountPrice) && $product->discountPrice > 0): ?>
                                        <span class="text-success fw-bold"><?= number_format($product->discountPrice, 0, ',', '.') ?> đ</span>
                                    <?php else: ?>
                                        <span class="text-muted">Không có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Số lượng tồn kho</th>
                                <td><?= $product->quantity ?? 0 ?></td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    <?php if (isset($product->status) && $product->status == 1): ?>
                                        <span class="badge bg-success">Hiển thị</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Ẩn</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td><?= htmlspecialchars($product->createdAt ?? 'Chưa cập nhật') ?></td>
                            </tr>
                            <tr>
                                <th>Ngày cập nhật gần nhất</th>
                                <td><?= htmlspecialchars($product->updatedAt ?? 'Chưa cập nhật') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="fw-bold"><i class="fas fa-align-left me-1"></i> Mô tả chi tiết</h5>
                    <div class="border rounded p-3 bg-light">
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