<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION["user"] ?? null;

// Lấy tên hiển thị an toàn dù session lưu dưới dạng mảng hay đối tượng
$userName = 'Admin';
if (is_array($user)) {
    $userName = $user['fullName'] ?? $user['username'] ?? 'Admin';
} elseif (is_object($user)) {
    $userName = $user->fullName ?? $user->fullname ?? $user->username ?? 'Admin';
}
?>
<div class="container-fluid d-flex justify-content-between align-items-center bg-white shadow-sm py-2 px-4">
    <div class="d-flex align-items-center">
        <h5 class="m-0 fw-bold text-primary">Mini Shop - Hệ thống Quản trị</h5>
    </div>

    <div class="d-flex align-items-center gap-3">
        <?php if (!empty($user)): ?>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&background=0d6efd&color=fff&rounded=true&size=40" alt="Avatar" class="rounded-circle shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex flex-column text-end">
                    <span class="text-muted" style="font-size: 0.75rem;">Xin chào,</span>
                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                        <?= htmlspecialchars($userName) ?>
                    </span>
                </div>
      <a href="/MINISHOP_TRANTHINGOCYEN/admin/logout">Đăng xuất</a>
            </div>
        <?php else: ?>
            <div class="d-flex align-items-center gap-2">
                <a href="/MINISHOP_TRANTHINGOCYEN/views/admin/login.php" class="btn btn-primary btn-sm px-4 fw-bold">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>