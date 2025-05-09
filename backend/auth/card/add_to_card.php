<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter un article au panier.']);
    exit;
}

// Inclure la configuration de la base de données et les fonctions
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../functions.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $package_id = isset($_POST['package_id']) ? $_POST['package_id'] : '';
    $package_type = isset($_POST['package_type']) ? $_POST['package_type'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $custom_domain = isset($_POST['custom_domain']) ? $_POST['custom_domain'] : null;
    
    // Validation des données
    if (empty($package_id) || empty($package_type)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Données invalides.']);
        exit;
    }
    
    // Extraire l'ID numérique du package_id (par exemple, "wp1" -> 1, "ssl2" -> 2)
    $numeric_id = preg_replace('/[^0-9]/', '', $package_id);
    
    // Ajouter au panier
    $result = addToCart($_SESSION['user_id'], $package_type, $numeric_id, $quantity, $custom_domain);
    
    // Répondre avec un JSON
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Article ajouté au panier avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout au panier.']);
    }
} else {
    // Si la méthode n'est pas POST, renvoyer une erreur
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
