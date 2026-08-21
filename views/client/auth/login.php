<div class="container py-5" style="max-width: 480px;">
    <div class="card border rounded shadow-sm p-4">
        <h3 class="text-center fw-bold mb-3">Đăng nhập tài khoản</h3>
        
        <?php if (!empty($_SESSION['login_notice'])): ?>
            <div class="alert alert-warning py-2 small mb-3 text-center fw-semibold">
                <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($_SESSION['login_notice']) ?>
            </div>
            <?php unset($_SESSION['login_notice']); ?>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>auth/login" method="POST">
            <?= csrf_field() ?>
            <?php 
            $redirectTarget = $_POST['redirect'] ?? ($_GET['redirect'] ?? ($_SESSION['redirect_after_login'] ?? ''));
            ?>
            <?php if (!empty($redirectTarget)): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập..." required autofocus>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Đăng nhập và tiếp tục</button>
            
            <div class="text-center mt-3 small">
                <span>Chưa có tài khoản? </span>
                <a href="<?= BASE_URL ?>auth/register" class="fw-bold text-decoration-none">Đăng ký ngay</a>
            </div>
        </form>
    </div>
</div>