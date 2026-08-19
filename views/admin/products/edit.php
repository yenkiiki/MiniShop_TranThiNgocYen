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

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>