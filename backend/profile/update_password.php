<?php
session_start();
require_once '../config/db.php';
require_once '../functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $response = [
        'success' => false,
        'message' => 'Vous devez être connecté pour effectuer cette action.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = [
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ];
    echo json_encode($response);
    exit;
}

// Récupérer les données du formulaire
$currentPassword = isset($_POST['currentPassword']) ? trim($_POST['currentPassword']) : '';
$newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
$confirmPassword = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

// Validation des données
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $response = [
        'success' => false,
        'message' => 'Veuillez remplir tous les champs.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier si les nouveaux mots de passe correspondent
if ($newPassword !== $confirmPassword) {
    $response = [
        'success' => false,
        'message' => 'Les nouveaux mots de passe ne correspondent pas.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier si le nouveau mot de passe est assez fort
if (strlen($newPassword) < 8) {
    $response = [
        'success' => false,
        'message' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.'
    ];
    echo json_encode($response);
    exit;
}

// Récupérer le mot de passe actuel de l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $response = [
            'success' => false,
            'message' => 'Utilisateur non trouvé.'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Vérifier si le mot de passe actuel est correct
    if (!password_verify($currentPassword, $user['password'])) {
        $response = [
            'success' => false,
            'message' => 'Le mot de passe actuel est incorrect.'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Hasher le nouveau mot de passe
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Mettre à jour le mot de passe
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
    
    $response = [
        'success' => true,
        'message' => 'Votre mot de passe a été mis à jour avec succès.'
    ];
    echo json_encode($response);
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Une erreur est survenue lors de la mise à jour du mot de passe: ' . $e->getMessage()
    ];
    echo json_encode($response);
}
