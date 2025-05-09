<?php
// Démarrer la session
session_start();

// Inclure la configuration de la base de données
require_once 'backend/config/db.php';

// Vérifier si un token est fourni
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$validToken = false;
$userId = null;

if (!empty($token)) {
    // Vérifier si le token existe et n'a pas expiré
    $stmt = $pdo->prepare("
        SELECT pr.user_id, u.email 
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.token = ? AND pr.expiry_date > NOW() AND pr.used = 0
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
    
    if ($reset) {
        $validToken = true;
        $userId = $reset['user_id'];
        $userEmail = $reset['email'];
    }
}

// Traitement du formulaire de réinitialisation
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validation des mots de passe
    if (empty($password)) {
        $message = "Veuillez entrer un mot de passe.";
        $messageType = "error";
    } elseif (strlen($password) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
        $messageType = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageType = "error";
    } else {
        // Mettre à jour le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $result = $stmt->execute([$hashedPassword, $userId]);
        
        if ($result) {
            // Marquer le token comme utilisé
            $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            $message = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
            $messageType = "success";
            
            // Rediriger vers la page de connexion après 3 secondes
            header("Refresh: 3; URL=login.php");
        } else {
            $message = "Une erreur est survenue lors de la réinitialisation du mot de passe. Veuillez réessayer.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Réinitialisation de mot de passe - KmerHosting.site</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">
  
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
      tailwind.config = {
          theme: {
              extend: {
                  colors: {
                      kmerblue: {
                          DEFAULT: '#004a6e',
                          light: '#005d8a',
                          dark: '#003a57'
                      },
                      kmergreen: {
                          DEFAULT: '#10b981',
                          light: '#34d399',
                          dark: '#059669'
                      }
                  }
              }
          }
      }
  </script>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="max-w-md w-full">
    <div class="text-center mb-8">
      <a href="index.php">
        <img src="assets/images/logo.png" alt="KmerHosting Logo" class="h-16 mx-auto mb-4">
      </a>
      <h1 class="text-2xl font-bold text-gray-800">Réinitialisation de mot de passe</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-8">
      <?php if (!$validToken): ?>
        <div class="text-center py-6">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-500 mb-4">
            <i class="fas fa-exclamation-circle text-2xl"></i>
          </div>
          <h2 class="text-xl font-bold text-gray-800 mb-2">Lien invalide ou expiré</h2>
          <p class="text-gray-600 mb-6">Le lien de réinitialisation que vous avez utilisé est invalide ou a expiré.</p>
          <a href="login.php" class="inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
            Retour à la page de connexion
          </a>
        </div>
      <?php else: ?>
        <?php if (!empty($message)): ?>
          <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo $message; ?>
          </div>
          
          <?php if ($messageType === 'success'): ?>
            <div class="text-center">
              <p class="text-gray-600 mb-4">Vous allez être redirigé vers la page de connexion...</p>
              <a href="login.php" class="inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                Connexion
              </a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php if (empty($message) || $messageType !== 'success'): ?>
          <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>">
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-gray-700 font-medium">Nouveau mot de passe</label>
                <span class="text-xs text-gray-500">Min. 8 caractères</span>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" id="password" name="password" required minlength="8"
                       class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
                <button type="button" class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 focus:outline-none">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            
            <div class="mb-6">
              <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirmer le mot de passe</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                       class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
                <button type="button" class="toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 focus:outline-none">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            
            <button type="submit" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
              <i class="fas fa-save mr-2"></i> Réinitialiser mon mot de passe
            </button>
          </form>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    
    <div class="mt-6 text-center text-gray-500 text-sm">
      <p>© 2023 KmerHosting. Tous droits réservés.</p>
      <div class="mt-2 flex justify-center space-x-4">
        <a href="terms.php" class="hover:text-kmergreen">Conditions d'utilisation</a>
        <a href="privacy.php" class="hover:text-kmergreen">Politique de confidentialité</a>
      </div>
    </div>
  </div>
  
  <script>
    // Affichage/masquage mot de passe
    document.addEventListener('DOMContentLoaded', function() {
      const toggleButtons = document.querySelectorAll('.toggle-password');
      
      toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
          const input = this.parentNode.querySelector('input');
          const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
          input.setAttribute('type', type);
          this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
      });
    });
  </script>
</body>
</html>
