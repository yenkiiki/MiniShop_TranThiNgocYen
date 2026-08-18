    <?php
    $pageTitle = "Cập nhật danh mục - Mini Shop";
    ob_start();
    ?>

    <div class="container-fluid px-4">
        <h1 class="mt-4">Quản lý danh mục</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php?controller=dashboard&action=index">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php?controller=category&action=index">Danh mục sản phẩm</a></li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-edit me-1"></i>
                    Cập nhật danh mục
                </div>
                <a href="index.php?controller=category&action=index" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <!-- Hiển thị thông báo lỗi nếu có -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" action="index.php?controller=category&action=edit&id=<?= $category->id ?>">
                    <!-- Tên danh mục -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="cateName" class="form-control" value="<?= htmlspecialchars($category->cateName) ?>">
                    </div>

                    <!-- Slug -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category->slug ?? '') ?>">
                    </div>

                    <!-- Hình ảnh -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh danh mục</label>
                        <div class="mb-2">
                            <?php if (!empty($category->image)): ?>
                                <img src="/MiniShop_TranThiNgocYen/uploads/categories/<?= htmlspecialchars($category->image) ?>" 
                                    alt="<?= htmlspecialchars($category->cateName) ?>" 
                                    class="img-thumbnail rounded shadow-sm" 
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <span class="text-muted small">Chưa có ảnh</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text text-muted">Chọn ảnh mới nếu bạn muốn thay thế ảnh hiện tại.</div>
                    </div>

                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" rows="5" class="form-control"><?= htmlspecialchars($category->description ?? '') ?></textarea>
                    </div>

                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Trạng thái</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="1" <?= $category->status == 1 ? "checked" : "" ?>>
                            <label class="form-check-label">Hiển thị</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="0" <?= $category->status == 0 ? "checked" : "" ?>>
                            <label class="form-check-label">Ẩn</label>
                        </div>
                    </div>

                    <!-- Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="index.php?controller=category&action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    $content = ob_get_clean();
    include __DIR__ . "/../layouts/master.php";
    ?>