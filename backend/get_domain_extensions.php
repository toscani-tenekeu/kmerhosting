<?php
// Inclure la configuration de la base de données
require_once 'config/db.php';

// Fonction pour récupérer toutes les extensions de domaine
function getDomainExtensions() {
    global $conn;
    
    $sql = "SELECT * FROM domain_packages ORDER BY price ASC";
    $result = $conn->query($sql);
    
    $extensions = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $extensions[] = $row;
        }
    }
    
    return $extensions;
}

// Si ce fichier est appelé directement, renvoyer les extensions au format JSON
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(getDomainExtensions());
}
?>
