-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 10 mai 2025 à 10:45
-- Version du serveur : 5.7.40
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `kmerhosting`
--

-- --------------------------------------------------------

--
-- Structure de la table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `custom_domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cart_history`
--

DROP TABLE IF EXISTS `cart_history`;
CREATE TABLE IF NOT EXISTS `cart_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `custom_domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cart_history`
--

INSERT INTO `cart_history` (`id`, `user_id`, `product_type`, `product_id`, `quantity`, `custom_domain`, `added_at`, `updated_at`) VALUES
(24, 1, 'domain', '1', 1, 'ce.site', '2025-05-10 08:11:26', NULL),
(25, 1, 'wordpress', '2', 1, NULL, '2025-05-10 08:33:17', NULL),
(26, 1, 'ssl', '3', 1, NULL, '2025-05-10 08:33:23', NULL),
(27, 1, 'hosting', '3', 1, NULL, '2025-05-10 08:33:27', NULL),
(28, 1, 'wordpress', '1', 1, NULL, '2025-05-10 09:26:10', NULL),
(29, 1, 'ssl', '3', 1, NULL, '2025-05-10 09:26:15', NULL),
(30, 1, 'hosting', '3', 1, NULL, '2025-05-10 09:33:51', NULL),
(31, 1, 'wordpress', '1', 1, NULL, '2025-05-10 09:36:24', NULL),
(32, 1, 'hosting', '7', 1, NULL, '2025-05-10 11:06:20', NULL),
(33, 1, 'wordpress', '2', 1, NULL, '2025-05-10 11:26:09', NULL),
(34, 1, 'hosting', '4', 1, NULL, '2025-05-10 11:26:21', NULL),
(35, 1, 'ssl', '2', 1, NULL, '2025-05-10 11:27:19', NULL),
(36, 1, 'domain', '1', 1, 'toscani.computer', '2025-05-10 11:27:34', NULL),
(37, 1, 'domain', '1', 1, 'toscani.technology', '2025-05-10 11:32:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `credit`
--

DROP TABLE IF EXISTS `credit`;
CREATE TABLE IF NOT EXISTS `credit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '500.00',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `credit`
--

INSERT INTO `credit` (`id`, `user_id`, `amount`, `last_updated`) VALUES
(1, 1, '411500.00', '2025-05-10 10:40:22');

-- --------------------------------------------------------

--
-- Structure de la table `credit_logs`
--

DROP TABLE IF EXISTS `credit_logs`;
CREATE TABLE IF NOT EXISTS `credit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('recharge','payment','refund','adjustment') NOT NULL,
  `description` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `credit_logs`
--

INSERT INTO `credit_logs` (`id`, `user_id`, `amount`, `type`, `description`, `created_at`) VALUES
(1, 1, '1000.00', 'recharge', 'Recharge via Fapshi (TransID: mAdwdvdkr9)', '2025-05-08 23:59:34'),
(2, 1, '1000.00', 'recharge', 'Recharge via Fapshi (TransID: yNwWNGCLGZ)', '2025-05-09 00:53:29'),
(3, 1, '1000.00', 'recharge', 'Recharge via Fapshi (TransID: yNwWNGCLGZ)', '2025-05-09 00:56:26'),
(4, 1, '1000.00', 'recharge', 'Recharge via Fapshi (TransID: yNwWNGCLGZ)', '2025-05-09 00:57:37'),
(5, 1, '1000.00', 'recharge', 'Recharge via Fapshi (TransID: yNwWNGCLGZ)', '2025-05-09 00:59:19'),
(6, 1, '1000.00', 'recharge', 'Recharge via Fapshi (TransID: yNwWNGCLGZ)', '2025-05-09 01:05:59');

-- --------------------------------------------------------

--
-- Structure de la table `domain_packages`
--

DROP TABLE IF EXISTS `domain_packages`;
CREATE TABLE IF NOT EXISTS `domain_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `renewal_price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `features` text NOT NULL,
  `popular` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `domain_packages`
--

INSERT INTO `domain_packages` (`id`, `extension`, `price`, `renewal_price`, `description`, `features`, `popular`) VALUES
(1, '.com', '9500.00', '9500.00', 'L\'extension la plus populaire pour les sites web commerciaux.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 1),
(2, '.org', '9800.00', '9800.00', 'Idéal pour les organisations à but non lucratif et les associations.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 1),
(3, '.net', '10500.00', '10500.00', 'Parfait pour les entreprises technologiques et les réseaux.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 1),
(4, '.cm', '35000.00', '35000.00', 'Extension nationale du Cameroun, idéale pour les entreprises locales.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 1),
(5, '.info', '12000.00', '12000.00', 'Idéal pour les sites d\'information et de ressources.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(6, '.biz', '11000.00', '11000.00', 'Conçu pour les entreprises et les activités commerciales.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(7, '.site', '8500.00', '8500.00', 'Une extension polyvalente pour tout type de site web.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(8, '.online', '9000.00', '9000.00', 'Parfait pour les entreprises qui opèrent principalement en ligne.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(9, '.store', '10000.00', '10000.00', 'Idéal pour les boutiques en ligne et les e-commerces.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(10, '.app', '14000.00', '14000.00', 'Conçu pour les applications mobiles et les développeurs.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(11, '.dev', '14500.00', '14500.00', 'Parfait pour les développeurs et les projets de développement.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(12, '.co', '15000.00', '15000.00', 'Une alternative populaire à .com pour les entreprises.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(13, '.africa', '18000.00', '18000.00', 'Extension régionale pour les entreprises africaines.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(14, '.ai', '30000.00', '30000.00', 'Idéal pour les entreprises d\'intelligence artificielle.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0),
(15, '.cm', '35000.00', '35000.00', 'Extension nationale du Cameroun.', 'Protection WHOIS,DNS Management,Redirection Email,Renouvellement automatique', 0);

-- --------------------------------------------------------

--
-- Structure de la table `hosting_packages`
--

DROP TABLE IF EXISTS `hosting_packages`;
CREATE TABLE IF NOT EXISTS `hosting_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `disk_space` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bandwidth` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domains` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdomains` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mysql_databases` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ftp_accounts` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `php_access` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nodejs_access` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `python_access` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `litespeed` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `directadmin` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inodes` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ssl` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sites_count` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_accounts` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_redirects` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mailing_lists` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_responders` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_pointers` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cgi_access` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spamassassin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catch_all` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cron_jobs` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_info` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_keys` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dns_control` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `security_txt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_builder` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommended` tinyint(1) NOT NULL DEFAULT '0',
  `features` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hosting_packages`
--

INSERT INTO `hosting_packages` (`id`, `name`, `price`, `disk_space`, `bandwidth`, `domains`, `subdomains`, `mysql_databases`, `ftp_accounts`, `php_access`, `nodejs_access`, `python_access`, `litespeed`, `directadmin`, `inodes`, `ssl`, `sites_count`, `email_accounts`, `email_redirects`, `mailing_lists`, `auto_responders`, `domain_pointers`, `cgi_access`, `spamassassin`, `catch_all`, `cron_jobs`, `redis`, `system_info`, `login_keys`, `dns_control`, `security_txt`, `site_builder`, `recommended`, `features`) VALUES
(1, 'Starter', '5000.00', '100 MB', '1000 MB', 'Non inclus', '1', '1', '1', 'Oui', 'Non inclus', 'Non inclus', 'Inclus', 'Inclus', '500', 'Non inlcus', '1', 'Non inclus', 'Non inclus', '0', 'Non inclus', 'Non inclus', 'Non inclus', 'Non inclus', 'Non inclus', 'Non inclus', 'Non inclus', 'Non inclus', 'Oui', 'Oui', 'Oui', 'Non inclus', 0, 'PHP,phpMyAdmin'),
(2, 'Eco', '7500.00', '1 GB', '5 000 MB', 'Non inclus', '3', '5', '3', 'Oui', 'Oui', 'Oui', 'Inclus', 'Inclus', '300 000', 'Non inclus', '3', '1', '3', '1', '3', '2', 'Oui', 'Oui', 'Oui', '3', 'Non inclus', 'Oui', 'Oui', 'Oui', 'Oui', 'Inclus', 0, 'PHP,Laravel,Node.js,Python,phpMyAdmin'),
(3, 'Basic', '10000.00', '2 GB', '20 000 MB', 'Non inclus', '10', '15', '5', 'Oui', 'Oui', 'Oui', 'Inclus', 'Inclus', '800 000', 'Non inclus', '10', '5', '10', '10', '10', '5', 'Oui', 'Oui', 'Oui', '10', 'Oui', 'Oui', 'Oui', 'Inclus', 'Oui', 'Inclus', 0, 'PHP,Laravel,Node.js,Python,Softaculous,phpMyAdmin'),
(4, 'Standard', '15000.00', '5 GB', '50 000 MB', '1 (.com)', '10', '20', '10', 'Oui', 'Oui', 'Oui', 'Inclus', 'Inclus', '1 000 000', 'Inclus', '15', '10', '10', '20', '20', '10', 'Oui', 'Oui', 'Oui', '20', 'Inclus', 'Oui', 'Oui', 'Oui', 'Oui', 'Inclus', 1, 'PHP,Laravel,Node.js,Python,Softaculous,phpMyAdmin'),
(5, 'Business', '25000.00', '8 GB', '100 000 MB', '1 (.*)', 'Illimité', '30', '20', 'Oui', 'Oui', 'Oui', 'Inclus', 'Inclus', '2 000 000', 'Inclus', '30', '20', '20', '30', '30', '20', 'Oui', 'Oui', 'Oui', '100', 'Inclus', 'Oui', 'Oui', 'Oui', 'Oui', 'Inclus', 0, 'PHP,Laravel,Node.js,Python,Softaculous,phpMyAdmin,10'),
(6, 'Premium', '45000.00', '10 GB', 'Illimitée', '2 (.*)', 'Illimitée', 'Illimitée', '100', 'Oui', 'Oui', 'Oui', 'Inclus', 'Inclus', '3 000 000', 'Inclus', '50', 'Illimité', 'Illimité', 'Illimité', 'Illimité', 'Illimité', 'Oui', 'Oui', 'Oui', '50', 'Inclus', 'Oui', 'Oui', 'Oui', 'Oui', 'Inclus', 0, 'PHP,Laravel,Node.js,Python,Softaculous,phpMyAdmin,30'),
(7, 'Ultimate', '60000.00', '15 GB', 'Illimitée', '3 (.*)', 'Illimité', 'Illimitée', 'Illimité', 'Oui', 'Oui', 'Oui', 'Inclus', 'Inclus', 'Illimitée', 'Inclus + Protection Wildcard', 'Illimité', 'Illimité', 'Illimitée', 'Illimitée', 'Illimité', 'Illimité', 'Oui', 'Oui', 'Oui', 'Illimité', 'Inclus (Smart data caching)', 'Oui', 'Oui', 'Inclus', 'Oui', 'Inclus', 0, 'Sécurité +,PHP,Laravel,Node.js,Python,Softaculous,phpMyAdmin,100');

-- --------------------------------------------------------

--
-- Structure de la table `offers`
--

DROP TABLE IF EXISTS `offers`;
CREATE TABLE IF NOT EXISTS `offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `icon_color` varchar(20) DEFAULT NULL,
  `features` text NOT NULL,
  `link_url` varchar(255) NOT NULL,
  `link_text` varchar(100) NOT NULL,
  `link_color` varchar(30) DEFAULT NULL,
  `card_border_color` varchar(30) DEFAULT NULL,
  `card_highlighted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `offers`
--

INSERT INTO `offers` (`id`, `title`, `subtitle`, `description`, `icon`, `icon_color`, `features`, `link_url`, `link_text`, `link_color`, `card_border_color`, `card_highlighted`) VALUES
(1, 'Hébergement Web', NULL, 'Solutions d\'hébergement web performantes et fiables pour tous types de sites web, du blog personnel aux applications d\'entreprise.', 'fa-server', '#10b981', 'Serveurs haute performance\nStockage SSD rapide\nBande passante illimitée\nSupport technique 24/7', '#', 'Voir nos offres', '#10b981', NULL, 0),
(2, 'Hébergement WordPress', NULL, 'Hébergement optimisé pour WordPress avec des performances exceptionnelles et une sécurité renforcée pour votre site WordPress.', 'fa-wordpress', '#10b981', 'Cache LiteSpeed intégré\nInstallation en 1 clic\nMises à jour automatiques\nProtection contre les malwares', '#', 'Voir nos offres WordPress', '#10b981', '#10b981', 1),
(3, 'Noms de Domaine', NULL, 'Enregistrez votre nom de domaine parfait parmi une large sélection d\'extensions pour établir votre présence en ligne.', 'fa-globe', '#10b981', 'Plus de 300 extensions disponibles\nProtection WHOIS gratuite\nTransfert facile\nGestion DNS simplifiée', '#', 'Rechercher un domaine', '#10b981', NULL, 0),
(4, 'Certificats SSL', NULL, 'Sécurisez votre site web avec nos certificats SSL. Protégez les données de vos visiteurs et améliorez votre référencement.', 'fa-shield-alt', '#10b981', 'Chiffrement 256 bits\nInstallation gratuite\nCompatibilité navigateurs\nRenouvellement automatique', '#', 'Voir nos certificats SSL', '#10b981', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '63500.00', 'credit', 'completed', '2025-05-09 01:29:20', NULL),
(2, 1, '63500.00', 'credit', 'completed', '2025-05-09 01:30:19', NULL),
(3, 1, '63500.00', 'credit', 'completed', '2025-05-09 01:32:03', NULL),
(4, 1, '63500.00', 'credit', 'completed', '2025-05-09 01:36:29', NULL),
(5, 1, '63500.00', 'credit', 'completed', '2025-05-09 01:41:05', NULL),
(6, 1, '63500.00', 'credit', 'completed', '2025-05-09 01:45:15', NULL),
(7, 1, '36000.00', 'credit', 'completed', '2025-05-09 02:41:17', NULL),
(8, 1, '36000.00', 'credit', 'completed', '2025-05-09 02:45:55', NULL),
(9, 1, '30000.00', 'credit', 'completed', '2025-05-09 02:47:45', NULL),
(10, 1, '11000.00', 'credit', 'completed', '2025-05-09 14:56:20', NULL),
(11, 1, '9500.00', 'credit', 'completed', '2025-05-09 15:03:29', NULL),
(12, 1, '25000.00', 'credit', 'completed', '2025-05-09 15:05:15', NULL),
(13, 1, '7500.00', 'credit', 'completed', '2025-05-09 15:08:00', NULL),
(14, 1, '75000.00', 'credit', 'completed', '2025-05-09 16:54:03', NULL),
(15, 1, '9000.00', 'credit', 'completed', '2025-05-09 20:01:08', NULL),
(16, 1, '9000.00', 'credit', 'completed', '2025-05-09 20:37:14', NULL),
(17, 1, '9000.00', 'credit', 'completed', '2025-05-09 21:19:50', NULL),
(18, 1, '9000.00', 'credit', 'completed', '2025-05-09 21:23:05', NULL),
(19, 1, '9000.00', 'credit', 'completed', '2025-05-09 21:41:49', NULL),
(20, 1, '9000.00', 'credit', 'completed', '2025-05-09 21:52:50', NULL),
(21, 1, '18000.00', 'credit', 'completed', '2025-05-10 08:25:03', NULL),
(22, 1, '18000.00', 'credit', 'completed', '2025-05-10 08:27:01', NULL),
(23, 1, '18000.00', 'credit', 'completed', '2025-05-10 08:31:09', NULL),
(24, 1, '18000.00', 'credit', 'completed', '2025-05-10 08:32:21', NULL),
(25, 1, '45000.00', 'credit', 'completed', '2025-05-10 08:34:09', NULL),
(26, 1, '25000.00', 'credit', 'completed', '2025-05-10 09:26:26', NULL),
(27, 1, '10000.00', 'credit', 'completed', '2025-05-10 09:34:03', NULL),
(28, 1, '15000.00', 'credit', 'completed', '2025-05-10 09:36:33', NULL),
(29, 1, '60000.00', 'credit', 'completed', '2025-05-10 11:25:43', NULL),
(30, 1, '16000.00', 'credit', 'completed', '2025-05-10 11:28:00', NULL),
(31, 1, '16000.00', 'credit', 'completed', '2025-05-10 11:29:03', NULL),
(32, 1, '16000.00', 'credit', 'completed', '2025-05-10 11:29:36', NULL),
(33, 1, '16000.00', 'credit', 'completed', '2025-05-10 11:30:39', NULL),
(34, 1, '7000.00', 'credit', 'completed', '2025-05-10 11:31:03', NULL),
(35, 1, '9000.00', 'credit', 'completed', '2025-05-10 11:33:06', NULL),
(36, 1, '9000.00', 'credit', 'completed', '2025-05-10 11:40:22', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_type`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 'hosting', '7', 1, '60000.00'),
(2, 1, 'ssl', 'ssl1', 1, '3500.00'),
(3, 2, 'hosting', '7', 1, '60000.00'),
(4, 2, 'ssl', 'ssl1', 1, '3500.00'),
(5, 3, 'hosting', '7', 1, '60000.00'),
(6, 3, 'ssl', 'ssl1', 1, '3500.00'),
(7, 4, 'hosting', '7', 1, '60000.00'),
(8, 4, 'ssl', 'ssl1', 1, '3500.00'),
(9, 5, 'hosting', '7', 1, '60000.00'),
(10, 5, 'ssl', 'ssl1', 1, '3500.00'),
(11, 6, 'hosting', '7', 1, '60000.00'),
(12, 6, 'ssl', 'ssl1', 1, '3500.00'),
(13, 7, 'wordpress', '2', 1, '25000.00'),
(14, 7, 'hosting', '2', 1, '7500.00'),
(15, 7, 'ssl', '1', 1, '3500.00'),
(16, 8, 'wordpress', '2', 1, '25000.00'),
(17, 8, 'hosting', '2', 1, '7500.00'),
(18, 8, 'ssl', '1', 1, '3500.00'),
(19, 9, 'wordpress', '1', 1, '15000.00'),
(20, 9, 'ssl', '3', 1, '10000.00'),
(21, 9, 'hosting', '1', 1, '5000.00'),
(22, 10, 'domain', '1', 1, '11000.00'),
(23, 11, 'domain', '1', 1, '9500.00'),
(24, 12, 'wordpress', '2', 1, '25000.00'),
(25, 13, 'hosting', '2', 1, '7500.00'),
(26, 14, 'wordpress', '1', 1, '15000.00'),
(27, 14, 'wordpress', '3', 1, '40000.00'),
(28, 14, 'ssl', '3', 1, '10000.00'),
(29, 14, 'hosting', '3', 1, '10000.00'),
(30, 15, 'domain', '1', 1, '9000.00'),
(31, 16, 'domain', '1', 1, '9000.00'),
(32, 17, 'domain', 'gta.store', 1, '9000.00'),
(33, 18, 'domain', 'exemple.wiki', 1, '9000.00'),
(34, 19, 'domain', '1', 1, '9000.00'),
(35, 20, 'domain', '1', 1, '9000.00'),
(36, 21, 'domain', '1', 1, '9000.00'),
(37, 21, 'domain', '1', 1, '9000.00'),
(38, 22, 'domain', '1', 1, '9000.00'),
(39, 22, 'domain', '1', 1, '9000.00'),
(40, 23, 'domain', '1', 1, '9000.00'),
(41, 23, 'domain', '1', 1, '9000.00'),
(42, 24, 'domain', '1', 1, '9000.00'),
(43, 24, 'domain', '1', 1, '9000.00'),
(44, 25, 'wordpress', '2', 1, '25000.00'),
(45, 25, 'ssl', '3', 1, '10000.00'),
(46, 25, 'hosting', '3', 1, '10000.00'),
(47, 26, 'wordpress', '1', 1, '15000.00'),
(48, 26, 'ssl', '3', 1, '10000.00'),
(49, 27, 'hosting', '3', 1, '10000.00'),
(50, 28, 'wordpress', '1', 1, '15000.00'),
(51, 29, 'hosting', '7', 1, '60000.00'),
(52, 30, 'ssl', '2', 1, '7000.00'),
(53, 30, 'domain', '1', 1, '9000.00'),
(54, 31, 'ssl', '2', 1, '7000.00'),
(55, 31, 'domain', '1', 1, '9000.00'),
(56, 32, 'ssl', '2', 1, '7000.00'),
(57, 32, 'domain', '1', 1, '9000.00'),
(58, 33, 'ssl', '2', 1, '7000.00'),
(59, 33, 'domain', '1', 1, '9000.00'),
(60, 34, 'ssl', '2', 1, '7000.00'),
(61, 35, 'domain', '1', 1, '9000.00'),
(62, 36, 'domain', '1', 1, '9000.00');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(191) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expiry_date`, `used`, `created_at`) VALUES
(1, 1, '60d7d995ff8e5f9545f174ee8dca9406', '2025-05-10 08:47:17', 1, '2025-05-09 09:47:17'),
(2, 1, '07dd806f45221d458fb23782594091e8', '2025-05-10 08:52:45', 1, '2025-05-09 09:52:45'),
(3, 1, 'ff0b3a60dac0ee7aac136884d52fc660', '2025-05-10 10:12:25', 1, '2025-05-09 11:12:25'),
(4, 1, '0688cc75204eba89a31be5b48f7c5f24', '2025-05-10 10:28:12', 1, '2025-05-09 11:28:12'),
(5, 1, '05f27cc091b9a32cb78e6d964d99e66c', '2025-05-10 10:31:48', 1, '2025-05-09 11:31:48'),
(6, 1, 'a878c3b259e02e4d471a1aeb94ca69f5', '2025-05-10 10:51:51', 1, '2025-05-09 11:51:51'),
(7, 1, 'd72c64d87c71ab547fe17c7209360b7a', '2025-05-10 10:59:56', 0, '2025-05-09 11:59:56');

-- --------------------------------------------------------

--
-- Structure de la table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `external_id` varchar(191) NOT NULL,
  `trans_id` varchar(191) DEFAULT NULL,
  `status` enum('CREATED','PENDING','SUCCESSFUL','FAILED','EXPIRED') NOT NULL DEFAULT 'PENDING',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `external_id` (`external_id`),
  KEY `trans_id` (`trans_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `user_id`, `amount`, `phone`, `email`, `external_id`, `trans_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '1000.00', '694193493', 'toscanisoft@gmail.com', 'kmerhosting_1746743296_1', NULL, 'PENDING', '2025-05-08 23:28:19', NULL),
(2, 1, '1000.00', '694193493', 'toscanisoft@gmail.com', 'kmerhosting_1746744360_1', NULL, 'PENDING', '2025-05-08 23:46:01', NULL),
(3, 1, '1000.00', '694193493', 'toscanisoft@gmail.com', 'kmerhosting_1746744861_1', 'mAdwdvdkr9', 'SUCCESSFUL', '2025-05-08 23:54:23', '2025-05-08 23:59:34'),
(4, 1, '1000.00', '694193493', 'toscanisoft@gmail.com', 'kmerhosting_1746748319_1', 'yNwWNGCLGZ', 'SUCCESSFUL', '2025-05-09 00:52:01', '2025-05-09 01:10:37'),
(5, 1, '1000.00', '694193493', 'toscanisoft@gmail.com', 'kmerhosting_1746749445_1', 'AB6Nhn3bED', 'SUCCESSFUL', '2025-05-09 01:10:47', '2025-05-09 01:11:14');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `features` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `name`, `icon`, `description`, `features`, `link`, `link_text`) VALUES
(1, 'Hébergement Web', 'fas fa-server', 'Solutions d\'hébergement partagé performantes et fiables pour tous types de sites web, du blog personnel aux sites d\'entreprise.', 'Espace SSD,Bande passante illimitée,Comptes email', '#packages', 'En savoir plus'),
(2, 'Hébergement WordPress', 'fab fa-wordpress', 'Hébergement optimisé pour WordPress avec installation en 1 clic, mises à jour automatiques et performances améliorées.', 'Installation en 1 clic,Cache LiteSpeed optimisé,Mises à jour automatiques,Plugins de sécurité inclus,Optimisation des performances', '#wordpress-packages', 'Voir nos packs WordPress'),
(3, 'Noms de Domaine', 'fas fa-globe', 'Enregistrez votre nom de domaine parfait parmi une large gamme d\'extensions (.com, .net, .org, .site, etc.).', 'Protection WHOIS,Transfert gratuit,DNS Management', 'domaines.html', 'En savoir plus'),
(4, 'Certificats SSL', 'fas fa-shield-alt', 'Sécurisez votre site web avec un certificat SSL pour protéger les données de vos visiteurs et améliorer votre référencement.', 'SSL Let\'s Encrypt gratuit,Installation automatique,Renouvellement automatique', '#packages', 'En savoir plus');

-- --------------------------------------------------------

--
-- Structure de la table `ssl_packages`
--

DROP TABLE IF EXISTS `ssl_packages`;
CREATE TABLE IF NOT EXISTS `ssl_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `features` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ssl_packages`
--

INSERT INTO `ssl_packages` (`id`, `name`, `price`, `features`, `recommended`) VALUES
(1, 'SSL Basic', '3500.00', 'Cryptage 256-bit,Protection pour 1 domaine,Installation gratuite,Support technique,Compatibilité navigateurs', 0),
(2, 'SSL Pro', '7000.00', 'Cryptage 256-bit,Protection multi-domaines (jusqu\'à 5),Installation gratuite,Support technique prioritaire,Compatibilité navigateurs,Sceau de sécurité,Garantie de remboursement', 1),
(3, 'SSL Business', '10000.00', 'Cryptage 256-bit,Protection Wildcard (*.votredomaine.com),Installation gratuite,Support technique 24/7,Compatibilité navigateurs,Sceau de sécurité premium,Garantie de remboursement,Validation d\'organisation,Assurance de 1 000 000$', 0);

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(20) NOT NULL,
  `description` text,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `amount`, `type`, `description`, `created_at`) VALUES
(1, 1, '63500.00', 'debit', 'Paiement de la commande #1', '2025-05-09 01:29:20'),
(2, 1, '63500.00', 'debit', 'Paiement de la commande #2', '2025-05-09 01:30:19'),
(3, 1, '63500.00', 'debit', 'Paiement de la commande #3', '2025-05-09 01:32:03'),
(4, 1, '63500.00', 'debit', 'Paiement de la commande #4', '2025-05-09 01:36:29'),
(5, 1, '63500.00', 'debit', 'Paiement de la commande #5', '2025-05-09 01:41:05'),
(6, 1, '63500.00', 'debit', 'Paiement de la commande #6', '2025-05-09 01:45:15'),
(7, 1, '36000.00', 'debit', 'Paiement de la commande #7', '2025-05-09 02:41:17'),
(8, 1, '36000.00', 'debit', 'Paiement de la commande #8', '2025-05-09 02:45:55'),
(9, 1, '30000.00', 'debit', 'Paiement de la commande #9', '2025-05-09 02:47:45'),
(10, 1, '11000.00', 'debit', 'Paiement de la commande #10', '2025-05-09 14:56:20'),
(11, 1, '9500.00', 'debit', 'Paiement de la commande #11', '2025-05-09 15:03:29'),
(12, 1, '25000.00', 'debit', 'Paiement de la commande #12', '2025-05-09 15:05:15'),
(13, 1, '7500.00', 'debit', 'Paiement de la commande #13', '2025-05-09 15:08:00'),
(14, 1, '75000.00', 'debit', 'Paiement de la commande #14', '2025-05-09 16:54:03'),
(15, 1, '9000.00', 'debit', 'Paiement de la commande #15', '2025-05-09 20:01:08'),
(16, 1, '9000.00', 'debit', 'Paiement de la commande #16', '2025-05-09 20:37:14'),
(17, 1, '9000.00', 'debit', 'Paiement de la commande #17', '2025-05-09 21:19:50'),
(18, 1, '9000.00', 'debit', 'Paiement de la commande #18', '2025-05-09 21:23:05'),
(19, 1, '9000.00', 'debit', 'Paiement de la commande #19', '2025-05-09 21:41:49'),
(20, 1, '9000.00', 'debit', 'Paiement de la commande #20', '2025-05-09 21:52:50'),
(21, 1, '18000.00', 'debit', 'Paiement de la commande #21', '2025-05-10 08:25:03'),
(22, 1, '18000.00', 'debit', 'Paiement de la commande #22', '2025-05-10 08:27:01'),
(23, 1, '18000.00', 'debit', 'Paiement de la commande #23', '2025-05-10 08:31:09'),
(24, 1, '18000.00', 'debit', 'Paiement de la commande #24', '2025-05-10 08:32:21'),
(25, 1, '45000.00', 'debit', 'Paiement de la commande #25', '2025-05-10 08:34:09'),
(26, 1, '25000.00', 'debit', 'Paiement de la commande #26', '2025-05-10 09:26:26'),
(27, 1, '10000.00', 'debit', 'Paiement de la commande #27', '2025-05-10 09:34:03'),
(28, 1, '15000.00', 'debit', 'Paiement de la commande #28', '2025-05-10 09:36:33'),
(29, 1, '60000.00', 'debit', 'Paiement de la commande #29', '2025-05-10 11:25:43'),
(30, 1, '16000.00', 'debit', 'Paiement de la commande #30', '2025-05-10 11:28:00'),
(31, 1, '16000.00', 'debit', 'Paiement de la commande #31', '2025-05-10 11:29:03'),
(32, 1, '16000.00', 'debit', 'Paiement de la commande #32', '2025-05-10 11:29:36'),
(33, 1, '16000.00', 'debit', 'Paiement de la commande #33', '2025-05-10 11:30:39'),
(34, 1, '7000.00', 'debit', 'Paiement de la commande #34', '2025-05-10 11:31:03'),
(35, 1, '9000.00', 'debit', 'Paiement de la commande #35', '2025-05-10 11:33:06'),
(36, 1, '9000.00', 'debit', 'Paiement de la commande #36', '2025-05-10 11:40:22');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profile_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `verification_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_expiry` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `tel`, `password`, `created_at`, `updated_at`, `profile_image`, `company`, `country`, `address`, `city`, `region`, `postal_code`, `website`, `credit`, `verification_code`, `verification_expiry`, `is_verified`) VALUES
(1, 'TENEKEU TOSCANI', 'TOSCANI', 'toscanisoft@gmail.com', '694193493', '$2y$10$jEm29BFLEF33GkNHeK93n.csGhzYiZV1u3IBD/AYkXmk4x7PlRxeK', '2025-05-07 17:34:04', '2025-05-09 10:34:08', NULL, 'Toscanisoft', 'Cameroun', 'NKOABAN', 'Yaounde', 'Centre', '5734', 'https://toscani-tenekeu.onrender.com', '0.00', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_services`
--

DROP TABLE IF EXISTS `user_services`;
CREATE TABLE IF NOT EXISTS `user_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service_type` enum('hosting','wordpress','ssl','domain') COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_id` int(11) NOT NULL,
  `domain_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` enum('monthly','quarterly','semi_annual','annual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'annual',
  `status` enum('pending','active','suspended','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `auto_renew` tinyint(1) NOT NULL DEFAULT '1',
  `connection_info` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON data with connection information',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_services`
--

INSERT INTO `user_services` (`id`, `user_id`, `service_type`, `service_id`, `domain_name`, `start_date`, `expiry_date`, `price`, `billing_cycle`, `status`, `auto_renew`, `connection_info`, `created_at`, `updated_at`) VALUES
(36, 1, 'domain', 1, 'toscani.technology', '2025-05-10', '2026-05-10', '9000.00', 'annual', 'pending', 1, NULL, '2025-05-10 10:40:22', '2025-05-10 10:40:22');

-- --------------------------------------------------------

--
-- Structure de la table `user_settings`
--

DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `theme` enum('light','dark') NOT NULL DEFAULT 'dark',
  `language` varchar(5) NOT NULL DEFAULT 'fr',
  `notifications_email` tinyint(1) NOT NULL DEFAULT '1',
  `notifications_sms` tinyint(1) NOT NULL DEFAULT '0',
  `notifications_expiry` tinyint(1) NOT NULL DEFAULT '1',
  `notifications_invoice` tinyint(1) NOT NULL DEFAULT '1',
  `notifications_news` tinyint(1) NOT NULL DEFAULT '0',
  `privacy_show_services` tinyint(1) NOT NULL DEFAULT '0',
  `privacy_show_domains` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `theme`, `language`, `notifications_email`, `notifications_sms`, `notifications_expiry`, `notifications_invoice`, `notifications_news`, `privacy_show_services`, `privacy_show_domains`, `created_at`, `updated_at`) VALUES
(1, 1, 'dark', 'en', 1, 0, 1, 1, 0, 0, 0, '2025-05-08 05:40:46', '2025-05-08 05:41:06');

-- --------------------------------------------------------

--
-- Structure de la table `wordpress_packages`
--

DROP TABLE IF EXISTS `wordpress_packages`;
CREATE TABLE IF NOT EXISTS `wordpress_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `disk_space` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bandwidth` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domains` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subdomains` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mysql_databases` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ftp_accounts` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `php_access` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `litespeed` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `directadmin` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inodes` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ssl` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sites_count` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_accounts` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_redirects` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mailing_lists` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_responders` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain_pointers` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cgi_access` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spamassassin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catch_all` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cron_jobs` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_info` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_keys` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dns_control` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `security_txt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_builder` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recommended` tinyint(1) NOT NULL DEFAULT '0',
  `features` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `wordpress_packages`
--

INSERT INTO `wordpress_packages` (`id`, `name`, `price`, `disk_space`, `bandwidth`, `domains`, `subdomains`, `mysql_databases`, `ftp_accounts`, `php_access`, `litespeed`, `directadmin`, `inodes`, `ssl`, `sites_count`, `email_accounts`, `email_redirects`, `mailing_lists`, `auto_responders`, `domain_pointers`, `cgi_access`, `spamassassin`, `catch_all`, `cron_jobs`, `redis`, `system_info`, `login_keys`, `dns_control`, `security_txt`, `site_builder`, `recommended`, `features`) VALUES
(1, 'WordPress Basic', '15000.00', '3 GB', '200 GB', 'Non inclus', '5', '10', '5', 'Oui', 'Inclus', 'Inclus', '10 000', 'Inclus', '3', '5', '5', '10', '10', '5', 'Oui', 'Non inclus', 'Non inclus', '5', 'Non inclus', 'Oui', 'Oui', 'Non inclus', 'Non inclus', 'Non inclus', 0, 'WordPress,Joomla,Drupal,Prestashop,LiteSpeed'),
(2, 'WordPress Pro', '25000.00', '5 GB', '500 GB', '1 (.com)', '10', '30', '20', 'Oui', 'Inclus', 'Inclus', '200 000', 'Inclus', '10', '15', '15', '20', '30', '10', 'Oui', 'Inclus', 'Inclus', '20', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 'Oui', 1, 'WordPress,Joomla,Drupal,Prestashop,LiteSpeed,Optimisation,WooCommerce'),
(3, 'WordPress Business', '40000.00', '10 GB', 'Illimitée', '1 (.*)', 'Illimitée', '100', '100', 'Oui', 'Inclus', 'Inclus', 'Illimitée', 'SSL Wildcard: Inclus', '30', '100', '100', 'Illimitée', '100', '100', 'Oui', 'Premium', 'Inclus', '100', 'Inclus', 'Oui', 'Oui', 'Inclus', 'Oui', 'Premium', 0, 'WordPress,Joomla,Drupal,Prestashop,LiteSpeed,Optimisation,Sécurité,WooCommerce');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `credit`
--
ALTER TABLE `credit`
  ADD CONSTRAINT `credit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
