<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";

$categoryDAO = new CategoryDAO();
$message = "";
$error = "";

// XỬ LÝ XÓA KHI NGƯỜI DÙNG NHẤN NÚT XÓA (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id > 0) {
        try {
            $catOld = $categoryDAO->findById($id);
            $result = $categoryDAO->delete($id);
            if ($result) {
                if ($catOld && !empty($catOld->image)) {
                    $imagePath = __DIR__ . "/../../../uploads/categories/" . $catOld->image;
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                }
                header("Location: index.php?msg=delete_success");
                exit();
            } else {
                $error = "Xóa danh mục thất bại. Vui lòng thử lại!";
            }
        } catch (Exception $e) {
            $error = "Không thể xóa danh mục này vì đang có sản phẩm liên kết!";
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'delete_success') {
    $message = "Xóa danh mục thành công!";
}

// 4. Đọc các tham số keyword, limit, page từ URL
$keyword = trim($_GET["keyword"] ?? "");
$limit = (int)($_GET["limit"] ?? 2);
if (!in_array($limit, [10, 20, 30])) {
    $limit = 2;
}

$page = (int)($_GET["page"] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// 5. Truyền $keyword vào count() và getPage()
$totalRecords = $categoryDAO->count($keyword);
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

if ($page > $totalPages) {
    $page = $totalPages;
}

$categories = $categoryDAO->getPage($limit, $offset, $keyword);
$pageTitle = "Quản lý danh mục - Mini Shop";

ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh mục sản phẩm</li>
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
            <form method="GET" class="d-flex">
                <input
                    type="text"
                    name="keyword"
                    value="<?= htmlspecialchars($keyword) ?>"
                    class="form-control"
                    placeholder="Nhập tên danh mục...">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <button class="btn btn-primary ms-2 text-nowrap">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-list me-1"></i>
                Danh sách danh mục
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
        <div class="card-body">
            
            <?php if (!empty($keyword) && empty($categories)): ?>
                <div class="alert alert-warning text-center my-3" role="alert">
                    Không tìm thấy danh mục phù hợp với từ khóa: <strong><?= htmlspecialchars($keyword) ?></strong>
                </div>
            <?php else: ?>
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
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?= $stt++ ?></td>
                                        <td>
                                            <?php if (!empty($cat->image)): ?>
                                                <img src="../../../uploads/categories/<?= htmlspecialchars($cat->image) ?>"
                                                    alt="<?= htmlspecialchars($cat->cateName) ?>"
                                                    class="img-thumbnail rounded shadow-sm"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border p-2">Không có</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start fw-bold"><?= htmlspecialchars($cat->cateName) ?></td>
                                        <td><code><?= htmlspecialchars($cat->slug ?? '') ?></code></td>
                                        <td>
                                            <?php if ($cat->status == 1): ?>
                                                <span class="badge bg-success fs-6">Hiển thị</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary fs-6">Ẩn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $cat->createdAt ?></td>
                                        <td class="text-nowrap">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a href="detail.php?id=<?= $cat->id ?>" class="btn btn-info text-white px-3 py-2 btn-sm"
                                                    title="Chi tiết">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                                <a href="edit.php?id=<?= $cat->id ?>" class="btn btn-warning text-white px-3 py-2 btn-sm"
                                                    title="Sửa">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');"
                                                    style="display:inline-block; margin: 0;">
                                                    <input type="hidden" name="id" value="<?= $cat->id ?>">
                                                    <button type="submit" name="btnDelete" class="btn btn-danger px-3 py-2 btn-sm"
                                                        title="Xóa">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Không tìm thấy dữ liệu.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Khu vực chọn số lượng hiển thị và phân trang -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center">
                        <label class="me-2">Hiển thị:</label>
                        <form method="GET">
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
                                <!-- Nút Trước -->
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">Trước</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Nút Sau -->
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?>">Sau</a>
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
include "../layouts/master.php";
?>