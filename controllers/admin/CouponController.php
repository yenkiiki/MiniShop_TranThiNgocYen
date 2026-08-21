<?php
namespace Controllers\Admin;

use DAO\CouponDAO;
use Models\Coupon;
use Exception;

class CouponController
{
    private CouponDAO $couponDAO;

    public function __construct()
    {
        $this->couponDAO = new CouponDAO();
    }

    /**
     * 1. Danh sách mã giảm giá
     */
    public function index()
    {
        $message = "";
        $error = "";

        // Xóa mã giảm giá khi nhận POST btnDelete
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete'])) {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if ($id > 0) {
                if ($this->couponDAO->delete($id)) {
                    header("Location: " . BASE_URL . "admin/coupon/index?msg=delete_success");
                    exit;
                } else {
                    $error = "Xóa mã giảm giá thất bại. Vui lòng thử lại!";
                }
            }
        }

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'delete_success') $message = "Xóa mã giảm giá thành công!";
            if ($_GET['msg'] === 'create_success') $message = "Tạo mã giảm giá mới thành công!";
            if ($_GET['msg'] === 'update_success') $message = "Cập nhật mã giảm giá thành công!";
        }

        $keyword = trim($_GET["keyword"] ?? "");
        $limit = (int)($_GET["limit"] ?? 10);
        if (!in_array($limit, [10, 20, 30])) $limit = 10;

        $page = (int)($_GET["page"] ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $totalRecords = $this->couponDAO->count($keyword);
        $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;
        if ($page > $totalPages) $page = $totalPages;

        $coupons = $this->couponDAO->getPage($limit, $offset, $keyword);
        $pageTitle = "Quản lý Mã giảm giá (Coupons) - Admin";

        require_once __DIR__ . '/../../views/admin/coupons/index.php';
    }

    /**
     * 2. Thêm mới mã giảm giá
     */
    public function create()
    {
        $errors = [];
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $code = strtoupper(trim($_POST["code"] ?? ""));
            $name = trim($_POST["name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $discountType = in_array($_POST["discount_type"] ?? '', ['fixed', 'percent']) ? $_POST["discount_type"] : 'fixed';
            $discountValue = (float)($_POST["discount_value"] ?? 0);
            $minOrderAmount = (float)($_POST["min_order_amount"] ?? 0);
            $maxDiscountAmount = (!empty($_POST["max_discount_amount"])) ? (float)$_POST["max_discount_amount"] : null;
            $usageLimit = (int)($_POST["usage_limit"] ?? 100);
            $startDate = !empty($_POST["start_date"]) ? $_POST["start_date"] : null;
            $endDate = !empty($_POST["end_date"]) ? $_POST["end_date"] : null;
            $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

            if (empty($code)) {
                $errors[] = "Mã voucher không được để trống.";
            } elseif ($this->couponDAO->findByCode($code)) {
                $errors[] = "Mã voucher này đã tồn tại trong hệ thống, vui lòng chọn mã khác.";
            }

            if (empty($name)) {
                $errors[] = "Tên mã voucher không được để trống.";
            }

            if ($discountValue <= 0) {
                $errors[] = "Giá trị giảm phải lớn hơn 0.";
            }

            if ($discountType === 'percent' && $discountValue > 100) {
                $errors[] = "Giảm theo phần trăm không được vượt quá 100%.";
            }

            if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
                $errors[] = "Ngày bắt đầu không được lớn hơn ngày kết thúc.";
            }

            if (empty($errors)) {
                $coupon = new Coupon([
                    'code' => $code,
                    'name' => $name,
                    'description' => $description,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'min_order_amount' => $minOrderAmount,
                    'max_discount_amount' => $maxDiscountAmount,
                    'usage_limit' => $usageLimit,
                    'used_count' => 0,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status
                ]);

                $newId = $this->couponDAO->insert($coupon);
                if ($newId) {
                    header("Location: " . BASE_URL . "admin/coupon/index?msg=create_success");
                    exit;
                } else {
                    $errors[] = "Không thể lưu mã giảm giá. Vui lòng thử lại!";
                }
            }
        }

        $pageTitle = "Thêm mã giảm giá mới - Admin";
        require_once __DIR__ . '/../../views/admin/coupons/create.php';
    }

    /**
     * 3. Chỉnh sửa mã giảm giá
     */
    public function edit()
    {
        $id = (int)($_GET["id"] ?? 0);
        if ($id <= 0) {
            header("Location: " . BASE_URL . "admin/coupon/index");
            exit;
        }

        $coupon = $this->couponDAO->findById($id);
        if (!$coupon) {
            header("Location: " . BASE_URL . "admin/coupon/index?msg=not_found");
            exit;
        }

        $errors = [];
        $message = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $code = strtoupper(trim($_POST["code"] ?? ""));
            $name = trim($_POST["name"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $discountType = in_array($_POST["discount_type"] ?? '', ['fixed', 'percent']) ? $_POST["discount_type"] : 'fixed';
            $discountValue = (float)($_POST["discount_value"] ?? 0);
            $minOrderAmount = (float)($_POST["min_order_amount"] ?? 0);
            $maxDiscountAmount = (!empty($_POST["max_discount_amount"])) ? (float)$_POST["max_discount_amount"] : null;
            $usageLimit = (int)($_POST["usage_limit"] ?? 100);
            $usedCount = (int)($_POST["used_count"] ?? $coupon->usedCount);
            $startDate = !empty($_POST["start_date"]) ? $_POST["start_date"] : null;
            $endDate = !empty($_POST["end_date"]) ? $_POST["end_date"] : null;
            $status = isset($_POST["status"]) ? (int)$_POST["status"] : 1;

            if (empty($code)) {
                $errors[] = "Mã voucher không được để trống.";
            } else {
                $existC = $this->couponDAO->findByCode($code);
                if ($existC && $existC->id !== $id) {
                    $errors[] = "Mã voucher này đã được sử dụng cho một voucher khác.";
                }
            }

            if (empty($name)) {
                $errors[] = "Tên mã voucher không được để trống.";
            }

            if ($discountValue <= 0) {
                $errors[] = "Giá trị giảm phải lớn hơn 0.";
            }

            if ($discountType === 'percent' && $discountValue > 100) {
                $errors[] = "Giảm theo phần trăm không được vượt quá 100%.";
            }

            if (!empty($startDate) && !empty($endDate) && $startDate > $endDate) {
                $errors[] = "Ngày bắt đầu không được lớn hơn ngày kết thúc.";
            }

            if (empty($errors)) {
                $coupon->code = $code;
                $coupon->name = $name;
                $coupon->description = $description;
                $coupon->discountType = $discountType;
                $coupon->discountValue = $discountValue;
                $coupon->minOrderAmount = $minOrderAmount;
                $coupon->maxDiscountAmount = $maxDiscountAmount;
                $coupon->usageLimit = $usageLimit;
                $coupon->usedCount = $usedCount;
                $coupon->startDate = $startDate;
                $coupon->endDate = $endDate;
                $coupon->status = $status;

                if ($this->couponDAO->update($coupon)) {
                    header("Location: " . BASE_URL . "admin/coupon/index?msg=update_success");
                    exit;
                } else {
                    $errors[] = "Không thể cập nhật mã giảm giá. Vui lòng thử lại!";
                }
            }
        }

        $pageTitle = "Chỉnh sửa mã giảm giá - Admin";
        require_once __DIR__ . '/../../views/admin/coupons/edit.php';
    }

    /**
     * 4. Chi tiết mã giảm giá
     */
    public function detail()
    {
        $id = (int)($_GET["id"] ?? 0);
        if ($id <= 0) {
            header("Location: " . BASE_URL . "admin/coupon/index");
            exit;
        }

        $coupon = $this->couponDAO->findById($id);
        if (!$coupon) {
            header("Location: " . BASE_URL . "admin/coupon/index?msg=not_found");
            exit;
        }

        $pageTitle = "Chi tiết mã giảm giá #" . $coupon->code . " - Admin";
        require_once __DIR__ . '/../../views/admin/coupons/detail.php';
    }
}
