<?php
namespace Services;

use Config\MailConfig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

class EmailService
{
    public const ADMIN_EMAIL = MailConfig::ADMIN_EMAIL;

    /**
     * Gửi email xác nhận đơn hàng tới khách hàng và quản trị viên shop qua Gmail SMTP
     *
     * @param object|array $order Thông tin đơn hàng
     * @param object|array $customer Thông tin khách hàng
     * @param array $orderDetails Danh sách chi tiết sản phẩm trong đơn hàng
     * @param array $vnpayData Dữ liệu giao dịch VNPay (nếu thanh toán qua VNPay)
     * @return array Kết quả gửi email [success => bool, message => string]
     */
    public static function sendOrderConfirmation($order, $customer, array $orderDetails = [], array $vnpayData = []): array
    {
        $recipientEmail = is_object($customer) ? ($customer->email ?? '') : ($customer['email'] ?? '');
        $customerName = is_object($customer) ? ($customer->fullName ?? $customer->fullname ?? 'Quý khách') : ($customer['fullname'] ?? $customer['fullName'] ?? 'Quý khách');
        $customerPhone = is_object($customer) ? ($customer->phone ?? '') : ($customer['phone'] ?? '');
        $customerAddress = is_object($customer) ? ($customer->address ?? '') : ($customer['address'] ?? '');

        $orderCode = is_object($order) ? ($order->orderCode ?? $order->order_code ?? 'N/A') : ($order['order_code'] ?? $order['orderCode'] ?? 'N/A');
        $totalAmount = is_object($order) ? (float)($order->totalAmount ?? $order->total_amount ?? 0) : (float)($order['total_amount'] ?? $order['totalAmount'] ?? 0);
        $shippingFee = is_object($order) ? (float)($order->shippingFee ?? $order->shipping_fee ?? 0) : (float)($order['shipping_fee'] ?? $order['shippingFee'] ?? 0);
        $paymentMethod = is_object($order) ? ($order->paymentMethod ?? $order->payment_method ?? 'COD') : ($order['payment_method'] ?? $order['paymentMethod'] ?? 'COD');
        $orderNote = is_object($order) ? ($order->note ?? '') : ($order['note'] ?? '');
        $createdAt = is_object($order) ? ($order->createdAt ?? $order->created_at ?? date('Y-m-d H:i:s')) : ($order['created_at'] ?? $order['createdAt'] ?? date('Y-m-d H:i:s'));

        $subtotal = 0;
        $itemsHtml = '';
        $stt = 1;

        foreach ($orderDetails as $item) {
            $proName = is_object($item) ? ($item->productName ?? $item->proname ?? ('Sản phẩm #' . ($item->productId ?? ''))) : ($item['productname'] ?? $item['proname'] ?? ('Sản phẩm #' . ($item['product_id'] ?? '')));
            $quantity = is_object($item) ? (int)($item->quantity ?? 1) : (int)($item['quantity'] ?? 1);
            $price = is_object($item) ? (float)($item->price ?? 0) : (float)($item['price'] ?? 0);
            $itemSubtotal = is_object($item) ? (float)($item->subtotal ?? ($price * $quantity)) : (float)($item['subtotal'] ?? ($price * $quantity));
            $subtotal += $itemSubtotal;

            $itemsHtml .= "
            <tr style='border-bottom: 1px solid #e5e7eb;'>
                <td style='padding: 10px; text-align: center; color: #6b7280;'>{$stt}</td>
                <td style='padding: 10px; font-weight: 600; color: #1f2937;'>{$proName}</td>
                <td style='padding: 10px; text-align: center; font-weight: 600;'>{$quantity}</td>
                <td style='padding: 10px; text-align: right; color: #4b5563;'>" . number_format($price, 0, ',', '.') . " đ</td>
                <td style='padding: 10px; text-align: right; font-weight: 600; color: #dc2626;'>" . number_format($itemSubtotal, 0, ',', '.') . " đ</td>
            </tr>";
            $stt++;
        }

        // Xác định nhãn phương thức thanh toán
        $isVNPay = (strtoupper($paymentMethod) === 'VNPAY' || !empty($vnpayData));
        $isBank = ($paymentMethod === 'ChuyenKhoan' || $paymentMethod === 'Chuyển khoản');

        if ($isVNPay) {
            $paymentText = 'Cổng thanh toán điện tử VNPAY (Đã thanh toán thành công)';
            $badgeColor = '#0284c7';
        } elseif ($isBank) {
            $paymentText = 'Chuyển khoản ngân hàng';
            $badgeColor = '#2563eb';
        } else {
            $paymentText = 'Thanh toán khi nhận hàng (COD)';
            $badgeColor = '#16a34a';
        }

        // Khối thông tin VNPay nếu có
        $vnpaySectionHtml = '';
        if ($isVNPay && !empty($vnpayData)) {
            $transNo = htmlspecialchars($vnpayData['transNo'] ?? ($vnpayData['vnp_TransactionNo'] ?? 'N/A'));
            $bankCode = htmlspecialchars($vnpayData['bankCode'] ?? ($vnpayData['vnp_BankCode'] ?? 'VNPAY'));
            $payDateRaw = $vnpayData['payDate'] ?? ($vnpayData['vnp_PayDate'] ?? date('YmdHis'));
            $formattedPayDate = (strlen($payDateRaw) === 14) 
                ? substr($payDateRaw, 6, 2) . '/' . substr($payDateRaw, 4, 2) . '/' . substr($payDateRaw, 0, 4) . ' ' . substr($payDateRaw, 8, 2) . ':' . substr($payDateRaw, 10, 2) . ':' . substr($payDateRaw, 12, 2)
                : date('d/m/Y H:i:s');

            $vnpaySectionHtml = "
            <div style='background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1.5px solid #0284c7; padding: 18px; border-radius: 8px; margin: 20px 0;'>
                <div style='display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px dashed #bae6fd; padding-bottom: 8px;'>
                    <h4 style='margin: 0; color: #0369a1; font-size: 16px;'>🌐 THÔNG TIN GIAO DỊCH VNPAY</h4>
                    <span style='background-color: #16a34a; color: #ffffff; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;'>ĐÃ THANH TOÁN</span>
                </div>
                <table style='width: 100%; font-size: 14px; color: #334155;'>
                    <tr>
                        <td style='padding: 4px 0; width: 45%; color: #64748b;'>Mã giao dịch VNPAY:</td>
                        <td style='padding: 4px 0; font-weight: bold; color: #0c4a6e;'>{$transNo}</td>
                    </tr>
                    <tr>
                        <td style='padding: 4px 0; color: #64748b;'>Ngân hàng thanh toán:</td>
                        <td style='padding: 4px 0; font-weight: bold; color: #0284c7;'>{$bankCode}</td>
                    </tr>
                    <tr>
                        <td style='padding: 4px 0; color: #64748b;'>Số tiền đã thanh toán:</td>
                        <td style='padding: 4px 0; font-weight: bold; color: #dc2626; font-size: 15px;'>" . number_format($totalAmount, 0, ',', '.') . " đ</td>
                    </tr>
                    <tr>
                        <td style='padding: 4px 0; color: #64748b;'>Thời gian giao dịch:</td>
                        <td style='padding: 4px 0; color: #334155;'>{$formattedPayDate}</td>
                    </tr>
                    <tr>
                        <td style='padding: 4px 0; color: #64748b;'>Trạng thái giao dịch:</td>
                        <td style='padding: 4px 0; font-weight: bold; color: #16a34a;'>Hợp lệ - Thành công (Mã 00)</td>
                    </tr>
                </table>
            </div>";
        } elseif ($isBank) {
            $vnpaySectionHtml = "
            <div style='background-color: #eff6ff; border: 1px dashed #3b82f6; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                <h4 style='margin: 0 0 8px 0; color: #1d4ed8;'>💳 Thông tin chuyển khoản ngân hàng:</h4>
                <p style='margin: 2px 0;'><strong>Ngân hàng:</strong> Vietcombank</p>
                <p style='margin: 2px 0;'><strong>Số tài khoản:</strong> 999988886666</p>
                <p style='margin: 2px 0;'><strong>Chủ tài khoản:</strong> TRAN THI NGOC YEN</p>
                <p style='margin: 2px 0;'><strong>Nội dung CK:</strong> <span style='background: #dbeafe; padding: 2px 6px; border-radius: 4px; font-weight: bold;'>DH {$orderCode} {$customerPhone}</span></p>
            </div>";
        }

        $subjectText = $isVNPay 
            ? "[VNPAY THÀNH CÔNG] Đơn hàng #{$orderCode} - " . number_format($totalAmount, 0, ',', '.') . " đ"
            : "Xác nhận đơn hàng #{$orderCode} - Mini Shop";

        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$subjectText}</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; background-color: #f1f5f9; margin: 0; padding: 20px;'>
            <div style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0;'>
                <!-- Header -->
                <div style='background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; padding: 28px 24px; text-align: center;'>
                    <h1 style='margin: 0; font-size: 24px; letter-spacing: 1px;'>🛒 MINISHOP</h1>
                    <p style='margin: 6px 0 0 0; font-size: 14px; opacity: 0.9;'>HỆ THỐNG MỸ PHẨM & CHĂM SÓC DA CHÍNH HÃNG</p>
                </div>

                <div style='padding: 24px;'>
                    <div style='margin-bottom: 20px;'>
                        <p style='font-size: 16px; margin-top: 0; color: #1e293b;'>Xin chào <strong>{$customerName}</strong>,</p>
                        <p style='color: #475569; margin-bottom: 0;'>Đơn hàng <strong>#{$orderCode}</strong> đã được hệ thống tiếp nhận thành công vào lúc <strong>{$createdAt}</strong>.</p>
                    </div>

                    {$vnpaySectionHtml}

                    <!-- Customer Info -->
                    <div style='background-color: #f8fafc; border-left: 4px solid {$badgeColor}; padding: 15px; margin: 20px 0; border-radius: 4px;'>
                        <h3 style='margin: 0 0 10px 0; color: #1e293b; font-size: 15px;'>📍 THÔNG TIN NHẬN HÀNG</h3>
                        <p style='margin: 4px 0; color: #334155;'><strong>Người nhận:</strong> {$customerName}</p>
                        <p style='margin: 4px 0; color: #334155;'><strong>Số điện thoại:</strong> {$customerPhone}</p>
                        <p style='margin: 4px 0; color: #334155;'><strong>Địa chỉ giao hàng:</strong> {$customerAddress}</p>
                        <p style='margin: 4px 0; color: #334155;'><strong>Hình thức thanh toán:</strong> <span style='color: {$badgeColor}; font-weight: bold;'>{$paymentText}</span></p>
                        " . (!empty($orderNote) ? "<p style='margin: 4px 0; color: #334155;'><strong>Ghi chú:</strong> {$orderNote}</p>" : "") . "
                    </div>

                    <!-- Products Table -->
                    <h3 style='color: #1e293b; font-size: 15px; margin-bottom: 10px;'>📦 CHI TIẾT SẢN PHẨM</h3>
                    <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;'>
                        <thead>
                            <tr style='background-color: #f1f5f9; color: #475569;'>
                                <th style='padding: 10px; text-align: center; width: 40px; border-bottom: 2px solid #cbd5e1;'>#</th>
                                <th style='padding: 10px; text-align: left; border-bottom: 2px solid #cbd5e1;'>Sản phẩm</th>
                                <th style='padding: 10px; text-align: center; width: 60px; border-bottom: 2px solid #cbd5e1;'>SL</th>
                                <th style='padding: 10px; text-align: right; width: 110px; border-bottom: 2px solid #cbd5e1;'>Đơn giá</th>
                                <th style='padding: 10px; text-align: right; width: 120px; border-bottom: 2px solid #cbd5e1;'>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemsHtml}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='4' style='padding: 10px; text-align: right; font-weight: 600; color: #64748b;'>Tiền hàng:</td>
                                <td style='padding: 10px; text-align: right; font-weight: 600; color: #334155;'>" . number_format($subtotal, 0, ',', '.') . " đ</td>
                            </tr>
                            <tr>
                                <td colspan='4' style='padding: 10px; text-align: right; font-weight: 600; color: #64748b;'>Phí vận chuyển:</td>
                                <td style='padding: 10px; text-align: right; font-weight: 600; color: #0284c7;'>" . ($shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . ' đ' : 'Miễn phí') . "</td>
                            </tr>
                            <tr style='background-color: #fef2f2;'>
                                <td colspan='4' style='padding: 12px 10px; text-align: right; font-weight: bold; color: #991b1b; font-size: 15px;'>Tổng thanh toán:</td>
                                <td style='padding: 12px 10px; text-align: right; font-weight: bold; color: #dc2626; font-size: 17px;'>" . number_format($totalAmount, 0, ',', '.') . " đ</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 13px;'>
                        <p style='margin: 4px 0;'>Quản trị viên shop: <strong>" . MailConfig::ADMIN_EMAIL . "</strong></p>
                        <p style='margin: 4px 0;'>Hotline hỗ trợ 24/7: <strong>0901 234 567</strong></p>
                        <p style='margin: 4px 0; color: #94a3b8;'>Mini Shop - Trân trọng cảm ơn quý khách!</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        // 1. Luôn lưu bản sao HTML vào storage/emails
        $storageDir = __DIR__ . '/../storage/emails';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        $logFile = $storageDir . '/order_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $orderCode) . '_' . date('Ymd_His') . '.html';
        @file_put_contents($logFile, $body);

        // 2. Gửi qua PHPMailer (Gmail SMTP)
        $adminMailSent = false;
        $customerMailSent = false;
        $smtpError = null;

        // Danh sách người nhận: luôn gửi tới admin, và gửi thêm cho khách nếu có email
        $recipients = [
            ['email' => MailConfig::ADMIN_EMAIL, 'name' => 'Quản trị viên MiniShop']
        ];
        if (!empty($recipientEmail) && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL) && strtolower($recipientEmail) !== strtolower(MailConfig::ADMIN_EMAIL)) {
            $recipients[] = ['email' => $recipientEmail, 'name' => $customerName];
        }

        foreach ($recipients as $target) {
            $sendResult = self::sendViaPHPMailer($target['email'], $target['name'], $subjectText, $body);
            if ($target['email'] === MailConfig::ADMIN_EMAIL) {
                $adminMailSent = $sendResult['success'];
            } else {
                $customerMailSent = $sendResult['success'];
            }
            if (!$sendResult['success']) {
                $smtpError = $sendResult['error'];
            }
        }

        return [
            'success' => ($adminMailSent || $customerMailSent),
            'admin_mail_sent' => $adminMailSent,
            'customer_mail_sent' => $customerMailSent,
            'admin_recipient' => MailConfig::ADMIN_EMAIL,
            'customer_recipient' => $recipientEmail,
            'log_file' => $logFile,
            'smtp_error' => $smtpError
        ];
    }

    /**
     * Gửi email sử dụng thư viện PHPMailer kết nối Gmail SMTP
     */
    private static function sendViaPHPMailer(string $toEmail, string $toName, string $subject, string $htmlBody): array
    {
        // Kiểm tra xem đã nạp class PHPMailer chưa
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once __DIR__ . '/../vendor/autoload.php';
            }
        }

        try {
            $mail = new PHPMailer(true);

            // Cấu hình máy chủ SMTP
            $mail->isSMTP();
            $mail->Host       = MailConfig::SMTP_HOST;
            $mail->SMTPAuth   = MailConfig::SMTP_AUTH;
            $mail->Username   = MailConfig::SMTP_USER;
            $mail->Password   = MailConfig::SMTP_PASS;
            
            if (MailConfig::SMTP_PORT == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port       = MailConfig::SMTP_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->Timeout    = 15; // 15 giây timeout
            
            // Bật xác thực SSL linh hoạt cho môi trường local XAMPP
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Thiết lập người gửi & người nhận
            $mail->setFrom(MailConfig::FROM_EMAIL, MailConfig::FROM_NAME);
            $mail->addReplyTo(MailConfig::ADMIN_EMAIL, MailConfig::FROM_NAME);
            $mail->addAddress($toEmail, $toName);

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();

            // Ghi log thành công
            self::logMail("SUCCESS: Gửi email thành công tới {$toEmail} (Tiêu đề: {$subject})");
            return ['success' => true, 'error' => null];

        } catch (MailerException $e) {
            $errMessage = "PHPMailer Error: " . $e->getMessage();
            self::logMail("FAILED: Không thể gửi email tới {$toEmail} - {$errMessage}");
            return ['success' => false, 'error' => $errMessage];
        } catch (\Throwable $e) {
            $errMessage = "General Error: " . $e->getMessage();
            self::logMail("FAILED: Lỗi hệ thống khi gửi email tới {$toEmail} - {$errMessage}");
            return ['success' => false, 'error' => $errMessage];
        }
    }

    /**
     * Ghi nhật ký trạng thái gửi email
     */
    private static function logMail(string $message): void
    {
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/mail.log';
        $logLine = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        @file_put_contents($logFile, $logLine, FILE_APPEND);
    }
}
