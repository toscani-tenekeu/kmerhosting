<?php
// Paramètres de connexion à la base de données
$host = 'localhost';
$dbname = 'kmerhosting';
$username = 'root';
$password = 'root'; // Laissez vide si aucun mot de passe n'est défini

// Connexion PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Connexion MySQLi (pour la compatibilité avec le code existant)
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Rendre les connexions disponibles globalement
global $pdo, $conn;
