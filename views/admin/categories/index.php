<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><i class="fas fa-tags me-1"></i> Quản lý danh mục</div>
        <a href="/MINISHOP_TRANTHINGOCYEN/admin/category/create" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> Thêm danh mục mới
        </a>
    </div>
    <div class="card-body">
        <!-- Form Tìm kiếm và Lọc -->
        <form method="GET" action="/MINISHOP_TRANTHINGOCYEN/admin/category" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Từ khóa:</label>
                <input type="text" class="form-control" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Nhập tên danh mục hoặc slug...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Trạng thái:</label>
                <select name="search_status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1" <?= (isset($searchStatus) && $searchStatus === '1') ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= (isset($searchStatus) && $searchStatus === '0') ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
            <div class="col-md-5 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
                <a href="/MINISHOP_TRANTHINGOCYEN/admin/category" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Làm mới</a>
            </div>
        </form>

        <!-- Hiển thị thông báo (nếu có) -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Hình ảnh</th>
                        <th class="text-start">Tên danh mục</th>
                        <th>Slug</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Chức năng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php $stt = $offset + 1; ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $stt++ ?></td>
                                <td>
                                    <?php if (!empty($category->image)): ?>
                                        <img src="/MINISHOP_TRANTHINGOCYEN/uploads/categories/<?= htmlspecialchars($category->image) ?>"
                                            alt="Logo" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                                    <?php else: ?>
                                        <span class="text-muted">Không có ảnh</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-start fw-bold text-primary"><?= htmlspecialchars($category->cateName) ?></td>
                                <td><code><?= htmlspecialchars($category->slug ?? '') ?></code></td>
                                <td>
                                    <?php
                                    $sttKey = $category->status;
                                    $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                    ?>
                                    <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                </td>
                                <td><?= htmlspecialchars($category->createdAt) ?></td>
                                <td class="text-nowrap">
                                    <a href="/MINISHOP_TRANTHINGOCYEN/admin/category/detail/<?= $category->id ?>"
                                        class="btn btn-info text-white btn-sm" title="Chi tiết">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </a>
                                    <a href="/MINISHOP_TRANTHINGOCYEN/admin/category/edit/<?= $category->id ?>"
                                        class="btn btn-warning text-white btn-sm" title="Sửa">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="/MINISHOP_TRANTHINGOCYEN/admin/category/delete" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?= $category->id ?>">
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

        <!-- Khu vực phân trang và chọn số dòng hiển thị (giống Brand) -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <!-- Chọn giới hạn hiển thị dòng -->
            <form method="GET" action="/MINISHOP_TRANTHINGOCYEN/admin/category" id="limitForm" class="d-flex align-items-center gap-2">
                <!-- Giữ lại các giá trị tìm kiếm và lọc khi đổi limit -->
                <?php if (!empty($keyword)): ?>
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                <?php endif; ?>
                <?php if ($searchStatus !== ''): ?>
                    <input type="hidden" name="search_status" value="<?= htmlspecialchars($searchStatus) ?>">
                <?php endif; ?>

                <span>Hiển thị:</span>
                <select name="limit" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                    <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($limit == 20) ? 'selected' : '' ?>>20</option>
                    <option value="30" <?= ($limit == 30) ? 'selected' : '' ?>>30</option>
                </select>
            </form>

            <!-- Các nút phân trang -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <?php 
                            $queryParams = [];
                            if (!empty($keyword)) $queryParams['keyword'] = $keyword;
                            if ($searchStatus !== '') $queryParams['search_status'] = $searchStatus;
                            if ($limit != 10) $queryParams['limit'] = $limit;
                        ?>

                        <!-- Nút Trước -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <?php 
                                $queryParams['page'] = $page - 1;
                                $prevUrl = "/MINISHOP_TRANTHINGOCYEN/admin/category?" . http_build_query($queryParams);
                            ?>
                            <a class="page-link" href="<?= $prevUrl ?>">Trước</a>
                        </li>

                        <!-- Các số trang -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php 
                                $queryParams['page'] = $i;
                                $pageUrl = "/MINISHOP_TRANTHINGOCYEN/admin/category?" . http_build_query($queryParams);
                            ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pageUrl ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Nút Sau -->
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <?php 
                                $queryParams['page'] = $page + 1;
                                $nextUrl = "/MINISHOP_TRANTHINGOCYEN/admin/category?" . http_build_query($queryParams);
                            ?>
                            <a class="page-link" href="<?= $nextUrl ?>">Sau</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../../views/admin/layouts/master.php";
?>