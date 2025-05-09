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

// Vérifier si un fichier a été envoyé
if (!isset($_FILES['profileImage']) || $_FILES['profileImage']['error'] !== UPLOAD_ERR_OK) {
    $response = [
        'success' => false,
        'message' => 'Aucun fichier n\'a été envoyé ou une erreur s\'est produite lors de l\'envoi.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier le type de fichier
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$fileType = $_FILES['profileImage']['type'];

if (!in_array($fileType, $allowedTypes)) {
    $response = [
        'success' => false,
        'message' => 'Le type de fichier n\'est pas autorisé. Seuls les formats JPEG, PNG, GIF et WEBP sont acceptés.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier la taille du fichier (max 2MB)
$maxFileSize = 2 * 1024 * 1024; // 2MB en octets
if ($_FILES['profileImage']['size'] > $maxFileSize) {
    $response = [
        'success' => false,
        'message' => 'Le fichier est trop volumineux. La taille maximale autorisée est de 2MB.'
    ];
    echo json_encode($response);
    exit;
}

// Créer le répertoire de destination s'il n'existe pas
$uploadDir = '../../uploads/profiles/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Générer un nom de fichier unique
$fileName = $_SESSION['user_id'] . '_' . time() . '_' . basename($_FILES['profileImage']['name']);
$targetFile = $uploadDir . $fileName;

// Déplacer le fichier téléchargé vers le répertoire de destination
if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile)) {
    // Mettre à jour le chemin de l'image de profil dans la base de données
    try {
        // Récupérer l'ancienne image de profil
        $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Supprimer l'ancienne image si elle existe
        if (!empty($user['profile_image']) && file_exists('../../' . $user['profile_image'])) {
            unlink('../../' . $user['profile_image']);
        }
        
        // Mettre à jour le chemin de l'image dans la base de données
        $relativePath = 'uploads/profiles/' . $fileName;
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$relativePath, $_SESSION['user_id']]);
        
        $response = [
            'success' => true,
            'message' => 'Votre image de profil a été mise à jour avec succès.',
            'imagePath' => $relativePath
        ];
        echo json_encode($response);
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'message' => 'Une erreur est survenue lors de la mise à jour de l\'image de profil: ' . $e->getMessage()
        ];
        echo json_encode($response);
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Une erreur est survenue lors du téléchargement de l\'image.'
    ];
    echo json_encode($response);
}
