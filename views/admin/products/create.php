<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/product">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item active">Thêm mới sản phẩm</li>
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

    <?php if ($message != ""): ?>
        <div class="alert alert-warning"><?= $message ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i> Form thêm mới sản phẩm
        </div>
        <div class="card-body">
           <form action="" method="POST" enctype="multipart/form-data">
<?= csrf_field() ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="productName" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="productName" name="productName"
                            value="<?= htmlspecialchars($_POST['productName'] ?? '') ?>" placeholder="Nhập tên sản phẩm...">
                    </div>
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-bold">Slug (Đường dẫn thân thiện)</label>
                        <input type="text" class="form-control" id="slug" name="slug"
                            value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" placeholder="Tự động tạo nếu để trống...">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="categoryId" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="categoryId" id="categoryId" class="form-select">
                            <option value="0">-- Chọn danh mục sản phẩm --</option>
                            <?php foreach ($categories as $item): ?>
                                <option value="<?= $item->id ?>" <?= (isset($_POST['categoryId']) && $_POST['categoryId'] == $item->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item->cateName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brandId" class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                        <select name="brandId" id="brandId" class="form-select">
                            <option value="0">-- Chọn thương hiệu sản phẩm --</option>
                            <?php foreach ($brands as $item): ?>
                                <option value="<?= $item->id ?>" <?= (isset($_POST['brandId']) && $_POST['brandId'] == $item->id) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item->brandName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="price" class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" step="1000" class="form-control" id="price" name="price"
                            value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="discountPrice" class="form-label fw-bold">Giá khuyến mãi (VNĐ)</label>
                        <input type="number" step="1000" class="form-control" id="discountPrice" name="discountPrice"
                            value="<?= htmlspecialchars($_POST['discountPrice'] ?? '0') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-bold">Số lượng trong kho <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                            value="<?= htmlspecialchars($_POST['quantity'] ?? '10') ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="text-center mb-3" id="preview">
                            <span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh chính được chọn</span>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Hình ảnh chính sản phẩm</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <div class="form-text">Hỗ trợ JPG, JPEG, PNG, WEBP, GIF (Tối đa 2 MB).</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-center mb-3" id="preview-gallery">
                            <span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh phụ nào được chọn</span>
                        </div>
                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">Hình ảnh phụ (Gallery)</label>
                            <input type="file" id="images" name="images[]" class="form-control" accept="image/*" multiple>
                            <div class="form-text">Chọn nhiều hình ảnh (Mỗi ảnh tối đa 2 MB).</div>
                        </div>
                    </div>
                </div>

                <!-- KHỐI CẤU HÌNH BIẾN THỂ SẢN PHẨM & LIÊN KẾT HÌNH ẢNH -->
                <div class="card border-primary border-opacity-25 mb-4 shadow-sm">
                    <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center py-2">
                        <div class="fw-bold text-primary">
                            <i class="fas fa-layer-group me-1"></i> Quản lý Biến thể sản phẩm (Màu sắc / Phiên bản / Dung lượng...)
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add-variant">
                            <i class="fas fa-plus-circle me-1"></i> Thêm biến thể
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="fas fa-info-circle me-1"></i> <strong>Quy tắc liên kết hình ảnh tự động:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li><strong>Biến thể 1:</strong> Gắn với <strong>Ảnh chính</strong> của sản phẩm.</li>
                                <li><strong>Biến thể 2:</strong> Gắn với <strong>Ảnh phụ thứ 1</strong> (trong Gallery ảnh phụ tải lên).</li>
                                <li><strong>Biến thể 3:</strong> Gắn với <strong>Ảnh phụ thứ 2</strong>... và tương tự cho các biến thể tiếp theo.</li>
                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-center mb-0" id="table-variants">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th class="text-start" style="min-width: 180px;">Tên biến thể <span class="text-danger">*</span></th>
                                        <th style="width: 130px;">Mã SKU</th>
                                        <th style="width: 150px;">Giá bán (VNĐ)</th>
                                        <th style="width: 150px;">Giá KM (VNĐ)</th>
                                        <th style="width: 100px;">Tồn kho</th>
                                        <th style="min-width: 160px;">Ảnh liên kết</th>
                                        <th style="width: 60px;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="variant-list">
                                    <!-- Mặc định có 1 biến thể đầu tiên gắn với ảnh chính -->
                                    <tr class="variant-row">
                                        <td class="fw-bold variant-index">1</td>
                                        <td>
                                            <input type="text" name="variants[0][variant_name]" class="form-control form-control-sm" placeholder="VD: Màu Đen / Bản Tiêu Chuẩn" value="Phiên bản Tiêu chuẩn" required>
                                        </td>
                                        <td>
                                            <input type="text" name="variants[0][sku]" class="form-control form-control-sm" placeholder="Tự tạo...">
                                        </td>
                                        <td>
                                            <input type="number" step="1000" name="variants[0][price]" class="form-control form-control-sm" placeholder="Mặc định theo giá gốc">
                                        </td>
                                        <td>
                                            <input type="number" step="1000" name="variants[0][discount_price]" class="form-control form-control-sm" placeholder="Mặc định theo KM">
                                        </td>
                                        <td>
                                            <input type="number" name="variants[0][quantity]" class="form-control form-control-sm text-center" value="10" min="0">
                                        </td>
                                        <td>
                                            <span class="badge bg-primary px-2 py-1 variant-img-badge">
                                                <i class="fas fa-image me-1"></i> Gắn với Ảnh chính
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-remove-variant" title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="status" class="form-label fw-bold">Trạng thái hiển thị</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="5"
                        placeholder="Nhập mô tả sản phẩm..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>admin/product" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('image').addEventListener('change', function (event) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail rounded';
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<span class="text-muted fst-italic d-block border rounded p-4 bg-light">Chưa có hình ảnh chính được chọn</span>';
        }
    });

    document.getElementById('images').addEventListener('change', function (event) {
        const previewGallery = document.getElementById('preview-gallery');
        previewGallery.innerHTML = '';
        const files = event.target.files;
        if (files.length > 0) {
            const container = document.createElement('div');
            container.className = 'd-flex flex-wrap gap-2 justify-content-center';
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail rounded';
                    img.style.width = '70px';
                    img.style.height = '70px';
                    img.style.objectFit = 'cover';
                    container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
    // Quản lý thêm / xóa biến thể động
    let variantIndex = 1;
    const variantList = document.getElementById('variant-list');
    const btnAddVariant = document.getElementById('btn-add-variant');

    function updateVariantBadges() {
        const rows = variantList.querySelectorAll('.variant-row');
        rows.forEach((row, idx) => {
            row.querySelector('.variant-index').textContent = idx + 1;
            
            // Cập nhật name attributes
            row.querySelector('input[name*="[variant_name]"]').name = `variants[${idx}][variant_name]`;
            row.querySelector('input[name*="[sku]"]').name = `variants[${idx}][sku]`;
            row.querySelector('input[name*="[price]"]').name = `variants[${idx}][price]`;
            row.querySelector('input[name*="[discount_price]"]').name = `variants[${idx}][discount_price]`;
            row.querySelector('input[name*="[quantity]"]').name = `variants[${idx}][quantity]`;

            // Cập nhật nhãn ảnh liên kết
            const badge = row.querySelector('.variant-img-badge');
            if (idx === 0) {
                badge.className = 'badge bg-primary px-2 py-1 variant-img-badge';
                badge.innerHTML = '<i class="fas fa-image me-1"></i> Gắn với Ảnh chính';
            } else {
                badge.className = 'badge bg-success px-2 py-1 variant-img-badge';
                badge.innerHTML = `<i class="fas fa-images me-1"></i> Gắn với Ảnh phụ ${idx}`;
            }
        });
    }

    if (btnAddVariant) {
        btnAddVariant.addEventListener('click', function () {
            const currentCount = variantList.querySelectorAll('.variant-row').length;
            const newTr = document.createElement('tr');
            newTr.className = 'variant-row';
            newTr.innerHTML = `
                <td class="fw-bold variant-index">${currentCount + 1}</td>
                <td>
                    <input type="text" name="variants[${currentCount}][variant_name]" class="form-control form-control-sm" placeholder="VD: Màu Trắng / Switch Vàng" required>
                </td>
                <td>
                    <input type="text" name="variants[${currentCount}][sku]" class="form-control form-control-sm" placeholder="Tự tạo...">
                </td>
                <td>
                    <input type="number" step="1000" name="variants[${currentCount}][price]" class="form-control form-control-sm" placeholder="Mặc định theo giá gốc">
                </td>
                <td>
                    <input type="number" step="1000" name="variants[${currentCount}][discount_price]" class="form-control form-control-sm" placeholder="Mặc định theo KM">
                </td>
                <td>
                    <input type="number" name="variants[${currentCount}][quantity]" class="form-control form-control-sm text-center" value="10" min="0">
                </td>
                <td>
                    <span class="badge bg-success px-2 py-1 variant-img-badge">
                        <i class="fas fa-images me-1"></i> Gắn với Ảnh phụ ${currentCount}
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-remove-variant" title="Xóa">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            variantList.appendChild(newTr);
            updateVariantBadges();
        });
    }

    if (variantList) {
        variantList.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-variant')) {
                const rows = variantList.querySelectorAll('.variant-row');
                if (rows.length <= 1) {
                    alert('Sản phẩm cần có tối thiểu 1 biến thể!');
                    return;
                }
                e.target.closest('.variant-row').remove();
                updateVariantBadges();
            }
        });
    }
</script>