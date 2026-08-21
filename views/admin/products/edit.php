<?php
$pageTitle = "Cập nhật sản phẩm - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard/index">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/product/index">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Cập nhật sản phẩm</li>
    </ol>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Form cập nhật sản phẩm
        </div>
        <div class="card-body">
            <form action="/MINISHOP_TRANTHINGOCYEN/admin/product/edit/<?= $productOld->id ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="proName" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="proName" name="proName" value="<?= htmlspecialchars($_POST['proName'] ?? $productOld->proName) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-bold">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($_POST['slug'] ?? $productOld->slug) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="categoryId" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="categoryId" id="categoryId" class="form-select" required>
                            <option value="0">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $item): ?>
                                <option value="<?= $item->id ?>" <?= $productOld->categoryId == $item->id ? 'selected' : '' ?>><?= htmlspecialchars($item->cateName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brandId" class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                        <select name="brandId" id="brandId" class="form-select" required>
                            <option value="0">-- Chọn thương hiệu --</option>
                            <?php foreach ($brands as $item): ?>
                                <option value="<?= $item->id ?>" <?= $productOld->brandId == $item->id ? 'selected' : '' ?>><?= htmlspecialchars($item->brandName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-bold">Giá bán <span class="text-danger">*</span></label>
                        <input type="number" step="1000" class="form-control" id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? $productOld->price) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="discountPrice" class="form-label fw-bold">Giá khuyến mãi</label>
                        <input type="number" step="1000" class="form-control" id="discountPrice" name="discountPrice" value="<?= htmlspecialchars($_POST['discountPrice'] ?? ($productOld->discountPrice ?? 0)) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-bold">Số lượng kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($_POST['quantity'] ?? $productOld->quantity) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 border-end">
                        <label class="form-label fw-bold text-primary">Hình ảnh chính</label>
                        <div class="text-center mb-3">
                            <?php if (!empty($productOld->image)): ?>
                                <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($productOld->image) ?>" class="img-thumbnail rounded shadow-sm" width="150" alt="">
                            <?php else: ?>
                                <span class="text-muted">Chưa có ảnh</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Đổi ảnh chính mới</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Hình ảnh phụ (Gallery)</label>
                        <?php 
                        $currentImages = $this->productDAO->getImagesByProductId($id);
                        if (!empty($currentImages)): 
                        ?>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <?php foreach ($currentImages as $subImg): ?>
                                    <div class="border p-2 rounded text-center bg-light" style="width: 100px;">
                                        <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($subImg->image) ?>" width="80" height="70" class="object-fit-cover rounded mb-2" alt="">
                                        <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/edit/<?= $id ?>?action=delete_sub&image_id=<?= $subImg->id ?>" 
                                           class="btn btn-danger btn-sm w-100" 
                                           style="font-size: 11px;"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa hình ảnh phụ này không?');">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted fst-italic mb-3">Sản phẩm chưa có ảnh phụ nào.</div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="sub_images" class="form-label">Thêm ảnh phụ mới</label>
                            <input type="file" id="sub_images" name="sub_images[]" class="form-control" accept="image/*" multiple>
                        </div>
                    </div>
                </div>

                <!-- KHỐI CẤU HÌNH BIẾN THỂ SẢN PHẨM & LIÊN KẾT HÌNH ẢNH -->
                <div class="card border-primary border-opacity-25 mb-4 shadow-sm">
                    <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center py-2">
                        <div class="fw-bold text-primary">
                            <i class="fas fa-layer-group me-1"></i> Quản lý Biến thể sản phẩm (Màu sắc / Phiên bản / Dung lượng...)
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-variant-edit">
                            <i class="fas fa-plus-circle me-1"></i> Thêm biến thể mới
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="fas fa-info-circle me-1"></i> <strong>Quy tắc liên kết hình ảnh tự động:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li><strong>Biến thể 1:</strong> Gắn với <strong>Ảnh chính</strong> của sản phẩm.</li>
                                <li><strong>Biến thể 2:</strong> Gắn với <strong>Ảnh phụ thứ 1</strong> (trong Gallery).</li>
                                <li><strong>Biến thể 3:</strong> Gắn với <strong>Ảnh phụ thứ 2</strong>... và tương tự cho các biến thể tiếp theo.</li>
                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-center mb-0" id="table-variants-edit">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 80px;">Ảnh</th>
                                        <th class="text-start" style="min-width: 180px;">Tên biến thể <span class="text-danger">*</span></th>
                                        <th style="width: 130px;">Mã SKU</th>
                                        <th style="width: 140px;">Giá bán (VNĐ)</th>
                                        <th style="width: 140px;">Giá KM (VNĐ)</th>
                                        <th style="width: 90px;">Tồn kho</th>
                                        <th style="min-width: 150px;">Liên kết ảnh</th>
                                        <th style="width: 60px;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="variant-list-edit">
                                    <?php if (!empty($variants)): ?>
                                        <?php foreach ($variants as $idx => $var): ?>
                                            <?php
                                            $vImg = !empty($var->image) ? $var->image : (($idx === 0) ? $productOld->image : ($currentImages[$idx - 1]->image ?? ''));
                                            ?>
                                            <tr class="variant-row-edit">
                                                <td class="fw-bold variant-index-edit"><?= $idx + 1 ?></td>
                                                <td>
                                                    <input type="hidden" name="variants[<?= $idx ?>][id]" value="<?= $var->id ?>">
                                                    <input type="hidden" name="variants[<?= $idx ?>][image]" value="<?= htmlspecialchars($var->image ?? '') ?>">
                                                    <?php if (!empty($vImg)): ?>
                                                        <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($vImg) ?>" class="rounded border" width="50" height="50" style="object-fit: cover;" alt="">
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Chưa có</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <input type="text" name="variants[<?= $idx ?>][variant_name]" class="form-control form-control-sm" value="<?= htmlspecialchars($var->variantName) ?>" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="variants[<?= $idx ?>][sku]" class="form-control form-control-sm" value="<?= htmlspecialchars($var->sku ?? '') ?>">
                                                </td>
                                                <td>
                                                    <input type="number" step="1000" name="variants[<?= $idx ?>][price]" class="form-control form-control-sm" value="<?= $var->price ?? '' ?>" placeholder="<?= number_format($productOld->price, 0, '', '') ?>">
                                                </td>
                                                <td>
                                                    <input type="number" step="1000" name="variants[<?= $idx ?>][discount_price]" class="form-control form-control-sm" value="<?= $var->discountPrice ?? '' ?>" placeholder="<?= number_format($productOld->discountPrice ?? 0, 0, '', '') ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="variants[<?= $idx ?>][quantity]" class="form-control form-control-sm text-center" value="<?= $var->quantity ?>" min="0">
                                                </td>
                                                <td>
                                                    <?php if ($idx === 0): ?>
                                                        <span class="badge bg-primary px-2 py-1 variant-img-badge-edit">
                                                            <i class="fas fa-image me-1"></i> Gắn với Ảnh chính
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success px-2 py-1 variant-img-badge-edit">
                                                            <i class="fas fa-images me-1"></i> Gắn với Ảnh phụ <?= $idx ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-variant-edit" title="Xóa">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="variant-row-edit">
                                            <td class="fw-bold variant-index-edit">1</td>
                                            <td>
                                                <input type="hidden" name="variants[0][id]" value="0">
                                                <input type="hidden" name="variants[0][image]" value="">
                                                <?php if (!empty($productOld->image)): ?>
                                                    <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($productOld->image) ?>" class="rounded border" width="50" height="50" style="object-fit: cover;" alt="">
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Chưa có</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <input type="text" name="variants[0][variant_name]" class="form-control form-control-sm" value="Phiên bản Tiêu chuẩn" required>
                                            </td>
                                            <td>
                                                <input type="text" name="variants[0][sku]" class="form-control form-control-sm" placeholder="Tự tạo...">
                                            </td>
                                            <td>
                                                <input type="number" step="1000" name="variants[0][price]" class="form-control form-control-sm" placeholder="<?= number_format($productOld->price, 0, '', '') ?>">
                                            </td>
                                            <td>
                                                <input type="number" step="1000" name="variants[0][discount_price]" class="form-control form-control-sm" placeholder="<?= number_format($productOld->discountPrice ?? 0, 0, '', '') ?>">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[0][quantity]" class="form-control form-control-sm text-center" value="<?= $productOld->quantity ?>" min="0">
                                            </td>
                                            <td>
                                                <span class="badge bg-primary px-2 py-1 variant-img-badge-edit">
                                                    <i class="fas fa-image me-1"></i> Gắn với Ảnh chính
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-remove-variant-edit" title="Xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status" class="form-label fw-bold">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" <?= $productOld->status == 1 ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= $productOld->status == 0 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? $productOld->description) ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/index" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const variantListEdit = document.getElementById('variant-list-edit');
    const btnAddVariantEdit = document.getElementById('btn-add-variant-edit');

    function updateVariantBadgesEdit() {
        const rows = variantListEdit.querySelectorAll('.variant-row-edit');
        rows.forEach((row, idx) => {
            row.querySelector('.variant-index-edit').textContent = idx + 1;
            
            const idInput = row.querySelector('input[name*="[id]"]');
            if (idInput) idInput.name = `variants[${idx}][id]`;

            const imgInput = row.querySelector('input[name*="[image]"]');
            if (imgInput) imgInput.name = `variants[${idx}][image]`;

            row.querySelector('input[name*="[variant_name]"]').name = `variants[${idx}][variant_name]`;
            row.querySelector('input[name*="[sku]"]').name = `variants[${idx}][sku]`;
            row.querySelector('input[name*="[price]"]').name = `variants[${idx}][price]`;
            row.querySelector('input[name*="[discount_price]"]').name = `variants[${idx}][discount_price]`;
            row.querySelector('input[name*="[quantity]"]').name = `variants[${idx}][quantity]`;

            const badge = row.querySelector('.variant-img-badge-edit');
            if (badge) {
                if (idx === 0) {
                    badge.className = 'badge bg-primary px-2 py-1 variant-img-badge-edit';
                    badge.innerHTML = '<i class="fas fa-image me-1"></i> Gắn với Ảnh chính';
                } else {
                    badge.className = 'badge bg-success px-2 py-1 variant-img-badge-edit';
                    badge.innerHTML = `<i class="fas fa-images me-1"></i> Gắn với Ảnh phụ ${idx}`;
                }
            }
        });
    }

    if (btnAddVariantEdit) {
        btnAddVariantEdit.addEventListener('click', function () {
            const currentCount = variantListEdit.querySelectorAll('.variant-row-edit').length;
            const newTr = document.createElement('tr');
            newTr.className = 'variant-row-edit';
            newTr.innerHTML = `
                <td class="fw-bold variant-index-edit">${currentCount + 1}</td>
                <td>
                    <input type="hidden" name="variants[${currentCount}][id]" value="0">
                    <input type="hidden" name="variants[${currentCount}][image]" value="">
                    <span class="badge bg-secondary">Mới</span>
                </td>
                <td>
                    <input type="text" name="variants[${currentCount}][variant_name]" class="form-control form-control-sm" placeholder="VD: Màu Trắng / 256GB" required>
                </td>
                <td>
                    <input type="text" name="variants[${currentCount}][sku]" class="form-control form-control-sm" placeholder="Tự tạo...">
                </td>
                <td>
                    <input type="number" step="1000" name="variants[${currentCount}][price]" class="form-control form-control-sm" placeholder="Theo giá gốc">
                </td>
                <td>
                    <input type="number" step="1000" name="variants[${currentCount}][discount_price]" class="form-control form-control-sm" placeholder="Theo KM">
                </td>
                <td>
                    <input type="number" name="variants[${currentCount}][quantity]" class="form-control form-control-sm text-center" value="10" min="0">
                </td>
                <td>
                    <span class="badge bg-success px-2 py-1 variant-img-badge-edit">
                        <i class="fas fa-images me-1"></i> Gắn với Ảnh phụ ${currentCount}
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-variant-edit" title="Xóa">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            variantListEdit.appendChild(newTr);
            updateVariantBadgesEdit();
        });
    }

    if (variantListEdit) {
        variantListEdit.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-variant-edit')) {
                const rows = variantListEdit.querySelectorAll('.variant-row-edit');
                if (rows.length <= 1) {
                    alert('Sản phẩm cần có tối thiểu 1 biến thể!');
                    return;
                }
                e.target.closest('.variant-row-edit').remove();
                updateVariantBadgesEdit();
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>