<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
if (!$item_id) {
    echo json_encode(['status' => 'error', 'message' => 'ID d\'article invalide']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$item_id, $_SESSION['user_id']]);
    
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données']);
}
