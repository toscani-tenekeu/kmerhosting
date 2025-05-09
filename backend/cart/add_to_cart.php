<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la configuration de la base de données et les fonctions
require_once __DIR__ . '/../../backend/config/db.php';
require_once __DIR__ . '/../../backend/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter des articles au panier.']);
    exit;
}

// Vérifier si les données nécessaires sont présentes
if (!isset($_POST['package_id']) || !isset($_POST['package_type'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$package_id = $_POST['package_id'];
$package_type = $_POST['package_type'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Récupérer les informations du package
$package = null;
$product_id = '';

switch ($package_type) {
    case 'hosting':
        $package = getHostingPackageById($package_id);
        $product_id = $package_id;
        break;
    case 'wordpress':
        // Enlever "wp" du début si présent
        $id = (strpos($package_id, 'wp') === 0) ? substr($package_id, 2) : $package_id;
        $package = getWordpressPackageById($id);
        $product_id = $id;
        break;
    case 'ssl':
        // Enlever "ssl" du début si présent
        $id = (strpos($package_id, 'ssl') === 0) ? substr($package_id, 3) : $package_id;
        $package = getSSLPackageById($id);
        $product_id = $id;
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Type de package invalide.']);
        exit;
}

if (!$package) {
    echo json_encode(['success' => false, 'message' => 'Package introuvable.']);
    exit;
}

try {
    // Vérifier si le produit existe déjà dans le panier
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND product_type = ? AND product_id = ?");
    $stmt->execute([$user_id, $package_type, $product_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Mettre à jour la quantité
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ?, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([$quantity, $existing['id']]);
    } else {
        // Insérer nouveau produit
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_type, product_id, quantity, added_at) VALUES (?, ?, ?, ?, NOW())");
        $result = $stmt->execute([$user_id, $package_type, $product_id, $quantity]);
    }
    
    if ($result) {
        // Récupérer le nombre d'articles dans le panier
        $cartCount = getCartItemCount($user_id);
        
        echo json_encode(['success' => true, 'message' => 'Article ajouté au panier.', 'cart_count' => $cartCount]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout au panier.']);
    }
} catch (PDOException $e) {
    error_log("Erreur PDO lors de l'ajout au panier: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
