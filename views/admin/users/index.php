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

$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$searchRole = isset($_GET["search_role"]) && $_GET["search_role"] !== "" ? (int)$_GET["search_role"] : "";
$searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

$users = [];
try {
    $db = new Database();
    $conn = $db->getConnection();

    $sql = "SELECT id, fullname, username, email, phone, address, role, status, created_at, updated_at FROM users WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($keyword)) {
        $sql .= " AND (fullname LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $searchTerm = "%" . $keyword . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ssss";
    }

    if ($searchRole !== "") {
        $sql .= " AND role = ?";
        $params[] = $searchRole;
        $types .= "i";
    }

    if ($searchStatus !== "") {
        $sql .= " AND status = ?";
        $params[] = $searchStatus;
        $types .= "i";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $user = new User();
        $user->id = (int)$row["id"];
        $user->fullName = $row["fullname"];
        $user->userName = $row["username"];
        $user->email = $row["email"];
        $user->phone = $row["phone"] ?? '';
        $user->address = $row["address"] ?? '';
        $user->role = (int)$row["role"];
        $user->status = (int)$row["status"];
        $user->createdAt = $row["created_at"];
        $user->updatedAt = $row["updated_at"];
        $users[] = $user;
    }
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

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-search me-1"></i> Tìm kiếm</div>
        <div class="card-body">
            <form method="GET" class="row g-3">
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
                            <?php foreach ($users as $user): ?>
                                <tr>
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
                                <td colspan="8" class="text-center text-danger fw-bold py-4">Không tìm thấy dữ liệu.</td>
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