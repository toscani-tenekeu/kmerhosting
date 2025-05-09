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
$firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
$lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$company = isset($_POST['company']) ? trim($_POST['company']) : null;
$website = isset($_POST['website']) ? trim($_POST['website']) : null;
$address = isset($_POST['address']) ? trim($_POST['address']) : null;
$city = isset($_POST['city']) ? trim($_POST['city']) : null;
$region = isset($_POST['region']) ? trim($_POST['region']) : null;
$postalCode = isset($_POST['postalCode']) ? trim($_POST['postalCode']) : null;
$country = isset($_POST['country']) ? trim($_POST['country']) : null;

// Validation des données
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
    $response = [
        'success' => false,
        'message' => 'Veuillez remplir tous les champs obligatoires.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier si l'email est valide
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = [
        'success' => false,
        'message' => 'L\'adresse email n\'est pas valide.'
    ];
    echo json_encode($response);
    exit;
}

// Vérifier si l'email existe déjà pour un autre utilisateur
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        $response = [
            'success' => false,
            'message' => 'Cette adresse email est déjà utilisée par un autre compte.'
        ];
        echo json_encode($response);
        exit;
    }
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Une erreur est survenue lors de la vérification de l\'email.'
    ];
    echo json_encode($response);
    exit;
}

// Construire le nom complet
$fullname = $firstName . ' ' . $lastName;

// Mettre à jour les informations de l'utilisateur
try {
    $stmt = $pdo->prepare("
        UPDATE users SET 
        fullname = ?, 
        email = ?, 
        tel = ?, 
        company = ?, 
        website = ?, 
        address = ?, 
        city = ?, 
        region = ?, 
        postal_code = ?, 
        country = ? 
        WHERE id = ?
    ");
    $stmt->execute([
        $fullname,
        $email,
        $phone,
        $company,
        $website,
        $address,
        $city,
        $region,
        $postalCode,
        $country,
        $_SESSION['user_id']
    ]);

    $response = [
        'success' => true,
        'message' => 'Votre profil a été mis à jour avec succès.'
    ];
    echo json_encode($response);
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'message' => 'Une erreur est survenue lors de la mise à jour du profil: ' . $e->getMessage()
    ];
    echo json_encode($response);
}
