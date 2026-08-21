
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `brandname` varchar(100) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `brands` (`id`, `brandname`, `slug`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Logitech', 'logitech', 'logitech.png', 'Thương hiệu thiết bị ngoại vi Thụy Sĩ', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(2, 'Razer', 'razer', 'razer.png', 'Thương hiệu gaming gear hàng đầu', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(3, 'Corsair', 'corsair', 'corsair.png', 'Linh kiện và phụ kiện máy tính', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(4, 'Asus', 'asus', 'asus.png', 'Thương hiệu phần cứng toàn cầu', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(5, 'SteelSeries', 'steelseries', 'steelseries.png', 'Phụ kiện gaming chuyên nghiệp', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09');

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `catename` varchar(100) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `catename`, `slug`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Chuột Gaming', 'chuot-gaming', 'chuot.jpg', 'Các loại chuột chơi game cao cấp', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(2, 'Bàn phím Cơ', 'ban-phim-co', 'banphim.jpg', 'Bàn phím cơ cơ học chất lượng', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(3, 'Tai nghe', 'tai-nghe', 'tainghe.jpg', 'Tai nghe âm thanh vòm đỉnh cao', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(4, 'Màn hình', 'man-hinh', 'manhinh.jpg', 'Màn hình tần số quét cao', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(5, 'Lót chuột', 'lot-chuot', 'lotchuot.jpg', 'Tấm lót chuột chống trượt', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09');

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `customers` (`id`, `fullname`, `phone`, `email`, `address`, `note`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn Khách', '0988888881', 'khach1@gmail.com', '123 Lê Lợi, Q1, TPHCM', 'Giao giờ hành chính', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(2, 'Trần Văn Bình', '0988888882', 'binhtv@gmail.com', '456 Nguyễn Huệ, Q1, TPHCM', 'Gọi trước khi giao', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(3, 'Hoàng Thị Mai', '0988888883', 'maiht@gmail.com', '789 Điện Biên Phủ, Bình Thạnh', 'Để hàng ở bảo vệ', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(4, 'Đặng Văn Cường', '0988888884', 'cuongdv@gmail.com', '12 Cách Mạng Tháng 8, Q3', '', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(5, 'Vũ Bích Phương', '0988888885', 'phuongvb@gmail.com', '99 Hoàng Văn Thụ, Phú Nhuận', 'Giao sau 5h chiều', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_code` varchar(30) NOT NULL,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `shipping_fee` decimal(12,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'COD',
  `note` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `orders` (`id`, `customer_id`, `user_id`, `order_code`, `total_amount`, `shipping_fee`, `payment_method`, `note`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'ORD20260101', 2990000.00, 0.00, 'COD', 'Đơn hàng giao gấp', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(2, 2, 2, 'ORD20260102', 2400000.00, 0.00, 'Chuyển khoản', 'Khách hẹn lấy chiều', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(3, 3, 3, 'ORD20260103', 2850000.00, 30000.00, 'COD', '', 0, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(4, 4, NULL, 'ORD20260104', 8900000.00, 0.00, 'Chuyển khoản', 'Đơn tự đặt online', 0, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(5, 5, 3, 'ORD20260105', 530000.00, 30000.00, 'COD', '', 2, '2026-08-05 13:48:10', '2026-08-05 13:48:10');

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 1, 2990000.00, 2990000.00, '2026-08-05 13:48:10'),
(2, 2, 2, 1, 2400000.00, 2400000.00, '2026-08-05 13:48:10'),
(3, 3, 3, 1, 2850000.00, 2850000.00, '2026-08-05 13:48:10'),
(4, 4, 4, 1, 8900000.00, 8900000.00, '2026-08-05 13:48:10'),
(5, 5, 5, 1, 500000.00, 500000.00, '2026-08-05 13:48:10');


CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `proname` varchar(200) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `discount_price` decimal(10,0) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `proname`, `slug`, `price`, `discount_price`, `quantity`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Chuột Logitech G Pro X Superlight', 'chuot-logitech-g-pro-x-superlight', 3500000, 2990000, 20, 'gprox.jpg', 'Chuột siêu nhẹ dành cho game thủ', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(2, 2, 2, 'Bàn phím Razer BlackWidow V3', 'ban-phim-razer-blackwidow-v3', 2800000, 2400000, 15, 'blackwidow.jpg', 'Bàn phím cơ switch xanh', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(3, 3, 3, 'Tai nghe Corsair HS80 RGB', 'tai-nghe-corsair-hs80-rgb', 3200000, 2850000, 10, 'hs80.jpg', 'Tai nghe không dây âm thanh 7.1', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(4, 4, 4, 'Màn hình Asus ROG Strix XG27AQ', 'man-hinh-asus-rog-strix-xg27aq', 9500000, 8900000, 8, 'xg27aq.jpg', 'Màn hình 2K 170Hz IPS', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09'),
(5, 5, 5, 'Lót chuột SteelSeries QcK Heavy', 'lot-chuot-steelseries-qck-heavy', 600000, 500000, 50, 'qck.jpg', 'Lót chuột dày dặn êm ái', 1, '2026-08-05 13:48:09', '2026-08-05 13:48:09');


CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_images` (`id`, `product_id`, `image`, `sort_order`, `created_at`) VALUES
(1, 1, 'gprox_angle1.jpg', 1, '2026-08-05 13:48:09'),
(2, 1, 'gprox_angle2.jpg', 2, '2026-08-05 13:48:09'),
(3, 2, 'blackwidow_front.jpg', 1, '2026-08-05 13:48:09'),
(4, 3, 'hs80_side.jpg', 1, '2026-08-05 13:48:09'),
(5, 4, 'xg27aq_back.jpg', 1, '2026-08-05 13:48:09');



CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discount_percent` int(11) NOT NULL DEFAULT 0,
  `sale_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sales` (`id`, `product_id`, `discount_percent`, `sale_price`, `start_date`, `end_date`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 20, 2800000.00, '2026-08-01 00:00:00', '2026-08-31 23:59:59', 'Flash Sale Mùa Hè 20%', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(2, 2, 15, 2380000.00, '2026-08-01 00:00:00', '2026-08-31 23:59:59', 'Giảm giá hot trong tuần 15%', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(3, 3, 30, 2240000.00, '2026-08-01 00:00:00', '2026-08-31 23:59:59', 'Xả kho siêu ưu đãi 30%', 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10');

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` tinyint(4) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `email`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn Quản Trị', 'admin', '$2y$10$e846/cE1Q4g1O72P55.sCOEw0L5GjQ74j/kO63n7Z0Gg31MhK75jK', 'admin@gmail.com', '0901234567', 'TP. Hồ Chí Minh', 1, 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(2, 'Trần Thị Ngọc Yến', 'yentn', '$2y$10$e846/cE1Q4g1O72P55.sCOEw0L5GjQ74j/kO63n7Z0Gg31MhK75jK', 'yentn@gmail.com', '0912345678', 'Hà Nội', 0, 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(3, 'Lê Văn Nhân Viên', 'nhanvien1', '$2y$10$e846/cE1Q4g1O72P55.sCOEw0L5GjQ74j/kO63n7Z0Gg31MhK75jK', 'nv1@gmail.com', '0923456789', 'Đà Nẵng', 0, 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(4, 'Phạm Hoàng Nam', 'namph', '$2y$10$e846/cE1Q4g1O72P55.sCOEw0L5GjQ74j/kO63n7Z0Gg31MhK75jK', 'namph@gmail.com', '0934567890', 'Cần Thơ', 0, 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10'),
(5, 'Đỗ Hoàng Long', 'longdh', '$2y$10$e846/cE1Q4g1O72P55.sCOEw0L5GjQ74j/kO63n7Z0Gg31MhK75jK', 'longdh@gmail.com', '0945678901', 'Hải Phòng', 0, 1, '2026-08-05 13:48:10', '2026-08-05 13:48:10');

ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);


ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);


ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `fk_orders_customers` (`customer_id`),
  ADD KEY `fk_orders_users` (`user_id`);


ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_details_orders` (`order_id`),
  ADD KEY `fk_order_details_products` (`product_id`);


ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_products_categories` (`category_id`),
  ADD KEY `fk_products_brands` (`brand_id`);

ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_products` (`product_id`);

ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sales_products` (`product_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_details_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_details_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brands` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
