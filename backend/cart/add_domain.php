<?php
// Démarrer la session
session_start();

// Inclure les fonctions
require_once '../functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Renvoyer une réponse JSON avec un message d'erreur
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour ajouter un domaine au panier.'
    ]);
    exit;
}

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ]);
    exit;
}

// Récupérer les données du domaine
$domain = isset($_POST['domain']) ? trim($_POST['domain']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

// Vérifier si le domaine est valide
if (empty($domain)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Le nom de domaine est requis.'
    ]);
    exit;
}

// Récupérer l'ID de l'utilisateur
$user_id = $_SESSION['user_id'];

// Vérifier si le domaine est déjà dans le panier
$conn = $GLOBALS['conn'];
$stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_type = 'domain' AND custom_domain = ?");
$stmt->bind_param("is", $user_id, $domain);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Ce domaine est déjà dans votre panier.'
    ]);
    exit;
}

// Déterminer l'extension du domaine
$extension = '';
if (preg_match('/\.([a-z]{2,})$/i', $domain, $matches)) {
    $extension = strtolower($matches[1]);
}

// Trouver l'ID du package correspondant à l'extension
$package_id = 1; // ID par défaut
$stmt = $conn->prepare("SELECT id FROM domain_packages WHERE extension = ?");
$stmt->bind_param("s", $extension);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $package_id = $row['id'];
}

// Ajouter le domaine au panier
$stmt = $conn->prepare("INSERT INTO cart (user_id, product_type, product_id, quantity, custom_domain, added_at) VALUES (?, 'domain', ?, 1, ?, NOW())");
$stmt->bind_param("iis", $user_id, $package_id, $domain);


if ($stmt->execute()) {
    // Récupérer le nombre d'articles dans le panier
    $cartCount = getCartItemCount($user_id);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Le domaine a été ajouté à votre panier.',
        'cartCount' => $cartCount
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de l\'ajout du domaine au panier.'
    ]);
}

// Ajouter le domaine au historique des paniers
$stmt = $conn->prepare("INSERT INTO cart_history (user_id, product_type, product_id, quantity, custom_domain, added_at) VALUES (?, 'domain', ?, 1, ?, NOW())");
$stmt->bind_param("iis", $user_id, $package_id, $domain);


if ($stmt->execute()) {
   ;
} else {
   ;
}
?>