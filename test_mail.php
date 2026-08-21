<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/services/EmailService.php';
$orderData = [
    'order_code' => 'TEST123',
    'total_amount' => 150000,
    'shipping_fee' => 0,
    'payment_method' => 'VNPAY',
    'note' => 'Test mail',
    'created_at' => date('Y-m-d H:i:s')
];

$customerData = [
    'fullname' => 'Nguyễn Thị Ngọc Yến',
    'phone' => '0123456789',
    'email' => 'tranngocyen280905@gmail.com',
    'address' => 'Hồ Chí Minh'
];

$result = \Services\EmailService::sendOrderConfirmation($orderData, $customerData, []);
echo "<pre>";
print_r($result);
echo "</pre>";