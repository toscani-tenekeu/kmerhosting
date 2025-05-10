<?php
// Inclure la configuration de la base de données
require_once __DIR__ . '/config/db.php';

/**
 * Fonction pour obtenir le crédit disponible d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return float Crédit disponible
 */
function getUserCredit($user_id) {
    global $conn;
    
    // Vérifier si l'utilisateur a déjà un crédit
    $sql = "SELECT amount FROM credit WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Créer un crédit initial XAF
        $sql = "INSERT INTO credit (user_id, amount) VALUES (?, 0.00)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return 0.00;
    }
    
    $row = $result->fetch_assoc();
    return floatval($row['amount']);
}

/**
 * Fonction pour mettre à jour le crédit d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @param float $new_credit Nouveau montant de crédit
 * @return bool True si la mise à jour a réussi, false sinon
 */
function updateUserCredit($user_id, $new_credit) {
    global $conn;
    
    $sql = "UPDATE credit SET amount = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $new_credit, $user_id);
    return $stmt->execute();
}

/**
 * Fonction pour obtenir les services actifs d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return array Services actifs de l'utilisateur
 */
function getUserActiveServices($user_id) {
    global $conn;
    
    $sql = "SELECT us.*, 
        CASE 
            WHEN us.service_type = 'hosting' THEN hp.name
            WHEN us.service_type = 'wordpress' THEN wp.name
            WHEN us.service_type = 'ssl' THEN sp.name
            ELSE 'Service inconnu'
        END as service_name
        FROM user_services us
        LEFT JOIN hosting_packages hp ON us.service_type = 'hosting' AND us.service_id = hp.id
        LEFT JOIN wordpress_packages wp ON us.service_type = 'wordpress' AND us.service_id = wp.id
        LEFT JOIN ssl_packages sp ON us.service_type = 'ssl' AND us.service_id = sp.id
        WHERE us.user_id = ? AND us.status = 'active'
        ORDER BY us.expiry_date ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    return $services;
}

/**
 * Fonction pour obtenir les éléments du panier d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return array Éléments du panier
 */
function getUserCartItems($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT c.*, 
            CASE 
                WHEN c.product_type = 'hosting' THEN hp.name
                WHEN c.product_type = 'wordpress' THEN wp.name
                WHEN c.product_type = 'ssl' THEN sp.name
                WHEN c.product_type = 'domain' THEN c.custom_domain
                ELSE 'Produit inconnu'
            END as product_name,
            CASE 
                WHEN c.product_type = 'hosting' THEN hp.price
                WHEN c.product_type = 'wordpress' THEN wp.price
                WHEN c.product_type = 'ssl' THEN sp.price
                WHEN c.product_type = 'domain' THEN 9000
                ELSE 0
            END as price
            FROM cart c
            LEFT JOIN hosting_packages hp ON c.product_type = 'hosting' AND c.product_id = hp.id
            LEFT JOIN wordpress_packages wp ON c.product_type = 'wordpress' AND c.product_id = wp.id
            LEFT JOIN ssl_packages sp ON c.product_type = 'ssl' AND REPLACE(c.product_id, 'ssl', '') = sp.id
            WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        if (
            ($row['product_type'] === 'ssl' && empty($row['product_name'])) ||
            ($row['product_type'] === 'wordpress' && empty($row['product_name'])) ||
            ($row['product_type'] === 'hosting' && empty($row['product_name'])) ||
            ($row['product_type'] === 'domain' && empty($row['product_name']))
        ) {
            if ($row['product_type'] === 'domain' && !empty($row['custom_domain'])) {
                $row['product_name'] = $row['custom_domain'];
                $row['price'] = 9000; // Prix fixe pour les domaines
            } else {
                $row['product_name'] = 'Produit inconnu (id: ' . $row['product_id'] . ')';
            }
        }
        
        $cartItems[] = $row;
    }
    
    return $cartItems;
}

/**
 * Fonction pour calculer le total du panier
 * @param array $cartItems Éléments du panier
 * @return float Total du panier
 */
function calculateCartTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

/**
 * Fonction pour formater le prix
 * @param float $price Prix à formater
 * @return string Prix formaté
 */
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

/**
 * Fonction pour vérifier si un utilisateur est connecté
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Fonction pour rediriger vers la page de connexion si l'utilisateur n'est pas connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Fonction pour obtenir les informations d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return array Informations de l'utilisateur
 */
function getUserInfo($user_id) {
    global $conn;
    
    $sql = "SELECT * FROM users WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

/**
 * Fonction pour obtenir les informations de l'utilisateur actuel
 * @return array|null Informations de l'utilisateur ou null si non connecté
 */
function getCurrentUser() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        return null;
    }
    
    global $conn;
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Fonction pour obtenir tous les packages d'hébergement
 * @return array Packages d'hébergement
 */
function getHostingPackages() {
    global $conn;
    
    $sql = "SELECT * FROM hosting_packages ORDER BY price ASC";
    
    $result = $conn->query($sql);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Fonction pour obtenir tous les packages WordPress
 * @return array Packages WordPress
 */
function getWordpressPackages() {
    global $conn;
    
    $sql = "SELECT * FROM wordpress_packages ORDER BY price ASC";
    
    $result = $conn->query($sql);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Fonction pour obtenir tous les packages SSL
 * @return array Packages SSL
 */
function getSSLPackages() {
    global $conn;
    
    $sql = "SELECT * FROM ssl_packages ORDER BY price ASC";
    
    $result = $conn->query($sql);
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Fonction pour obtenir tous les packages de domaines
 * @return array Packages de domaines
 */
function getDomainPackages() {
    global $conn;
    
    $sql = "SELECT * FROM domain_packages ORDER BY price ASC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        return [];
    }
    
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    
    return $packages;
}

/**
 * Fonction pour ajouter un élément au panier
 * @param int $user_id ID de l'utilisateur
 * @param string $product_type Type de produit (hosting, wordpress, ssl)
 * @param int $product_id ID du produit
 * @param int $quantity Quantité
 * @return bool True si l'ajout a réussi, false sinon
 */
function addToCart($user_id, $product_type, $product_id, $quantity = 1) {
    global $conn;

    // Forcer l'ID à être un entier
    if (!is_numeric($product_id)) {
        // Extraire l'ID numérique d'une chaîne comme 'ssl3', 'wp2', etc.
        $numeric_id = preg_replace('/[^0-9]/', '', $product_id);
        if ($numeric_id === '' || !is_numeric($numeric_id)) {
            // ID non valide, on refuse l'ajout
            return false;
        }
        $product_id = (int)$numeric_id;
    } else {
        $product_id = (int)$product_id;
    }

    // Vérifier si l'élément existe déjà dans le panier
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_type = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $user_id, $product_type, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // L'élément existe déjà, mettre à jour la quantité
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;

        $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $row['id']);
        return $stmt->execute();
    } else {
        // Ajouter un nouvel élément a l'historique
        $sql = "INSERT INTO cart_history (user_id, product_type, product_id, quantity) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isis", $user_id, $product_type, $product_id, $quantity);
        $stmt->execute();

        // Ajouter un nouvel élément
        $sql = "INSERT INTO cart (user_id, product_type, product_id, quantity) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isis", $user_id, $product_type, $product_id, $quantity);
        return $stmt->execute();

    }
}

/**
 * Fonction pour mettre à jour la quantité d'un élément du panier
 * @param int $cart_id ID de l'élément du panier
 * @param int $quantity Nouvelle quantité
 * @return bool True si la mise à jour a réussi, false sinon
 */
function updateCartItemQuantity($cart_id, $quantity) {
    global $conn;
    
    $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cart_id);
    return $stmt->execute();
}

/**
 * Fonction pour supprimer un élément du panier
 * @param int $cart_id ID de l'élément du panier
 * @return bool True si la suppression a réussi, false sinon
 */
function removeCartItem($cart_id) {
    global $conn;
    
    $sql = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    return $stmt->execute();
}

/**
 * Fonction pour vider le panier d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return bool True si le vidage a réussi, false sinon
 */
function clearCart($user_id) {
    global $conn;
    
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    return $stmt->execute();
}

/**
 * Fonction pour obtenir le nom d'un produit
 * @param string $product_type Type de produit (hosting, wordpress, ssl)
 * @param int $product_id ID du produit
 * @return string Nom du produit
 */
function getProductName($product_type, $product_id) {
    global $conn;
    
    switch ($product_type) {
        case 'hosting':
            $sql = "SELECT name FROM hosting_packages WHERE id = ?";
            break;
        case 'wordpress':
            $sql = "SELECT name FROM wordpress_packages WHERE id = ?";
            break;
        case 'ssl':
            $sql = "SELECT name FROM ssl_packages WHERE id = ?";
            break;
        case 'domain':
            return 'Domaine: ' . $product_id;
        default:
            return 'Produit en attente de validation';
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return 'Produit inconnu';
    }
    
    $row = $result->fetch_assoc();
    return $row['name'];
}

/**
 * Fonction pour obtenir le nombre d'éléments dans le panier d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return int Nombre d'éléments dans le panier
 */
function getCartItemCount($user_id) {
    global $conn;
    
    $sql = "SELECT COUNT(*) FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    
    return (int)$row[0];
}

/**
 * Fonction pour obtenir tous les services
 * @return array Services
 */
function getAllServices() {
    global $conn;
    
    $sql = "SELECT * FROM services";
    
    $result = $conn->query($sql);
    
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    return $services;
}

/**
 * Fonction pour obtenir un service par son ID
 * @param int $service_id ID du service
 * @return array|bool Informations du service ou false si non trouvé
 */
function getServiceById($service_id) {
    global $conn;
    
    $sql = "SELECT * FROM services WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Fonction pour obtenir un package d'hébergement par son ID
 * @param int $package_id ID du package
 * @return array|bool Informations du package ou false si non trouvé
 */
function getHostingPackageById($package_id) {
    global $conn;
    
    $sql = "SELECT * FROM hosting_packages WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Fonction pour obtenir un package WordPress par son ID
 * @param int $package_id ID du package
 * @return array|bool Informations du package ou false si non trouvé
 */
function getWordpressPackageById($package_id) {
    global $conn;
    
    $sql = "SELECT * FROM wordpress_packages WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Fonction pour obtenir un package SSL par son ID
 * @param int $package_id ID du package
 * @return array|bool Informations du package ou false si non trouvé
 */
function getSSLPackageById($package_id) {
    global $conn;
    
    $sql = "SELECT * FROM ssl_packages WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Fonction pour obtenir un package de domaine par son ID
 * @param int $package_id ID du package
 * @return array|bool Informations du package ou false si non trouvé
 */
function getDomainPackageById($package_id) {
    global $conn;
    
    $sql = "SELECT * FROM domain_packages WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Fonction pour obtenir les détails d'utilisation d'un service
 * @param int $service_id ID du service
 * @param string $service_type Type de service
 * @return array Détails d'utilisation
 */
function getServiceUsageDetails($service_id, $service_type) {
    // Cette fonction devrait être implémentée pour récupérer les données réelles d'utilisation
    // Pour l'instant, nous retournons des données fictives
    
    $usage = [
        'disk_used' => 2.1,
        'bandwidth_used' => 45,
        'databases_used' => 3,
        'ftp_accounts_used' => 2,
        'subdomains_used' => 3
    ];
    
    return $usage;
}

/**
 * Fonction pour calculer le pourcentage d'utilisation
 * @param float $used Quantité utilisée
 * @param float $total Quantité totale
 * @return int Pourcentage d'utilisation
 */
function calculateUsagePercentage($used, $total) {
    if ($total == 0) return 0;
    return min(100, round(($used / $total) * 100));
}

/**
 * Fonction pour convertir une chaîne de caractères séparée par des virgules en tableau
 * @param string $string Chaîne de caractères
 * @return array Tableau
 */
function stringToArray($string) {
    return explode(',', $string);
}

/**
 * Fonction pour obtenir les services d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return array Services de l'utilisateur
 */
function getUserServices($user_id) {
    global $conn;
    
    $sql = "SELECT us.*, 
        CASE 
            WHEN us.service_type = 'hosting' THEN hp.name
            WHEN us.service_type = 'wordpress' THEN wp.name
            WHEN us.service_type = 'ssl' THEN sp.name
            WHEN us.service_type = 'domain' THEN CONCAT('Domaine: ', us.domain_name)
            ELSE 'Service inconnu'
        END as service_name
        FROM user_services us
        LEFT JOIN hosting_packages hp ON us.service_type = 'hosting' AND us.service_id = hp.id
        LEFT JOIN wordpress_packages wp ON us.service_type = 'wordpress' AND us.service_id = wp.id
        LEFT JOIN ssl_packages sp ON us.service_type = 'ssl' AND us.service_id = sp.id
        WHERE us.user_id = ? 
        ORDER BY us.expiry_date ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    
    return $services;
}

/**
 * Fonction pour obtenir le badge de statut
 * @param string $status Statut
 * @return string HTML du badge
 */
function getStatusBadge($status) {
    switch ($status) {
        case 'active':
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-900/30 text-green-400">Actif</span>';
        case 'pending':
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-900/30 text-yellow-400">En attente</span>';
        case 'suspended':
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-900/30 text-red-400">Suspendu</span>';
        case 'cancelled':
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-900/30 text-gray-400">Annulé</span>';
        case 'expired':
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-900/30 text-red-400">Expiré</span>';
        default:
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-900/30 text-gray-400">' . ucfirst($status) . '</span>';
    }
}

/**
 * Fonction pour obtenir le libellé du cycle de facturation
 * @param string $cycle Cycle de facturation
 * @return string Libellé
 */
function getBillingCycleLabel($cycle) {
    switch ($cycle) {
        case 'monthly':
            return 'Mensuel';
        case 'quarterly':
            return 'Trimestriel';
        case 'semi_annual':
            return 'Semestriel';
        case 'annual':
            return 'Annuel';
        default:
            return 'Annuel';
    }
}

/**
 * Fonction pour obtenir les caractéristiques d'un package
 * @param string $features Caractéristiques séparées par des virgules
 * @return array Tableau de caractéristiques
 */
function explodeFeatures($features) {
    if (empty($features)) {
        return [];
    }
    return explode(',', $features);
}

/**
 * Fonction pour vérifier si une fonctionnalité est incluse
 * @param string $feature Fonctionnalité à vérifier
 * @return bool True si la fonctionnalité est incluse, false sinon
 */
function isFeatureIncluded($feature) {
    return $feature !== 'Non' && $feature !== '0' && $feature !== 'non' && $feature !== 'Non inclus';
}

/**
 * Fonction pour obtenir la classe CSS d'une fonctionnalité
 * @param string $feature Fonctionnalité à vérifier
 * @return string Classe CSS
 */
function getFeatureClass($feature) {
    return isFeatureIncluded($feature) ? 'text-kmergreen' : 'text-gray-400';
}

/**
 * Récupérer les offres dynamiques pour la section Nos Offres
 * @return array
 */
function getOffers() {
    global $conn;
    $sql = "SELECT * FROM offers ORDER BY id ASC";
    $result = $conn->query($sql);
    $offers = [];
    while ($row = $result->fetch_assoc()) {
        $row['features'] = explode("\n", $row['features']);
        $offers[] = $row;
    }
    return $offers;
}

/**
 * Fonction pour créer une commande à partir du panier
 * @param int $user_id ID de l'utilisateur
 * @param string $payment_method Méthode de paiement utilisée
 * @return int|bool ID de la commande créée ou false en cas d'échec
 */
function createOrderFromCart($user_id, $payment_method = 'credit') {
    global $conn;
    
    // Récupérer les articles du panier
    $cartItems = getUserCartItems($user_id);
    
    if (empty($cartItems)) {
        return false;
    }
    
    // Calculer le total
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Créer la commande
    $sql = "INSERT INTO orders (user_id, total_amount, payment_method, status, created_at) 
            VALUES (?, ?, ?, 'completed', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $user_id, $total, $payment_method);
    
    if (!$stmt->execute()) {
        return false;
    }
    
    $order_id = $conn->insert_id;
    
    // Ajouter les articles à la commande
    foreach ($cartItems as $item) {
        $sql = "INSERT INTO order_items (order_id, product_type, product_id, quantity, price) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issis", $order_id, $item['product_type'], $item['product_id'], $item['quantity'], $item['price']);
        
        if (!$stmt->execute()) {
            // En cas d'erreur, on pourrait annuler la commande, mais pour simplifier, on continue
            error_log("Erreur lors de l'ajout de l'article à la commande: " . $stmt->error);
        }
    }
    
    // Enregistrer la transaction
    $sql = "INSERT INTO transactions (user_id, amount, type, description, created_at) 
            VALUES (?, ?, 'debit', ?, NOW())";
    
    $description = "Paiement de la commande #" . $order_id;
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $user_id, $total, $description);
    $stmt->execute();
    
    return $order_id;
}

/**
 * Fonction pour générer des informations de connexion par défaut pour un nouveau service
 * @param string $service_type Type de service (hosting, wordpress, ssl)
 * @param int $user_id ID de l'utilisateur
 * @return string Informations de connexion au format JSON
 */
function generateDefaultConnectionInfo($service_type, $user_id) {
    // Récupérer les informations de l'utilisateur
    $user = getUserInfo($user_id);
    $username = $user['username'] ?? 'user' . $user_id;
    
    switch ($service_type) {
        case 'hosting':
        case 'wordpress':
            return json_encode([
                'directadmin_url' => 'http://panel.kmerhosting.site',
                'directadmin_username' => $username . date('His'),
               'directadmin_password' => $username . rand(1000, 9999) . date('His'),
                'server_ip' => 'hidden'
            ]);
        case 'ssl':
            return null; // Les certificats SSL n'ont généralement pas d'informations de connexion
        default:
            return null;
    }
}

/**
 * Fonction pour créer les services à partir d'une commande - Version corrigée
 * @param int $order_id ID de la commande
 * @return bool True si la création a réussi, false sinon
 */
function createServicesFromOrder($order_id) {
    global $conn;
    
    // Récupérer les informations de la commande
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $order = $result->fetch_assoc();
    $user_id = $order['user_id'];
    
    // Récupérer les articles de la commande
    $sql = "SELECT * FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $success = true;
    
    while ($item = $result->fetch_assoc()) {
        // Déterminer la durée du service en fonction du type de produit
        $duration = 365; // Par défaut 365 jours (1 an)
        
        switch ($item['product_type']) {
            case 'hosting':
                $duration = 365; // 1 an
                break;
            case 'wordpress':
                $duration = 365; // 1 an
                break;
            case 'ssl':
                $duration = 365; // 1 an
                break;
            case 'domain':
                $duration = 365; // 1 an
                break;
        }
        
        // Calculer la date d'expiration
        $expiry_date = date('Y-m-d H:i:s', strtotime('+' . $duration . ' days'));
        
        // Extraire l'ID numérique si nécessaire
        $service_id = $item['product_id'];
        if (!is_numeric($service_id)) {
            $service_id = preg_replace('/[^0-9]/', '', $service_id);
        }
        
        // Générer les informations de connexion par défaut
        $connection_info = generateDefaultConnectionInfo($item['product_type'], $user_id);
        
        // Pour les domaines, récupérer le nom de domaine depuis le panier 
        if ($item['product_type'] === 'domain') {
            // Vérifier si le product_id est un nom de domaine ou un ID
            $domain_name = $item['product_id'];
            
            // Si c'est un ID numérique, essayer de récupérer le nom de domaine depuis le panier
            if (is_numeric($domain_name)) {
                // Correction ici: utilisation de mysqli au lieu de PDO
                $domain_query = "SELECT custom_domain FROM cart WHERE user_id = ? ORDER BY id DESC LIMIT 1";
                $domain_stmt = $conn->prepare($domain_query);
                $domain_stmt->bind_param("i", $user_id);
                $domain_stmt->execute();
                $domain_result = $domain_stmt->get_result();
                
                if ($domain_result && $domain_result->num_rows > 0) {
                    $domain_row = $domain_result->fetch_assoc();
                    if (!empty($domain_row['custom_domain'])) {
                        $domain_name = $domain_row['custom_domain'];
                    }
                }
            }
            
            // Créer le service avec le nom de domaine
            $sql = "INSERT INTO user_services (user_id, service_type, service_id, domain_name, start_date, expiry_date, price, billing_cycle, status, auto_renew, connection_info, created_at) 
            VALUES (?, ?, ?, ?, NOW(), ?, ?, 'annual', 'pending', 1, ?, NOW())";
            
            try {
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    error_log("Prepare failed: " . $conn->error);
                    $success = false;
                    continue;
                }
                
                if (!$stmt->bind_param("issssds", $user_id, $item['product_type'], $service_id, $domain_name, $expiry_date, $item['price'], $connection_info)) {
                    error_log("Binding parameters failed: " . $stmt->error);
                    $success = false;
                    continue;
                }
                
                if (!$stmt->execute()) {
                    error_log("Erreur lors de la création du service: " . $stmt->error);
                    $success = false;
                }
            } catch (Exception $e) {
                error_log("Exception during service creation: " . $e->getMessage());
                $success = false;
            }
        } else {
            // Pour les autres types de services, utiliser la requête existante
            $sql = "INSERT INTO user_services (user_id, service_type, service_id, domain_name, start_date, expiry_date, price, billing_cycle, status, auto_renew, connection_info, created_at) 
            VALUES (?, ?, ?, NULL, NOW(), ?, ?, 'annual', 'pending', 1, ?, NOW())";

            try {
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    error_log("Prepare failed: " . $conn->error);
                    $success = false;
                    continue;
                }
                
                if (!$stmt->bind_param("isssds", $user_id, $item['product_type'], $service_id, $expiry_date, $item['price'], $connection_info)) {
                    error_log("Binding parameters failed: " . $stmt->error);
                    $success = false;
                    continue;
                }
                
                if (!$stmt->execute()) {
                    error_log("Erreur lors de la création du service: " . $stmt->error);
                    $success = false;
                }
            } catch (Exception $e) {
                error_log("Exception during service creation: " . $e->getMessage());
                $success = false;
            }
        }
    }
    
    return $success;
}

/**
 * Fonction pour obtenir les détails d'une commande
 * @param int $order_id ID de la commande
 * @return array|bool Détails de la commande ou false si non trouvée
 */
function getOrderDetails($order_id) {
    global $conn;
    
    // Récupérer les informations de la commande
    $sql = "SELECT o.*, u.username, u.email, u.fullname 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $order = $result->fetch_assoc();
    
    // Récupérer les articles de la commande
    $sql = "SELECT oi.*, 
            CASE 
                WHEN oi.product_type = 'hosting' THEN hp.name
                WHEN oi.product_type = 'wordpress' THEN wp.name
                WHEN oi.product_type = 'ssl' THEN sp.name
                WHEN oi.product_type = 'domain' THEN oi.product_id
                ELSE 'Produit inconnu'
            END as product_name
            FROM order_items oi
            LEFT JOIN hosting_packages hp ON oi.product_type = 'hosting' AND oi.product_id = hp.id
            LEFT JOIN wordpress_packages wp ON oi.product_type = 'wordpress' AND oi.product_id = wp.id
            LEFT JOIN ssl_packages sp ON oi.product_type = 'ssl' AND REPLACE(oi.product_id, 'ssl', '') = sp.id
            WHERE oi.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        // Pour les domaines, afficher le nom du domaine comme nom de produit
        if ($row['product_type'] === 'domain') {
            $row['product_name'] = 'Domaine: ' . $row['product_id'];
        }
        $items[] = $row;
    }
    
    $order['items'] = $items;
    
    return $order;
}
?>
