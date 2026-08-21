<div class="container py-5" style="max-width: 600px;">
    <h2 class="text-center mb-4">Đăng ký tài khoản</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="<?= BASE_URL ?>auth/login">Đăng nhập tại đây</a></div>
    <?php endif; ?>
    <form action="<?= BASE_URL ?>auth/register" method="POST">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Họ và tên *</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tên đăng nhập *</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu *</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <textarea name="address" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">Đăng ký</button>
        <p class="text-center mt-3">Đã có tài khoản? <a href="<?= BASE_URL ?>auth/login">Đăng nhập ngay</a></p>
    </form>
</div>