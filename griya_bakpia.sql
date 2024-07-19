-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Jul 2024 pada 05.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `griya_bakpia`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_user`
--

CREATE TABLE `auth_user` (
  `auth_user_id` int(10) UNSIGNED NOT NULL,
  `auth_user_user_id` int(10) UNSIGNED NOT NULL,
  `auth_user_username` varchar(255) NOT NULL,
  `auth_user_token` varchar(255) NOT NULL,
  `auth_user_date_login` datetime DEFAULT NULL,
  `auth_user_date_expired` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_user`
--

INSERT INTO `auth_user` (`auth_user_id`, `auth_user_user_id`, `auth_user_username`, `auth_user_token`, `auth_user_date_login`, `auth_user_date_expired`) VALUES
(1, 1, 'bakpia', 'YmFrcGlhMTIzNDU2', '2024-07-02 09:56:06', '2024-07-02 03:56:06'),
(2, 2, 'bakpia2', 'YmFrcGlhMjEyMzQ1Ng==', '2024-06-14 16:49:44', '2024-06-14 10:49:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `category`
--

CREATE TABLE `category` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_created_at` datetime DEFAULT NULL,
  `category_updated_at` datetime DEFAULT NULL,
  `category_deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_created_at`, `category_updated_at`, `category_deleted_at`) VALUES
(1, 'Basah', '2024-06-13 11:52:11', '2024-06-14 14:58:22', NULL),
(2, 'Kering', '2024-06-13 11:52:12', NULL, NULL),
(3, 'test', '2024-06-14 13:38:33', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product`
--

CREATE TABLE `product` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` int(11) DEFAULT NULL,
  `product_type_id` int(10) UNSIGNED NOT NULL,
  `product_type_name` varchar(255) NOT NULL,
  `product_category_id` int(10) UNSIGNED NOT NULL,
  `product_category_name` varchar(255) NOT NULL,
  `product_value_id` int(10) UNSIGNED NOT NULL,
  `product_value_value` int(11) NOT NULL,
  `product_created_at` datetime DEFAULT NULL,
  `product_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_price`, `product_type_id`, `product_type_name`, `product_category_id`, `product_category_name`, `product_value_id`, `product_value_value`, `product_created_at`, `product_updated_at`) VALUES
(1, 'Kacang Hijau', 40000, 1, 'Griya Bakpia Premium', 1, 'Basah', 1, 15, '2024-06-13 13:57:25', '2024-06-13 14:53:53'),
(2, 'Keju', 40000, 1, 'Griya Bakpia Premium', 1, 'Basah', 1, 15, '2024-06-13 13:57:36', NULL),
(3, 'Coklat', 40000, 1, 'Griya Bakpia Premium', 1, 'Basah', 1, 15, '2024-06-13 13:57:41', NULL),
(4, 'Durian', 40000, 1, 'Griya Bakpia Premium', 1, 'Basah', 1, 15, '2024-06-13 13:57:48', NULL),
(5, 'Mix', 42000, 1, 'Griya Bakpia Premium', 1, 'Basah', 1, 15, '2024-06-13 13:57:55', NULL),
(6, 'Kacang Hijau Originial', 24000, 2, 'Bakpia 465', 1, 'Basah', 2, 20, '2024-06-13 13:58:25', NULL),
(7, 'Kacang Hijau Rasa Keju', 26000, 2, 'Bakpia 465', 1, 'Basah', 2, 20, '2024-06-13 13:58:42', NULL),
(8, 'Kacang Hijau Rasa Coklat', 26000, 2, 'Bakpia 465', 1, 'Basah', 2, 20, '2024-06-13 13:58:52', NULL),
(9, 'Telo Ungu', 24000, 2, 'Bakpia 465', 1, 'Basah', 1, 15, '2024-06-13 13:59:10', NULL),
(10, 'Keju', 24000, 3, 'Bakpia 465 Kering', 2, 'Kering', 2, 20, '2024-06-13 13:59:25', NULL),
(11, 'Coklat', 24000, 3, 'Bakpia 465 Kering', 2, 'Kering', 2, 20, '2024-06-13 13:59:30', NULL),
(12, 'Aneka Rasa', 24000, 3, 'Bakpia 465 Kering', 2, 'Kering', 2, 20, '2024-06-13 13:59:50', NULL),
(13, 'Mix Keju Coklat', 24000, 3, 'Bakpia 465 Kering', 2, 'Kering', 2, 20, '2024-06-13 14:00:00', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `type`
--

CREATE TABLE `type` (
  `type_id` int(10) UNSIGNED NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_created_at` datetime DEFAULT NULL,
  `type_updated_at` datetime DEFAULT NULL,
  `type_deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `type`
--

INSERT INTO `type` (`type_id`, `type_name`, `type_created_at`, `type_updated_at`, `type_deleted_at`) VALUES
(1, 'Griya Bakpia Premium', '2024-06-13 13:55:59', '2024-06-14 15:33:55', NULL),
(2, 'Bakpia 465', '2024-06-13 13:55:59', NULL, NULL),
(3, 'Bakpia 465 Kering', '2024-06-13 13:56:31', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`user_id`, `user_username`, `user_password`) VALUES
(1, 'bakpia', '123456'),
(2, 'bakpia2', '123456');

-- --------------------------------------------------------

--
-- Struktur dari tabel `value`
--

CREATE TABLE `value` (
  `value_id` int(10) UNSIGNED NOT NULL,
  `value_value` int(11) NOT NULL,
  `value_created_at` datetime DEFAULT NULL,
  `value_updated_at` datetime DEFAULT NULL,
  `value_deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `value`
--

INSERT INTO `value` (`value_id`, `value_value`, `value_created_at`, `value_updated_at`, `value_deleted_at`) VALUES
(1, 10, '2024-06-13 09:57:48', '2024-06-14 16:42:25', NULL),
(2, 20, '2024-06-13 09:57:54', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `auth_user`
--
ALTER TABLE `auth_user`
  ADD PRIMARY KEY (`auth_user_id`),
  ADD KEY `FK_auth_user_user` (`auth_user_user_id`);

--
-- Indeks untuk tabel `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indeks untuk tabel `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `FK_product_type` (`product_type_id`),
  ADD KEY `FK_product_category` (`product_category_id`),
  ADD KEY `FK_product_value` (`product_value_id`);

--
-- Indeks untuk tabel `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks untuk tabel `value`
--
ALTER TABLE `value`
  ADD PRIMARY KEY (`value_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `auth_user`
--
ALTER TABLE `auth_user`
  MODIFY `auth_user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `type`
--
ALTER TABLE `type`
  MODIFY `type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `value`
--
ALTER TABLE `value`
  MODIFY `value_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `auth_user`
--
ALTER TABLE `auth_user`
  ADD CONSTRAINT `FK_auth_user_user` FOREIGN KEY (`auth_user_user_id`) REFERENCES `user` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_product_category` FOREIGN KEY (`product_category_Id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `FK_product_type` FOREIGN KEY (`product_type_id`) REFERENCES `type` (`type_id`),
  ADD CONSTRAINT `FK_product_value` FOREIGN KEY (`product_value_id`) REFERENCES `value` (`value_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
