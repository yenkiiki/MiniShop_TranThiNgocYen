<?php
// Bật hiển thị lỗi để dễ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi các DAO và Model cần thiết
require_once __DIR__ . "/../../../config/Database.php";
require_once __DIR__ . "/../../../dao/OrderDAO.php";

$orderDAO = new OrderDAO();
$message = "";
$error = "";

// Mảng định nghĩa trạng thái đơn hàng mới theo yêu cầu
$statusList = [
    0 => ['label' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
    1 => ['label' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
    2 => ['label' => 'Đang giao', 'class' => 'bg-primary text-white'],
    3 => ['label' => 'Hoàn thành', 'class' => 'bg-success text-white'],
    4 => ['label' => 'Đã hủy', 'class' => 'bg-danger text-white']
];

// XỬ LÝ CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG NHANH (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnUpdateStatus'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $status = isset($_POST['status']) ? (int) $_POST['status'] : 0;

    if ($id > 0) {
        try {
            $result = $orderDAO->updateStatus($id, $status);
            if ($result) {
                header("Location: index.php?msg=update_success");
                exit();
            } else {
                $error = "Cập nhật trạng thái thất bại!";
            }
        } catch (Exception $e) {
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'update_success') {
    $message = "Cập nhật trạng thái đơn hàng thành công!";
}

// --- NHẬN CÁC TIÊU CHÍ TỪ FORM TÌM KIẾM (METHOD GET) ---
$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$searchStatus = isset($_GET["search_status"]) && $_GET["search_status"] !== "" ? (int)$_GET["search_status"] : "";

$orders = [];
try {
    $db = new Database();
    $conn = $db->getConnection();

    // Xây dựng câu lệnh SQL cơ bản có sử dụng JOIN để lấy tên khách hàng và nhân viên
    $sql = "SELECT o.*, c.fullname as customer_name, u.fullname as user_name 
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            LEFT JOIN users u ON o.user_id = u.id WHERE 1=1";
    
    $params = [];
    $types = "";

    // 1. Tìm theo Mã đơn hàng hoặc Tên khách hàng (nếu có nhập keyword)
    if (!empty($keyword)) {
        $sql .= " AND (o.order_code LIKE ? OR c.fullname LIKE ?)";
        $searchTerm = "%" . $keyword . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }

    // 2. Tìm theo Trạng thái (nếu có chọn trạng thái)
    if ($searchStatus !== "") {
        $sql .= " AND o.status = ?";
        $params[] = $searchStatus;
        $types .= "i";
    }

    $sql .= " ORDER BY o.id DESC";

    // Chuẩn bị và thực thi câu lệnh với Prepared Statement để bảo mật
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} catch (Exception $e) {
    $error = "Lỗi tải dữ liệu: " . $e->getMessage();
}

$pageTitle = "Quản lý đơn hàng - Mini Shop";
ob_start();
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý đơn hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Danh sách đơn hàng</li>
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

  <!-- search -->
    <div class="card mb-4">
       
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Tìm kiếm theo Mã đơn hàng hoặc Tên khách hàng -->
                <div class="col-md-5">
                    <label class="form-label fw-bold">Từ khóa tìm kiếm:</label>
                    <input type="text" name="keyword" class="form-control" placeholder="Nhập mã đơn hàng hoặc tên khách hàng..." 
                           value="<?= htmlspecialchars($keyword) ?>">
                </div>

                <!-- Tìm kiếm theo Trạng thái -->
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

                <!-- Nút thao tác -->
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

    <!-- BẢNG HIỂN THỊ ĐƠN HÀNG -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-shopping-cart me-1"></i> Danh sách đơn hàng
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th class="text-start">Khách hàng</th>
                            <th class="text-start">Nhân viên xử lý</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php $stt = 1; ?>
                            <?php foreach ($orders as $od): ?>
                                <tr>
                                    <td><?= $stt++ ?></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($od['order_code']) ?></td>
                                    <td class="text-start">
                                        <?= htmlspecialchars($od['customer_name'] ?? 'Khách lẻ / Khách vãng lai') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($od['user_name'] ?? 'Chưa phân công') ?></td>
                                    <td><?= $od['created_at'] ?></td>
                                    <td><span class="text-danger fw-bold"><?= number_format($od['total_amount'], 0, ',', '.') ?> đ</span></td>
                                    <td>
                                        <?php
                                        $sttKey = (int) $od['status'];
                                        $badgeInfo = $statusList[$sttKey] ?? ['label' => 'Không rõ', 'class' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="detail.php?id=<?= $od['id'] ?>" class="btn btn-info text-white btn-sm" title="Chi tiết">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>

                                        <button type="button" class="btn btn-warning text-white btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#statusModal<?= $od['id'] ?>" title="Cập nhật trạng thái">
                                            <i class="fas fa-edit"></i> Trạng thái
                                        </button>

                                        <!-- Modal Cập nhật Trạng Thái -->
                                        <div class="modal fade text-start" id="statusModal<?= $od['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="" method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cập nhật trạng thái đơn: <?= htmlspecialchars($od['order_code']) ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $od['id'] ?>">
                                                            <div class="mb-3 p-2 bg-light border rounded">
                                                                <span class="fw-bold">Trạng thái hiện tại:</span>
                                                                <span class="badge <?= $badgeInfo['class'] ?>"><?= $badgeInfo['label'] ?></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Chọn trạng thái mới:</label>
                                                                <select name="status" class="form-select">
                                                                    <?php foreach ($statusList as $key => $value): ?>
                                                                        <option value="<?= $key ?>" <?= $od['status'] == $key ? 'selected' : '' ?>>
                                                                            <?= $value['label'] ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" name="btnUpdateStatus" class="btn btn-primary btn-sm">Lưu thay đổi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Hiển thị thông báo khi không tìm thấy dữ liệu -->
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