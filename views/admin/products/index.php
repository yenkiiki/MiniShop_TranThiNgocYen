<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/ProductDAO.php";

$productDAO = new ProductDAO();
$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($id > 0) {
        try {
            $result = $productDAO->delete($id);
            if ($result) {
                header("Location: index.php?msg=delete_success");
                exit();
            } else {
                $error = "Xóa sản phẩm thất bại. Vui lòng thử lại!";
            }
        } catch (Exception $e) {
            $error = "Không thể xóa sản phẩm này do ràng buộc dữ liệu!";
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'delete_success') {
    $message = "Xóa sản phẩm thành công!";
}

// 4. Đọc các tham số keyword, limit, page từ URL
$keyword = trim($_GET["keyword"] ?? "");
$limit = (int)($_GET["limit"] ?? 10);
if (!in_array($limit, [10, 20, 30])) {
    $limit = 10;
}

$page = (int)($_GET["page"] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// 5. Truyền thêm $keyword vào phương thức count() và getPage()
$totalRecords = $productDAO->count("products", "productname", $keyword);
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

if ($page > $totalPages) {
    $page = $totalPages;
}

$products = $productDAO->getPage($limit, $offset, $keyword);
$pageTitle = "Quản lý sản phẩm - Mini Shop";

ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
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

    <!-- 1. Tạo ô tìm kiếm -->
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="GET" class="d-flex">
                <input
                    type="text"
                    name="keyword"
                    value="<?= htmlspecialchars($keyword) ?>"
                    class="form-control"
                    placeholder="Nhập tên sản phẩm...">
                <!-- Giữ số sản phẩm/trang -->
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <button class="btn btn-primary ms-2 text-nowrap">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-box me-1"></i> Danh sách sản phẩm</div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
        <div class="card-body">
            
            <!-- Xử lý hiển thị khi có hoặc không có kết quả -->
            <?php if (!empty($keyword) && empty($products)): ?>
                <!-- Trường hợp: Có nhập từ khóa nhưng không tìm thấy sản phẩm -->
                <div class="alert alert-warning text-center my-3" role="alert">
                    Không tìm thấy sản phẩm phù hợp với từ khóa: <strong><?= htmlspecialchars($keyword) ?></strong>
                </div>
            <?php else: ?>
                <!-- Trường hợp thông thường hoặc tìm thấy sản phẩm -->
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
                                                <img src="../../../uploads/products/<?= htmlspecialchars($pro->image) ?>" alt=""
                                                    width="50" height="50" style="object-fit: cover;" class="rounded">
                                            <?php else: ?>
                                                <span class="text-muted small">Không có ảnh</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start fw-bold"><?= htmlspecialchars($pro->proName ?? $pro->productname ?? '') ?></td>
                                        <td><?= htmlspecialchars($pro->cateName ?? $pro->catename ?? '') ?></td>
                                        <td><?= htmlspecialchars($pro->brandName ?? $pro->brandname ?? '') ?></td>
                                        <td>
                                            <span class="text-danger fw-bold"><?= number_format($pro->price, 0, ',', '.') ?> đ</span>
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
                                                <a href="detail.php?id=<?= $pro->id ?>" class="btn btn-info text-white btn-sm" title="Chi tiết">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                                <a href="edit.php?id=<?= $pro->id ?>" class="btn btn-warning text-white btn-sm" title="Sửa">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');" style="display:inline-block; margin: 0;">
                                                    <input type="hidden" name="id" value="<?= $pro->id ?>">
                                                    <button type="submit" name="btnDelete" class="btn btn-danger btn-sm" title="Xóa">
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

                <!-- Khu vực chứa Select chọn số lượng hiển thị và Thanh phân trang -->
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

                    <!-- Thanh phân trang (Pagination) có đầy đủ nút Đầu, Cuối, Trước, Sau -->
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