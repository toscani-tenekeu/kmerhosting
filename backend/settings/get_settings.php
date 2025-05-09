<?php
// Inclure la configuration de la base de données
require_once __DIR__ . '/../config/db.php';

/**
 * Fonction pour obtenir les paramètres d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @return array Paramètres de l'utilisateur
 */
function getUserSettings($user_id) {
    global $conn;
    
    // Vérifier si l'utilisateur a déjà des paramètres
    $sql = "SELECT * FROM user_settings WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Si l'utilisateur n'a pas encore de paramètres, créer des paramètres par défaut
    if ($result->num_rows === 0) {
        $sql = "INSERT INTO user_settings (user_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Récupérer les paramètres par défaut
        $sql = "SELECT * FROM user_settings WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    return $result->fetch_assoc();
}
