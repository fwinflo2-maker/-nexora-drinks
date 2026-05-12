-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 27 avr. 2026 à 04:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nexora`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('nexora-cache-5ccfe7d38595aeba1e59f226da4b0b15', 'i:1;', 1777254991),
('nexora-cache-5ccfe7d38595aeba1e59f226da4b0b15:timer', 'i:1777254991;', 1777254991);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cash_sessions`
--

CREATE TABLE `cash_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `cashier_id` bigint(20) UNSIGNED NOT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `closing_balance` decimal(15,2) DEFAULT NULL,
  `total_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cash` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_mobile` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discrepancy` decimal(15,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `team_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'Bières', NULL, 1, '2026-04-26 21:06:47', NULL),
(2, 3, 'Sodas', NULL, 1, '2026-04-26 21:06:47', NULL),
(3, 3, 'Eaux', NULL, 1, '2026-04-26 21:06:47', NULL),
(4, 3, 'Vins & Spiritueux', NULL, 1, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `phone2` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gps_lat` decimal(10,7) DEFAULT NULL,
  `gps_lng` decimal(10,7) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms_days` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `commercial_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_type` varchar(20) NOT NULL DEFAULT 'detail',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `team_id`, `name`, `phone`, `phone2`, `email`, `address`, `gps_lat`, `gps_lng`, `zone`, `credit_limit`, `payment_terms_days`, `commercial_id`, `client_type`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Bar Le Diplomate', '693832499', NULL, NULL, 'Quartier Deido', NULL, NULL, 'Douala', 500000.00, 0, 6, 'detail', 1, NULL, '2026-04-26 21:06:47', NULL, NULL),
(2, 3, 'Snack Bar La Joie', '679314371', NULL, NULL, 'Quartier Akwa', NULL, NULL, 'Douala', 500000.00, 0, 6, 'detail', 1, NULL, '2026-04-26 21:06:47', NULL, NULL),
(3, 3, 'Restaurant Chez Wou', '691837832', NULL, NULL, 'Quartier Bonamoussadi', NULL, NULL, 'Douala', 500000.00, 0, 6, 'detail', 1, NULL, '2026-04-26 21:06:47', NULL, NULL),
(4, 3, 'Grossiste Mboppi', '686136742', NULL, NULL, 'Quartier Bépanda', NULL, NULL, 'Douala', 500000.00, 0, 6, 'grossiste', 1, NULL, '2026-04-26 21:06:47', NULL, NULL),
(5, 3, 'Alimentation Centrale', '697678196', NULL, NULL, 'Quartier Makepe', NULL, NULL, 'Douala', 500000.00, 0, 6, 'detail', 1, NULL, '2026-04-26 21:06:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `client_packaging_balances`
--

CREATE TABLE `client_packaging_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `packaging_type_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_owed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `client_packaging_balances`
--

INSERT INTO `client_packaging_balances` (`id`, `team_id`, `client_id`, `packaging_type_id`, `quantity_owed`, `last_updated_at`) VALUES
(1, 3, 1, 1, 18, NULL),
(2, 3, 2, 1, 26, NULL),
(3, 3, 3, 1, 36, NULL),
(4, 3, 4, 1, 34, NULL),
(5, 3, 5, 1, 10, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `client_prices`
--

CREATE TABLE `client_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client_visits`
--

CREATE TABLE `client_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `commercial_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `visited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gps_lat` decimal(10,7) DEFAULT NULL,
  `gps_lng` decimal(10,7) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `outcome` enum('order','no_order','closed','absent') NOT NULL DEFAULT 'order',
  `duration_minutes` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','delivered','partial','failed') NOT NULL DEFAULT 'pending',
  `sequence_number` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `deliveries`
--

INSERT INTO `deliveries` (`id`, `team_id`, `route_id`, `order_id`, `client_id`, `status`, `sequence_number`, `delivered_at`, `signature_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 1, 1, 'delivered', 1, '2026-04-26 21:06:47', NULL, NULL, '2026-04-26 21:06:47', NULL),
(2, 3, 1, 2, 2, 'delivered', 2, '2026-04-26 21:06:47', NULL, NULL, '2026-04-26 21:06:47', NULL),
(3, 3, 1, 3, 3, 'delivered', 3, '2026-04-26 21:06:47', NULL, NULL, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `delivery_items`
--

CREATE TABLE `delivery_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `ordered_qty` int(10) UNSIGNED NOT NULL,
  `delivered_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `returned_qty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `reason_partial` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','orange_money','mtn_momo','wave','cheque','transfer') NOT NULL DEFAULT 'cash',
  `receipt_path` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `field_orders`
--

CREATE TABLE `field_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `commercial_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `items_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items_json`)),
  `gps_lat` decimal(10,7) DEFAULT NULL,
  `gps_lng` decimal(10,7) DEFAULT NULL,
  `offline_created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `synced_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('pending','synced','error') NOT NULL DEFAULT 'pending',
  `converted_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sync_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(30) NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('invoice','proforma','credit_note') NOT NULL DEFAULT 'invoice',
  `status` enum('draft','sent','paid','partial','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `invoices`
--

INSERT INTO `invoices` (`id`, `team_id`, `invoice_number`, `client_id`, `order_id`, `type`, `status`, `subtotal`, `tax_amount`, `total`, `paid_amount`, `due_date`, `pdf_path`, `sent_at`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'FAC-2026-0001', 1, 1, 'invoice', 'paid', 203400.00, 0.00, 203400.00, 203400.00, '2026-05-11', NULL, NULL, 8, '2026-04-26 21:06:47', NULL, NULL),
(2, 3, 'FAC-2026-0002', 2, 2, 'invoice', 'paid', 218400.00, 0.00, 218400.00, 218400.00, '2026-05-11', NULL, NULL, 8, '2026-04-26 21:06:47', NULL, NULL),
(3, 3, 'FAC-2026-0003', 3, 3, 'invoice', 'paid', 195600.00, 0.00, 195600.00, 195600.00, '2026-05-11', NULL, NULL, 8, '2026-04-26 21:06:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_14_170933_add_two_factor_columns_to_users_table', 1),
(5, '2026_01_27_000001_create_teams_table', 1),
(6, '2026_01_27_000002_add_current_team_id_to_users_table', 1),
(7, '2026_04_25_164754_create_categories_table', 1),
(8, '2026_04_25_164758_create_products_table', 1),
(9, '2026_04_25_164759_create_warehouses_table', 1),
(10, '2026_04_25_164800_create_stock_levels_table', 1),
(11, '2026_04_25_164801_create_stock_movements_table', 1),
(12, '2026_04_25_191315_add_tenant_fields_to_teams_table', 1),
(13, '2026_04_25_191438_create_clients_table', 1),
(14, '2026_04_25_191443_create_suppliers_table', 1),
(16, '2026_04_26_200000_add_nexora_role_to_users_table', 2),
(17, '2026_04_27_000002_create_orders_tables', 2),
(18, '2026_04_27_000003_create_delivery_tables', 2),
(19, '2026_04_27_000004_create_finance_tables', 2),
(20, '2026_04_27_000005_create_operational_tables', 2),
(21, '2026_04_27_000006_create_consignment_tables', 3);

-- --------------------------------------------------------

--
-- Structure de la table `nexora_notifications`
--

CREATE TABLE `nexora_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `data_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_json`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `channel` enum('terrain','televente','client_direct','import') NOT NULL DEFAULT 'televente',
  `status` enum('draft','confirmed','preparing','loaded','delivered','invoiced','cancelled') NOT NULL DEFAULT 'draft',
  `delivery_date` date DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commercial_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `team_id`, `order_number`, `client_id`, `channel`, `status`, `delivery_date`, `warehouse_id`, `commercial_id`, `notes`, `subtotal`, `discount_amount`, `total`, `created_by`, `synced_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'CMD-2026-0001', 1, 'terrain', 'delivered', '2026-04-26', 1, 6, NULL, 203400.00, 0.00, 203400.00, 6, NULL, '2026-04-25 21:06:47', NULL, NULL),
(2, 3, 'CMD-2026-0002', 2, 'terrain', 'delivered', '2026-04-26', 1, 6, NULL, 218400.00, 0.00, 218400.00, 6, NULL, '2026-04-25 21:06:47', NULL, NULL),
(3, 3, 'CMD-2026-0003', 3, 'terrain', 'delivered', '2026-04-26', 1, 6, NULL, 195600.00, 0.00, 195600.00, 6, NULL, '2026-04-25 21:06:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `discount_pct`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 15, 7200.00, 0.00, 108000.00, '2026-04-26 21:06:47', NULL),
(2, 1, 3, 6, 7800.00, 0.00, 46800.00, '2026-04-26 21:06:47', NULL),
(3, 1, 4, 9, 5400.00, 0.00, 48600.00, '2026-04-26 21:06:47', NULL),
(4, 2, 5, 10, 9600.00, 0.00, 96000.00, '2026-04-26 21:06:47', NULL),
(5, 2, 1, 7, 7200.00, 0.00, 50400.00, '2026-04-26 21:06:47', NULL),
(6, 2, 1, 10, 7200.00, 0.00, 72000.00, '2026-04-26 21:06:47', NULL),
(7, 3, 5, 9, 9600.00, 0.00, 86400.00, '2026-04-26 21:06:47', NULL),
(8, 3, 2, 14, 6000.00, 0.00, 84000.00, '2026-04-26 21:06:47', NULL),
(9, 3, 6, 6, 4200.00, 0.00, 25200.00, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `packaging_damages`
--

CREATE TABLE `packaging_damages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `packaging_movement_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `cost_xaf` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `packaging_movements`
--

CREATE TABLE `packaging_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `packaging_type_id` bigint(20) UNSIGNED NOT NULL,
  `movement_type` enum('out','in') NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `delivery_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `packaging_movements`
--

INSERT INTO `packaging_movements` (`id`, `team_id`, `client_id`, `packaging_type_id`, `movement_type`, `quantity`, `delivery_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 1, 'in', 29, 1, NULL, 7, '2026-04-26 21:06:47', NULL),
(2, 3, 2, 1, 'in', 25, 2, NULL, 7, '2026-04-26 21:06:47', NULL),
(3, 3, 3, 1, 'in', 13, 3, NULL, 7, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `packaging_types`
--

CREATE TABLE `packaging_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `unit_value_xaf` decimal(15,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `packaging_types`
--

INSERT INTO `packaging_types` (`id`, `team_id`, `name`, `description`, `unit_value_xaf`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'Casier 65cl (Vide)', NULL, 2000.00, 1, '2026-04-26 21:06:47', NULL),
(2, 3, 'Casier 33cl (Vide)', NULL, 2500.00, 1, '2026-04-26 21:06:47', NULL),
(3, 3, 'Bouteille Verre 65cl', NULL, 100.00, 1, '2026-04-26 21:06:47', NULL),
(4, 3, 'Bouteille Verre 33cl', NULL, 50.00, 1, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method` enum('cash','orange_money','mtn_momo','wave','cheque','transfer','credit') NOT NULL DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL,
  `mobile_money_ref` varchar(100) DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `payments`
--

INSERT INTO `payments` (`id`, `team_id`, `client_id`, `invoice_id`, `amount`, `method`, `reference`, `mobile_money_ref`, `received_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 1, 203400.00, 'orange_money', NULL, 'OM87641829', '2026-04-26 21:06:47', 8, '2026-04-26 21:06:47', NULL),
(2, 3, 2, 2, 218400.00, 'orange_money', NULL, 'OM55514822', '2026-04-26 21:06:47', 8, '2026-04-26 21:06:47', NULL),
(3, 3, 3, 3, 195600.00, 'orange_money', NULL, 'OM90050400', '2026-04-26 21:06:47', 8, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `base_unit` varchar(20) NOT NULL DEFAULT 'bouteille',
  `units_per_pack` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `units_per_case` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `units_per_pallet` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `min_sale_price` decimal(15,2) DEFAULT NULL,
  `vat_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_consignable` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `team_id`, `category_id`, `name`, `sku`, `barcode`, `description`, `image_path`, `base_unit`, `units_per_pack`, `units_per_case`, `units_per_pallet`, `purchase_price`, `sale_price`, `min_sale_price`, `vat_rate`, `is_consignable`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 1, 'Castel Beer 65cl', 'CST-65', NULL, NULL, NULL, 'bouteille', 1, 12, 1, 420.00, 600.00, NULL, 0.00, 1, 1, '2026-04-26 21:06:47', NULL, NULL),
(2, 3, 1, '33 Export 65cl', '33-65', NULL, NULL, NULL, 'bouteille', 1, 12, 1, 350.00, 500.00, NULL, 0.00, 1, 1, '2026-04-26 21:06:47', NULL, NULL),
(3, 3, 1, 'Beaufort Lager 50cl', 'BF-50', NULL, NULL, NULL, 'bouteille', 1, 20, 1, 455.00, 650.00, NULL, 0.00, 1, 1, '2026-04-26 21:06:47', NULL, NULL),
(4, 3, 2, 'Top Pamplemousse 60cl', 'TOP-P-60', NULL, NULL, NULL, 'bouteille', 1, 12, 1, 315.00, 450.00, NULL, 0.00, 1, 1, '2026-04-26 21:06:47', NULL, NULL),
(5, 3, 2, 'Coca-Cola 1.5L PET', 'CC-15-PET', NULL, NULL, NULL, 'bouteille', 1, 6, 1, 560.00, 800.00, NULL, 0.00, 0, 1, '2026-04-26 21:06:47', NULL, NULL),
(6, 3, 3, 'Supermont 1.5L', 'SM-15', NULL, NULL, NULL, 'bouteille', 1, 6, 1, 245.00, 350.00, NULL, 0.00, 0, 1, '2026-04-26 21:06:47', NULL, NULL),
(7, 3, 4, 'Vin Rouge Château 75cl', 'VIN-R-75', NULL, NULL, NULL, 'bouteille', 1, 6, 1, 2450.00, 3500.00, NULL, 0.00, 0, 1, '2026-04-26 21:06:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `receptions`
--

CREATE TABLE `receptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `status` enum('pending','partial','complete') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reception_items`
--

CREATE TABLE `reception_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reception_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL,
  `lot_number` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `routes`
--

CREATE TABLE `routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned',
  `total_distance_km` decimal(8,2) DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `routes`
--

INSERT INTO `routes` (`id`, `team_id`, `name`, `date`, `driver_id`, `vehicle_id`, `status`, `total_distance_km`, `departure_time`, `arrival_time`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, 'Tournée Deido - 26/04/2026', '2026-04-26', 7, 1, 'completed', NULL, NULL, NULL, 4, '2026-04-25 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0vEr0X4CfSlRQCMorEtz57zlC77cMrQs0So70BC5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI5dW1qNzVIWHdKSGlVbEd6aDJFQVFiYnZsaE5NS3JBSThmSERHMVZ1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1777251133),
('uKAec18XqYQFEOTJJYJHNg2gjQUyIQCgweHYbOzL', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJZMkUxTlM2WHFGTmpDRjdEYU0wYW9aWTY2c3psRVJMRnZjeXFwMVA5IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDAiLCJyb3V0ZSI6ImhvbWUifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1777255807),
('VqCDzZa6hRE5bRUcQ41FwhW8v6Ki3sjcuswLmU2y', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJ6S1JHb1dYbjc2TXhBMnR3aGhEMEJRYlZaeVFsaEhXUk1jVk1xc0lQIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==', 1777250470);

-- --------------------------------------------------------

--
-- Structure de la table `stock_levels`
--

CREATE TABLE `stock_levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `reserved_quantity` int(11) NOT NULL DEFAULT 0,
  `min_threshold` int(10) UNSIGNED DEFAULT NULL,
  `max_threshold` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock_levels`
--

INSERT INTO `stock_levels` (`id`, `team_id`, `product_id`, `warehouse_id`, `quantity`, `reserved_quantity`, `min_threshold`, `max_threshold`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 1, 3808, 0, 100, NULL, '2026-04-26 21:06:47', NULL),
(2, 3, 2, 1, 539, 0, 100, NULL, '2026-04-26 21:06:47', NULL),
(3, 3, 3, 1, 4998, 0, 100, NULL, '2026-04-26 21:06:47', NULL),
(4, 3, 4, 1, 4649, 0, 100, NULL, '2026-04-26 21:06:47', NULL),
(5, 3, 5, 1, 2420, 0, 100, NULL, '2026-04-26 21:06:47', NULL),
(6, 3, 6, 1, 4659, 0, 100, NULL, '2026-04-26 21:06:47', NULL),
(7, 3, 7, 1, 4681, 0, 100, NULL, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `movement_type` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `payment_terms_days` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sync_logs`
--

CREATE TABLE `sync_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `sync_type` enum('full','incremental','push') NOT NULL DEFAULT 'incremental',
  `status` enum('success','partial','error') NOT NULL DEFAULT 'success',
  `records_sent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `records_received` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `errors_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`errors_json`)),
  `started_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_personal` tinyint(1) NOT NULL DEFAULT 0,
  `plan` varchar(30) NOT NULL DEFAULT 'starter',
  `settings_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings_json`)),
  `logo_path` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `teams`
--

INSERT INTO `teams` (`id`, `name`, `slug`, `is_personal`, `plan`, `settings_json`, `logo_path`, `domain`, `is_active`, `trial_ends_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin NEXORA\'s Team', 'ruiz', 1, 'starter', NULL, NULL, NULL, 1, NULL, '2026-04-26 20:33:40', '2026-04-26 20:33:40', NULL),
(2, 'Test User\'s Team', 'costa', 1, 'starter', NULL, NULL, NULL, 1, NULL, '2026-04-26 20:33:41', '2026-04-26 20:33:41', NULL),
(3, 'SABD - Société Africaine de Boissons', 'brasseries-demo', 0, 'pro', NULL, NULL, 'sabd.nexora.app', 1, NULL, '2026-04-26 21:06:44', '2026-04-26 21:06:44', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `team_invitations`
--

CREATE TABLE `team_invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(64) NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `invited_by` bigint(20) UNSIGNED NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `team_members`
--

CREATE TABLE `team_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `team_members`
--

INSERT INTO `team_members` (`id`, `team_id`, `user_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'owner', '2026-04-26 20:33:40', '2026-04-26 20:33:40'),
(2, 2, 2, 'owner', '2026-04-26 20:33:41', '2026-04-26 20:33:41'),
(3, 3, 3, 'admin', '2026-04-26 21:06:45', '2026-04-26 21:06:45'),
(4, 3, 4, 'manager', '2026-04-26 21:06:45', '2026-04-26 21:06:45'),
(5, 3, 5, 'magasinier', '2026-04-26 21:06:45', '2026-04-26 21:06:45'),
(6, 3, 6, 'commercial', '2026-04-26 21:06:46', '2026-04-26 21:06:46'),
(7, 3, 7, 'livreur', '2026-04-26 21:06:46', '2026-04-26 21:06:46'),
(8, 3, 8, 'caissier', '2026-04-26 21:06:47', '2026-04-26 21:06:47');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nexora_role` varchar(255) DEFAULT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `current_team_id`, `nexora_role`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin NEXORA', 'superadmin@nexora.app', '2026-04-26 20:33:40', '$2y$12$J8tKjQotz7g0h3bULAbDIO6S0hv8rBqlIHpfUnTpEojP/9X7Gq7kS', 1, 'super_admin', NULL, NULL, NULL, 'b6FYMEQGSu3wdFPFnz8NHebjMTFRbjiAHnaRiUYzWkcBQEcysAfvO61ns8HQ', '2026-04-26 20:33:40', '2026-04-26 22:39:29'),
(2, 'Test User', 'test@example.com', '2026-04-26 20:33:41', '$2y$12$ZKJi5No7LHRTYkWixeyoNO.k5/DJtu3J7ORpPwb6qPEOsGwbz6/au', 2, NULL, NULL, NULL, NULL, 'lM5suZBudT', '2026-04-26 20:33:41', '2026-04-26 20:33:41'),
(3, 'Directeur SABD', 'admin@sabd.cm', NULL, '$2y$12$EddWzF1UGozrhc6XF6y59OirXTMQyBQdZNfqof70MxwXtcxPXqrRO', 3, NULL, NULL, NULL, NULL, NULL, '2026-04-26 21:06:45', '2026-04-26 21:06:45'),
(4, 'Manager Ops', 'manager@sabd.cm', NULL, '$2y$12$RSCh37lxUMAZgeclQOvU4uCaQIUbVU0lGqz2ovy.mCaO3FC/d.zuW', 3, NULL, NULL, NULL, NULL, NULL, '2026-04-26 21:06:45', '2026-04-26 21:06:45'),
(5, 'Chef Magasinier', 'magasinier@sabd.cm', NULL, '$2y$12$VB/li9Bxid26WW3pDttFJ.etXLbYcZ8oP9Gp4NNgJixcb.w12OUpq', 3, NULL, NULL, NULL, NULL, NULL, '2026-04-26 21:06:45', '2026-04-26 21:06:45'),
(6, 'Commercial Terrain 1', 'commercial1@sabd.cm', NULL, '$2y$12$o4gLMrv5FWnL66JmAW1K0uihuBsAAUy.wXofMSAGrwW9do2t.8HWO', 3, NULL, NULL, NULL, NULL, NULL, '2026-04-26 21:06:46', '2026-04-26 21:06:46'),
(7, 'Livreur Principal', 'livreur@sabd.cm', NULL, '$2y$12$pLen.J5yvk1VzaGfT6NHAuUY772FvPAb6bXfJ7X4tSQKtfsdYXaea', 3, NULL, NULL, NULL, NULL, NULL, '2026-04-26 21:06:46', '2026-04-26 21:06:46'),
(8, 'Caissière Centrale', 'caisse@sabd.cm', NULL, '$2y$12$WZWjenQm/OIZNsCrXdwH1Ok7qCKZBwnb521qjhlCya5Uq.w7zlKiO', 3, NULL, NULL, NULL, NULL, NULL, '2026-04-26 21:06:47', '2026-04-26 21:06:47');

-- --------------------------------------------------------

--
-- Structure de la table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `plate` varchar(30) DEFAULT NULL,
  `capacity_cases` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vehicles`
--

INSERT INTO `vehicles` (`id`, `team_id`, `name`, `plate`, `capacity_cases`, `is_active`, `driver_id`, `created_at`, `updated_at`) VALUES
(1, 3, 'Camion Fuso 5T', 'LT 123 AB', 300, 1, 7, '2026-04-26 21:06:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'main',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `warehouses`
--

INSERT INTO `warehouses` (`id`, `team_id`, `name`, `address`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'Dépôt Central Akwa', 'Zone Industrielle Bassa', 'main', 1, '2026-04-26 21:06:47', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `cash_sessions`
--
ALTER TABLE `cash_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_sessions_cashier_id_foreign` (`cashier_id`),
  ADD KEY `cash_sessions_team_id_cashier_id_index` (`team_id`,`cashier_id`),
  ADD KEY `cash_sessions_team_id_opened_at_index` (`team_id`,`opened_at`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_team_id_is_active_index` (`team_id`,`is_active`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_commercial_id_foreign` (`commercial_id`),
  ADD KEY `clients_team_id_is_active_index` (`team_id`,`is_active`),
  ADD KEY `clients_team_id_client_type_index` (`team_id`,`client_type`),
  ADD KEY `clients_team_id_commercial_id_index` (`team_id`,`commercial_id`);

--
-- Index pour la table `client_packaging_balances`
--
ALTER TABLE `client_packaging_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_packaging_balances_client_id_packaging_type_id_unique` (`client_id`,`packaging_type_id`),
  ADD KEY `client_packaging_balances_packaging_type_id_foreign` (`packaging_type_id`),
  ADD KEY `client_packaging_balances_team_id_client_id_index` (`team_id`,`client_id`);

--
-- Index pour la table `client_prices`
--
ALTER TABLE `client_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_prices_client_id_product_id_unique` (`client_id`,`product_id`),
  ADD KEY `client_prices_product_id_foreign` (`product_id`),
  ADD KEY `client_prices_created_by_foreign` (`created_by`),
  ADD KEY `client_prices_team_id_client_id_index` (`team_id`,`client_id`);

--
-- Index pour la table `client_visits`
--
ALTER TABLE `client_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_visits_commercial_id_foreign` (`commercial_id`),
  ADD KEY `client_visits_client_id_foreign` (`client_id`),
  ADD KEY `client_visits_team_id_commercial_id_index` (`team_id`,`commercial_id`),
  ADD KEY `client_visits_team_id_client_id_index` (`team_id`,`client_id`),
  ADD KEY `client_visits_team_id_visited_at_index` (`team_id`,`visited_at`);

--
-- Index pour la table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deliveries_route_id_foreign` (`route_id`),
  ADD KEY `deliveries_order_id_foreign` (`order_id`),
  ADD KEY `deliveries_client_id_foreign` (`client_id`),
  ADD KEY `deliveries_team_id_route_id_index` (`team_id`,`route_id`),
  ADD KEY `deliveries_team_id_status_index` (`team_id`,`status`),
  ADD KEY `deliveries_team_id_client_id_index` (`team_id`,`client_id`);

--
-- Index pour la table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_items_delivery_id_foreign` (`delivery_id`),
  ADD KEY `delivery_items_product_id_foreign` (`product_id`);

--
-- Index pour la table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_created_by_foreign` (`created_by`),
  ADD KEY `expenses_team_id_category_index` (`team_id`,`category`),
  ADD KEY `expenses_team_id_date_index` (`team_id`,`date`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `field_orders`
--
ALTER TABLE `field_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field_orders_client_id_foreign` (`client_id`),
  ADD KEY `field_orders_converted_order_id_foreign` (`converted_order_id`),
  ADD KEY `field_orders_team_id_sync_status_index` (`team_id`,`sync_status`),
  ADD KEY `field_orders_commercial_id_sync_status_index` (`commercial_id`,`sync_status`);

--
-- Index pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_client_id_foreign` (`client_id`),
  ADD KEY `invoices_order_id_foreign` (`order_id`),
  ADD KEY `invoices_created_by_foreign` (`created_by`),
  ADD KEY `invoices_team_id_status_index` (`team_id`,`status`),
  ADD KEY `invoices_team_id_client_id_index` (`team_id`,`client_id`),
  ADD KEY `invoices_team_id_due_date_index` (`team_id`,`due_date`),
  ADD KEY `invoices_team_id_type_index` (`team_id`,`type`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nexora_notifications`
--
ALTER TABLE `nexora_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nexora_notifications_user_id_foreign` (`user_id`),
  ADD KEY `nexora_notifications_team_id_user_id_read_at_index` (`team_id`,`user_id`,`read_at`),
  ADD KEY `nexora_notifications_team_id_type_index` (`team_id`,`type`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_client_id_foreign` (`client_id`),
  ADD KEY `orders_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `orders_commercial_id_foreign` (`commercial_id`),
  ADD KEY `orders_created_by_foreign` (`created_by`),
  ADD KEY `orders_team_id_status_index` (`team_id`,`status`),
  ADD KEY `orders_team_id_client_id_index` (`team_id`,`client_id`),
  ADD KEY `orders_team_id_delivery_date_index` (`team_id`,`delivery_date`),
  ADD KEY `orders_team_id_commercial_id_index` (`team_id`,`commercial_id`);

--
-- Index pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_order_id_index` (`order_id`);

--
-- Index pour la table `packaging_damages`
--
ALTER TABLE `packaging_damages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `packaging_damages_team_id_foreign` (`team_id`),
  ADD KEY `packaging_damages_packaging_movement_id_foreign` (`packaging_movement_id`),
  ADD KEY `packaging_damages_created_by_foreign` (`created_by`);

--
-- Index pour la table `packaging_movements`
--
ALTER TABLE `packaging_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `packaging_movements_client_id_foreign` (`client_id`),
  ADD KEY `packaging_movements_packaging_type_id_foreign` (`packaging_type_id`),
  ADD KEY `packaging_movements_delivery_id_foreign` (`delivery_id`),
  ADD KEY `packaging_movements_created_by_foreign` (`created_by`),
  ADD KEY `packaging_movements_team_id_client_id_index` (`team_id`,`client_id`),
  ADD KEY `packaging_movements_team_id_movement_type_index` (`team_id`,`movement_type`),
  ADD KEY `packaging_movements_team_id_created_at_index` (`team_id`,`created_at`);

--
-- Index pour la table `packaging_types`
--
ALTER TABLE `packaging_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `packaging_types_team_id_is_active_index` (`team_id`,`is_active`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_client_id_foreign` (`client_id`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `payments_created_by_foreign` (`created_by`),
  ADD KEY `payments_team_id_method_index` (`team_id`,`method`),
  ADD KEY `payments_team_id_client_id_index` (`team_id`,`client_id`),
  ADD KEY `payments_team_id_received_at_index` (`team_id`,`received_at`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_team_id_sku_unique` (`team_id`,`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_team_id_is_active_index` (`team_id`,`is_active`),
  ADD KEY `products_team_id_category_id_index` (`team_id`,`category_id`);

--
-- Index pour la table `receptions`
--
ALTER TABLE `receptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receptions_supplier_id_foreign` (`supplier_id`),
  ADD KEY `receptions_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `receptions_created_by_foreign` (`created_by`),
  ADD KEY `receptions_team_id_status_index` (`team_id`,`status`),
  ADD KEY `receptions_team_id_supplier_id_index` (`team_id`,`supplier_id`);

--
-- Index pour la table `reception_items`
--
ALTER TABLE `reception_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reception_items_reception_id_foreign` (`reception_id`),
  ADD KEY `reception_items_product_id_foreign` (`product_id`);

--
-- Index pour la table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `routes_driver_id_foreign` (`driver_id`),
  ADD KEY `routes_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `routes_created_by_foreign` (`created_by`),
  ADD KEY `routes_team_id_date_index` (`team_id`,`date`),
  ADD KEY `routes_team_id_status_index` (`team_id`,`status`),
  ADD KEY `routes_team_id_driver_id_index` (`team_id`,`driver_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `stock_levels`
--
ALTER TABLE `stock_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_levels_product_id_warehouse_id_unique` (`product_id`,`warehouse_id`),
  ADD KEY `stock_levels_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_levels_team_id_product_id_index` (`team_id`,`product_id`);

--
-- Index pour la table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_product_id_foreign` (`product_id`),
  ADD KEY `stock_movements_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_movements_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`),
  ADD KEY `stock_movements_team_id_product_id_index` (`team_id`,`product_id`),
  ADD KEY `stock_movements_team_id_warehouse_id_index` (`team_id`,`warehouse_id`),
  ADD KEY `stock_movements_team_id_movement_type_index` (`team_id`,`movement_type`),
  ADD KEY `stock_movements_created_at_index` (`created_at`);

--
-- Index pour la table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suppliers_team_id_is_active_index` (`team_id`,`is_active`);

--
-- Index pour la table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sync_logs_user_id_status_index` (`user_id`,`status`),
  ADD KEY `sync_logs_user_id_started_at_index` (`user_id`,`started_at`);

--
-- Index pour la table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teams_slug_unique` (`slug`),
  ADD UNIQUE KEY `teams_domain_unique` (`domain`);

--
-- Index pour la table `team_invitations`
--
ALTER TABLE `team_invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `team_invitations_code_unique` (`code`),
  ADD KEY `team_invitations_team_id_foreign` (`team_id`),
  ADD KEY `team_invitations_invited_by_foreign` (`invited_by`);

--
-- Index pour la table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `team_members_team_id_user_id_unique` (`team_id`,`user_id`),
  ADD KEY `team_members_user_id_foreign` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_current_team_id_foreign` (`current_team_id`);

--
-- Index pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_driver_id_foreign` (`driver_id`),
  ADD KEY `vehicles_team_id_is_active_index` (`team_id`,`is_active`);

--
-- Index pour la table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouses_team_id_is_active_index` (`team_id`,`is_active`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cash_sessions`
--
ALTER TABLE `cash_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `client_packaging_balances`
--
ALTER TABLE `client_packaging_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `client_prices`
--
ALTER TABLE `client_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `client_visits`
--
ALTER TABLE `client_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `delivery_items`
--
ALTER TABLE `delivery_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `field_orders`
--
ALTER TABLE `field_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `nexora_notifications`
--
ALTER TABLE `nexora_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `packaging_damages`
--
ALTER TABLE `packaging_damages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `packaging_movements`
--
ALTER TABLE `packaging_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `packaging_types`
--
ALTER TABLE `packaging_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `receptions`
--
ALTER TABLE `receptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reception_items`
--
ALTER TABLE `reception_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `stock_levels`
--
ALTER TABLE `stock_levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sync_logs`
--
ALTER TABLE `sync_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `team_invitations`
--
ALTER TABLE `team_invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cash_sessions`
--
ALTER TABLE `cash_sessions`
  ADD CONSTRAINT `cash_sessions_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_sessions_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_commercial_id_foreign` FOREIGN KEY (`commercial_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `clients_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `client_packaging_balances`
--
ALTER TABLE `client_packaging_balances`
  ADD CONSTRAINT `client_packaging_balances_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_packaging_balances_packaging_type_id_foreign` FOREIGN KEY (`packaging_type_id`) REFERENCES `packaging_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_packaging_balances_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `client_prices`
--
ALTER TABLE `client_prices`
  ADD CONSTRAINT `client_prices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_prices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_prices_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_prices_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `client_visits`
--
ALTER TABLE `client_visits`
  ADD CONSTRAINT `client_visits_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_visits_commercial_id_foreign` FOREIGN KEY (`commercial_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_visits_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deliveries_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD CONSTRAINT `delivery_items_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `field_orders`
--
ALTER TABLE `field_orders`
  ADD CONSTRAINT `field_orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `field_orders_commercial_id_foreign` FOREIGN KEY (`commercial_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `field_orders_converted_order_id_foreign` FOREIGN KEY (`converted_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `field_orders_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `nexora_notifications`
--
ALTER TABLE `nexora_notifications`
  ADD CONSTRAINT `nexora_notifications_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nexora_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_commercial_id_foreign` FOREIGN KEY (`commercial_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `packaging_damages`
--
ALTER TABLE `packaging_damages`
  ADD CONSTRAINT `packaging_damages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `packaging_damages_packaging_movement_id_foreign` FOREIGN KEY (`packaging_movement_id`) REFERENCES `packaging_movements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `packaging_damages_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `packaging_movements`
--
ALTER TABLE `packaging_movements`
  ADD CONSTRAINT `packaging_movements_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `packaging_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `packaging_movements_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `packaging_movements_packaging_type_id_foreign` FOREIGN KEY (`packaging_type_id`) REFERENCES `packaging_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `packaging_movements_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `packaging_types`
--
ALTER TABLE `packaging_types`
  ADD CONSTRAINT `packaging_types_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `receptions`
--
ALTER TABLE `receptions`
  ADD CONSTRAINT `receptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receptions_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receptions_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receptions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reception_items`
--
ALTER TABLE `reception_items`
  ADD CONSTRAINT `reception_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reception_items_reception_id_foreign` FOREIGN KEY (`reception_id`) REFERENCES `receptions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `routes_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `routes_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `routes_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `stock_levels`
--
ALTER TABLE `stock_levels`
  ADD CONSTRAINT `stock_levels_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_levels_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_levels_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD CONSTRAINT `sync_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `team_invitations`
--
ALTER TABLE `team_invitations`
  ADD CONSTRAINT `team_invitations_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_invitations_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `team_members_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_current_team_id_foreign` FOREIGN KEY (`current_team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicles_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `warehouses_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
