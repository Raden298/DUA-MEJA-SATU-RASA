-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 03:41 PM
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
-- Database: `dua_meja_satu_rasa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `name_id` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `description_id` text DEFAULT NULL,
  `description_en` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name_id`, `name_en`, `description_id`, `description_en`) VALUES
(1, 'Signature Fusion', 'Signature Fusion', NULL, NULL),
(2, 'Makanan Utama', 'Main Course', NULL, NULL),
(3, 'Dessert', 'Dessert', NULL, NULL),
(4, 'Minuman Segar', 'Fresh Drink', NULL, NULL),
(5, 'Beverages', 'Beverages', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description_id` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_signature` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description_id`, `description_en`, `price`, `is_signature`, `is_active`, `image_path`) VALUES
(1, 1, 'Sambal Rendang Pasta', 'Al dente spaghetti dengan saus rendang creamy, disajikan dengan sambal terasi halus di atasnya.', 'Al dente spaghetti served with creamy rendang sauce and a touch of spicy terasi sambal.', 320000.00, 1, 1, NULL),
(2, 1, 'Sate Carbonara Skewer', 'Sate ayam panggang dengan saus carbonara parmesan, ditaburi bawang goreng dan parsley.', 'Grilled chicken skewers with parmesan carbonara sauce, topped with crispy shallots and parsley.', 295000.00, 1, 1, NULL),
(3, 1, 'Margherita Bali Matah', 'Pizza tipis ala Italia dengan keju mozzarella, tomat, basil, dan sambal matah segar.', 'Thin-crust Italian-style pizza with mozzarella, tomato, basil, and fresh sambal matah.', 350000.00, 1, 1, NULL),
(4, 2, 'Nasi Risotto Nusantara', 'Risotto creamy dimasak dengan kaldu ayam dan rempah Nusantara, disajikan dengan suwiran ayam bumbu kuning.', 'Creamy risotto cooked with chicken broth and Indonesian spices, served with shredded yellow-spice chicken.', 250000.00, 0, 1, NULL),
(5, 2, 'Lasagna Daun Pisang', 'Lasagna beef bolognese dipanggang dalam balutan daun pisang dengan aroma bakaran khas Nusantara.', 'Beef bolognese lasagna baked inside banana leaf, with a smoky Nusantara aroma.', 275000.00, 0, 1, NULL),
(6, 2, 'Ayam Geprek Parmigiana', 'Ayam crispy ala parmigiana dengan saus tomat pedas, keju leleh, dan sambal geprek di samping.', 'Crispy parmigiana-style chicken with spicy tomato sauce, melted cheese, and sambal geprek on the side.', 230000.00, 0, 1, NULL),
(7, 2, 'Penne Bumbu Kare Padang', 'Penne al dente dalam saus kare kental berpadu bumbu Padang dengan potongan daging empuk.', 'Al dente penne pasta in rich curry sauce blended with Padang spices and tender beef pieces.', 245000.00, 0, 1, NULL),
(8, 2, 'Iga Bakar Herb Italiano', 'Iga sapi bakar bumbu kecap dengan bawang putih dan rosemary, disajikan dengan mashed singkong.', 'Grilled beef ribs in sweet soy, garlic, and rosemary, served with cassava mash.', 390000.00, 0, 1, NULL),
(9, 3, 'Tiramisu Ketan Hitam', 'Layer tiramisu klasik dipadukan dengan ketan hitam manis dan sedikit santan.', 'Classic tiramisu layered with sweet black glutinous rice and a touch of coconut milk.', 150000.00, 0, 1, NULL),
(10, 3, 'Panna Cotta Es Teler', 'Panna cotta vanila lembut dengan topping alpukat, kelapa muda, dan nangka ala es teler.', 'Silky vanilla panna cotta topped with avocado, young coconut, and jackfruit in es teler style.', 145000.00, 0, 1, NULL),
(11, 3, 'Gelato Klepon', 'Gelato rasa pandan dengan swirl gula merah dan taburan kelapa parut sangrai.', 'Pandan-flavored gelato with palm sugar swirl and toasted grated coconut.', 120000.00, 0, 1, NULL),
(12, 3, 'Cannoli Ronde Jahe', 'Cannoli renyah isi krim mascarpone beraroma jahe dan kacang tanah.', 'Crispy cannoli shells filled with ginger-infused mascarpone cream and crushed peanuts.', 140000.00, 0, 1, NULL),
(13, 3, 'Affogato Kopyor', 'Es krim kelapa kopyor disiram espresso panas.', 'Kopyor coconut ice cream served with a shot of hot espresso.', 130000.00, 0, 1, NULL),
(14, 4, 'Italian Lime Serai Spritz', 'Minuman soda jeruk nipis dengan infused serai dan daun jeruk, disajikan dingin.', 'Sparkling lime drink infused with lemongrass and kaffir lime leaves, served chilled.', 95000.00, 0, 1, NULL),
(15, 4, 'Basil Mango Lassi Fizz', 'Lassi mangga segar dengan sentuhan basil dan soda ringan.', 'Fresh mango lassi with a hint of basil and a touch of soda.', 110000.00, 0, 1, NULL),
(16, 4, 'Es Jeruk Amalfi Nusantara', 'Es jeruk peras lokal dengan sedikit zest lemon Italia dan madu.', 'Fresh local orange juice with a bit of Italian lemon zest and honey.', 85000.00, 0, 1, NULL),
(17, 4, 'Strawberry Bandung Sparkle', 'Minuman susu stroberi ringan dengan soda dan aroma rose.', 'Light strawberry milk drink with soda and a hint of rose aroma.', 95000.00, 0, 1, NULL),
(18, 4, 'Teh Rosella Sicilian', 'Teh rosella dingin dengan irisan jeruk dan sirup orange.', 'Iced rosella tea with orange slices and a dash of orange syrup.', 90000.00, 0, 1, NULL),
(19, 5, 'Kopi Tubruk Espresso Blend', 'Kopi tubruk robusta dengan tambahan shot espresso, rasa kuat dan bold.', 'Traditional robusta tubruk coffee with an extra shot of espresso for a strong, bold flavor.', 75000.00, 0, 1, NULL),
(20, 5, 'Latte Gula Aren Affogato', 'Caffè latte creamy dengan gula aren, bisa ditambah scoop es krim sebagai affogato.', 'Creamy caffè latte with palm sugar, optionally topped with a scoop of ice cream as affogato.', 95000.00, 0, 1, NULL),
(21, 5, 'Teh Tarik Macchiato', 'Teh tarik dengan foam susu ala macchiato, disajikan hangat.', 'Pulled milk tea with macchiato-style milk foam, served warm.', 85000.00, 0, 1, NULL),
(22, 5, 'Chocolate Hazelnut Wedang', 'Cokelat panas hazelnut dengan jahe dan kayu manis ala wedang.', 'Hot hazelnut chocolate with ginger and cinnamon in wedang style.', 90000.00, 0, 1, NULL),
(23, 5, 'Kopi Susu Roma Senja', 'Kopi susu gula aren dengan cinnamon dan sedikit zest jeruk.', 'Milk coffee with palm sugar, cinnamon, and a touch of orange zest.', 80000.00, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `guests` int(11) NOT NULL DEFAULT 2,
  `notes` text DEFAULT NULL,
  `payment_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `reservation_code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `code`, `capacity`, `is_active`) VALUES
(1, 'A1', 2, 1),
(2, 'A2', 2, 1),
(3, 'A3', 2, 1),
(4, 'A4', 2, 1),
(5, 'A5', 2, 1),
(6, 'B1', 2, 1),
(7, 'B2', 2, 1),
(8, 'B3', 2, 1),
(9, 'B4', 2, 1),
(10, 'B5', 2, 1),
(11, 'C1', 4, 1),
(12, 'C2', 4, 1),
(13, 'C3', 4, 1),
(14, 'C4', 4, 1),
(15, 'C5', 4, 1),
(16, 'D1', 6, 1),
(17, 'D2', 6, 1),
(18, 'D3', 6, 1),
(19, 'D4', 6, 1),
(20, 'D5', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_code` (`reservation_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_id` (`table_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
