<?php
namespace Config;

class MailConfig
{
    // =========================================================================
    // CẤU HÌNH GMAIL SMTP GỬI THÔNG BÁO ĐƠN HÀNG THỰC TẾ
    // =========================================================================
    public const SMTP_HOST = 'smtp.gmail.com';
    public const SMTP_PORT = 587; // 587 (TLS) hoặc 465 (SSL)
    public const SMTP_SECURE = 'tls'; // 'tls' hoặc 'ssl'
    public const SMTP_AUTH = true;

    // Địa chỉ Gmail gửi đi
    public const SMTP_USER = 'tranngocyen280905@gmail.com';

    // MẬT KHẨU ỨNG DỤNG GMAIL (Google App Password)
    public const SMTP_PASS = 'qpdyoemxeywqtyun';

    // Tên hiển thị người gửi
    public const FROM_EMAIL = 'tranngocyen280905@gmail.com';
    public const FROM_NAME = 'MiniShop - Mỹ Phẩm & Chăm Sóc Da';

    // Gmail quản trị viên nhận thông báo mọi đơn hàng mới và thanh toán VNPay
    public const ADMIN_EMAIL = 'tranngocyen280905@gmail.com';
}
