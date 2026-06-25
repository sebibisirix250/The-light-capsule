-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 02:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `website_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `type` enum('product','gallery','service') NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `parent_id`, `is_active`, `created_at`) VALUES
(1, 'Photography Services', 'photography-services', 'service', NULL, 1, '2026-03-15 12:57:42'),
(2, 'Digital Packs', 'digital-packs', 'product', NULL, 1, '2026-03-15 12:57:42'),
(3, 'Fine Art Gallery', 'fine-art-gallery', 'gallery', NULL, 1, '2026-03-15 12:57:42'),
(4, 'Picture frames', 'picture-frames', 'product', NULL, 1, '2026-03-15 17:09:37'),
(5, 'another category to test', 'another-cat', 'product', NULL, 1, '2026-03-29 12:05:30'),
(6, 'andyetanotherone', 'and-yet-another-one', 'gallery', NULL, 1, '2026-03-31 20:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_item_types`
--

CREATE TABLE `gallery_item_types` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `type_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_item_types`
--

INSERT INTO `gallery_item_types` (`item_id`, `type_id`) VALUES
(9, 1),
(9, 9),
(10, 5),
(10, 10),
(11, 10),
(12, 10),
(13, 10),
(14, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(18, 10),
(18, 11),
(19, 4),
(19, 10),
(19, 11),
(20, 4),
(20, 10),
(20, 11),
(21, 4),
(21, 10),
(21, 11),
(22, 4),
(22, 10),
(22, 11),
(23, 10),
(30, 11),
(31, 1),
(33, 6),
(36, 12),
(40, 11);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_metadata`
--

CREATE TABLE `gallery_metadata` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `gallery_type` varchar(50) DEFAULT NULL,
  `orientation` enum('landscape','portrait','square','panorama') DEFAULT NULL,
  `width_px` int(10) UNSIGNED DEFAULT NULL,
  `height_px` int(10) UNSIGNED DEFAULT NULL,
  `aspect_ratio` varchar(20) DEFAULT NULL,
  `edit_style` varchar(50) DEFAULT NULL,
  `dominant_color` varchar(30) DEFAULT NULL,
  `thumb_image` varchar(255) NOT NULL,
  `modal_image` varchar(255) NOT NULL,
  `full_image` varchar(255) NOT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `file_format` varchar(20) DEFAULT NULL,
  `file_size_bytes` bigint(20) UNSIGNED DEFAULT NULL,
  `capture_date` date DEFAULT NULL,
  `capture_location` varchar(150) DEFAULT NULL,
  `camera_make` varchar(100) DEFAULT NULL,
  `camera_model` varchar(100) DEFAULT NULL,
  `lens` varchar(100) DEFAULT NULL,
  `focal_length` varchar(50) DEFAULT NULL,
  `aperture` varchar(20) DEFAULT NULL,
  `shutter_speed` varchar(30) DEFAULT NULL,
  `iso_value` varchar(20) DEFAULT NULL,
  `is_printable` tinyint(1) NOT NULL DEFAULT 1,
  `is_licensed` tinyint(1) NOT NULL DEFAULT 1,
  `is_downloadable` tinyint(1) NOT NULL DEFAULT 1,
  `watermark_preview` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_metadata`
--

INSERT INTO `gallery_metadata` (`item_id`, `gallery_type`, `orientation`, `width_px`, `height_px`, `aspect_ratio`, `edit_style`, `dominant_color`, `thumb_image`, `modal_image`, `full_image`, `original_filename`, `file_format`, `file_size_bytes`, `capture_date`, `capture_location`, `camera_make`, `camera_model`, `lens`, `focal_length`, `aperture`, `shutter_speed`, `iso_value`, `is_printable`, `is_licensed`, `is_downloadable`, `watermark_preview`, `sort_order`) VALUES
(9, NULL, 'portrait', 3950, 4938, '0.79991899554475', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b82f5502d769.34629257_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b82f5502d769.34629257_modal.jpg', 'assets/uploads/gallery/full/gallery_69b82f5502d769.34629257.jpg', '_DSC0729.jpg', 'jpg', 9719808, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(10, NULL, 'landscape', 5752, 3835, '1.4998696219035', 'cinematic', NULL, 'assets/uploads/gallery/thumbs/gallery_69b8335bb5acc9.61852329_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b8335bb5acc9.61852329_modal.jpg', 'assets/uploads/gallery/full/gallery_69b8335bb5acc9.61852329.jpg', '_DSC0034.jpg', 'jpg', 17171209, NULL, 'Arcalia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(11, NULL, 'landscape', 5991, 3370, '1.7777448071217', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b8421ed600b7.27097741_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b8421ed600b7.27097741_modal.jpg', 'assets/uploads/gallery/full/gallery_69b8421ed600b7.27097741.jpg', '_DSC0154-1.jpg', 'jpg', 16230719, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(12, NULL, 'landscape', 5191, 2920, '1.7777397260274', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b8422184f6a6.74480686_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b8422184f6a6.74480686_modal.jpg', 'assets/uploads/gallery/full/gallery_69b8422184f6a6.74480686.jpg', '_DSC0168.jpg', 'jpg', 12708307, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 1),
(13, NULL, 'portrait', 4000, 5000, '0.8', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b84224048c53.81142525_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b84224048c53.81142525_modal.jpg', 'assets/uploads/gallery/full/gallery_69b84224048c53.81142525.jpg', '_DSC0177.jpg', 'jpg', 18065845, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 2),
(14, NULL, 'landscape', 4511, 3609, '1.2499307287337', 'cinematic', NULL, 'assets/uploads/gallery/thumbs/gallery_69b944dcd9a375.02296922_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b944dcd9a375.02296922_modal.jpg', 'assets/uploads/gallery/full/gallery_69b944dcd9a375.02296922.jpg', '_DSC0008.jpg', 'jpg', 11551780, NULL, 'Cluj-Napoca', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(15, NULL, 'portrait', 3397, 4246, '0.80004710315591', 'cinematic', NULL, 'assets/uploads/gallery/thumbs/gallery_69b944df57d772.70877885_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b944df57d772.70877885_modal.jpg', 'assets/uploads/gallery/full/gallery_69b944df57d772.70877885.jpg', '_DSC0054.jpg', 'jpg', 5713024, NULL, 'Cluj-Napoca', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 1),
(16, NULL, 'portrait', 4000, 5000, '0.8', 'cinematic', NULL, 'assets/uploads/gallery/thumbs/gallery_69b944e1a7fa08.98888516_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b944e1a7fa08.98888516_modal.jpg', 'assets/uploads/gallery/full/gallery_69b944e1a7fa08.98888516.jpg', '_DSC0055.jpg', 'jpg', 15180691, NULL, 'Cluj-Napoca', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 2),
(17, NULL, 'landscape', 5924, 3332, '1.7779111644658', 'cinematic', NULL, 'assets/uploads/gallery/thumbs/gallery_69b944e47fcbc6.82218673_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b944e47fcbc6.82218673_modal.jpg', 'assets/uploads/gallery/full/gallery_69b944e47fcbc6.82218673.jpg', '_DSC0081.jpg', 'jpg', 11695779, NULL, 'Cluj-Napoca', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 3),
(18, NULL, 'landscape', 5499, 3666, '1.5', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b9500fd13237.19706795_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b9500fd13237.19706795_modal.jpg', 'assets/uploads/gallery/full/gallery_69b9500fd13237.19706795.jpg', '_DSC0164.jpg', 'jpg', 1233716, '2024-03-30', 'Bistrita', 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '240/10', 'f/4.0', '1/80', '100', 1, 1, 1, 0, 0),
(19, NULL, 'landscape', 3435, 2748, '1.25', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b9501275d479.44583904_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b9501275d479.44583904_modal.jpg', 'assets/uploads/gallery/full/gallery_69b9501275d479.44583904.jpg', '_DSC0189-Enhanced-NR.jpg', 'jpg', 1104888, '2024-03-30', 'Bistrita', 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '700/10', 'f/2.8', '1/250', '400', 1, 1, 1, 0, 1),
(20, NULL, 'portrait', 4000, 4443, '0.90029259509341', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b95014616a19.56979805_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b95014616a19.56979805_modal.jpg', 'assets/uploads/gallery/full/gallery_69b95014616a19.56979805.jpg', '_DSC0345.jpg', 'jpg', 2765750, '2024-03-30', 'Bistrita', 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '240/10', 'f/2.8', '1/800', '100', 1, 1, 1, 0, 2),
(21, NULL, 'portrait', 3967, 5950, '0.66672268907563', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b950171aeca8.11393845_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b950171aeca8.11393845_modal.jpg', 'assets/uploads/gallery/full/gallery_69b950171aeca8.11393845.jpg', '_DSC0504.jpg', 'jpg', 3313375, '2024-03-30', 'Bistrita', 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '560/10', 'f/2.8', '1/640', '100', 1, 1, 1, 0, 3),
(22, NULL, 'landscape', 4963, 3960, '1.2532828282828', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69b95019d9a8e7.11353475_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69b95019d9a8e7.11353475_modal.jpg', 'assets/uploads/gallery/full/gallery_69b95019d9a8e7.11353475.jpg', '_DSC0595.jpg', 'jpg', 2345780, '2024-03-30', 'Bistrita', 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '240/10', 'f/4.5', '1/60', '250', 1, 1, 1, 0, 0),
(23, NULL, 'portrait', 3127, 3504, '0.89240867579909', 'natural', NULL, 'assets/uploads/gallery/thumbs/gallery_69bec04c8cf657.84129971_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69bec04c8cf657.84129971_modal.jpg', 'assets/uploads/gallery/full/gallery_69bec04c8cf657.84129971.jpg', '_DSC0433.jpg', 'jpg', 7230624, NULL, 'Tihuta', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(24, NULL, 'portrait', 3725, 4656, '0.80004295532646', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69bee1203d8fd9.37934475_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69bee1203d8fd9.37934475_modal.jpg', 'assets/uploads/gallery/full/gallery_69bee1203d8fd9.37934475.jpg', '_DSC0350.jpg', 'jpg', 11955087, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(25, NULL, 'landscape', 2813, 2250, '1.2502222222222', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69bee123424b05.91568124_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69bee123424b05.91568124_modal.jpg', 'assets/uploads/gallery/full/gallery_69bee123424b05.91568124.jpg', '009.jpg', 'jpg', 5721851, '2022-08-04', NULL, 'DJI', 'FC7303', '20.7 mm', '449/100', 'f/2.8', '1/120', '100', 1, 1, 1, 0, 1),
(26, NULL, 'landscape', 2813, 2250, '1.2502222222222', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69bee124e0a443.71227955_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69bee124e0a443.71227955_modal.jpg', 'assets/uploads/gallery/full/gallery_69bee124e0a443.71227955.jpg', '011.jpg', 'jpg', 5530157, '2022-08-11', NULL, 'DJI', 'FC7303', '20.7 mm', '449/100', 'f/2.8', '1/320', '100', 1, 1, 1, 0, 2),
(27, NULL, 'portrait', 4000, 6000, '0.66666666666667', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69bee12667e326.69968806_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69bee12667e326.69968806_modal.jpg', 'assets/uploads/gallery/full/gallery_69bee12667e326.69968806.jpg', '012.jpg', 'jpg', 23434362, '2022-08-04', NULL, 'NIKON CORPORATION', 'NIKON D7200', '18.0-105.0 mm f/3.5-5.6', '220/10', 'f/8.0', '1/125', '800', 1, 1, 1, 0, 3),
(28, NULL, 'portrait', 3933, 5899, '0.66672317341922', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69bee128e38933.58218679_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69bee128e38933.58218679_modal.jpg', 'assets/uploads/gallery/full/gallery_69bee128e38933.58218679.jpg', '019.jpg', 'jpg', 24563640, '2022-08-04', NULL, 'NIKON CORPORATION', 'NIKON D7200', '18.0-105.0 mm f/3.5-5.6', '340/10', 'f/5.6', '1/50', '640', 1, 1, 1, 0, 0),
(30, NULL, 'landscape', 5000, 4000, '1.25', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69c287ca65fb17.63228129_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69c287ca65fb17.63228129_modal.jpg', 'assets/uploads/gallery/full/gallery_69c287ca65fb17.63228129.jpg', 'DSC_0099.jpg', 'jpg', 17788924, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(31, NULL, 'portrait', 3585, 5377, '0.66672865910359', 'artistic', NULL, 'assets/uploads/gallery/thumbs/gallery_69c2a8bfc93732.37565238_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69c2a8bfc93732.37565238_modal.jpg', 'assets/uploads/gallery/full/gallery_69c2a8bfc93732.37565238.jpg', '018.jpg', 'jpg', 13243196, '2018-09-25', NULL, 'NIKON CORPORATION', 'NIKON D5300', '18.0-105.0 mm f/3.5-5.6', '380/10', 'f/4.5', '1/40', '320', 1, 1, 1, 0, 0),
(33, NULL, 'landscape', 4000, 2250, '1.7777777777778', 'documentary', NULL, 'assets/uploads/gallery/thumbs/gallery_69c914f727f3f8.37755041_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69c914f727f3f8.37755041_modal.jpg', 'assets/uploads/gallery/full/gallery_69c914f727f3f8.37755041.jpg', '004.jpg', 'jpg', 7761595, '2021-04-11', 'Kayman', 'DJI', 'FC7303', '20.7 mm', '449/100', 'f/2.8', '1/3200', '100', 1, 1, 1, 0, 0),
(34, NULL, 'portrait', 4000, 5000, '0.8', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69c9152e7d0616.78775582_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69c9152e7d0616.78775582_modal.jpg', 'assets/uploads/gallery/full/gallery_69c9152e7d0616.78775582.jpg', '141.jpg', 'jpg', 27108474, '2022-10-17', NULL, 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/3.5', '1/160', '100', 1, 1, 1, 0, 0),
(35, NULL, 'portrait', 3919, 4899, '0.79995917534191', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69c9153185ac82.47163628_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69c9153185ac82.47163628_modal.jpg', 'assets/uploads/gallery/full/gallery_69c9153185ac82.47163628.jpg', '150.jpg', 'jpg', 2728272, '2024-04-20', NULL, 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '660/10', 'f/11.0', '1/160', '100', 1, 1, 1, 0, 1),
(36, NULL, 'portrait', 4000, 6000, '0.66666666666667', 'cinematic', NULL, 'assets/uploads/gallery/thumbs/gallery_69cc2de7623e44.62188037_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69cc2de7623e44.62188037_modal.jpg', 'assets/uploads/gallery/full/gallery_69cc2de7623e44.62188037.jpg', '090.jpg', 'jpg', 27923831, '2022-10-12', 'Landa', 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/1.8', '1/80', '400', 1, 1, 1, 0, 0),
(37, NULL, 'landscape', 5607, 2936, '1.9097411444142', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69cc2e4b4f30f2.36476202_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69cc2e4b4f30f2.36476202_modal.jpg', 'assets/uploads/gallery/full/gallery_69cc2e4b4f30f2.36476202.jpg', '167.jpg', 'jpg', 10441610, '2022-01-09', NULL, 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/10.0', '1/50', '100', 1, 1, 1, 0, 0),
(38, NULL, 'portrait', 4000, 6000, '0.66666666666667', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69cc2e4e125e05.88576184_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69cc2e4e125e05.88576184_modal.jpg', 'assets/uploads/gallery/full/gallery_69cc2e4e125e05.88576184.jpg', '169.jpg', 'jpg', 1758336, '2024-04-20', NULL, 'NIKON CORPORATION', 'NIKON D5300', 'TAMRON SP 24-70mm F2.8 Di VC USD A007N', '420/10', 'f/5.6', '1/80', '100', 1, 1, 1, 0, 1),
(39, NULL, 'portrait', 3875, 4844, '0.79995871180842', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69cc2e508f4b47.01905556_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69cc2e508f4b47.01905556_modal.jpg', 'assets/uploads/gallery/full/gallery_69cc2e508f4b47.01905556.jpg', '293.jpg', 'jpg', 1943411, '2024-05-11', NULL, 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/1.8', '1/2500', '100', 1, 1, 1, 0, 2),
(40, NULL, 'portrait', 3444, 5166, '0.66666666666667', 'natural', NULL, 'assets/uploads/gallery/thumbs/gallery_69d04af3a2dec1.36994774_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69d04af3a2dec1.36994774_modal.jpg', 'assets/uploads/gallery/full/gallery_69d04af3a2dec1.36994774.jpg', '140.jpg', 'jpg', 24391581, '2019-04-21', 'Gafi', 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/6.3', '1/60', '250', 1, 1, 1, 0, 0),
(41, NULL, 'portrait', 3048, 3810, '0.8', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69d04b98246198.95670542_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69d04b98246198.95670542_modal.jpg', 'assets/uploads/gallery/full/gallery_69d04b98246198.95670542.jpg', '_DSC0236.jpg', 'jpg', 6671712, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0),
(42, NULL, 'landscape', 5984, 3366, '1.7777777777778', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69d04b99ed92f1.87519969_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69d04b99ed92f1.87519969_modal.jpg', 'assets/uploads/gallery/full/gallery_69d04b99ed92f1.87519969.jpg', '037.jpg', 'jpg', 2937690, '2022-10-17', NULL, 'NIKON CORPORATION', 'NIKON D7200', '18.0-105.0 mm f/3.5-5.6', '580/10', 'f/5.0', '1/500', '100', 1, 1, 1, 0, 1),
(43, NULL, 'portrait', 4000, 6000, '0.66666666666667', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69d04b9c1894a0.76506458_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69d04b9c1894a0.76506458_modal.jpg', 'assets/uploads/gallery/full/gallery_69d04b9c1894a0.76506458.jpg', '107.jpg', 'jpg', 9954700, '2022-10-17', NULL, 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/5.6', '1/160', '100', 1, 1, 1, 0, 2),
(44, NULL, 'landscape', 5000, 4000, '1.25', 'artistic', NULL, 'assets/uploads/gallery/thumbs/gallery_69fc93fd999032.85402749_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69fc93fd999032.85402749_modal.jpg', 'assets/uploads/gallery/full/gallery_69fc93fd999032.85402749.jpg', '269.jpg', 'jpg', 1385866, '2024-05-11', 'Gafi', 'NIKON CORPORATION', 'NIKON D5300', '35.0 mm f/1.8', '350/10', 'f/1.8', '1/200', '100', 1, 1, 1, 0, 0),
(45, NULL, 'landscape', 4299, 3439, '1.250072695551', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69fc94a0db3a36.63534746_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69fc94a0db3a36.63534746_modal.jpg', 'assets/uploads/gallery/full/gallery_69fc94a0db3a36.63534746.jpg', '050.jpg', 'jpg', 21667862, '2022-07-20', NULL, 'NIKON CORPORATION', 'NIKON D7200', '105.0 mm f/2.8', '1050/10', 'f/8.0', '1/320', '800', 1, 1, 1, 0, 0),
(46, NULL, 'portrait', 3551, 4439, '0.79995494480739', NULL, NULL, 'assets/uploads/gallery/thumbs/gallery_69fc95696487b9.01125198_thumb.jpg', 'assets/uploads/gallery/modal/gallery_69fc95696487b9.01125198_modal.jpg', 'assets/uploads/gallery/full/gallery_69fc95696487b9.01125198.jpg', '_DSC0241.jpg', 'jpg', 9455805, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_types`
--

CREATE TABLE `gallery_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_types`
--

INSERT INTO `gallery_types` (`id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Individual', 'individual', 1),
(2, 'Group', 'group', 2),
(3, 'Weddings', 'weddings', 3),
(4, 'Automotive', 'automotive', 4),
(5, 'Real Estate', 'real-estate', 5),
(6, 'Advertisement', 'advertisement', 6),
(7, 'Baptism', 'baptism', 7),
(8, 'Sports', 'sports', 8),
(9, 'Events', 'events', 9),
(10, 'Landscapes', 'landscapes', 10),
(11, 'Wildlife', 'wildlife', 11),
(12, 'Aerial', 'aerial', 12);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('service','gallery','digital_product','product') NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_physical` tinyint(1) NOT NULL DEFAULT 0,
  `stock_quantity` int(10) NOT NULL DEFAULT 0,
  `is_limited_edition` tinyint(1) NOT NULL DEFAULT 0,
  `cover_image` varchar(255) DEFAULT NULL,
  `delivery_mode` enum('manual_email','service_request','download_later') NOT NULL DEFAULT 'manual_email',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `type`, `code`, `title`, `slug`, `short_description`, `full_description`, `price`, `is_physical`, `stock_quantity`, `is_limited_edition`, `cover_image`, `delivery_mode`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'service', 'SERVICE-WEDDING-01', 'Wedding Photography', 'wedding-photography', 'Professional wedding photography coverage.', 'A complete wedding photography service with planning, event coverage, and edited final delivery.', 1500.00, 0, 0, 0, NULL, 'service_request', 1, 1, '2026-03-15 12:57:57', '2026-03-15 12:57:57'),
(2, 'product', 'PACK-CINEMATIC-01', 'Cinematic Lightroom Pack', 'cinematic-lightroom-pack', 'A pack of cinematic presets for photo editing.', 'A curated Lightroom preset pack designed for cinematic tones, contrast control, and moody color grading.', 29.98, 0, 0, 0, 'assets/uploads/products/prod_69bee2cc948ad0.57546513_027.jpg', 'download_later', 1, 1, '2026-03-15 12:57:57', '2026-03-31 12:04:43'),
(4, 'product', 'PACK-1773587188', 'test', 'testslug', 'testdescript', 'fulltestdescript', 1.45, 0, 0, 0, 'assets/uploads/products/prod_69b6d2277a9ce_2024PMV5Presets.webp', 'manual_email', 0, 1, '2026-03-15 15:06:28', '2026-03-31 12:04:43'),
(5, 'product', 'PACK-1773592374', 'test2cat', 'test2catslug', 'test2catdesc', 'test2catfulldesc', 52.48, 0, 0, 0, 'assets/uploads/products/prod_69b6df36ae2767.87188288_2024PMV5Presets.webp', 'manual_email', 0, 1, '2026-03-15 16:32:54', '2026-03-31 12:04:43'),
(9, 'gallery', 'GALLERY-1773678422', 'El Nino Goat Rally', 'el-nino-portrait', 'A portrait of a famous singer.', 'A portrait of famous singer El Nino who appeared at motor show Goat Rally 2025 in Cluj-Napoca.', 20.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b82f5502d769.34629257_thumb.jpg', 'manual_email', 0, 1, '2026-03-16 16:27:02', '2026-03-16 16:27:02'),
(10, 'gallery', 'GALLERY-1773679454', 'Castelul Bethlen', 'castelul-bethlen', 'A beautifull castle', 'The magnific Bethlen castle and its beautiful botanical garden located in Arcalia, Romania.', 25.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b8335bb5acc9.61852329_thumb.jpg', 'manual_email', 0, 1, '2026-03-16 16:44:14', '2026-03-16 16:44:14'),
(11, 'gallery', 'GALLERY-1773683233-0', '_DSC0154-1', 'dsc0154-1', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b8421ed600b7.27097741_thumb.jpg', 'manual_email', 0, 1, '2026-03-16 17:47:13', '2026-03-16 17:47:13'),
(12, 'gallery', 'GALLERY-1773683236-1', '_DSC0168', 'dsc0168', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b8422184f6a6.74480686_thumb.jpg', 'manual_email', 0, 1, '2026-03-16 17:47:16', '2026-03-16 17:47:16'),
(13, 'gallery', 'GALLERY-1773683238-2', '_DSC0177', 'dsc0177', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b84224048c53.81142525_thumb.jpg', 'manual_email', 0, 1, '2026-03-16 17:47:18', '2026-03-16 17:47:18'),
(14, 'gallery', 'GALLERY-1773749471-0', '_DSC0008', 'dsc0008', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b944dcd9a375.02296922_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:11:11', '2026-03-17 12:11:11'),
(15, 'gallery', 'GALLERY-1773749473-1', '_DSC0054', 'dsc0054', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b944df57d772.70877885_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:11:13', '2026-03-17 12:11:13'),
(16, 'gallery', 'GALLERY-1773749476-2', '_DSC0055', 'dsc0055', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b944e1a7fa08.98888516_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:11:16', '2026-03-17 12:11:16'),
(17, 'gallery', 'GALLERY-1773749479-3', '_DSC0081', 'dsc0081', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b944e47fcbc6.82218673_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:11:19', '2026-03-17 12:11:19'),
(18, 'gallery', 'GALLERY-1773752338-0', '_DSC0164', 'dsc0164', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b9500fd13237.19706795_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:58:58', '2026-03-17 12:58:58'),
(19, 'gallery', 'GALLERY-1773752340-1', '_DSC0189-Enhanced-NR', 'dsc0189-enhanced-nr', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b9501275d479.44583904_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:59:00', '2026-03-17 12:59:00'),
(20, 'gallery', 'GALLERY-1773752343-2', '_DSC0345', 'dsc0345', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b95014616a19.56979805_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:59:03', '2026-03-17 12:59:03'),
(21, 'gallery', 'GALLERY-1773752345-3', '_DSC0504', 'dsc0504', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b950171aeca8.11393845_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:59:05', '2026-03-17 12:59:05'),
(22, 'gallery', 'GALLERY-1773752348-4', '_DSC0595', 'dsc0595', 'hh', '', 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69b95019d9a8e7.11353475_thumb.jpg', 'manual_email', 0, 1, '2026-03-17 12:59:08', '2026-03-29 12:01:46'),
(23, 'gallery', 'GALLERY-1774108750', 'Peisaj Tihuta', 'peisaj-tihuta', 'test', 'testtest', 10.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69bec04c8cf657.84129971_thumb.jpg', 'manual_email', 0, 1, '2026-03-21 15:59:10', '2026-03-21 15:59:10'),
(24, 'gallery', 'GALLERY-1774117155-0', '_DSC0350', 'dsc0350', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69bee1203d8fd9.37934475_thumb.jpg', 'manual_email', 0, 1, '2026-03-21 18:19:15', '2026-03-21 18:19:15'),
(25, 'gallery', 'GALLERY-1774117156-1', '009', '009', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69bee123424b05.91568124_thumb.jpg', 'manual_email', 0, 1, '2026-03-21 18:19:16', '2026-03-21 18:19:16'),
(26, 'gallery', 'GALLERY-1774117158-2', '011', '011', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69bee124e0a443.71227955_thumb.jpg', 'manual_email', 0, 1, '2026-03-21 18:19:18', '2026-03-21 18:19:18'),
(27, 'gallery', 'GALLERY-1774117160-3', '012', '012', NULL, NULL, 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69bee12667e326.69968806_thumb.jpg', 'manual_email', 0, 1, '2026-03-21 18:19:20', '2026-03-21 18:19:20'),
(28, 'gallery', 'GALLERY-1774117163-4', '019', '019', '', '', 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69bee128e38933.58218679_thumb.jpg', 'manual_email', 0, 1, '2026-03-21 18:19:23', '2026-03-24 11:58:30'),
(30, 'gallery', 'GALLERY-1774356429', 'test title', 'test-title', 'test 2', '', 0.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69c287ca65fb17.63228129_thumb.jpg', 'manual_email', 0, 1, '2026-03-24 12:47:09', '2026-03-24 13:42:54'),
(31, 'gallery', 'GALLERY-1774364866', 'testo3', '3-testo', 'yadiyada testo', '', 20.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69c2a8bfc93732.37565238_thumb.jpg', 'manual_email', 0, 1, '2026-03-24 15:07:46', '2026-03-24 15:07:46'),
(32, 'product', 'PACK-1774370567', 'another test', 'another-test', 'eafAF', 'EFFSgdgszgggggggg', 40.00, 0, 1, 0, 'assets/uploads/products/prod_69cbbbe3815a35.83675040_cyberpunk_2083-wallpaper-3554x1999.jpg', 'manual_email', 0, 1, '2026-03-24 16:42:47', '2026-03-31 12:31:07'),
(33, 'gallery', 'GALLERY-1774785784', 'yadiyadah', 'anothertesto', 'testopesto', 'fullotestopestodescripto', 50.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69c914f727f3f8.37755041_thumb.jpg', 'manual_email', 0, 1, '2026-03-29 12:03:04', '2026-03-29 12:03:04'),
(34, 'gallery', 'GALLERY-1774785841-0', '141', '141', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69c9152e7d0616.78775582_thumb.jpg', 'manual_email', 0, 1, '2026-03-29 12:04:01', '2026-03-29 12:04:01'),
(35, 'gallery', 'GALLERY-1774785844-1', '150', '150', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69c9153185ac82.47163628_thumb.jpg', 'manual_email', 0, 1, '2026-03-29 12:04:04', '2026-03-29 12:04:04'),
(36, 'gallery', 'GALLERY-1774988778', 'yetanotehrtest', 'yet-another-test', 'yethfHFSUOKiii', 'sDFsdfADSFADASdfgdgzsdgfdfgdzgzxv', 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69cc2de7623e44.62188037_thumb.jpg', 'manual_email', 0, 1, '2026-03-31 20:26:18', '2026-03-31 20:27:23'),
(37, 'gallery', 'GALLERY-1774988878-0', '167', '167', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69cc2e4b4f30f2.36476202_thumb.jpg', 'manual_email', 0, 1, '2026-03-31 20:27:58', '2026-03-31 20:27:58'),
(38, 'gallery', 'GALLERY-1774988880-1', '169', '169', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69cc2e4e125e05.88576184_thumb.jpg', 'manual_email', 0, 1, '2026-03-31 20:28:00', '2026-03-31 20:28:00'),
(39, 'gallery', 'GALLERY-1774988883-2', '293', '293', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69cc2e508f4b47.01905556_thumb.jpg', 'manual_email', 0, 1, '2026-03-31 20:28:03', '2026-03-31 20:28:03'),
(40, 'gallery', 'GALLERY-1775258358', 'V99 TEST', 'v99-test', 'yupi', 'yessir', 25.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69d04af3a2dec1.36994774_thumb.jpg', 'manual_email', 0, 1, '2026-04-03 23:19:18', '2026-04-03 23:20:09'),
(41, 'gallery', 'GALLERY-1775258521-0', '_DSC0236', 'dsc0236', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69d04b98246198.95670542_thumb.jpg', 'manual_email', 0, 1, '2026-04-03 23:22:01', '2026-04-03 23:22:01'),
(42, 'gallery', 'GALLERY-1775258524-1', '037', '037', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69d04b99ed92f1.87519969_thumb.jpg', 'manual_email', 0, 1, '2026-04-03 23:22:04', '2026-04-03 23:22:04'),
(43, 'gallery', 'GALLERY-1775258526-2', '107', '107', NULL, NULL, 40.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69d04b9c1894a0.76506458_thumb.jpg', 'manual_email', 0, 1, '2026-04-03 23:22:06', '2026-04-03 23:22:06'),
(44, 'gallery', 'GALLERY-1778160639', 'hfgzfdgZG', 'test-watermark', 'descwatertest', 'descwatertestdescwatertestdescwatertestdescwatertest', 15.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69fc93fd999032.85402749_thumb.jpg', 'manual_email', 0, 0, '2026-05-07 13:30:39', '2026-05-07 13:31:40'),
(45, 'gallery', 'GALLERY-1778160803', 'gAFAfgsdfgdsgdfgz', 'test2545', 'testewatermark2', 'testewatermark2testewatermark2testewatermark2', 12.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69fc94a0db3a36.63534746_thumb.jpg', 'manual_email', 0, 0, '2026-05-07 13:33:23', '2026-05-07 13:36:20'),
(46, 'gallery', 'GALLERY-1778161004', 'hxfxdfgzxfgzfgz', 'gSDGSDGdfrgdfhzdfhg', 'fgzdfhzdfhzdfhz', 'fhzhzdfgzdhfdh', 2.00, 0, 0, 0, 'assets/uploads/gallery/thumbs/gallery_69fc95696487b9.01125198_thumb.jpg', 'manual_email', 0, 1, '2026-05-07 13:36:44', '2026-05-07 13:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `item_categories`
--

CREATE TABLE `item_categories` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_categories`
--

INSERT INTO `item_categories` (`item_id`, `category_id`) VALUES
(2, 2),
(5, 2),
(32, 4);

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `display_order` int(10) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`id`, `item_id`, `image_path`, `display_order`, `created_at`) VALUES
(13, 32, 'assets/uploads/products/gallery/prod_gal_69cbc64567ea82.14883591_blue_ferrari_sports_car-wallpaper-3554x1999.jpg', 1, '2026-03-31 13:04:05'),
(14, 32, 'assets/uploads/products/gallery/prod_gal_69cbc645684444.86004793_maserati_mc20_sports_car-wallpaper-3554x1999.jpg', 2, '2026-03-31 13:04:05'),
(15, 32, 'assets/uploads/products/gallery/prod_gal_69cbc645688b50.24767658_novitec_ferrari_sf90xx_hybrid_supercar_luxury_side_view-wallpaper-3554x1999.jpg', 3, '2026-03-31 13:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `item_options`
--

CREATE TABLE `item_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `option_name` varchar(100) NOT NULL,
  `option_value` varchar(150) NOT NULL,
  `extra_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_options`
--

INSERT INTO `item_options` (`id`, `item_id`, `option_name`, `option_value`, `extra_price`, `is_active`, `sort_order`) VALUES
(50, 9, 'file_format', 'jpg', 0.00, 1, 0),
(51, 9, 'file_format', 'png', 5.00, 1, 1),
(52, 9, 'file_format', 'web', 5.00, 1, 2),
(53, 9, 'license', 'personal', 0.00, 1, 0),
(54, 9, 'license', 'comercial', 30.00, 1, 1),
(55, 9, 'resolution', 'web', 0.00, 1, 0),
(56, 9, 'resolution', 'high_res_print', 10.00, 1, 1),
(71, 10, 'file_format', 'jpg', 0.00, 1, 0),
(72, 10, 'file_format', 'png', 5.00, 1, 1),
(73, 10, 'file_format', 'web', 5.00, 1, 2),
(74, 10, 'license', 'personal', 0.00, 1, 0),
(75, 10, 'license', 'comercial', 25.00, 1, 1),
(76, 10, 'resolution', 'web', 0.00, 1, 0),
(77, 10, 'resolution', 'high_res_print', 10.00, 1, 1),
(78, 11, 'file_format', 'jpg', 0.00, 1, 1),
(79, 11, 'file_format', 'tif', 10.00, 1, 2),
(80, 11, 'resolution_package', 'web', 0.00, 1, 1),
(81, 11, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(82, 11, 'usage', 'personal', 0.00, 1, 1),
(83, 11, 'usage', 'commercial', 50.00, 1, 2),
(84, 12, 'file_format', 'jpg', 0.00, 1, 1),
(85, 12, 'file_format', 'tif', 10.00, 1, 2),
(86, 12, 'resolution_package', 'web', 0.00, 1, 1),
(87, 12, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(88, 12, 'usage', 'personal', 0.00, 1, 1),
(89, 12, 'usage', 'commercial', 50.00, 1, 2),
(90, 13, 'file_format', 'jpg', 0.00, 1, 1),
(91, 13, 'file_format', 'tif', 10.00, 1, 2),
(92, 13, 'resolution_package', 'web', 0.00, 1, 1),
(93, 13, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(94, 13, 'usage', 'personal', 0.00, 1, 1),
(95, 13, 'usage', 'commercial', 50.00, 1, 2),
(96, 14, 'file_format', 'jpg', 0.00, 1, 1),
(97, 14, 'file_format', 'tif', 10.00, 1, 2),
(98, 14, 'resolution_package', 'web', 0.00, 1, 1),
(99, 14, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(100, 14, 'usage', 'personal', 0.00, 1, 1),
(101, 14, 'usage', 'commercial', 50.00, 1, 2),
(102, 15, 'file_format', 'jpg', 0.00, 1, 1),
(103, 15, 'file_format', 'tif', 10.00, 1, 2),
(104, 15, 'resolution_package', 'web', 0.00, 1, 1),
(105, 15, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(106, 15, 'usage', 'personal', 0.00, 1, 1),
(107, 15, 'usage', 'commercial', 50.00, 1, 2),
(108, 16, 'file_format', 'jpg', 0.00, 1, 1),
(109, 16, 'file_format', 'tif', 10.00, 1, 2),
(110, 16, 'resolution_package', 'web', 0.00, 1, 1),
(111, 16, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(112, 16, 'usage', 'personal', 0.00, 1, 1),
(113, 16, 'usage', 'commercial', 50.00, 1, 2),
(114, 17, 'file_format', 'jpg', 0.00, 1, 1),
(115, 17, 'file_format', 'tif', 10.00, 1, 2),
(116, 17, 'resolution_package', 'web', 0.00, 1, 1),
(117, 17, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(118, 17, 'usage', 'personal', 0.00, 1, 1),
(119, 17, 'usage', 'commercial', 50.00, 1, 2),
(120, 18, 'file_format', 'jpg', 0.00, 1, 1),
(121, 18, 'file_format', 'tif', 5.00, 1, 2),
(122, 18, 'resolution_package', 'web', 5.00, 1, 1),
(123, 18, 'resolution_package', 'print_high_res', 10.00, 1, 2),
(124, 18, 'usage', 'personal', 0.00, 1, 1),
(125, 18, 'usage', 'commercial', 25.00, 1, 2),
(126, 19, 'file_format', 'jpg', 0.00, 1, 1),
(127, 19, 'file_format', 'tif', 5.00, 1, 2),
(128, 19, 'resolution_package', 'web', 5.00, 1, 1),
(129, 19, 'resolution_package', 'print_high_res', 10.00, 1, 2),
(130, 19, 'usage', 'personal', 0.00, 1, 1),
(131, 19, 'usage', 'commercial', 25.00, 1, 2),
(132, 20, 'file_format', 'jpg', 0.00, 1, 1),
(133, 20, 'file_format', 'tif', 5.00, 1, 2),
(134, 20, 'resolution_package', 'web', 5.00, 1, 1),
(135, 20, 'resolution_package', 'print_high_res', 10.00, 1, 2),
(136, 20, 'usage', 'personal', 0.00, 1, 1),
(137, 20, 'usage', 'commercial', 25.00, 1, 2),
(138, 21, 'file_format', 'jpg', 0.00, 1, 1),
(139, 21, 'file_format', 'tif', 5.00, 1, 2),
(140, 21, 'resolution_package', 'web', 5.00, 1, 1),
(141, 21, 'resolution_package', 'print_high_res', 10.00, 1, 2),
(142, 21, 'usage', 'personal', 0.00, 1, 1),
(143, 21, 'usage', 'commercial', 25.00, 1, 2),
(150, 23, 'file_format', 'jpg', 0.00, 1, 1),
(151, 23, 'file_format', 'tif', 10.00, 1, 2),
(152, 23, 'resolution_package', 'web', 0.00, 1, 1),
(153, 23, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(154, 23, 'usage', 'personal', 0.00, 1, 1),
(155, 23, 'usage', 'commercial', 50.00, 1, 2),
(162, 28, 'file_format', 'jpg', 0.00, 1, 1),
(163, 28, 'file_format', 'tif', 10.00, 1, 2),
(164, 28, 'resolution_package', 'web', 0.00, 1, 1),
(165, 28, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(166, 28, 'usage', 'personal', 0.00, 1, 1),
(167, 28, 'usage', 'commercial', 50.00, 1, 2),
(174, 30, 'file_format', 'jpg', 0.00, 1, 1),
(175, 30, 'file_format', 'tif', 10.00, 1, 2),
(176, 30, 'resolution_package', 'web', 0.00, 1, 1),
(177, 30, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(178, 30, 'usage', 'personal', 0.00, 1, 1),
(179, 30, 'usage', 'commercial', 50.00, 1, 2),
(180, 31, 'file_format', 'jpg', 0.00, 1, 1),
(181, 31, 'file_format', 'tif', 10.00, 1, 2),
(182, 31, 'resolution_package', 'web', 0.00, 1, 1),
(183, 31, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(184, 31, 'usage', 'personal', 0.00, 1, 1),
(185, 31, 'usage', 'commercial', 50.00, 1, 2),
(186, 22, 'file_format', 'jpg', 0.00, 1, 1),
(187, 22, 'file_format', 'tif', 5.00, 1, 2),
(188, 22, 'resolution_package', 'web', 5.00, 1, 1),
(189, 22, 'resolution_package', 'print_high_res', 10.00, 1, 2),
(190, 22, 'usage', 'personal', 0.00, 1, 1),
(191, 22, 'usage', 'commercial', 25.00, 1, 2),
(192, 33, 'file_format', 'jpg', 0.00, 1, 1),
(193, 33, 'file_format', 'tif', 10.00, 1, 2),
(194, 33, 'resolution_package', 'web', 0.00, 1, 1),
(195, 33, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(196, 33, 'usage', 'personal', 0.00, 1, 1),
(197, 33, 'usage', 'commercial', 50.00, 1, 2),
(198, 34, 'file_format', 'jpg', 0.00, 1, 1),
(199, 34, 'file_format', 'tif', 10.00, 1, 2),
(200, 34, 'resolution_package', 'web', 0.00, 1, 1),
(201, 34, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(202, 34, 'usage', 'personal', 0.00, 1, 1),
(203, 34, 'usage', 'commercial', 50.00, 1, 2),
(204, 35, 'file_format', 'jpg', 0.00, 1, 1),
(205, 35, 'file_format', 'tif', 10.00, 1, 2),
(206, 35, 'resolution_package', 'web', 0.00, 1, 1),
(207, 35, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(208, 35, 'usage', 'personal', 0.00, 1, 1),
(209, 35, 'usage', 'commercial', 50.00, 1, 2),
(216, 36, 'file_format', 'jpg', 0.00, 1, 1),
(217, 36, 'file_format', 'tif', 10.00, 1, 2),
(218, 36, 'resolution_package', 'web', 0.00, 1, 1),
(219, 36, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(220, 36, 'usage', 'personal', 0.00, 1, 1),
(221, 36, 'usage', 'commercial', 50.00, 1, 2),
(234, 40, 'file_format', 'jpg', 0.00, 1, 1),
(235, 40, 'file_format', 'tif', 10.00, 1, 2),
(236, 40, 'resolution_package', 'web', 0.00, 1, 1),
(237, 40, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(238, 40, 'usage', 'personal', 0.00, 1, 1),
(239, 40, 'usage', 'commercial', 50.00, 1, 2),
(240, 40, 'file_format', 'jpg', 0.17, 1, 3),
(241, 44, 'file_format', 'jpg', 0.00, 1, 1),
(242, 44, 'file_format', 'tif', 10.00, 1, 2),
(243, 44, 'resolution_package', 'web', 0.00, 1, 1),
(244, 44, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(245, 44, 'usage', 'personal', 0.00, 1, 1),
(246, 44, 'usage', 'commercial', 50.00, 1, 2),
(247, 45, 'file_format', 'jpg', 0.00, 1, 1),
(248, 45, 'file_format', 'tif', 10.00, 1, 2),
(249, 45, 'resolution_package', 'web', 0.00, 1, 1),
(250, 45, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(251, 45, 'usage', 'personal', 0.00, 1, 1),
(252, 45, 'usage', 'commercial', 50.00, 1, 2),
(253, 46, 'file_format', 'jpg', 0.00, 1, 1),
(254, 46, 'file_format', 'tif', 10.00, 1, 2),
(255, 46, 'resolution_package', 'web', 0.00, 1, 1),
(256, 46, 'resolution_package', 'print_high_res', 30.00, 1, 2),
(257, 46, 'usage', 'personal', 0.00, 1, 1),
(258, 46, 'usage', 'commercial', 50.00, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `option_templates`
--

CREATE TABLE `option_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `item_type` enum('gallery','digital_product','service') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `option_templates`
--

INSERT INTO `option_templates` (`id`, `name`, `item_type`, `is_active`, `created_at`) VALUES
(1, 'Default Gallery Template', 'gallery', 1, '2026-03-17 12:50:17'),
(2, 'test1', 'gallery', 1, '2026-03-17 12:56:29'),
(3, 'test 2', 'gallery', 1, '2026-03-21 18:22:18'),
(4, 'ugeFUIgu', 'gallery', 1, '2026-03-29 12:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `option_template_items`
--

CREATE TABLE `option_template_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_id` int(10) UNSIGNED NOT NULL,
  `option_name` varchar(100) NOT NULL,
  `option_value` varchar(150) NOT NULL,
  `extra_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `option_template_items`
--

INSERT INTO `option_template_items` (`id`, `template_id`, `option_name`, `option_value`, `extra_price`, `sort_order`, `is_active`) VALUES
(1, 1, 'file_format', 'jpg', 0.00, 1, 1),
(2, 1, 'file_format', 'png', 10.00, 2, 1),
(3, 1, 'file_format', 'web', 10.00, 2, 1),
(4, 1, 'resolution_package', 'web', 0.00, 1, 1),
(5, 1, 'resolution_package', 'print_high_res', 30.00, 2, 1),
(6, 1, 'usage', 'personal', 0.00, 1, 1),
(7, 1, 'usage', 'commercial', 25.00, 2, 1),
(21, 3, 'file_format', 'jpg', 0.00, 1, 1),
(22, 3, 'file_format', 'tif', 10.00, 2, 1),
(23, 3, 'resolution_package', 'web', 0.00, 1, 1),
(24, 3, 'resolution_package', 'print_high_res', 30.00, 2, 1),
(25, 3, 'usage', 'personal', 0.00, 1, 1),
(26, 3, 'usage', 'commercial', 50.00, 2, 1),
(27, 2, 'file_format', 'jpg', 0.00, 1, 1),
(28, 2, 'file_format', 'tif', 5.00, 2, 1),
(29, 2, 'lalala', 'lalala', 80.00, 0, 1),
(30, 2, 'resolution_package', 'web', 5.00, 1, 1),
(31, 2, 'resolution_package', 'print_high_res', 10.00, 2, 1),
(32, 2, 'usage', 'personal', 0.00, 1, 1),
(33, 2, 'usage', 'commercial', 25.00, 2, 1),
(34, 4, 'file_format', 'jpg', 0.00, 1, 1),
(35, 4, 'file_format', 'tif', 10.00, 2, 1),
(36, 4, 'resolution_package', 'web', 0.00, 1, 1),
(37, 4, 'resolution_package', 'print_high_res', 30.00, 2, 1),
(38, 4, 'usage', 'personal', 0.00, 1, 1),
(39, 4, 'usage', 'commercial', 50.00, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_type` enum('digital','service','mixed') NOT NULL DEFAULT 'digital',
  `status` enum('pending','confirmed','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','awaiting_manual_confirmation','paid','not_required') NOT NULL DEFAULT 'unpaid',
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `contact_name` varchar(100) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address_line` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_type`, `status`, `payment_status`, `total_price`, `notes`, `contact_name`, `contact_email`, `contact_phone`, `city`, `address_line`, `postal_code`, `created_at`, `updated_at`) VALUES
(1, 2, 'digital', 'confirmed', 'paid', 59.98, NULL, 'Andrei Cozali', 'andrei.cozahi@gmail.com', '+40770855215', NULL, NULL, NULL, '2026-03-15 13:51:46', '2026-03-15 14:22:58'),
(3, 1, 'service', 'completed', 'paid', 0.00, 'Service: Weddings\n\nPreferred date: 2026-03-19 08:00\nLocation: Maramures\n\nDetails:\ndetails for test', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40770825741', NULL, 'Maramures', NULL, '2026-03-17 13:38:40', '2026-03-17 14:22:04'),
(4, 1, 'service', 'pending', 'not_required', 0.00, 'Service: Individual Sessions\n\nPreferred date: 2026-03-19 08:00\nLocation: test 2\n\nDetails:\ntest2details', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, 'test 2', NULL, '2026-03-17 13:52:22', '2026-03-17 13:52:22'),
(5, 1, 'mixed', 'pending', 'paid', 90.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-17 16:39:00', '2026-03-20 13:39:13'),
(6, 1, 'service', 'pending', 'not_required', 0.00, 'Service: Automotive\n\nPreferred date: 2026-03-27 15:00\nLocation: Venise\n\nDetails:\nMazda C7', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, 'Venise', NULL, '2026-03-17 17:01:17', '2026-03-17 17:01:17'),
(7, 1, 'digital', 'pending', 'paid', 1.45, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-17 18:34:02', '2026-03-17 18:36:53'),
(8, 1, 'mixed', 'cancelled', 'awaiting_manual_confirmation', 75.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-20 14:18:42', '2026-03-20 14:44:39'),
(9, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 45.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-20 15:35:01', '2026-03-20 15:35:01'),
(10, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 45.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-20 16:05:17', '2026-03-20 16:05:17'),
(11, 6, 'service', 'pending', 'not_required', 0.00, 'Service: Individual Sessions\n\nPreferred date: 2026-03-26 12:44\nLocation: Local\n\nDetails:\nyadiyada', 'Adewale Nurga', 'ade.nurga@gmail.com', '+32582411745', NULL, 'Local', NULL, '2026-03-20 17:44:49', '2026-03-20 17:44:49'),
(12, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 135.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-21 15:46:06', '2026-03-21 15:46:06'),
(13, 7, 'digital', 'processing', 'not_required', 52.47, NULL, 'Maxim Landa', 'maxim.landa@hmail.com', NULL, NULL, NULL, NULL, '2026-03-21 17:12:36', '2026-03-24 14:42:24'),
(14, 7, 'service', 'completed', 'paid', 0.00, 'Service: Weddings\nPreferred Date: 2026-04-16 at 19:24\nLocation: Lacas\n\nClient Message:\nda', 'Maxim Landa', 'maxim.landa@hmail.com', NULL, NULL, 'Lacas', NULL, '2026-03-21 17:19:32', '2026-03-24 14:59:26'),
(15, 8, 'service', 'pending', 'not_required', 0.00, 'Service: Weddings\nPreferred Date: 2026-03-26 at 17:10\nLocation: ggg\n\nClient Message:\nghdsfgs', 'Kendrick Kalamar', 'kenkalamar@yahoo.com', '+52741852522', NULL, 'ggg', NULL, '2026-03-25 15:08:47', '2026-03-25 15:08:47'),
(16, 8, 'mixed', 'pending', 'awaiting_manual_confirmation', 15.00, NULL, 'Kendrick Kalamar', 'kenkalamar@yahoo.com', '+52741852522', NULL, NULL, NULL, '2026-03-25 15:17:56', '2026-03-25 15:17:56'),
(17, 8, 'digital', 'pending', 'awaiting_manual_confirmation', 29.98, NULL, 'Kendrick Kalamar', 'kenkalamar@yahoo.com', '+52741852522', NULL, NULL, NULL, '2026-03-25 15:19:07', '2026-03-25 15:19:07'),
(18, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 40.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', NULL, NULL, NULL, NULL, '2026-03-28 15:02:09', '2026-03-28 15:02:09'),
(19, 8, 'mixed', 'processing', 'awaiting_manual_confirmation', 50.00, NULL, 'Kendrick Kalamar', 'kenkalamar@yahoo.com', '+52741852521', NULL, NULL, NULL, '2026-03-28 17:12:07', '2026-03-29 12:04:18'),
(20, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 40.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-03-29 13:30:21', '2026-03-29 13:30:21'),
(21, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 137.48, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-03-29 14:05:38', '2026-03-29 14:05:38'),
(22, 1, 'digital', 'pending', 'awaiting_manual_confirmation', 40.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-03-31 14:50:49', '2026-03-31 14:50:49'),
(23, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 50.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-03-31 15:10:48', '2026-03-31 15:10:48'),
(24, 1, 'digital', 'pending', 'awaiting_manual_confirmation', 40.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-03-31 15:11:58', '2026-03-31 15:11:58'),
(25, 9, 'service', 'confirmed', 'unpaid', 0.00, 'Service: Group Sessions\nPreferred Date: 2026-03-13 at 22:42\nLocation: dfggdsz\n\nClient Message:\nzrgzsefzsfez', 'Cioi Alex', 'calex@gmail.com', '+40785952855', NULL, 'dfggdsz', NULL, '2026-03-31 19:41:02', '2026-03-31 20:31:10'),
(26, 1, 'service', 'pending', 'not_required', 0.00, 'Service: Group Sessions\nPreferred Date: 2026-04-25\nSelected Options: Outside City, Fifty Plus Photos\nLocation: Acasuca\n\nClient Message:\nfrumos', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, 'Acasuca', NULL, '2026-03-31 22:28:46', '2026-03-31 22:28:46'),
(27, 1, 'service', 'pending', 'not_required', 0.00, 'Service: Automotive\nPreferred Date: 2026-04-30\nSelected Options: Outside City, Multiple Sessions, Premium Package\nLocation: ggg\n\nClient Message:\nggggg', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, 'ggg', NULL, '2026-03-31 22:48:15', '2026-03-31 22:48:15'),
(28, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 45.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-04-01 22:18:28', '2026-04-01 22:18:28'),
(29, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 15.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-04-02 09:56:29', '2026-04-02 09:56:29'),
(30, 1, 'digital', 'pending', 'awaiting_manual_confirmation', 52.48, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-04-02 10:54:58', '2026-04-02 10:54:58'),
(31, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 45.00, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-04-02 15:49:37', '2026-04-02 15:49:37'),
(32, 1, 'service', 'confirmed', 'not_required', 0.00, 'Service: Real Estate\nPreferred Date: 2026-04-28\nSelected Options: Outside City, Multiple Buildings\nLocation: hh\n\nClient Message:\nhh', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, 'hh', NULL, '2026-04-02 15:50:17', '2026-04-02 16:43:54'),
(33, 1, 'service', 'pending', 'not_required', 0.00, 'Service: Real Estate\nPreferred Date: 2026-04-17\nSelected Options: Outside City, Multiple Buildings\n\nClient Message:\nhh', 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-04-03 23:51:52', '2026-04-03 23:51:52'),
(34, 1, 'mixed', 'pending', 'awaiting_manual_confirmation', 94.98, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-04-15 10:31:24', '2026-04-15 10:31:24'),
(35, 2, 'mixed', 'pending', 'awaiting_manual_confirmation', 55.15, NULL, 'Andrei Cozali', 'andrei.cozahi@gmail.com', '+40770855215', NULL, NULL, NULL, '2026-05-05 16:13:29', '2026-05-05 16:13:29'),
(36, 1, 'digital', 'pending', 'awaiting_manual_confirmation', 29.98, NULL, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', NULL, NULL, NULL, '2026-05-18 16:14:49', '2026-05-18 16:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED DEFAULT NULL,
  `item_type` varchar(50) NOT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_title` varchar(255) NOT NULL,
  `selected_options` text DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `item_type`, `item_code`, `item_title`, `selected_options`, `quantity`, `unit_price`, `line_total`, `created_at`) VALUES
(1, 1, 2, 'digital_product', NULL, 'Cinematic Lightroom Pack', NULL, 2, 29.99, 59.98, '2026-03-15 13:51:46'),
(4, 3, NULL, 'service', NULL, 'Weddings', NULL, 1, 0.00, 0.00, '2026-03-17 13:38:40'),
(5, 4, NULL, 'service', NULL, 'Individual Sessions', NULL, 1, 0.00, 0.00, '2026-03-17 13:52:22'),
(6, 5, 20, 'gallery', NULL, '_DSC0345', 'file_format: tif | resolution_package: web | usage: commercial', 1, 75.00, 75.00, '2026-03-17 16:39:00'),
(7, 5, 14, 'gallery', NULL, '_DSC0008', 'file_format: jpg | resolution_package: web | usage: personal', 1, 15.00, 15.00, '2026-03-17 16:39:00'),
(8, 6, NULL, 'service', NULL, 'Automotive', NULL, 1, 0.00, 0.00, '2026-03-17 17:01:17'),
(9, 7, 4, 'digital_product', NULL, 'test', NULL, 1, 1.45, 1.45, '2026-03-17 18:34:02'),
(10, 8, 19, 'gallery', NULL, '_DSC0189-Enhanced-NR', 'file_format: jpg | resolution_package: print_high_res | usage: commercial', 1, 75.00, 75.00, '2026-03-20 14:18:42'),
(11, 9, 18, 'gallery', NULL, '_DSC0164', 'file_format: jpg | resolution_package: web | usage: personal', 1, 45.00, 45.00, '2026-03-20 15:35:01'),
(12, 10, 20, 'gallery', NULL, '_DSC0345', 'file_format: jpg | resolution_package: web | usage: personal', 1, 45.00, 45.00, '2026-03-20 16:05:17'),
(13, 11, NULL, 'service', NULL, 'Individual Sessions', NULL, 1, 0.00, 0.00, '2026-03-20 17:44:49'),
(14, 12, 18, 'gallery', NULL, '_DSC0164', 'file_format: jpg | resolution_package: web | usage: personal', 3, 45.00, 135.00, '2026-03-21 15:46:06'),
(15, 13, 5, 'digital_product', NULL, 'test2cat', NULL, 1, 52.47, 52.47, '2026-03-21 17:12:36'),
(16, 14, NULL, 'service', NULL, 'Weddings', NULL, 1, 0.00, 0.00, '2026-03-21 17:19:32'),
(17, 15, NULL, 'service', NULL, 'Weddings', NULL, 1, 0.00, 0.00, '2026-03-25 15:08:47'),
(18, 16, 28, 'gallery', NULL, '019', 'file_format: jpg | resolution_package: web | usage: personal', 1, 15.00, 15.00, '2026-03-25 15:17:56'),
(19, 17, 2, 'digital_product', NULL, 'Cinematic Lightroom Pack', NULL, 1, 29.98, 29.98, '2026-03-25 15:19:07'),
(20, 18, 12, 'gallery', NULL, '_DSC0168', 'file_format: jpg | resolution_package: web | usage: personal', 1, 40.00, 40.00, '2026-03-28 15:02:09'),
(21, 19, 31, 'gallery', NULL, 'testo3', 'file_format: jpg | resolution_package: print_high_res | usage: personal', 1, 50.00, 50.00, '2026-03-28 17:12:07'),
(22, 20, 34, 'gallery', NULL, '141', 'file_format: jpg | resolution_package: web | usage: personal', 1, 40.00, 40.00, '2026-03-29 13:30:21'),
(23, 21, 5, 'digital_product', NULL, 'test2cat', NULL, 1, 52.48, 52.48, '2026-03-29 14:05:38'),
(24, 21, 31, 'gallery', NULL, 'testo3', 'file_format: jpg | resolution_package: web | usage: personal', 1, 20.00, 20.00, '2026-03-29 14:05:38'),
(25, 21, 18, 'gallery', NULL, '_DSC0164', 'file_format: jpg | resolution_package: web | usage: personal', 1, 45.00, 45.00, '2026-03-29 14:05:38'),
(26, 21, 9, 'gallery', NULL, 'El Nino Goat Rally', 'file_format: jpg | license: personal | resolution: web', 1, 20.00, 20.00, '2026-03-29 14:05:38'),
(27, 22, 32, 'product', NULL, 'another test', NULL, 1, 40.00, 40.00, '2026-03-31 14:50:49'),
(28, 23, 33, 'gallery', NULL, 'yadiyadah', 'file_format: jpg | resolution_package: web | usage: personal', 1, 50.00, 50.00, '2026-03-31 15:10:48'),
(29, 24, 32, 'product', NULL, 'another test', NULL, 1, 40.00, 40.00, '2026-03-31 15:11:58'),
(30, 25, NULL, 'service', NULL, 'Group Sessions', NULL, 1, 0.00, 0.00, '2026-03-31 19:41:02'),
(31, 26, NULL, 'service', NULL, 'Group Sessions', NULL, 1, 0.00, 0.00, '2026-03-31 22:28:46'),
(32, 27, NULL, 'service', NULL, 'Automotive', NULL, 1, 0.00, 0.00, '2026-03-31 22:48:15'),
(33, 28, 22, 'gallery', NULL, '_DSC0595', 'file_format: jpg | resolution_package: web | usage: personal', 1, 45.00, 45.00, '2026-04-01 22:18:28'),
(34, 29, 36, 'gallery', NULL, 'yetanotehrtest', 'file_format: jpg | resolution_package: web | usage: personal', 1, 15.00, 15.00, '2026-04-02 09:56:29'),
(35, 30, 5, 'product', NULL, 'test2cat', NULL, 1, 52.48, 52.48, '2026-04-02 10:54:58'),
(36, 31, 22, 'gallery', NULL, '_DSC0595', 'file_format: jpg | resolution_package: web | usage: personal', 1, 45.00, 45.00, '2026-04-02 15:49:37'),
(37, 32, NULL, 'service', NULL, 'Real Estate', NULL, 1, 0.00, 0.00, '2026-04-02 15:50:17'),
(38, 33, NULL, 'service', NULL, 'Real Estate', NULL, 1, 0.00, 0.00, '2026-04-03 23:51:52'),
(39, 34, 36, 'gallery', NULL, 'yetanotehrtest', 'file_format: jpg | resolution_package: web | usage: commercial', 1, 65.00, 65.00, '2026-04-15 10:31:24'),
(40, 34, 2, 'product', NULL, 'Cinematic Lightroom Pack', NULL, 1, 29.98, 29.98, '2026-04-15 10:31:24'),
(41, 35, 2, 'product', NULL, 'Cinematic Lightroom Pack', NULL, 1, 29.98, 29.98, '2026-05-05 16:13:29'),
(42, 35, 40, 'gallery', NULL, 'V99 TEST', 'file_format: jpg | resolution_package: web | usage: personal', 1, 25.17, 25.17, '2026-05-05 16:13:29'),
(43, 36, 2, 'product', NULL, 'Cinematic Lightroom Pack', NULL, 1, 29.98, 29.98, '2026-05-18 16:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token_hash` varchar(255) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 5, '$2y$10$/u8YjSSzjOzEsEc5z/0djOAVMXAtaQf9EbCXrQQW/fC7066WKPh4e', '2026-03-20 14:42:28', '2026-03-20 14:43:34', '2026-03-20 12:42:28'),
(2, 2, '$2y$10$scboos6qrpq1Omt75AbvlOxhkKNtm0pLKPBz3G9WGSyroOaQsXWT.', '2026-03-21 12:12:24', '2026-03-21 12:12:27', '2026-03-21 10:12:24'),
(3, 2, '$2y$10$acKqOmz0l8sKEk5PAKg0VekSsyFvpT9oyn.O/r9Q94co0sY7FOaiy', '2026-03-21 12:12:27', '2026-03-21 12:12:31', '2026-03-21 10:12:27'),
(4, 2, '$2y$10$jLykkgDOyqEyEd7T3mcABuQiBS1uraWgmybJzMjYW6WYzZds/c3ta', '2026-03-21 12:12:31', NULL, '2026-03-21 10:12:31'),
(5, 1, '$2y$10$iu9lrE0YmSeTH4MVUhpyxufowK8rrckETGHu55eKd/AI9qanVm2f6', '2026-03-21 19:25:32', '2026-03-21 19:44:26', '2026-03-21 17:25:32'),
(6, 1, '$2y$10$x.lQh0BzUni0TL04t7MfZOiEc4Ls5Uj5jvTv1F123kyKi1d/Gg7GG', '2026-03-21 19:44:26', '2026-03-21 19:48:26', '2026-03-21 17:44:26'),
(7, 1, '$2y$10$hk8FKjGWCWIndtbVDmMhdeJx.t.o1klfxYTwPbvCcDqJoxBh4oGNy', '2026-03-21 19:48:26', NULL, '2026-03-21 17:48:26'),
(8, 7, '$2y$10$a9QwTlxRa4JrXH4LLKl4DeVZuTT8T2fEih5dmGKviKo4kIIIb2602', '2026-03-21 19:50:42', '2026-03-21 20:00:51', '2026-03-21 17:50:42'),
(9, 8, '$2y$10$uZghExv8SaJwb5E8/O4xxOIFI7Y2t90PjQpgs6bRvv6pkVKKpYEf2', '2026-03-27 23:41:21', '2026-03-28 18:35:15', '2026-03-27 21:41:21'),
(10, 8, '$2y$10$vQXTlfgUl2dj4fO374e6M.cR/NQfHMNPQkvw0Bv/FObHF9Bs14KKC', '2026-03-28 18:35:16', NULL, '2026-03-28 16:35:16'),
(11, 9, '$2y$10$oFHvnIGf7V/jFtXmq5.EaOjlEqc942fLgwia8XuPpfNKC8SWeYwRi', '2026-03-31 22:41:43', '2026-03-31 22:43:49', '2026-03-31 19:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `method_key` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `internal_reference` varchar(100) NOT NULL,
  `external_reference` varchar(100) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'EUR',
  `request_payload` longtext DEFAULT NULL,
  `response_payload` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `order_id`, `method_key`, `provider`, `internal_reference`, `external_reference`, `status`, `amount`, `currency`, `request_payload`, `response_payload`, `created_at`, `updated_at`) VALUES
(1, 8, 'manual_review', 'internal_stub', 'PAY-8-519F4124', NULL, 'completed', 75.00, 'EUR', '{\"mode\":\"stub\",\"method_key\":\"manual_review\",\"cart_count\":1}', '{\"message\":\"Payment transaction initialized in stub mode.\"}', '2026-03-20 14:18:42', '2026-03-20 14:43:13'),
(2, 9, 'card_gateway', 'future_card_gateway', 'PAY-9-EFFF222D', NULL, 'pending', 45.00, 'EUR', '{\"mode\":\"stub\",\"method_key\":\"card_gateway\",\"order_type\":\"mixed\",\"cart_count\":1}', '{\"message\":\"Payment transaction initialized in stub mode.\"}', '2026-03-20 15:35:01', '2026-03-20 15:35:01'),
(3, 10, 'card_gateway', 'future_card_gateway', 'PAY-10-0E2F5E53', NULL, 'pending', 45.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":10,\"order_type\":\"mixed\",\"method_key\":\"card_gateway\",\"amount\":45}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-20 16:05:17', '2026-03-20 16:05:17'),
(4, 12, 'bank_transfer', 'future_bank_transfer', 'PAY-12-80D38E8E', NULL, 'pending', 135.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":12,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":135}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-21 15:46:06', '2026-03-21 15:46:06'),
(5, 13, 'bank_transfer', 'future_bank_transfer', 'PAY-13-5FF892C0', NULL, 'not_required', 52.47, 'EUR', '{\"mode\":\"stub\",\"order_id\":13,\"order_type\":\"digital\",\"method_key\":\"bank_transfer\",\"amount\":52.47}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-21 17:12:36', '2026-03-24 14:42:24'),
(6, 16, 'card_gateway', 'future_card_gateway', 'PAY-16-D8D3D767', NULL, 'pending', 15.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":16,\"order_type\":\"mixed\",\"method_key\":\"card_gateway\",\"amount\":15}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-25 15:17:56', '2026-03-25 15:17:56'),
(7, 17, 'card_gateway', 'future_card_gateway', 'PAY-17-F50AD4B2', NULL, 'pending', 29.98, 'EUR', '{\"mode\":\"stub\",\"order_id\":17,\"order_type\":\"digital\",\"method_key\":\"card_gateway\",\"amount\":29.98}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-25 15:19:07', '2026-03-25 15:19:07'),
(8, 18, 'bank_transfer', 'future_bank_transfer', 'PAY-18-1DDAC459', NULL, 'pending', 40.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":18,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":40}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-28 15:02:09', '2026-03-28 15:02:09'),
(9, 19, 'card_gateway', 'future_card_gateway', 'PAY-19-B5F8B90E', NULL, 'awaiting_confirmation', 50.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":19,\"order_type\":\"mixed\",\"method_key\":\"card_gateway\",\"amount\":50}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-28 17:12:07', '2026-03-29 12:04:18'),
(10, 20, 'card_gateway', 'future_card_gateway', 'PAY-20-89992EE6', NULL, 'pending', 40.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":20,\"order_type\":\"mixed\",\"method_key\":\"card_gateway\",\"amount\":40}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-29 13:30:21', '2026-03-29 13:30:21'),
(11, 21, 'card_gateway', 'future_card_gateway', 'PAY-21-688D9A83', NULL, 'pending', 137.48, 'EUR', '{\"mode\":\"stub\",\"order_id\":21,\"order_type\":\"mixed\",\"method_key\":\"card_gateway\",\"amount\":137.48}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-29 14:05:38', '2026-03-29 14:05:38'),
(12, 22, 'bank_transfer', 'future_bank_transfer', 'PAY-22-42E566B8', NULL, 'pending', 40.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":22,\"order_type\":\"digital\",\"method_key\":\"bank_transfer\",\"amount\":40}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-31 14:50:49', '2026-03-31 14:50:49'),
(13, 23, 'bank_transfer', 'future_bank_transfer', 'PAY-23-5CFA009C', NULL, 'pending', 50.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":23,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":50}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-31 15:10:48', '2026-03-31 15:10:48'),
(14, 24, 'bank_transfer', 'future_bank_transfer', 'PAY-24-570F9903', NULL, 'pending', 40.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":24,\"order_type\":\"digital\",\"method_key\":\"bank_transfer\",\"amount\":40}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-03-31 15:11:58', '2026-03-31 15:11:58'),
(15, 28, 'bank_transfer', 'future_bank_transfer', 'PAY-28-D3FA6F24', NULL, 'pending', 45.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":28,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":45}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-04-01 22:18:28', '2026-04-01 22:18:28'),
(16, 29, 'bank_transfer', 'future_bank_transfer', 'PAY-29-C813CB62', NULL, 'pending', 15.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":29,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":15}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-04-02 09:56:29', '2026-04-02 09:56:29'),
(17, 30, 'bank_transfer', 'future_bank_transfer', 'PAY-30-6861C656', NULL, 'pending', 52.48, 'EUR', '{\"mode\":\"stub\",\"order_id\":30,\"order_type\":\"digital\",\"method_key\":\"bank_transfer\",\"amount\":52.48}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-04-02 10:54:58', '2026-04-02 10:54:58'),
(18, 31, 'bank_transfer', 'future_bank_transfer', 'PAY-31-C9BB22EA', NULL, 'pending', 45.00, 'EUR', '{\"mode\":\"stub\",\"order_id\":31,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":45}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-04-02 15:49:37', '2026-04-02 15:49:37'),
(19, 34, 'bank_transfer', 'future_bank_transfer', 'PAY-34-9FEB8A59', NULL, 'pending', 94.98, 'EUR', '{\"mode\":\"stub\",\"order_id\":34,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":94.98}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-04-15 10:31:24', '2026-04-15 10:31:24'),
(20, 35, 'bank_transfer', 'future_bank_transfer', 'PAY-35-EF73481E', NULL, 'pending', 55.15, 'EUR', '{\"mode\":\"stub\",\"order_id\":35,\"order_type\":\"mixed\",\"method_key\":\"bank_transfer\",\"amount\":55.150000000000006}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-05-05 16:13:29', '2026-05-05 16:13:29'),
(21, 36, 'bank_transfer', 'future_bank_transfer', 'PAY-36-199C3917', NULL, 'pending', 29.98, 'EUR', '{\"mode\":\"stub\",\"order_id\":36,\"order_type\":\"digital\",\"method_key\":\"bank_transfer\",\"amount\":29.98}', '{\"message\":\"Payment session created in stub mode.\",\"next_action\":\"none\"}', '2026-05-18 16:14:49', '2026-05-18 16:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_date` date DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `cover_image` varchar(255) NOT NULL,
  `narrative_text` text DEFAULT NULL,
  `theme_color` varchar(50) DEFAULT 'rgba(255, 255, 255, 0)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `slug`, `title`, `event_date`, `category`, `cover_image`, `narrative_text`, `theme_color`, `is_active`, `created_at`) VALUES
(3, 'the-mihali-s-wedding-1774872344', 'The Mihali\'s Wedding', '2018-10-06', 'Wedding', '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6734eafb37.40305713.webp', '6th October 2018\r\nA Timeless Celebration: Doru and Cristina Mihali’s Wedding Experience\r\n\r\nThe dawn of Doru and Cristina Mihali’s wedding day broke with an early start at a family member’s home, where meticulous preparations were underway. Surrounded by a close-knit group of friends and family, every detail was attended to with care and enthusiasm. The genuine interactions and intricate details of the preparations were preserved with a refined touch, ensuring that the essence of their special day was elegantly recorded.\r\n\r\nThe journey then led to a picturesque monastery near Dej, Romania, a location of deep personal significance for the couple. The ceremony unfolded with adherence to cherished Orthodox traditions, each moment steeped in spiritual and cultural meaning. The serene backdrop of the monastery and the intimate connection between Doru, Cristina, and their guests were beautifully framed, capturing the tranquility and depth of this sacred occasion.\r\n\r\nFollowing the ceremony, the celebration continued with a series of elegant family photos before the group embarked on a brief road trip to their home city. At the mayor’s office, the formalities of their union were completed, and a swift photo session in the nearby park offered an opportunity to document the newlyweds in a relaxed and natural light. These moments, effortlessly preserved, highlighted the joy and spontaneity of their day.\r\n\r\nThe evening transitioned into a vibrant celebration, where dancing and heartfelt toasts marked the joyous culmination of the day’s events. The atmosphere was richly conveyed, from the lively dance floor to the touching toasts, reflecting the warmth and energy of the gathering.\r\n\r\nThe following day, the festivities extended with a special gathering for the couple’s grandmother’s birthday, adding a personal and heartfelt touch to the celebrations. This additional event, filled with delightful food and joyful conversation, was captured with an eye for detail, showcasing the close bonds of the family. The result is a collection of images that seamlessly weaves together the love, joy, and memorable moments of Doru and Cristina’s wedding journey.', '#d4af37', 1, '2026-03-30 12:05:44'),
(4, 'retromobile--1774902624', 'Retromobile ', '2024-05-19', 'Automotive', '/assets/images/portfolio/retromobile--1774902624/img_69cadd9081fef0.31832188.webp', 'Something about this event.', '#68cdee', 1, '2026-03-30 20:30:24');

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `aspect_ratio` decimal(5,3) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `file_path`, `sort_order`, `aspect_ratio`, `is_active`, `created_at`) VALUES
(59, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6718858c81.40643579.webp', 0, 1.410, 1, '2026-03-30 12:05:46'),
(60, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca671a23fe83.24483956.webp', 2, 1.387, 1, '2026-03-30 12:05:47'),
(61, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca671ba441a1.99491666.webp', 3, 1.500, 1, '2026-03-30 12:05:49'),
(62, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca671d25ead6.69197934.webp', 4, 1.362, 1, '2026-03-30 12:05:50'),
(63, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca671ecdef02.76102014.webp', 5, 1.500, 1, '2026-03-30 12:05:52'),
(64, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca67205fad05.46816093.webp', 6, 1.500, 1, '2026-03-30 12:05:53'),
(65, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6721f27539.80666258.webp', 8, 1.500, 1, '2026-03-30 12:05:55'),
(66, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6723ab4736.32083282.webp', 9, 1.343, 1, '2026-03-30 12:05:57'),
(67, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6725475f27.84265116.webp', 10, 1.500, 1, '2026-03-30 12:05:58'),
(68, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6726e34122.79631752.webp', 14, 1.500, 1, '2026-03-30 12:06:00'),
(69, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca67289c5000.71269222.webp', 12, 1.500, 1, '2026-03-30 12:06:02'),
(70, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca672a623df4.13391851.webp', 16, 0.826, 1, '2026-03-30 12:06:03'),
(71, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca672bc000c6.88247917.webp', 19, 1.500, 1, '2026-03-30 12:06:05'),
(72, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca672d621cf3.95069086.webp', 20, 0.836, 1, '2026-03-30 12:06:06'),
(73, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca672ebcf7f5.84407584.webp', 22, 1.500, 1, '2026-03-30 12:06:08'),
(74, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6730431414.06188279.webp', 23, 1.500, 1, '2026-03-30 12:06:09'),
(75, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6731efb5b8.59932048.webp', 13, 1.334, 1, '2026-03-30 12:06:11'),
(76, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca67333a08f5.96014706.webp', 15, 1.500, 1, '2026-03-30 12:06:12'),
(77, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6734eafb37.40305713.webp', 7, 1.500, 1, '2026-03-30 12:06:14'),
(78, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6736a01584.52692413.webp', 11, 1.500, 1, '2026-03-30 12:06:16'),
(79, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca67385d5bc8.31837395.webp', 18, 1.168, 1, '2026-03-30 12:06:18'),
(80, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca673a1c79a7.67778202.webp', 24, 1.250, 1, '2026-03-30 12:06:19'),
(81, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca673b86b061.96999187.webp', 25, 1.250, 1, '2026-03-30 12:06:21'),
(82, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca673d0865c4.48034054.webp', 26, 1.250, 1, '2026-03-30 12:06:22'),
(83, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca673e7fe312.96777859.webp', 27, 1.500, 1, '2026-03-30 12:06:24'),
(84, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6740360119.19211279.webp', 17, 1.500, 1, '2026-03-30 12:06:25'),
(85, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6741d72c57.54190583.webp', 28, 1.463, 1, '2026-03-30 12:06:27'),
(86, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca67438b8a79.18049723.webp', 21, 0.818, 1, '2026-03-30 12:06:28'),
(87, 3, '/assets/images/portfolio/the-mihali-s-wedding-1774872344/img_69ca6744c5b256.44967334.webp', 1, 1.469, 1, '2026-03-30 12:06:30'),
(88, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd60506489.28248193.webp', 0, 0.800, 1, '2026-03-30 20:30:25'),
(89, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd61c09a81.25985052.webp', 1, 1.778, 1, '2026-03-30 20:30:27'),
(90, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd630148c4.38318133.webp', 2, 0.800, 1, '2026-03-30 20:30:28'),
(91, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd647ed0b2.67838338.webp', 3, 0.800, 1, '2026-03-30 20:30:30'),
(92, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd66149b34.78995464.webp', 4, 1.250, 1, '2026-03-30 20:30:31'),
(93, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd6791aa52.12855784.webp', 5, 1.250, 1, '2026-03-30 20:30:33'),
(94, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd690bc5a6.13882218.webp', 6, 1.778, 1, '2026-03-30 20:30:34'),
(95, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd6a52def1.44765614.webp', 7, 1.778, 1, '2026-03-30 20:30:35'),
(96, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd6b9fee97.93816495.webp', 8, 1.778, 1, '2026-03-30 20:30:36'),
(97, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd6cede817.39255108.webp', 9, 1.778, 1, '2026-03-30 20:30:38'),
(98, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd6e4805a5.48912350.webp', 10, 0.800, 1, '2026-03-30 20:30:39'),
(99, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd6fdc1ab4.97186256.webp', 11, 0.800, 1, '2026-03-30 20:30:41'),
(100, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd7168f0f8.04306381.webp', 12, 0.800, 1, '2026-03-30 20:30:42'),
(101, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd72f29454.49351848.webp', 13, 1.500, 1, '2026-03-30 20:30:44'),
(102, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd747cc260.00310265.webp', 14, 0.800, 1, '2026-03-30 20:30:46'),
(103, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd760acc36.24993009.webp', 15, 1.778, 1, '2026-03-30 20:30:47'),
(104, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd77579007.35894036.webp', 16, 0.800, 1, '2026-03-30 20:30:48'),
(105, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd78d7d8a9.81515637.webp', 17, 0.800, 1, '2026-03-30 20:30:50'),
(106, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd7a5ed857.95364949.webp', 18, 1.778, 1, '2026-03-30 20:30:51'),
(107, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd7ba78d88.58759509.webp', 19, 0.800, 1, '2026-03-30 20:30:53'),
(108, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd7d2d0ae3.02735669.webp', 20, 0.667, 1, '2026-03-30 20:30:54'),
(109, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd7ea9f6e8.85049289.webp', 21, 1.500, 1, '2026-03-30 20:30:56'),
(110, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd802e82c6.89422040.webp', 22, 0.800, 1, '2026-03-30 20:30:57'),
(111, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd819693b0.61771211.webp', 23, 1.500, 1, '2026-03-30 20:30:59'),
(112, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd83207c55.51724701.webp', 24, 0.800, 1, '2026-03-30 20:31:00'),
(113, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd849e29f2.83823931.webp', 25, 1.250, 1, '2026-03-30 20:31:02'),
(114, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd862a1f36.39252085.webp', 26, 1.500, 1, '2026-03-30 20:31:03'),
(115, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd87b1ca37.79642700.webp', 27, 0.667, 1, '2026-03-30 20:31:05'),
(116, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd8949c465.75570242.webp', 28, 1.250, 1, '2026-03-30 20:31:06'),
(117, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd8ac25e81.72055883.webp', 29, 0.800, 1, '2026-03-30 20:31:08'),
(118, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd8c3ba655.82356230.webp', 30, 1.500, 1, '2026-03-30 20:31:09'),
(119, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd8dade9e7.02904090.webp', 31, 1.778, 1, '2026-03-30 20:31:11'),
(120, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd8f01eee2.30640967.webp', 32, 0.791, 1, '2026-03-30 20:31:12'),
(121, 4, '/assets/images/portfolio/retromobile--1774902624/img_69cadd9081fef0.31832188.webp', 33, 0.800, 1, '2026-03-30 20:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('client','admin') NOT NULL DEFAULT 'client',
  `remember_token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `remember_token`, `is_active`, `created_at`, `updated_at`, `last_activity`) VALUES
(1, 'Ontijt Sebastian', 'sebastian.ontijt@yahoo.com', '+40', '$2y$10$iqCmC6faD/KYavYT2QEu3.B2L7fh7QOKPJYh8Bcw0T78uUlKX5DFa', 'admin', NULL, 1, '2026-03-14 12:15:02', '2026-03-29 12:01:09', '2026-06-05 16:45:51'),
(2, 'Andrei Cozali', 'andrei.cozahi@gmail.com', '+40770855215', '$2y$10$65.z/UA5vscjmLy1a/vQfupLUih8WlklOXNpITmTSu4196IaFRJZi', 'client', NULL, 1, '2026-03-14 15:41:08', '2026-03-29 12:01:24', '2026-06-03 13:54:52'),
(3, 'Maria Texali', 'maria.texali@gmail.com', NULL, '$2y$10$2beLV0ka25F/Wlr4Ss7WteST7iN4.9ZrmfNWIlIFa8v2ZWbUbG0U.', 'client', NULL, 1, '2026-03-14 16:00:52', '2026-03-14 16:00:52', NULL),
(4, 'Max Hoi', 'max.hoi3@gmail.com', '+40743921542', '$2y$10$ksbHo5Q0QkrHbkZKg5tMx.09e2K8jF/ZdOa4VHtNLGrPMeKav.d0W', 'client', NULL, 1, '2026-03-14 16:12:03', '2026-03-14 16:14:17', NULL),
(5, 'Adi Medas', 'adi.medas98@fmail.com', '+40752814763', '$2y$10$LeFOZNtfI8oELzXXp2bjou5I22zHL5r0tItaMSSjrkDcZGcVKNPgy', 'client', NULL, 1, '2026-03-20 12:04:17', '2026-03-20 12:43:34', NULL),
(6, 'Adewale Nurga', 'ade.nurga@gmail.com', '+32582411745', '$2y$10$k1kEJv4EpwsJ.9UGUJYyCuhgjhqG0Cc79MMlY4PTl6J.4AR4rCtt2', 'client', NULL, 1, '2026-03-20 16:48:03', '2026-03-20 17:44:16', NULL),
(7, 'Maxim Landa', 'maxim.landa@gmail.com', '', '$2y$10$bcfBkuFvc3exQ6KMmlnXUe60heL9TFN1bQ/P129XZEZR8mRkkA22.', 'client', NULL, 1, '2026-03-21 17:05:23', '2026-03-31 20:25:16', NULL),
(8, 'Kendrick Kalamar', 'kenkalamar@yahoo.com', '+52741852521', '$2y$10$uWR09isTCuX81gYaaPoo6uWMpdIknDE9pcjlG8/Y3ZvVNhMSIxpjO', 'client', NULL, 1, '2026-03-25 15:04:26', '2026-04-03 23:23:30', '2026-03-31 22:29:58'),
(9, 'Cioi Alexis', 'calex@gmail.com', '+40785952855', '$2y$10$MBKHcZIgCD/JTi2YoeEMB.4bw3bNH3t6qFW2XfSpWNqG.gH.ashti', 'client', NULL, 1, '2026-03-31 19:39:13', '2026-03-31 19:43:49', '2026-03-31 22:41:02'),
(10, 'Ontijt Rodica', 'ontijtrodica@yahoo.com', '+40725632147', '$2y$10$NzIo1H6KBAs5Xf/CmIjSbOli328sDnfbv7nBFKj4T9ct3nniMVw2u', 'client', NULL, 0, '2026-04-01 17:31:20', '2026-05-19 10:25:07', '2026-04-02 00:42:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `gallery_item_types`
--
ALTER TABLE `gallery_item_types`
  ADD PRIMARY KEY (`item_id`,`type_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `gallery_metadata`
--
ALTER TABLE `gallery_metadata`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `gallery_types`
--
ALTER TABLE `gallery_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`item_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `item_options`
--
ALTER TABLE `item_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `option_templates`
--
ALTER TABLE `option_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `option_template_items`
--
ALTER TABLE `option_template_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_option_template_items_template` (`template_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_user_id` (`user_id`),
  ADD KEY `idx_password_resets_expires_at` (`expires_at`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_payment_transactions_internal_reference` (`internal_reference`),
  ADD KEY `idx_payment_transactions_order_id` (`order_id`),
  ADD KEY `idx_payment_transactions_status` (`status`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery_types`
--
ALTER TABLE `gallery_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `item_options`
--
ALTER TABLE `item_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT for table `option_templates`
--
ALTER TABLE `option_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `option_template_items`
--
ALTER TABLE `option_template_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery_item_types`
--
ALTER TABLE `gallery_item_types`
  ADD CONSTRAINT `gallery_item_types_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gallery_item_types_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `gallery_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery_metadata`
--
ALTER TABLE `gallery_metadata`
  ADD CONSTRAINT `gallery_metadata_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD CONSTRAINT `item_categories_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `item_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_options`
--
ALTER TABLE `item_options`
  ADD CONSTRAINT `item_options_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `option_template_items`
--
ALTER TABLE `option_template_items`
  ADD CONSTRAINT `fk_option_template_items_template` FOREIGN KEY (`template_id`) REFERENCES `option_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `fk_payment_transactions_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `fk_project_images` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
