<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/UserDAO.php";

$userDAO = new UserDAO();
$message = "";
$error = "";

$roleList = [
    0 => ['label' => 'Thành viên', 'class' => 'bg-secondary text-white'],
    1 => ['label' => 'Quản trị viên', 'class' => 'bg-primary text-white']
];

$statusList = [
    0 => ['label' => 'Khóa', 'class' => 'bg-danger text-white'],
    1 => ['label' => 'Hoạt động', 'class' => 'bg-success text-white']
];

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        try {
            if ($userDAO->delete($id)) {
                header("Location: index.php?msg=delete_success");
                exit();
            } else {
                $error = "Xóa tài khoản thất bại!";
            }
        } catch (Exception $e) {
            $error = "Không thể xóa tài khoản này!";
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'delete_success') $message = "Xóa tài khoản thành công!";
    elseif ($_GET['msg'] === 'update_success') $message = "Cập nhật thành công!";
    elseif ($_GET['msg'] === 'insert_success') $message = "Thêm mới thành công!";
}

// Nhận tham số tìm kiếm, lọc, phân trang và limit
$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$searchRole = isset($_GET["search_role"]) && $_GET["search_role"] !== "" ? (int)$_GET["search_role"] : "";
$searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

// Giới hạn số bản ghi trên trang (Mặc định 10, hỗ trợ 10, 20, 30)
$limit = (int)($_GET["limit"] ?? 2);
if (!in_array($limit, [10, 20, 30])) {
    $limit = 2;
}

$page = (int)($_GET["page"] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Tính toán tổng số bản ghi và tổng số trang
$totalRecords = $userDAO->countSearch($keyword, $searchRole, $searchStatus);
$totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

if ($page > $totalPages) {
    $page = $totalPages;
}

// Lấy dữ liệu phân trang
$users = [];
try {
    $users = $userDAO->getPage($limit, $offset, $keyword, $searchRole, $searchStatus);
} catch (Exception $e) {
    $error = "Lỗi tải dữ liệu: " . $e->getMessage();
}

$pageTitle = "Quản lý tài khoản người dùng";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý tài khoản</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh sách tài khoản</li>
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

    <!-- Form tìm kiếm và lọc -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-search me-1"></i> Tìm kiếm & Lọc tài khoản</div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Giữ giá trị limit hiện tại khi tìm kiếm -->
                <input type="hidden" name="limit" value="<?= $limit ?>">

                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Họ tên, Username, Email, SĐT..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-md-3">
                    <select name="search_role" class="form-select">
                        <option value="">-- Tất cả vai trò --</option>
                        <?php foreach ($roleList as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($searchRole !== "" && $searchRole == $key) ? 'selected' : '' ?>><?= $value['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="search_status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($statusList as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($searchStatus !== "" && $searchStatus == $key) ? 'selected' : '' ?>><?= $value['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary me-1"><i class="fas fa-search"></i> Tìm</button>
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Làm mới</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-users me-1"></i> Danh sách tài khoản</div>
            <a href="create.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Thêm mới</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>ID</th>
                            <th class="text-start">Họ tên</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td><?= $user->id ?></td>
                                    <td class="text-start fw-bold text-primary"><?= htmlspecialchars($user->fullName) ?></td>
                                    <td><?= htmlspecialchars($user->userName) ?></td>
                                    <td><?= htmlspecialchars($user->email) ?></td>
                                    <td>
                                        <?php 
                                            $roleKey = $user->role;
                                            $roleInfo = $roleList[$roleKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $roleInfo['class'] ?>"><?= $roleInfo['label'] ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $sttKey = $user->status;
                                            $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($user->createdAt) ?></td>
                                    <td class="text-nowrap">
                                        <a href="detail.php?id=<?= $user->id ?>" class="btn btn-info text-white btn-sm"><i class="fas fa-eye"></i> Xem</a>
                                        <a href="edit.php?id=<?= $user->id ?>" class="btn btn-warning text-white btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                                        <a href="index.php?action=delete&id=<?= $user->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');"><i class="fas fa-trash"></i> Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-danger fw-bold py-4">Không tìm thấy dữ liệu.</td>
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
                        <?php if ($searchRole !== ""): ?>
                            <input type="hidden" name="search_role" value="<?= $searchRole ?>">
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
                                <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchRole !== "" ? '&search_role=' . $searchRole : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Trước</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchRole !== "" ? '&search_role=' . $searchRole : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Nút Sau -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?><?= !empty($keyword) ? '&keyword=' . urlencode($keyword) : '' ?><?= $searchRole !== "" ? '&search_role=' . $searchRole : '' ?><?= $searchStatus !== "" ? '&search_status=' . $searchStatus : '' ?>">Sau</a>
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