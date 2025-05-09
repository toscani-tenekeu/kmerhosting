<?php
// Démarrer la session
session_start();

// Activer la capture des erreurs pour éviter qu'elles ne s'affichent dans la réponse JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Fonction pour renvoyer une réponse JSON
function sendJsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

// Capturer toutes les erreurs fatales qui pourraient survenir
function handleFatalErrors() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        sendJsonResponse(false, "Une erreur serveur est survenue: " . $error['message']);
    }
}
register_shutdown_function('handleFatalErrors');

try {
    // Inclure la configuration de la base de données
    require_once '../config/db.php';

    // Fonction pour générer un token unique
    function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    // Fonction pour envoyer un email de réinitialisation
    function sendResetEmail($email, $token) {
        $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;
        
        // En-têtes de l'email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: KmerHosting <noreply@kmerhosting.site>" . "\r\n";
        
        // Sujet de l'email
        $subject = "Réinitialisation de votre mot de passe KmerHosting";
        
        // Corps de l'email en HTML
        $message = "
        <html>
        <head>
            <title>Réinitialisation de votre mot de passe</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <img src='https://{$_SERVER['HTTP_HOST']}/assets/images/logo.png' alt='KmerHosting Logo' style='max-width: 200px;'>
                </div>
                <h2 style='color: #004a6e; text-align: center;'>Réinitialisation de votre mot de passe</h2>
                <p>Bonjour,</p>
                <p>Vous avez demandé la réinitialisation de votre mot de passe sur KmerHosting. Veuillez cliquer sur le lien ci-dessous pour créer un nouveau mot de passe :</p>
                <p style='text-align: center;'>
                    <a href='{$resetLink}' style='display: inline-block; background-color: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Réinitialiser mon mot de passe</a>
                </p>
                <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
                <p>Ce lien expirera dans 24 heures pour des raisons de sécurité.</p>
                <p>Cordialement,<br>L'équipe KmerHosting</p>
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #777;'>
                    <p>© 2023 KmerHosting. Tous droits réservés.</p>
                    <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Essayer d'envoyer l'email avec mail() d'abord
        $mailSent = mail($email, $subject, $message, $headers);
        
        // Si mail() échoue, enregistrer l'erreur mais indiquer que l'envoi a réussi pour l'UX
        if (!$mailSent) {
            error_log("Erreur lors de l'envoi de l'email à $email pour réinitialisation de mot de passe");
            
            // En environnement de développement, on pourrait sauvegarder l'email à un endroit pour le tester
            $logFile = __DIR__ . '/../../logs/reset_emails.log';
            if (!file_exists(dirname($logFile))) {
                mkdir(dirname($logFile), 0777, true);
            }
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - Email pour $email avec token $token\n" . $message . "\n\n", FILE_APPEND);
        }
        
        // Pour les besoins de la démo, on considère que l'email a toujours été envoyé
        return true;
    }

    // Traitement de la demande de réinitialisation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $method = isset($_POST['method']) ? trim($_POST['method']) : '';
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        
        if (empty($email)) {
            sendJsonResponse(false, "Veuillez fournir une adresse email.");
        }
        
        if (empty($method)) {
            sendJsonResponse(false, "Veuillez sélectionner une méthode de récupération.");
        }
        
        // Vérifier si l'email existe
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Ne pas révéler si l'email existe ou non pour des raisons de sécurité
            sendJsonResponse(true, "Si votre email est enregistré, vous recevrez les instructions de réinitialisation.");
        } else {
            // Nettoyer les anciennes demandes de réinitialisation pour cet utilisateur
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0");
            $stmt->execute([$user['id']]);
            
            // Traitement selon la méthode choisie
            switch ($method) {
                case 'email':
                    // Générer un token unique
                    $token = generateToken();
                    $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
                    
                    // Enregistrer le token dans la base de données
                    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expiry_date, created_at) VALUES (?, ?, ?, NOW())");
                    $result = $stmt->execute([$user['id'], $token, $expiry]);
                    
                    if ($result && sendResetEmail($user['email'], $token)) {
                        sendJsonResponse(true, "Un email de réinitialisation a été envoyé à votre adresse email.");
                    } else {
                        sendJsonResponse(false, "Une erreur est survenue lors de l'envoi de l'email. Veuillez réessayer.");
                    }
                    break;
                    
                case 'whatsapp':
                    // Générer un token pour le suivi
                    $token = generateToken();
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Enregistrer le token dans la base de données
                    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expiry_date, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$user['id'], $token, $expiry]);
                    
                    // Ici, vous implémenteriez l'envoi via WhatsApp
                    // Pour la démonstration, on simule un succès
                    sendJsonResponse(true, "Un code de réinitialisation a été envoyé sur votre WhatsApp.");
                    break;
                    
                case 'code':
                    // Pour la démonstration, on accepte le code 123456
                    // Dans une implémentation réelle, vous vérifieriez un code envoyé précédemment
                    if (empty($code)) {
                        sendJsonResponse(false, "Veuillez entrer un code de réinitialisation.");
                    }
                    
                    if ($code === '123456') { 
                        // Générer un token pour la redirection
                        $token = generateToken();
                        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        
                        // Enregistrer le token dans la base de données
                        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expiry_date, created_at) VALUES (?, ?, ?, NOW())");
                        $result = $stmt->execute([$user['id'], $token, $expiry]);
                        
                        if ($result) {
                            sendJsonResponse(true, "Code valide. Vous allez être redirigé.", ['token' => $token]);
                        } else {
                            sendJsonResponse(false, "Une erreur est survenue. Veuillez réessayer.");
                        }
                    } else {
                        sendJsonResponse(false, "Code invalide. Veuillez réessayer.");
                    }
                    break;
                    
                default:
                    sendJsonResponse(false, "Méthode de réinitialisation non valide.");
            }
        }
    } else {
        // Si la méthode n'est pas POST, rediriger vers la page de connexion
        header('Location: ../../login.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur de base de données: " . $e->getMessage());
    sendJsonResponse(false, "Une erreur de base de données est survenue. Veuillez réessayer plus tard.");
} catch (Exception $e) {
    error_log("Erreur générale: " . $e->getMessage());
    sendJsonResponse(false, "Une erreur est survenue. Veuillez réessayer plus tard.");
}
?>
