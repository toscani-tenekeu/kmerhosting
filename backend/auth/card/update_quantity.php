<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour mettre à jour le panier.']);
    exit;
}

// Inclure la configuration de la base de données et les fonctions
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $item_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    // Validation des données
    if ($item_id <= 0 || $quantity <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Données invalides.']);
        exit;
    }
    
    // Mettre à jour la quantité
    $result = updateCartItemQuantity($item_id, $quantity);
    
    // Répondre avec un JSON
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Quantité mise à jour avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la quantité.']);
    }
} else {
    // Si la méthode n'est pas POST, renvoyer une erreur
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>
