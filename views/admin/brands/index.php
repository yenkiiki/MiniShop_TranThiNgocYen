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

// XỬ LÝ XÓA THƯƠNG HIỆU (METHOD GET HOẶC POST)
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

// --- NHẬN TỪ KHÓA TỪ FORM TÌM KIẾM (METHOD GET) ---
$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

$brands = [];
try {
    $db = new Database();
    $conn = $db->getConnection();

    // Câu lệnh SQL linh hoạt kết hợp tìm kiếm và lọc trạng thái
    $sql = "SELECT id, brandname, slug, image, description, status, created_at, updated_at FROM brands WHERE 1=1";
    $params = [];
    $types = "";

    // 1. Tìm theo Tên thương hiệu hoặc Slug
    if (!empty($keyword)) {
        $sql .= " AND (brandname LIKE ? OR slug LIKE ?)";
        $searchTerm = "%" . $keyword . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }

    // 2. Lọc theo trạng thái
    if ($searchStatus !== "") {
        $sql .= " AND status = ?";
        $params[] = $searchStatus;
        $types .= "i";
    }

    $sql .= " ORDER BY brandname ASC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $brand = new Brand();
        $brand->id = (int)$row["id"];
        $brand->brandName = $row["brandname"];
        $brand->slug = $row["slug"];
        $brand->image = $row["image"];
        $brand->description = $row["description"];
        $brand->status = (int)$row["status"];
        $brand->createdAt = $row["created_at"];
        $brand->updatedAt = $row["updated_at"];
        $brands[] = $brand;
    }
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
                            <?php $stt = 1; ?>
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
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/master.php";
?>