<?php
$pageTitle = "Quản lý sản phẩm - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard/index">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh sách sản phẩm</li>
    </ol>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Ô tìm kiếm -->
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" action="/MINISHOP_TRANTHINGOCYEN/admin/product/index" class="d-flex">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" class="form-control"
                    placeholder="Nhập tên sản phẩm...">

                <input type="hidden" name="limit" value="<?= $limit ?? 10 ?>">
                <button class="btn btn-primary ms-2 text-nowrap" type="submit">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-box me-1"></i> Danh sách sản phẩm</div>
            <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
        <div class="card-body">

            <?php if (!empty($keyword) && empty($products)): ?>
                <div class="alert alert-warning text-center my-3" role="alert">
                    Không tìm thấy sản phẩm phù hợp với từ khóa: <strong><?= htmlspecialchars($keyword) ?></strong>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Hình ảnh</th>
                                <th class="text-start">Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Thương hiệu</th>
                                <th>Giá</th>
                                <th>Kho</th>
                                <th>Trạng thái</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php $stt = $offset + 1; ?>
                                <?php foreach ($products as $pro): ?>
                                    <tr>
                                        <td><?= $stt++ ?></td>
                                        <td>
                                            <?php if (!empty($pro->image)): ?>
                                                <img src="/MINISHOP_TRANTHINGOCYEN/uploads/products/<?= htmlspecialchars($pro->image) ?>"
                                                    alt="product" width="50" height="50" style="object-fit: cover;"
                                                    class="rounded shadow-sm">
                                            <?php else: ?>
                                                <span class="text-muted small">Không có ảnh</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start fw-bold">
                                            <?= htmlspecialchars($pro->proName ?? $pro->productname ?? '') ?>
                                        </td>
                                        <td><?= htmlspecialchars($pro->cateName ?? $pro->catename ?? '') ?></td>
                                        <td><?= htmlspecialchars($pro->brandName ?? $pro->brandname ?? '') ?></td>
                                        <td>
                                            <span class="text-danger fw-bold"><?= number_format($pro->price, 0, ',', '.') ?>
                                                đ</span>
                                        </td>
                                        <td><?= $pro->quantity ?></td>
                                        <td>
                                            <?php if ($pro->status == 1): ?>
                                                <span class="badge bg-success">Hiển thị</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Ẩn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/detail/<?= $pro->id ?>"
                                                    class="btn btn-info text-white btn-sm" title="Chi tiết">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                                <a href="/MINISHOP_TRANTHINGOCYEN/admin/product/edit/<?= $pro->id ?>"
                                                    class="btn btn-warning text-white btn-sm" title="Sửa">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <form method="POST" action="/MINISHOP_TRANTHINGOCYEN/admin/product/delete"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                                                    style="display:inline-block; margin: 0;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= $pro->id ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">Không tìm thấy dữ liệu.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang & Giới hạn hiển thị -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Hiển thị:</label>
                        <form method="GET" action="/MINISHOP_TRANTHINGOCYEN/admin/product/index">
                            <?php if (!empty($keyword)): ?>
                                <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                            <?php endif; ?>
                            <select name="limit" class="form-select" onchange="this.form.submit()">
                                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                                <option value="30" <?= $limit == 30 ? 'selected' : '' ?>>30</option>
                            </select>
                        </form>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="mb-0">
                            <ul class="pagination mb-0">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="/MINISHOP_TRANTHINGOCYEN/admin/product/index?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">Trước</a>
                                </li>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="/MINISHOP_TRANTHINGOCYEN/admin/product/index?limit=<?= $limit ?>&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="/MINISHOP_TRANTHINGOCYEN/admin/product/index?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">Sau</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/master.php";
?>