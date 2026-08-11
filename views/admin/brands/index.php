    <?php
    // Bật hiển thị lỗi để debug trong quá trình phát triển
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Gọi các DAO và Model cần thiết
    require_once __DIR__ . "/../../../config/Database.php";
    require_once __DIR__ . "/../../../dao/BrandDAO.php";

    $brandDAO = new BrandDAO();
    $message = "";
    $error = "";

    // Mảng định nghĩa trạng thái thương hiệu
    $statusList = [
        0 => ['label' => 'Ẩn / Khóa', 'class' => 'bg-danger text-white'],
        1 => ['label' => 'Đang hoạt động', 'class' => 'bg-success text-white']
    ];

    // XỬ LÝ XÓA THƯƠNG HIỆU
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            try {
                $result = $brandDAO->delete($id);
                if ($result) {
                    header("Location: index.php?msg=delete_success");
                    exit();
                } else {
                    $error = "Xóa thương hiệu thất bại!";
                }
            } catch (Exception $e) {
                $error = "Không thể xóa thương hiệu này vì đang có sản phẩm liên kết!";
            }
        }
    }

    // Kiểm tra thông báo từ URL
    if (isset($_GET['msg'])) {
        if ($_GET['msg'] === 'delete_success') {
            $message = "Xóa thương hiệu thành công!";
        } elseif ($_GET['msg'] === 'update_success') {
            $message = "Cập nhật thương hiệu thành công!";
        } elseif ($_GET['msg'] === 'insert_success') {
            $message = "Thêm mới thương hiệu thành công!";
        }
    }

    // --- NHẬN CÁC THAM SỐ TÌM KIẾM, LỌC, PHÂN TRANG VÀ LIMIT ---
    $keyword = trim($_GET["keyword"] ?? "");
    $searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

    $limit = (int)($_GET["limit"] ?? 2);
    if (!in_array($limit, [10, 20, 30])) {
        $limit = 2;
    }

    $page = (int)($_GET["page"] ?? 1);
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // Tính toán tổng số bản ghi và tổng số trang dựa trên điều kiện tìm kiếm/lọc
    $totalRecords = $brandDAO->count("brands", "brandname", $keyword, $searchStatus);
    $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    // Lấy dữ liệu phân trang
    $brands = [];
    try {
        $brands = $brandDAO->getPage($limit, $offset, $keyword, $searchStatus);
    } catch (Exception $e) {
        $error = "Lỗi tải dữ liệu: " . $e->getMessage();
    }

    // Tiêu đề trang
    $pageTitle = "Quản lý thương hiệu - Mini Shop";
    ob_start();
    ?>

    <div class="container-fluid px-4">
        <h1 class="mt-4">Quản lý thương hiệu</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Danh sách thương hiệu</li>
        </ol>

        <!-- THÔNG BÁO THÀNH CÔNG / LỖI -->
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

        <!-- FORM TÌM KIẾM VÀ BỘ LỌC -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-search me-1"></i> Tìm kiếm & Lọc thương hiệu</div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <!-- Giữ giá trị limit hiện tại khi tìm kiếm -->
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Từ khóa:</label>
                        <input type="text" name="keyword" class="form-control" placeholder="Nhập tên thương hiệu hoặc slug..." 
                            value="<?= htmlspecialchars($keyword) ?>">
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
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Làm mới
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- BẢNG HIỂN THỊ DANH SÁCH -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><i class="fas fa-tags me-1"></i> Danh sách thương hiệu</div>
                <a href="create.php" class="btn btn-success btn-sm">
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
                                                <img src="../../../uploads/brands/<?= htmlspecialchars($brand->image) ?>" alt="Logo" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
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
                                            <a href="edit.php?id=<?= $brand->id ?>" class="btn btn-warning text-white btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="index.php?action=delete&id=<?= $brand->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?');" title="Xóa">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
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

                <!-- THANH PHÂN TRANG VÀ CHỌN SỐ LƯỢNG HIỂN THỊ -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Hiển thị:</label>
                        <form method="GET">
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

                    <!-- Thanh phân trang (Pagination) -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mb-0">
                            <ul class="pagination mb-0">
                                <!-- Nút Trước -->
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Trước</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Nút Sau -->
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Sau</a>
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
    include "../layouts/master.php";
    ?>