<?php
$user = $_SESSION["user"] ?? null;
?>
<div class="container-fluid d-flex justify-content-between align-items-center bg-white shadow-sm py-2 px-4">
    <div class="d-flex align-items-center">
        <h5 class="m-0 fw-bold text-primary">Mini Shop - Hệ thống Quản trị</h5>
    </div>

    <div class="d-flex align-items-center gap-3">
        <?php if ($user): ?>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user->fullName) ?>&background=0d6efd&color=fff&rounded=true&size=40" alt="Avatar" class="rounded-circle shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex flex-column text-end">
                    <span class="text-muted" style="font-size: 0.75rem;">Xin chào,</span>
                    <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                        <?= htmlspecialchars($user->fullName) ?>
                    </span>
                </div>
                <a href="logout.php" class="text-decoration-none text-danger fw-bold ms-2" style="font-size: 0.9rem;">
                    | Đăng xuất
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>