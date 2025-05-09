-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 09 mai 2025 à 08:34
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, '260500.00', '2025-05-09 01:47:45');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

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
(9, 1, '30000.00', 'credit', 'completed', '2025-05-09 02:47:45', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

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
(21, 9, 'hosting', '1', 1, '5000.00');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

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
(9, 1, '30000.00', 'debit', 'Paiement de la commande #9', '2025-05-09 02:47:45');

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `tel`, `password`, `created_at`, `updated_at`, `profile_image`, `company`, `country`, `address`, `city`, `region`, `postal_code`, `website`, `credit`) VALUES
(1, 'TENEKEU TOSCANI', 'TOSCANI', 'toscanisoft@gmail.com', '694193493', '$2y$10$rS0OaJs545lPa19TNWcGuu.IP1Ra2WT2J/OsM0MtzsATz25qSn6Um', '2025-05-07 17:34:04', '2025-05-08 21:22:44', NULL, 'Toscanisoft', 'Cameroun', 'NKOABAN', 'Yaounde', 'Centre', '5734', 'https://toscani-tenekeu.onrender.com', '0.00');

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
  `billing_cycle` enum('monthly','quarterly','semi_annual','annual') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','active','suspended','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `auto_renew` tinyint(1) NOT NULL DEFAULT '1',
  `connection_info` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON data with connection information',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_services`
--

INSERT INTO `user_services` (`id`, `user_id`, `service_type`, `service_id`, `domain_name`, `start_date`, `expiry_date`, `price`, `billing_cycle`, `status`, `auto_renew`, `connection_info`, `created_at`, `updated_at`) VALUES
(1, 1, 'wordpress', 2, 'kmerhosting.site', '2023-05-07', '2025-08-05', '9500.00', 'quarterly', 'active', 1, '{\r\n  \"directadmin_url\": \"https://panel.kmerhosting.com:2222\",\r\n  \"directadmin_username\": \"user123\",\r\n  \"directadmin_password\": \"SecurePass123\",\r\n  \"phpmyadmin_url\": \"https://sql.kmerhosting.com/phpmyadmin\",\r\n  \"server_ip\": \"198.51.100.123\"\r\n}', '2025-05-07 22:53:48', '2025-05-08 11:58:27'),
(2, 1, 'hosting', 4, 'kmerhosting.com', '2023-05-07', '2025-08-05', '15000.00', 'quarterly', 'pending', 1, '{\r\n  \"directadmin_url\": \"https://panel.kmerhosting.com:2222\",\r\n  \"directadmin_username\": \"kmerhost\",\r\n  \"directadmin_password\": \"SecurePass456\",\r\n  \"phpmyadmin_url\": \"https://sql.kmerhosting.com/phpmyadmin\",\r\n  \"server_ip\": \"198.51.100.124\"\r\n}', '2025-05-08 11:58:27', '2025-05-08 12:08:52'),
(3, 1, 'ssl', 2, 'kmerhosting.com', '2023-05-07', '2025-08-05', '7000.00', 'annual', 'active', 1, NULL, '2025-05-08 11:58:27', '2025-05-08 11:58:27'),
(5, 1, 'hosting', 1, 'test-inactive.com', '2023-05-07', '2025-08-05', '5000.00', 'quarterly', 'suspended', 1, '{\r\n  \"directadmin_url\": \"https://panel.kmerhosting.com:2222\",\r\n  \"directadmin_username\": \"suspended_user\",\r\n  \"directadmin_password\": \"InactivePass123\",\r\n  \"phpmyadmin_url\": \"https://sql.kmerhosting.com/phpmyadmin\",\r\n  \"server_ip\": \"198.51.100.125\"\r\n}', '2025-05-08 11:58:28', '2025-05-08 11:58:28'),
(6, 1, 'hosting', 7, NULL, '2025-05-09', '2025-06-08', '60000.00', 'quarterly', 'pending', 1, NULL, '2025-05-09 00:45:15', '2025-05-09 00:49:14'),
(7, 1, 'ssl', 1, NULL, '2025-05-09', '2026-05-09', '3500.00', 'quarterly', 'active', 1, NULL, '2025-05-09 00:45:16', '2025-05-09 00:45:16'),
(8, 1, 'wordpress', 2, NULL, '2025-05-09', '2025-06-08', '25000.00', 'quarterly', 'pending', 1, '{\"directadmin_url\":\"https:\\/\\/panel.kmerhosting.site\",\"directadmin_username\":\"TOSCANI627\",\"directadmin_password\":\"Motdepasse@5323\",\"status\":\"Configuration en cours\"}', '2025-05-09 01:45:55', '2025-05-09 01:45:55'),
(9, 1, 'hosting', 2, NULL, '2025-05-09', '2025-06-08', '7500.00', 'quarterly', 'pending', 1, '{\"directadmin_url\":\"https:\\/\\/panel.kmerhosting.site\",\"directadmin_username\":\"TOSCANI604\",\"directadmin_password\":\"Motdepasse@9889\",\"status\":\"Configuration en cours\"}', '2025-05-09 01:45:55', '2025-05-09 01:45:55'),
(10, 1, 'ssl', 1, NULL, '2025-05-09', '2026-05-09', '3500.00', 'quarterly', 'pending', 1, NULL, '2025-05-09 01:45:55', '2025-05-09 01:45:55'),
(11, 1, 'wordpress', 1, NULL, '2025-05-09', '2025-06-08', '15000.00', 'quarterly', 'pending', 1, '{\"directadmin_url\":\"https:\\/\\/panel.kmerhosting.site\",\"directadmin_username\":\"TOSCANI746\",\"directadmin_password\":\"Motdepasse@5434\",\"status\":\"Configuration en cours\"}', '2025-05-09 01:47:45', '2025-05-09 01:47:45'),
(12, 1, 'ssl', 3, NULL, '2025-05-09', '2026-05-09', '10000.00', 'quarterly', 'pending', 1, NULL, '2025-05-09 01:47:45', '2025-05-09 01:47:45'),
(13, 1, 'hosting', 1, NULL, '2025-05-09', '2025-06-08', '5000.00', 'quarterly', 'pending', 1, '{\"directadmin_url\":\"https:\\/\\/panel.kmerhosting.site\",\"directadmin_username\":\"TOSCANI106\",\"directadmin_password\":\"Motdepasse@1685\",\"status\":\"Configuration en cours\"}', '2025-05-09 01:47:45', '2025-05-09 01:47:45');

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
