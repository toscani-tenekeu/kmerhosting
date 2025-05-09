<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action.']);
    exit;
}

// Inclure la configuration de la base de données
require_once __DIR__ . '/../config/db.php';

// Récupérer l'ID de l'utilisateur
$user_id = $_SESSION['user_id'];

try {
    // Commencer une transaction
    $conn->begin_transaction();
    
    // 1. Supprimer les services de l'utilisateur
    $sql = "DELETE FROM user_services WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 2. Supprimer les éléments du panier
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 3. Supprimer les commandes et les articles de commande
    $sql = "SELECT id FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $order_id = $row['id'];
        
        // Supprimer les articles de commande
        $sql = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    }
    
    // Supprimer les commandes
    $sql = "DELETE FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 4. Supprimer les transactions
    $sql = "DELETE FROM transactions WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 5. Supprimer le crédit
    $sql = "DELETE FROM credit WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 6. Supprimer les paramètres
    $sql = "DELETE FROM user_settings WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 7. Supprimer les transactions de paiement
    $sql = "DELETE FROM payment_transactions WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // 8. Enfin, supprimer l'utilisateur
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Valider la transaction
    $conn->commit();
    
    // Détruire la session
    session_destroy();
    
    // Retourner une réponse de succès
    echo json_encode(['success' => true, 'message' => 'Votre compte a été supprimé avec succès.']);
    
} catch (Exception $e) {
    // En cas d'erreur, annuler la transaction
    $conn->rollback();
    
    // Journaliser l'erreur
    error_log('Erreur lors de la suppression du compte: ' . $e->getMessage());
    
    // Retourner une réponse d'erreur
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de votre compte. Veuillez contacter le support.']);
}
?>
