<?php
require_once __DIR__ . '/../config/db.php';

function redirectWithMessage($url, $type, $message) {
    header('Location: ' . $url . '?' . $type . '=' . urlencode($message));
    exit;
}

// Récupérer et sécuriser les données
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$tel = trim($_POST['tel'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm-password'] ?? '';

// Validation stricte
if (!$fullname || !$username || !$email || !$tel || !$password || !$confirm) {
    redirectWithMessage('../../register.php', 'error', 'Tous les champs sont obligatoires.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage('../../register.php', 'error', 'Adresse email invalide.');
}
if ($password !== $confirm) {
    redirectWithMessage('../../register.php', 'error', 'Les mots de passe ne correspondent pas.');
}
if (strlen($password) < 8) {
    redirectWithMessage('../../register.php', 'error', 'Le mot de passe doit contenir au moins 8 caractères.');
}
// Vérifier unicité email/username
$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ? OR username = ?');
$stmt->execute([$email, $username]);
if ($stmt->fetchColumn() > 0) {
    redirectWithMessage('../../register.php', 'error', 'Email ou nom d\'utilisateur déjà utilisé.');
}
// Sécuriser les entrées
$fullname = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
$username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$tel = htmlspecialchars($tel, ENT_QUOTES, 'UTF-8');
$hash = password_hash($password, PASSWORD_DEFAULT);
// Insertion
$stmt = $pdo->prepare('INSERT INTO users (fullname, username, email, tel, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
try {
    $stmt->execute([$fullname, $username, $email, $tel, $hash]);
    redirectWithMessage('../../login.php', 'success', 'Compte créé avec succès. Connectez-vous.');
} catch (Exception $e) {
    redirectWithMessage('../../register.php', 'error', 'Erreur lors de la création du compte.');
}
