<?php
// Inclure la configuration de la base de données
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/get_settings.php';

/**
 * Fonction pour appliquer les paramètres d'un utilisateur à la session
 * @param int $user_id ID de l'utilisateur
 */
function applyUserSettings($user_id) {
    // Récupérer les paramètres de l'utilisateur
    $settings = getUserSettings($user_id);
    
    // Appliquer les paramètres à la session
    $_SESSION['user_settings'] = $settings;
    
    // Appliquer la langue
    if (isset($settings['language'])) {
        $_SESSION['language'] = $settings['language'];
    }
    
    // Appliquer le thème
    if (isset($settings['theme'])) {
        $_SESSION['theme'] = $settings['theme'];
    }
}
