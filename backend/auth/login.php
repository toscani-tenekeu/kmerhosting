<?php
// Démarrer la session
session_start();

// Rediriger si l'utilisateur est déjà connecté
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: customers/dashboard.php');
    exit;
}

// Stocker l'URL de redirection si elle est fournie
if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - KmerHosting.site</title>
  
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
<body class="bg-gray-100 min-h-screen flex flex-col md:flex-row">
  <!-- Left Side - Green-Blue Gradient Background with Logo -->
  <div class="bg-gradient-to-r from-kmergreen to-kmerblue md:w-1/2 flex flex-col items-center justify-center p-8 text-white">
    <a href="index.php" class="absolute top-4 left-4 text-white hover:text-kmergreen-light flex items-center">
      <i class="fas fa-arrow-left mr-2"></i> Retour à l'accueil
    </a>
    
    <div class="max-w-md w-full flex flex-col items-center">
      <div class="mb-8">
        <img src="assets/images/logo.png" alt="KmerHosting Logo" class="h-20 mb-6">
      </div>
      
      <h1 class="text-3xl md:text-4xl font-bold mb-4 text-center">Bienvenue sur KmerHosting</h1>
      <p class="text-xl text-center mb-8">Votre compte Toscanisoft vous donne accès à tous nos produits et services (KamerHosting, CV Builder, etc)</p>
      
      <div class="w-full max-w-sm bg-kmergreen bg-opacity-30 p-6 rounded-lg backdrop-blur-sm">
        <h2 class="text-xl font-semibold mb-4">Pourquoi choisir KmerHosting?</h2>
        <ul class="space-y-3">
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Hébergement web fiable et performant</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Support technique 24/7 à votre disposition</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Solutions adaptées aux entreprises camerounaises</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Paiements sécurisés via Mobile Money</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Right Side - Login Form -->
  <div class="md:w-1/2 flex items-center justify-center p-8">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Connexion</h2>
        
        <?php if (isset($_GET['error'])): ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['error']); ?>
          </div>
        <?php elseif (isset($_GET['success'])): ?>
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['success']); ?>
          </div>
        <?php endif; ?>
        
        <form id="login-form" action="backend/auth/login.php" method="POST">
          <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-2">Adresse email</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
              </div>
              <input type="email" id="email" name="email" required 
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
            </div>
          </div>
          
          <div class="mb-6">
            <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input type="password" id="password" name="password" required 
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
              <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 focus:outline-none">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
          
          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
              <input type="checkbox" class="form-checkbox h-4 w-4 text-kmergreen">
              <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>
            <a href="#" id="forgot-password-link" class="text-sm text-kmergreen hover:underline">Mot de passe oublié ?</a>
          </div>
          
          <button type="submit" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Se connecter
          </button>
        </form>
        
        <div class="mt-6 text-center">
          <p class="text-gray-600 mb-4">Nouveau sur Toscanisoft ?</p>
          <a href="register.php" class="block w-full text-center bg-white border border-kmergreen text-kmergreen hover:bg-kmergreen-light hover:bg-opacity-10 font-medium py-2 px-4 rounded-lg transition duration-300">
            Créer un compte
          </a>
        </div>
      </div>
      
      <div class="mt-6 text-center text-gray-500 text-sm">
        <p>© 2023 KmerHosting. Tous droits réservés.</p>
        <div class="mt-2 flex justify-center space-x-4">
          <a href="terms.php" class="hover:text-kmergreen">Conditions d'utilisation</a>
          <a href="privacy.php" class="hover:text-kmergreen">Politique de confidentialité</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal de récupération de mot de passe -->
  <div id="forgot-password-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
      <button id="close-forgot-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
      
      <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-kmergreen bg-opacity-10 text-kmergreen mb-4">
          <i class="fas fa-key text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800">Récupération de mot de passe</h3>
        <p class="text-gray-600 mt-2">Choisissez une méthode pour récupérer votre mot de passe</p>
      </div>
      
      <form id="forgot-password-form" class="space-y-4">
        <div class="mb-4">
          <label for="recovery-email" class="block text-gray-700 font-medium mb-2">Votre adresse email</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" id="recovery-email" name="recovery-email" required 
                   class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
          </div>
        </div>
        
        <div class="space-y-3">
          <p class="font-medium text-gray-700">Méthode de récupération:</p>
          
          <button type="button" class="recovery-option w-full flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-kmergreen transition duration-200" data-method="email">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
              <i class="fas fa-envelope text-blue-500"></i>
            </div>
            <div class="text-left">
              <h4 class="font-medium text-gray-800">Par email</h4>
              <p class="text-sm text-gray-500">Recevoir un lien de réinitialisation par email</p>
            </div>
          </button>
          
          <button type="button" class="recovery-option w-full flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-kmergreen transition duration-200" data-method="whatsapp">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
              <i class="fab fa-whatsapp text-green-500"></i>
            </div>
            <div class="text-left">
              <h4 class="font-medium text-gray-800">Par WhatsApp</h4>
              <p class="text-sm text-gray-500">Recevoir un code de réinitialisation par WhatsApp</p>
            </div>
          </button>
          
          <button type="button" class="recovery-option w-full flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-kmergreen transition duration-200" data-method="code">
            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
              <i class="fas fa-shield-alt text-purple-500"></i>
            </div>
            <div class="text-left">
              <h4 class="font-medium text-gray-800">Code de réinitialisation</h4>
              <p class="text-sm text-gray-500">Utiliser un code de réinitialisation prédéfini</p>
            </div>
          </button>
        </div>
        
        <div id="recovery-code-input" class="hidden mt-4">
          <label for="reset-code" class="block text-gray-700 font-medium mb-2">Code de réinitialisation</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-key text-gray-400"></i>
            </div>
            <input type="text" id="reset-code" name="reset-code" placeholder="Entrez votre code de réinitialisation" 
                   class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
          </div>
        </div>
        
        <button type="submit" id="submit-recovery" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center mt-6">
          <i class="fas fa-paper-plane mr-2"></i> Envoyer la demande
        </button>
      </form>
    </div>
  </div>
  
  <script>
    // Affichage/masquage mot de passe
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      const toggleBtn = document.querySelector('#login-form button[type="button"]');
      let show = false;
      toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        show = !show;
        passwordInput.type = show ? 'text' : 'password';
        toggleBtn.innerHTML = show ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
      });
      
      // Gestion du modal de récupération de mot de passe
      const forgotPasswordLink = document.getElementById('forgot-password-link');
      const forgotPasswordModal = document.getElementById('forgot-password-modal');
      const closeForgotModal = document.getElementById('close-forgot-modal');
      const recoveryOptions = document.querySelectorAll('.recovery-option');
      const recoveryCodeInput = document.getElementById('recovery-code-input');
      const forgotPasswordForm = document.getElementById('forgot-password-form');
      
      // Ouvrir le modal
      forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        forgotPasswordModal.classList.remove('hidden');
      });
      
      // Fermer le modal
      closeForgotModal.addEventListener('click', function() {
        forgotPasswordModal.classList.add('hidden');
      });
      
      // Fermer le modal en cliquant à l'extérieur
      window.addEventListener('click', function(e) {
        if (e.target === forgotPasswordModal) {
          forgotPasswordModal.classList.add('hidden');
        }
      });
      
      // Gestion des options de récupération
      let selectedMethod = null;
      
      recoveryOptions.forEach(option => {
        option.addEventListener('click', function() {
          // Réinitialiser toutes les options
          recoveryOptions.forEach(opt => {
            opt.classList.remove('ring-2', 'ring-kmergreen', 'bg-gray-50');
          });
          
          // Sélectionner l'option actuelle
          this.classList.add('ring-2', 'ring-kmergreen', 'bg-gray-50');
          selectedMethod = this.getAttribute('data-method');
          
          // Afficher le champ de code si nécessaire
          if (selectedMethod === 'code') {
            recoveryCodeInput.classList.remove('hidden');
          } else {
            recoveryCodeInput.classList.add('hidden');
          }
        });
      });
      
      // Soumission du formulaire
      forgotPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!selectedMethod) {
          Swal.fire({
            title: 'Attention',
            text: 'Veuillez sélectionner une méthode de récupération',
            icon: 'warning',
            confirmButtonColor: '#10b981'
          });
          return;
        }
        
        const email = document.getElementById('recovery-email').value;
        
        if (!email) {
          Swal.fire({
            title: 'Attention',
            text: 'Veuillez entrer votre adresse email',
            icon: 'warning',
            confirmButtonColor: '#10b981'
          });
          return;
        }
        
        // Simulation de l'envoi de la demande
        Swal.fire({
          title: 'Traitement en cours',
          text: 'Veuillez patienter...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        // Simuler un délai de traitement
        setTimeout(() => {
          let title, text, icon;
          
          switch(selectedMethod) {
            case 'email':
              title = 'Email envoyé';
              text = 'Un lien de réinitialisation a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception.';
              icon = 'success';
              break;
            case 'whatsapp':
              title = 'Code WhatsApp envoyé';
              text = 'Un code de réinitialisation a été envoyé sur votre WhatsApp. Veuillez vérifier vos messages.';
              icon = 'success';
              break;
            case 'code':
              const code = document.getElementById('reset-code').value;
              if (!code) {
                title = 'Attention';
                text = 'Veuillez entrer un code de réinitialisation';
                icon = 'warning';
              } else if (code === '123456') { // Code de démonstration
                title = 'Code valide';
                text = 'Votre code de réinitialisation est valide. Vous allez être redirigé vers la page de réinitialisation.';
                icon = 'success';
              } else {
                title = 'Code invalide';
                text = 'Le code de réinitialisation que vous avez entré est invalide. Veuillez réessayer.';
                icon = 'error';
              }
              break;
          }
          
          Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonColor: '#10b981'
          }).then(() => {
            if (icon === 'success') {
              forgotPasswordModal.classList.add('hidden');
            }
          });
        }, 2000);
      });
    });
  </script>
</body>
</html>
