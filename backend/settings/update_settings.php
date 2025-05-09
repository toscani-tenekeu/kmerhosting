<?php
// Inclure la configuration de la base de données
require_once __DIR__ . '/../config/db.php';

/**
 * Fonction pour mettre à jour les paramètres d'un utilisateur
 * @param int $user_id ID de l'utilisateur
 * @param array $settings Paramètres à mettre à jour
 * @return bool True si la mise à jour a réussi, false sinon
 */
function updateUserSettings($user_id, $settings) {
    global $conn;
    
    // Construire la requête SQL dynamiquement
    $sql_parts = [];
    $types = "";
    $values = [];
    
    foreach ($settings as $key => $value) {
        $sql_parts[] = "$key = ?";
        
        // Déterminer le type de paramètre
        if (is_int($value)) {
            $types .= "i";
        } elseif (is_float($value)) {
            $types .= "d";
        } else {
            $types .= "s";
        }
        
        $values[] = $value;
    }
    
    // Ajouter l'ID de l'utilisateur
    $types .= "i";
    $values[] = $user_id;
    
    $sql = "UPDATE user_settings SET " . implode(", ", $sql_parts) . " WHERE user_id = ?";
    
    $stmt = $conn->prepare($sql);
    
    // Créer un tableau de références pour bind_param
    $params = array_merge([$types], $values);
    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key];
    }
    
    call_user_func_array([$stmt, 'bind_param'], $refs);
    
    return $stmt->execute();
}

// Traitement du formulaire de mise à jour des paramètres
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action.']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Récupérer les paramètres du formulaire
    $settings = [];
    
    // Paramètres d'interface
    if (isset($_POST['theme'])) {
        $settings['theme'] = $_POST['theme'] === 'dark' ? 'dark' : 'light';
    }
    
    if (isset($_POST['language'])) {
        $settings['language'] = in_array($_POST['language'], ['fr', 'en']) ? $_POST['language'] : 'fr';
    }
    
    // Paramètres de notifications
    if (isset($_POST['notifications_email'])) {
        $settings['notifications_email'] = $_POST['notifications_email'] ? 1 : 0;
    }
    
    if (isset($_POST['notifications_sms'])) {
        $settings['notifications_sms'] = $_POST['notifications_sms'] ? 1 : 0;
    }
    
    if (isset($_POST['notifications_expiry'])) {
        $settings['notifications_expiry'] = $_POST['notifications_expiry'] ? 1 : 0;
    }
    
    if (isset($_POST['notifications_invoice'])) {
        $settings['notifications_invoice'] = $_POST['notifications_invoice'] ? 1 : 0;
    }
    
    if (isset($_POST['notifications_news'])) {
        $settings['notifications_news'] = $_POST['notifications_news'] ? 1 : 0;
    }
    
    // Paramètres de confidentialité
    if (isset($_POST['privacy_show_services'])) {
        $settings['privacy_show_services'] = $_POST['privacy_show_services'] ? 1 : 0;
    }
    
    if (isset($_POST['privacy_show_domains'])) {
        $settings['privacy_show_domains'] = $_POST['privacy_show_domains'] ? 1 : 0;
    }
    
    // Mettre à jour les paramètres
    $success = updateUserSettings($user_id, $settings);
    
    // Répondre en JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $success ? 'Paramètres mis à jour avec succès.' : 'Erreur lors de la mise à jour des paramètres.']);
    exit;
}
