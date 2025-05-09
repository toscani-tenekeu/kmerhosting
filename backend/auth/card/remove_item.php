<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer un article du panier.']);
    exit;
}

// Inclure la configuration de la base de données et les fonctions
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de l'article à supprimer
    $item_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    // Validation des données
    if ($item_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID d\'article invalide.']);
        exit;
    }
    
    // Supprimer l'article du panier
    $result = removeCartItem($item_id);
    
    // Répondre avec un JSON
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Article supprimé du panier avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de l\'article.']);
    }
} else {
    // Si la méthode n'est pas POST, renvoyer une erreur
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>
