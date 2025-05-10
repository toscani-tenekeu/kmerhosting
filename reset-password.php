<?php
// Démarrer la session
session_start();

// Éviter d'afficher des erreurs PHP directement
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Fonction pour gérer les erreurs
function handleError($message, $redirect = true) {
    $_SESSION['reset_error'] = $message;
    if ($redirect) {
        header('Location: login.php?error=' . urlencode($message));
        exit;
    }
    return false;
}

try {
    // Inclure la configuration de la base de données
    require_once 'backend/config/db.php';

    // Vérifier si un token est fourni
    $token = isset($_GET['token']) ? trim($_GET['token']) : '';
    $validToken = false;
    $userId = null;
    $userEmail = null;

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
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $message = "Le mot de passe doit contenir au moins une lettre majuscule.";
            $messageType = "error";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $message = "Le mot de passe doit contenir au moins un chiffre.";
            $messageType = "error";
        } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $message = "Le mot de passe doit contenir au moins un caractère spécial.";
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
                
                // Enregistrer l'action dans un journal de sécurité (optionnel)
                $logMessage = "Réinitialisation de mot de passe réussie pour l'utilisateur ID: $userId, Email: $userEmail, IP: " . $_SERVER['REMOTE_ADDR'] . ", Date: " . date('Y-m-d H:i:s');
                error_log($logMessage);
                
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
} catch (PDOException $e) {
    error_log("Erreur PDO dans reset-password.php: " . $e->getMessage());
    $message = "Une erreur de base de données est survenue. Veuillez réessayer plus tard.";
    $messageType = "error";
} catch (Exception $e) {
    error_log("Exception dans reset-password.php: " . $e->getMessage());
    $message = "Une erreur est survenue. Veuillez réessayer plus tard.";
    $messageType = "error";
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
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <?php echo htmlspecialchars($message); ?>
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
          <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" id="reset-password-form">
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-gray-700 font-medium">Nouveau mot de passe</label>
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
              <div class="mt-2 text-sm text-gray-500">
                <ul class="list-disc pl-5 space-y-1">
                  <li id="length-check" class="text-red-500">Au moins 8 caractères</li>
                  <li id="uppercase-check" class="text-red-500">Au moins une lettre majuscule</li>
                  <li id="number-check" class="text-red-500">Au moins un chiffre</li>
                  <li id="special-check" class="text-red-500">Au moins un caractère spécial</li>
                </ul>
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
              <div id="match-check" class="mt-2 text-sm text-red-500 hidden">
                Les mots de passe ne correspondent pas
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
    document.addEventListener('DOMContentLoaded', function() {
      // Affichage/masquage mot de passe
      const toggleButtons = document.querySelectorAll('.toggle-password');
      
      toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
          const input = this.parentNode.querySelector('input');
          const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
          input.setAttribute('type', type);
          this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
      });
      
      // Validation en temps réel du mot de passe
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirm_password');
      const lengthCheck = document.getElementById('length-check');
      const uppercaseCheck = document.getElementById('uppercase-check');
      const numberCheck = document.getElementById('number-check');
      const specialCheck = document.getElementById('special-check');
      const matchCheck = document.getElementById('match-check');
      
      if (passwordInput) {
        passwordInput.addEventListener('input', function() {
          const password = this.value;
          
          // Vérifier la longueur
          if (password.length >= 8) {
            lengthCheck.classList.remove('text-red-500');
            lengthCheck.classList.add('text-green-500');
          } else {
            lengthCheck.classList.remove('text-green-500');
            lengthCheck.classList.add('text-red-500');
          }
          
          // Vérifier la présence d'une majuscule
          if (/[A-Z]/.test(password)) {
            uppercaseCheck.classList.remove('text-red-500');
            uppercaseCheck.classList.add('text-green-500');
          } else {
            uppercaseCheck.classList.remove('text-green-500');
            uppercaseCheck.classList.add('text-red-500');
          }
          
          // Vérifier la présence d'un chiffre
          if (/[0-9]/.test(password)) {
            numberCheck.classList.remove('text-red-500');
            numberCheck.classList.add('text-green-500');
          } else {
            numberCheck.classList.remove('text-green-500');
            numberCheck.classList.add('text-red-500');
          }
          
          // Vérifier la présence d'un caractère spécial
          if (/[^A-Za-z0-9]/.test(password)) {
            specialCheck.classList.remove('text-red-500');
            specialCheck.classList.add('text-green-500');
          } else {
            specialCheck.classList.remove('text-green-500');
            specialCheck.classList.add('text-red-500');
          }
          
          // Vérifier la correspondance si le champ de confirmation est rempli
          if (confirmPasswordInput.value) {
            if (password === confirmPasswordInput.value) {
              matchCheck.classList.add('hidden');
            } else {
              matchCheck.classList.remove('hidden');
            }
          }
        });
      }
      
      if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
          const confirmPassword = this.value;
          const password = passwordInput.value;
          
          if (password === confirmPassword) {
            matchCheck.classList.add('hidden');
          } else {
            matchCheck.classList.remove('hidden');
          }
        });
      }
      
      // Validation du formulaire avant soumission
      const resetForm = document.getElementById('reset-password-form');
      
      if (resetForm) {
        resetForm.addEventListener('submit', function(e) {
          const password = passwordInput.value;
          const confirmPassword = confirmPasswordInput.value;
          
          let isValid = true;
          
          // Vérifier les critères du mot de passe
          if (password.length < 8 || 
              !/[A-Z]/.test(password) || 
              !/[0-9]/.test(password) || 
              !/[^A-Za-z0-9]/.test(password)) {
            isValid = false;
          }
          
          // Vérifier la correspondance
          if (password !== confirmPassword) {
            isValid = false;
          }
          
          if (!isValid) {
            e.preventDefault();
            Swal.fire({
              title: 'Erreur de validation',
              text: 'Veuillez corriger les erreurs dans le formulaire.',
              icon: 'error',
              confirmButtonColor: '#10b981'
            });
          }
        });
      }
    });
  </script>
</body>
</html>
