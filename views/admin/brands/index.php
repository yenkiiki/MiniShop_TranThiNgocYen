<?php
$msg = $_GET['msg'] ?? '';
$message = '';
$error = '';

if ($msg === 'delete_success') {
    $message = 'Xóa thương hiệu thành công!';
} elseif ($msg === 'update_success') {
    $message = 'Cập nhật thương hiệu thành công!';
} elseif ($msg === 'insert_success') {
    $message = 'Thêm mới thương hiệu thành công!';
} elseif ($msg === 'delete_fail') {
    $error = 'Xóa thương hiệu thất bại!';
} elseif ($msg === 'delete_error') {
    $error = 'Không thể xóa thương hiệu này vì đang có sản phẩm liên kết!';
}

$pageTitle = "Quản lý thương hiệu - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/MINISHOP_TRANTHINGOCYEN/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh sách thương hiệu</li>
    </ol>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-search me-1"></i> Tìm kiếm & Lọc thương hiệu</div>
        <div class="card-body">
            <!-- Đổi sang URL thân thiện cho form tìm kiếm -->
            <form action="/MINISHOP_TRANTHINGOCYEN/admin/brand" method="GET" class="row g-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <div class="col-md-5">
                    <label class="form-label fw-bold">Từ khóa:</label>
                    <input type="text" name="keyword" class="form-control"
                        placeholder="Nhập tên thương hiệu hoặc slug..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Trạng thái:</label>
                    <select name="search_status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($statusList as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($searchStatus !== "" && $searchStatus == $key) ? 'selected' : '' ?>>
                                <?= $value['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-tags me-1"></i> Danh sách thương hiệu</div>
            <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand/create" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Thêm thương hiệu mới
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Hình ảnh</th>
                            <th class="text-start">Tên thương hiệu</th>
                            <th>Slug</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($brands)): ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($brands as $brand): ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td>
                                        <?php if (!empty($brand->image)): ?>
                                            <img src="/MINISHOP_TRANTHINGOCYEN/uploads/brands/<?= htmlspecialchars($brand->image) ?>"
                                                alt="Logo" style="width: 50px; height: 50px; object-fit: cover;"
                                                class="rounded border">
                                        <?php else: ?>
                                            <span class="text-muted">Không có ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-start fw-bold text-primary"><?= htmlspecialchars($brand->brandName) ?></td>
                                    <td><code><?= htmlspecialchars($brand->slug ?? '') ?></code></td>
                                    <td>
                                        <?php
                                        $sttKey = $brand->status;
                                        $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($brand->createdAt) ?></td>
                                    <td class="text-nowrap">
                                        <!-- ĐÃ SỬA: Nút Chi tiết -->
                                        <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand/detail/<?= $brand->id ?>" class="btn btn-info text-white btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        
                                        <!-- ĐÃ SỬA: Nút Sửa -->
                                        <a href="/MINISHOP_TRANTHINGOCYEN/admin/brand/edit/<?= $brand->id ?>" class="btn btn-warning text-white btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        
                                        <!-- ĐÃ SỬA: Form Xóa -->
                                        <form action="/MINISHOP_TRANTHINGOCYEN/admin/brand/delete" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                            <input type="hidden" name="id" value="<?= $brand->id ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-danger fw-bold py-4">Không tìm thấy dữ liệu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <label class="me-2">Hiển thị:</label>
                    <form action="/MINISHOP_TRANTHINGOCYEN/admin/brand" method="GET">
                        <?php if (!empty($keyword)): ?>
                            <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                        <?php endif; ?>
                        <?php if ($searchStatus !== ""): ?>
                            <input type="hidden" name="search_status" value="<?= $searchStatus ?>">
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
                                    href="/MINISHOP_TRANTHINGOCYEN/admin/brand?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Trước</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link"
                                        href="/MINISHOP_TRANTHINGOCYEN/admin/brand?limit=<?= $limit ?>&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link"
                                    href="/MINISHOP_TRANTHINGOCYEN/admin/brand?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Sau</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . "/../../../views/admin/layouts/master.php";
?>