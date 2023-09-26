-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2023 at 07:07 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onlineshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `address2` text NOT NULL,
  `is_default` int(11) NOT NULL,
  `landmark` text NOT NULL,
  `pincode` text NOT NULL,
  `mobile` text NOT NULL,
  `availability` text NOT NULL,
  `address_type` text DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `user_id`, `name`, `address`, `address2`, `is_default`, `landmark`, `pincode`, `mobile`, `availability`, `address_type`, `created_date`) VALUES
(26, 5, 'Rajat', 'Aligarh', '', 0, '', '202001', '3423432', '', 'Home', '2023-04-03 23:05:44'),
(29, 5, 'Rajat Kumar', 'aligarh', '', 0, '', '202001', '32423434', '', 'Home', '2023-06-03 13:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `user_id`, `name`, `email`) VALUES
(1, 5, 'admin', 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `available_coupons`
--

CREATE TABLE `available_coupons` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL DEFAULT -1,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `validity` int(11) NOT NULL DEFAULT -1 COMMENT '-1 -> Lifetime, else number of days from created_date',
  `discount_type` varchar(10) NOT NULL COMMENT '% or FLAT',
  `discount` float NOT NULL DEFAULT 0,
  `coupon_code` varchar(50) DEFAULT NULL,
  `redeemed` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0->Not redeemed, 1->redeemed',
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `available_coupons`
--

INSERT INTO `available_coupons` (`id`, `item_id`, `coupon_id`, `user_id`, `validity`, `discount_type`, `discount`, `coupon_code`, `redeemed`, `created_date`) VALUES
(1, 6, 2, 5, -1, 'FLAT', 20, 'f3g5dh', 0, '2023-08-25 13:04:57'),
(2, -1, 3, 5, -1, '%', 0, 'f3g5d4', 0, '2023-08-25 13:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(10) NOT NULL,
  `brand_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`) VALUES
(1, 'hp'),
(2, 'dell'),
(3, 'asus'),
(4, 'samsung'),
(5, 'boat'),
(6, 'oppo'),
(7, 'cobb'),
(8, 'pantaloons'),
(9, 'raymonds'),
(10, 'sonata'),
(11, 'rayban'),
(12, 'addidas'),
(13, 'bata'),
(14, 'sony'),
(15, 'canon'),
(16, 'LG'),
(21, 'titan'),
(23, 'Apple'),
(24, 'Himalaya'),
(25, 'Fast Track'),
(26, 'Red Chief');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `save_type` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `item_id`, `user_id`, `qty`, `status`, `save_type`, `created_date`) VALUES
(1, 8, 5, 0, 0, 'CART', '2023-08-22 22:16:58'),
(2, 6, 5, 1, 0, 'CART', '2023-08-22 22:20:10'),
(3, 6, 5, 1, 1, 'WISHLIST', '2023-08-23 13:36:11'),
(4, 19, 5, 0, 0, 'CART', '2023-08-24 14:55:07'),
(5, 8, 5, 0, 0, 'WISHLIST', '2023-08-24 14:55:46'),
(6, 1, 5, 1, 0, 'CART', '2023-08-30 18:17:40'),
(7, 4, 5, 1, 0, 'CART', '2023-08-31 13:58:48'),
(8, 9, 5, 1, 0, 'CART', '2023-08-31 14:00:35'),
(9, 2, 5, 1, 1, 'WISHLIST', '2023-08-31 14:39:22'),
(10, 2, 5, 0, 0, 'CART', '2023-08-31 19:26:06'),
(11, 24, 5, 1, 0, 'CART', '2023-08-31 22:24:16'),
(12, 23, 5, 1, 1, 'WISHLIST', '2023-09-01 23:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `category_type` varchar(50) NOT NULL,
  `parent_category` varchar(100) DEFAULT NULL,
  `image_url` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_name`, `category_type`, `parent_category`, `image_url`, `created_date`) VALUES
(1, 'foot wear', 'sub_category', 'fashion', '/img/category7.jpg', '2023-08-14 20:34:45'),
(2, 'mens', 'sub_category', 'fashion', '/img/product-img1.jpg', '2023-08-14 20:34:45'),
(3, 'women', 'sub_category', 'fashion', '/img/product-img2.jpg', '2023-08-14 20:34:45'),
(4, 'kids', 'main', 'null', '', '2023-08-14 20:34:45'),
(5, 'fashion', 'main', 'null', '/img/item-1.png', '2023-08-14 20:34:45'),
(6, 'mobile phones', 'sub_category', 'Electronics', '/img/category2.png', '2023-08-14 20:34:45'),
(7, 'laptops', 'sub_category', 'Electronics', '/img/category3.png', '2023-08-14 20:34:45'),
(8, 'clothing', 'main', 'null', '/img/product-7.jpg', '2023-08-14 20:34:45'),
(9, 'camera', 'sub_category', 'Electronics', '/img/category4.png', '2023-08-14 20:34:45'),
(11, 'Beauty & makeup', 'main', 'null', '/img/floded-03.png', '2023-08-14 20:34:45'),
(13, 'Electronics', 'main', 'null', '/img/category6.png', '2023-08-14 20:34:45'),
(14, 'Gadgets', 'child_category', 'mens', '/img/category3.png', '2023-08-14 20:55:24');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `type` varchar(255) COLLATE latin1_danish_ci NOT NULL,
  `products_id` varchar(255) COLLATE latin1_danish_ci DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `coupon_code` varchar(255) COLLATE latin1_danish_ci NOT NULL,
  `discount` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cat_id` varchar(255) COLLATE latin1_danish_ci DEFAULT NULL,
  `site_id` int(11) NOT NULL,
  `service_id` varchar(255) COLLATE latin1_danish_ci DEFAULT NULL,
  `garment_id` varchar(255) COLLATE latin1_danish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_danish_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `type`, `products_id`, `start_date`, `end_date`, `coupon_code`, `discount`, `created_date`, `cat_id`, `site_id`, `service_id`, `garment_id`) VALUES
(5, '%', '368,372,484', '2020-06-05', '2027-06-15', 'F3VT3H', 5, '2022-03-05 04:12:26', NULL, 2, '483', NULL),
(6, 'Flat', '406', '2020-07-11', '2027-07-13', 'W3XZS2', 20, '2022-03-05 04:12:30', NULL, 2, '468', NULL),
(7, 'Flat', '368,372,434', '2020-07-23', '2020-08-13', 'NEXC8P', 10, '2021-02-13 12:47:49', NULL, 11, '484,474', ''),
(8, '%', '368,372,406,229,495,487,482', '2020-07-22', '2020-11-21', '027G8K', 20, '2021-02-13 12:47:49', NULL, 11, '468,472,469,480,474,495,512', '520'),
(9, 'Flat', '368,492', '2021-02-06', '2022-03-23', 'C6SZR6', 20, '2022-03-07 05:34:18', NULL, 11, '512', '513'),
(10, '%', '0', '2021-04-10', '2021-04-10', 'SALES123', 20, '2021-04-08 12:41:44', NULL, 2, NULL, NULL),
(12, '%', NULL, '2022-01-22', '2024-09-11', '96JLZV', 10, '2022-03-07 05:33:50', NULL, 3, NULL, NULL),
(13, 'Flat', '0', '2022-01-06', '2022-01-10', 'adsadsagfdsa', 21432, '2022-01-26 06:31:49', NULL, 2, NULL, NULL),
(16, 'Flat', '0', '2022-03-26', '2022-03-27', 'ddd', 22, '2022-03-20 08:05:41', NULL, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `msg_type` varchar(50) NOT NULL,
  `attachment` text NOT NULL,
  `status` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender`, `receiver`, `user_name`, `mobile`, `message`, `msg_type`, `attachment`, `status`, `created_date`) VALUES
(1, 'rajatagrawal4001@gmail.com', 'rajatagrawal9394@gmail.com', 'Rajat Agrawal', '', 'Hi..', 'message', '', 1, '2023-08-19 22:33:19'),
(2, '', 'rajatagrawal9394@gmail.com', '', '', '', 'message', '', 1, '2023-08-20 14:10:43'),
(3, 'prasoonkumar340@gmail.com', 'rajatagrawal9394@gmail.com', 'Rajat Agrawal', '', 'Hi....', 'message', '', 1, '2023-08-20 14:29:35'),
(4, 'prasoonkumar40@gmail.com', 'rajatagrawal9394@gmail.com', 'Rajat Agrawal', '', 'hello.', 'message', '', 1, '2023-08-20 14:32:41'),
(5, 'name@gmail.com', 'rajatagrawal9394@gmail.com', 'Rajat Agrawal', '', 'hggy', 'message', '', 1, '2023-08-20 14:40:50'),
(6, 'prasoonkumar340@gmail.com', 'rajatagrawal9394@gmail.com', 'Prasoon Kumar', '', 'gv', 'message', '', 1, '2023-08-20 14:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` text NOT NULL,
  `quantity` text NOT NULL,
  `address` text NOT NULL,
  `amount` int(11) NOT NULL COMMENT 'Contains Only Amount paid online _______ Total Amount Paid = amount + paid from ewallet & ebanking	',
  `coupon_id` text NOT NULL,
  `coupon_discount` float NOT NULL,
  `item_discount` float NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `item_id`, `quantity`, `address`, `amount`, `coupon_id`, `coupon_discount`, `item_discount`, `payment_status`, `payment_mode`, `order_date`, `order_status`) VALUES
(1, 5, '6', '1', '26', 402, '', 0, 47, 'PENDING', 'COD', '2023-08-29 17:52:01', 'PENDING'),
(3, 5, '1', '1', '26', 2360, '', 0, 40, 'PAID', 'RAZOR-PAY', '2023-08-30 12:50:18', 'PENDING'),
(4, 5, '4,9', '1,1', '26', 13650, '', 0, 49, 'PENDING', 'RAZOR-PAY', '2023-08-31 08:50:53', 'PENDING'),
(5, 5, '4,9', '1,1', '26', 13650, '', 0, 49, 'PAID', 'RAZOR-PAY', '2023-08-31 08:53:49', 'PENDING'),
(6, 5, '6', '1', '26', 402, '', 0, 47, 'PENDING', 'COD', '2023-08-31 14:00:55', 'PENDING'),
(7, 5, '24', '1', '29', 1375, '', 0, 25, 'PENDING', 'COD', '2023-09-02 07:29:06', 'PENDING'),
(8, 5, '1', '1', '26', 2360, '', 0, 40, 'PAID', 'RAZOR-PAY', '2023-09-25 16:39:15', 'PENDING');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `coupon_discount` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `user_id`, `order_id`, `item_id`, `quantity`, `price`, `discount`, `coupon_discount`, `status`, `order_date`) VALUES
(1, 5, 1, 6, 1, 402, 47, 0, 0, '2023-08-29 23:22:01'),
(2, 5, 3, 1, 1, 2360, 40, 0, 0, '2023-08-30 18:20:18'),
(3, 5, 4, 4, 1, 12473, 26, 0, 0, '2023-08-31 14:20:53'),
(4, 5, 4, 9, 1, 1177, 23, 0, 0, '2023-08-31 14:20:53'),
(5, 5, 5, 4, 1, 12473, 26, 0, 0, '2023-08-31 14:23:49'),
(6, 5, 5, 9, 1, 1177, 23, 0, 0, '2023-08-31 14:23:49'),
(7, 5, 6, 6, 1, 402, 47, 0, 0, '2023-08-31 19:30:55'),
(8, 5, 7, 24, 1, 1375, 25, 0, 0, '2023-09-02 12:59:06'),
(9, 5, 8, 1, 1, 2360, 40, 0, 0, '2023-09-25 22:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(20) NOT NULL,
  `Category` varchar(100) NOT NULL,
  `sub_cat` varchar(100) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `price` int(20) NOT NULL,
  `size` varchar(20) NOT NULL,
  `color` varchar(120) NOT NULL,
  `material` varchar(20) NOT NULL,
  `availability` varchar(20) NOT NULL,
  `discount` varchar(20) NOT NULL,
  `pic` varchar(20) NOT NULL,
  `description` varchar(120) NOT NULL,
  `status` int(11) NOT NULL,
  `label` int(11) NOT NULL COMMENT '0=None,1=featured,2=Sponsored\r\n',
  `views` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `Category`, `sub_cat`, `brand`, `product_name`, `price`, `size`, `color`, `material`, `availability`, `discount`, `pic`, `description`, `status`, `label`, `views`, `created_date`) VALUES
(1, 'fashion', '', 'Pantaloons', 'Pantaloons Men Solid sporty black jacket', 2400, 'LG', 'Yellow', 'solid', 'In Stock', '40', '/img/product1.jpg', 'this jacket is more comfortable in winter seasons. similar products are aval', 1, 1, 584, '2023-08-15 16:46:14'),
(2, 'Electronics', '', 'Dell', 'Dell Inspiron Ryzen 3 3254U(8GB RAM/500GB SSD', 55400, 'L', 'grey', 'metal body', 'In Stock', '0', '/img/product2.jpg', 'this is best laptop under 5k,it comes with 2gb dedicated graphics card. soud quality is high and 4.5GH processor speed ,', 1, 0, 8, '2023-08-15 19:46:14'),
(3, 'fashion', '', 'Cobb', 'Cobb Kids face Mask with MultiColours ', 435, 'M', 'Multi-Colours', 'solid', 'In Stock', '35', '/img/product3.jpg', 'it is face mask to protect from viruses and polluted air.it made with cotton cloth and easy to fit on face.', 1, 2, 125, '2023-08-15 15:46:14'),
(4, 'Electronics', '', 'Oppo Realme', 'realme Nazro 50A(Oxygen Blue, 128 GB) 4 GB RA', 12499, 'XL', 'Oxygen Blue', 'metal body', 'In Stock', '0', '/img/product4.jpg', 'this phone battery is 4600 mah powerfully backup and fast charging supported.', 1, 0, 2, '2023-08-15 15:46:14'),
(6, 'Electronics', '', 'Boat', 'Boat 100 Wired Headset(Red,In the Ear)', 449, '', 'red', 'solid', 'In Stock', '47', '/img/product5.jpg', 'It is best ear phone of boat because it provide best sound quality.', 1, 1, 25, '2023-08-15 15:46:14'),
(8, 'Electronics', '', 'HP', 'HP Pavallion NoteBook(4GB RAM/300GBSSD)', 35000, 'XL', 'Silver Mattle', 'solid', 'In Stock', '0', '/img/product6.jpg', 'Hp Pavallion is the perfect for students and home workers. it is super fast multi processor laptop.', 1, 1, 62, '2023-08-15 15:46:14'),
(9, 'fashion', '', 'Apollo', 'Apollo Men T-Shirt Green', 1200, 'LG', 'green', 'cotton', 'In Stock', '0', '/img/product7.jpg', 'best t-shirt under 1k.', 1, 2, 1, '2023-08-15 15:46:14'),
(10, 'Electronics', '', 'Apple', 'Apple i Phone x', 76000, 'L', 'blue', 'metal blue', 'In Stock', '0', '/img/product8.jpg', 'Best i phone under 80k in deals.', 1, 0, 0, '2023-08-15 15:46:14'),
(12, 'fashion', '', 'Rayban', 'Rayban Men Sun Glasses', 450, 'S', 'Sky blue', 'plastic', 'In Stock', '46', '/img/product8.jpg', 'best sunglasses of rayban under 500 rs.', 1, 1, 24, '2023-08-15 15:46:14'),
(14, 'Electronics', '', 'Sonata', 'Men Watches Combo Pack of 2', 1500, 'L', 'black', '', 'In Stock', '0', '/img/product9.jpg', ' trending watches from mi. colour is black and blue.', 1, 0, 1, '2023-08-15 15:46:14'),
(15, 'Electronics', '', 'Fast Track', 'Fast track Men Sports Watch Analog ', 1400, '', 'White', '', 'In Stock', '45', '/img/product10.jpg', 'best quality watches from fast track. colour is green.', 1, 2, 0, '2023-08-15 15:46:14'),
(18, 'Electronics', '', 'Dell', 'Dell Wireless Keyboard & Mouse', 1200, '', 'Black', '', 'In Stock', '22', '/img/product11.jpg', 'Dell Wireless Keyboard & Mouse comes with 1 year Warrunty and replacement.', 1, 0, 0, '2023-08-15 15:46:14'),
(19, 'Electronics', '', 'HP ', 'HP Victus gaming laptop', 60000, '', 'Mattle Grey', '', 'In Stock', '40', '/img/product12.jpg', 'Best gaming laptop and color black or mattel body 500gb ssd ram is 16gb', 1, 1, 2, '2023-08-15 15:46:14'),
(20, 'Beauty & makeup', '', 'Himalaya', 'Himalaya Neem Face Wash 400ml', 270, '', 'Green Gel', '', 'In Stock', '25', '/img/product13.jpg', 'Himalaya Neem face wash for purifying skin, bright skin. Ayurvedic Medicine for Pimples.', 1, 2, 0, '2023-08-15 15:46:14'),
(23, 'fashion', '', 'Red Cheif', 'Red Chief Men Shoes Brown', 2400, '', 'Blue', '', 'In Stock', '0', '/img/category7.jpg', 'Pure 100% leather shoes by Red Chief. it provides better quality of leather.', 1, 1, 43, '2023-08-15 15:46:14'),
(24, 'Gadgets', '', 'Logitech', 'mouse', 1400, '', 'color', '', 'In Stock', '25', '/img/product14.jpg', 'best quality ', 1, 0, 88, '2023-08-15 15:46:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_rating`
--

CREATE TABLE `product_rating` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `title` text NOT NULL,
  `comment` text NOT NULL,
  `helpful` text DEFAULT '0',
  `unhelpful` text NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_rating`
--

INSERT INTO `product_rating` (`id`, `item_id`, `user_id`, `rating`, `title`, `comment`, `helpful`, `unhelpful`, `status`, `created`, `modified`) VALUES
(1, 1, 5, 2, 'nice', 'Nice!!', '1', '0', 1, '2023-03-08 17:31:02', '2023-07-17 19:37:13'),
(5, 80, 3, 4, '', 'Nice..', '', '', 1, '2023-04-22 16:46:36', '2023-04-22 16:46:36'),
(7, 80, 3, 3, '', 'Rajat', '', '', 1, '2023-04-22 16:51:22', '2023-04-22 16:51:22'),
(8, 80, 3, 3, '', 'Rajat', '', '', 1, '2023-04-22 16:52:17', '2023-04-22 16:52:17'),
(10, 1, 5, 3, '', 'nice', '', '', 1, '2023-04-22 17:15:55', '2023-04-22 17:15:55'),
(11, 4, 3, 4, '', 'asda', '', '', 1, '2023-04-22 18:03:03', '2023-04-22 18:03:03'),
(12, 16, 1, 4, 'Rajat Agrawal', 'Exellent..', '0', '0', 1, '2023-07-17 20:12:54', '2023-07-17 20:12:54');

-- --------------------------------------------------------

--
-- Table structure for table `subscribe`
--

CREATE TABLE `subscribe` (
  `id` int(11) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `site_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subscribe`
--

INSERT INTO `subscribe` (`id`, `email_id`, `site_id`, `created_date`, `user_id`, `subscriber_id`) VALUES
(3, 'rajat9394@gmail.com', 0, '2023-03-09 09:12:13', 2, 5),
(21, 'rajat12@gmail.com', 0, '2023-09-02 14:01:09', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(45) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `mobile_verified` int(11) NOT NULL,
  `password` text NOT NULL,
  `address` varchar(55) NOT NULL,
  `city` varchar(20) NOT NULL,
  `pic` varchar(55) NOT NULL,
  `status` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `mobile_verified`, `password`, `address`, `city`, `pic`, `status`, `created_date`) VALUES
(1, 'Admin', 'admin@gmail.com', '0', 0, '81dc9bdb52d04dc20036dbd8313ed055', 'Surendra Nagar, Aligarh', 'Aligarh', 'student6.jpg', 0, '2023-08-21 16:25:00'),
(5, 'Rajat Agrawal', 'rajatagrawal9394@gmail.com', '8191816126', 1, '$2y$10$y1kcSpp.eNecpxDvMoONh.ud4xFB4cE.D.JqzZ/Qn0pG2wqhZRfvS', '', '', '/img/user1.jpg', 1, '2023-08-22 20:16:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `available_coupons`
--
ALTER TABLE `available_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_rating`
--
ALTER TABLE `product_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscribe`
--
ALTER TABLE `subscribe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `available_coupons`
--
ALTER TABLE `available_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `product_rating`
--
ALTER TABLE `product_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `subscribe`
--
ALTER TABLE `subscribe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
