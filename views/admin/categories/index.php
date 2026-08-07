<?php
// Bật hiển thị lỗi để dễ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/CategoryDAO.php";

$categoryDAO = new CategoryDAO();
$message = "";
$error = "";

// XỬ LÝ XÓA KHI NGƯỜI DÙNG NHẤN NÚT XÓA (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id > 0) {
        try {
            $result = $categoryDAO->delete($id);
            if ($result) {
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

// Kiểm tra thông báo thành công từ URL
if (isset($_GET['msg']) && $_GET['msg'] === 'delete_success') {
    $message = "Xóa danh mục thành công!";
}

// NHẬN TỪ KHÓA TỪ FORM GET
$keyword = "";
if (isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);
}

// Lấy danh sách danh mục theo từ khóa tìm kiếm
$categories = $categoryDAO->getAll($keyword);

// Đặt tiêu đề trang
$pageTitle = "Quản lý danh mục - Mini Shop";

// Bắt đầu buffer nội dung để truyền vào layout chung
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh mục sản phẩm</li>
    </ol>

    <!-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG HOẶC LỖI -->
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

    <!-- FORM TÌM KIẾM CƠ BẢN -->
    <form method="GET" class="row mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" placeholder="Nhập từ khóa..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </div>
    </form>

    <!-- Thẻ chứa bảng dữ liệu danh mục -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-list me-1"></i>
                Danh sách danh mục
            </div>
            <!-- Nút Thêm mới -->
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th class="text-start">Tên danh mục</th>
                            <th>Slug</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php $stt = 1; ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
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
                                            <a href="detail.php?id=<?= $cat->id ?>" class="btn btn-info text-white px-3 py-2" title="Chi tiết">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                            <a href="edit.php?id=<?= $cat->id ?>" class="btn btn-warning text-white px-3 py-2" title="Sửa">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa?');" style="display:inline-block; margin: 0;">
                                                <input type="hidden" name="id" value="<?= $cat->id ?>">
                                                <button type="submit" name="btnDelete" class="btn btn-danger px-3 py-2" title="Xóa">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Không tìm thấy dữ liệu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>