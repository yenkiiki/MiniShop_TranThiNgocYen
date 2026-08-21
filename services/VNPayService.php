<?php
namespace Services;

class VNPayService
{
    // Thông tin cấu hình môi trường TEST VNPay (Sandbox)
    public const TMN_CODE = 'P8G7KN5A';
    public const HASH_SECRET = 'DMMTITJHSIWHKKABNPHBLLJKNHSYDFUX';
    public const VNP_URL = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    public const ADMIN_EMAIL = 'tranngocyen280905@gmail.com';

    /**
     * Tạo URL chuyển hướng sang cổng thanh toán VNPay
     *
     * @param string $orderCode Mã đơn hàng (vnp_TxnRef)
     * @param float $totalAmount Tổng tiền thanh toán (VNĐ)
     * @param string $orderInfo Nội dung mô tả thanh toán
     * @param string $returnUrl URL nhận kết quả trả về từ VNPay
     * @param string $bankCode Mã ngân hàng (tùy chọn: VNPAYQR, VNBANK, INTCARD...)
     * @return string URL thanh toán VNPay
     */
    public static function createPaymentUrl(
        string $orderCode,
        float $totalAmount,
        string $orderInfo = '',
        string $returnUrl = '',
        string $bankCode = ''
    ): string {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        if (empty($returnUrl)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = defined('BASE_URL') ? BASE_URL : '/MiniShop_TranThiNgocYen/';
            $returnUrl = $protocol . $host . rtrim($baseUrl, '/') . '/cart/vnpayReturn';
        }

        if (empty($orderInfo)) {
            $orderInfo = "Thanh toan don hang " . $orderCode;
        }

        $vnp_Amount = (int) round($totalAmount * 100); // VNPay yêu cầu nhân 100
        $vnp_CreateDate = date('YmdHis');
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));
        $vnp_IpAddr = self::getClientIp();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => self::TMN_CODE,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $returnUrl,
            "vnp_TxnRef" => $orderCode,
            "vnp_ExpireDate" => $vnp_ExpireDate
        ];

        if (!empty($bankCode)) {
            $inputData['vnp_BankCode'] = $bankCode;
        }

        // Sắp xếp tham số theo alphabet
        ksort($inputData);

        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = self::VNP_URL . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, self::HASH_SECRET);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * Xác thực chữ ký và dữ liệu phản hồi từ VNPay
     *
     * @param array $queryParams Dữ liệu $_GET từ VNPay trả về
     * @return array Kết quả xác thực [isValid, isSuccess, message, orderCode, amount, transNo, bankCode, payDate]
     */
    public static function verifyReturn(array $queryParams): array
    {
        $vnp_SecureHash = $queryParams['vnp_SecureHash'] ?? '';
        $inputData = [];

        foreach ($queryParams as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $i = 0;
        $hashData = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, self::HASH_SECRET);
        $isValid = (hash_equals($secureHash, $vnp_SecureHash));

        $responseCode = $queryParams['vnp_ResponseCode'] ?? '';
        $isSuccess = ($isValid && $responseCode === '00');
        $message = self::getResponseMessage($responseCode);

        $orderCode = $queryParams['vnp_TxnRef'] ?? '';
        $amount = isset($queryParams['vnp_Amount']) ? ((float) $queryParams['vnp_Amount'] / 100) : 0;
        $transNo = $queryParams['vnp_TransactionNo'] ?? '';
        $bankCode = $queryParams['vnp_BankCode'] ?? '';
        $payDate = $queryParams['vnp_PayDate'] ?? date('YmdHis');

        return [
            'isValid' => $isValid,
            'isSuccess' => $isSuccess,
            'responseCode' => $responseCode,
            'message' => $message,
            'orderCode' => $orderCode,
            'amount' => $amount,
            'transNo' => $transNo,
            'bankCode' => $bankCode,
            'payDate' => $payDate,
            'raw' => $queryParams
        ];
    }

    /**
     * Dịch mã phản hồi VNPay sang tiếng Việt dễ hiểu
     */
    public static function getResponseMessage(string $code): string
    {
        return match ($code) {
            '00' => 'Giao dịch thanh toán thành công qua VNPAY.',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản chưa đăng ký dịch vụ InternetBanking.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực OTP.',
            '24' => 'Giao dịch không thành công do: Khách hàng đã hủy giao dịch.',
            '51' => 'Giao dịch không thành công do: Tài khoản không đủ số dư để thực hiện.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Giao dịch thất bại (Lỗi không xác định hoặc hệ thống VNPay đang bảo trì).',
            default => 'Giao dịch không thành công (Mã phản hồi: ' . $code . ').'
        };
    }

    /**
     * Lấy IP người dùng thực tế
     */
    private static function getClientIp(): string
    {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'] 
            ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
            ?? $_SERVER['HTTP_X_FORWARDED'] 
            ?? $_SERVER['HTTP_FORWARDED_FOR'] 
            ?? $_SERVER['HTTP_FORWARDED'] 
            ?? $_SERVER['REMOTE_ADDR'] 
            ?? '127.0.0.1';
            
        // Nếu có nhiều IP (qua proxy) lấy IP đầu tiên
        if (strpos($ipAddress, ',') !== false) {
            $parts = explode(',', $ipAddress);
            $ipAddress = trim($parts[0]);
        }

        return ($ipAddress === '::1') ? '127.0.0.1' : $ipAddress;
    }
}
