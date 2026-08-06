<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Hệ Thống Quản Trị - Admin Dashboard' ?></title>

    <!-- FontAwesome 6 Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
<!-- Sửa đường dẫn Bootstrap CSS thành tuyệt đối -->
<link rel="stylesheet" href="/MINISHOP_TRANTHINGOCYEN/assets/bootstrap.min.css">

<!-- Sửa đường dẫn FontAwesome hoặc file CSS custom nếu có -->
<link rel="stylesheet" href="/MINISHOP_TRANTHINGOCYEN/assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom border-secondary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary" href="index.php?view=dashboard">
            <i class="fa-solid fa-cart-shopping me-2"></i>ADMIN PANEL
        </a>
        <div class="d-flex align-items-center ms-auto">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D6EFD&color=fff" class="rounded-circle me-2" width="32" height="32" alt="Avatar">
                    <span>Xin chào, <strong>Quản trị viên</strong></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i>Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear me-2"></i>Cài đặt</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>