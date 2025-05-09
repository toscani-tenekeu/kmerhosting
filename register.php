<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - KmerHosting.site</title>
  
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
      
      <h1 class="text-3xl md:text-4xl font-bold mb-4 text-center">Créez votre compte Toscanisoft</h1>
      <p class="text-xl text-center mb-8">Accédez à tous nos produits et services (KmerHosting, CV Builder, etc)</p>
      
      <div class="w-full max-w-sm bg-kmergreen bg-opacity-30 p-6 rounded-lg backdrop-blur-sm">
        <h2 class="text-xl font-semibold mb-4">Avantages de votre compte</h2>
        <ul class="space-y-3">
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Accès à tous les services Toscanisoft</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Gestion simplifiée de vos services</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Support prioritaire pour vos demandes</span>
          </li>
          <li class="flex items-start">
            <i class="fas fa-check-circle text-kmergreen-light mt-1 mr-3"></i>
            <span>Offres exclusives pour les membres</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
  
  <!-- Right Side - Registration Form -->
  <div class="md:w-1/2 flex items-center justify-center p-8">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Créer un compte</h2>
        
        <?php if (isset($_GET['error'])): ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['error']); ?>
          </div>
        <?php elseif (isset($_GET['success'])): ?>
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['success']); ?>
          </div>
        <?php endif; ?>
        
        <form id="register-form" action="backend/auth/register.php" method="POST">
          <div class="mb-4">
            <label for="fullname" class="block text-gray-700 font-medium mb-2">Nom complet</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
              </div>
              <input type="text" id="fullname" name="fullname" required 
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
            </div>
          </div>
          
          <!-- Nouveau champ nom d'utilisateur -->
          <div class="mb-4">
            <label for="username" class="block text-gray-700 font-medium mb-2">Nom d'utilisateur</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-at text-gray-400"></i>
              </div>
              <input type="text" id="username" name="username" required 
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
            </div>
          </div>
          
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
          
          <div class="mb-4">
            <label for="tel" class="block text-gray-700 font-medium mb-2">Téléphone (WhatsApp)</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-phone text-gray-400"></i>
              </div>
              <input type="tel" id="tel" name="tel" required 
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
            </div>
          </div>
          
          <div class="mb-4">
            <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input type="password" id="password" name="password" required autocomplete="new-password"
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
              <button type="button" id="toggle-password" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 focus:outline-none" tabindex="-1">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <div id="password-strength" class="mt-1 text-xs text-gray-500">Force du mot de passe</div>
          </div>
          
          <div class="mb-6">
            <label for="confirm-password" class="block text-gray-700 font-medium mb-2">Confirmer le mot de passe</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input type="password" id="confirm-password" name="confirm-password" required autocomplete="new-password"
                     class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
              <button type="button" id="toggle-confirm-password" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 focus:outline-none" tabindex="-1">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
          
          <div class="mb-6">
            <label class="flex items-start">
              <input type="checkbox" name="terms" required class="mt-1 mr-2">
              <span class="text-sm text-gray-700">J'accepte les <a href="terms.php" class="text-kmergreen hover:underline">conditions d'utilisation</a> et la <a href="privacy.php" class="text-kmergreen hover:underline">politique de confidentialité</a></span>
            </label>
          </div>
          
          <button type="submit" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
            <i class="fas fa-user-plus mr-2"></i> Créer mon compte
          </button>
        </form>
        
        <div class="mt-6 text-center">
          <p class="text-gray-600 mb-4">Déjà un compte Toscanisoft ?</p>
          <a href="login.php" class="block w-full text-center bg-white border border-kmergreen text-kmergreen hover:bg-kmergreen-light hover:bg-opacity-10 font-medium py-2 px-4 rounded-lg transition duration-300">
            Se connecter
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
  
  <script>
    // Affichage/masquage mot de passe
    document.addEventListener('DOMContentLoaded', function() {
      // Toggle password
      const pwd = document.getElementById('password');
      const pwdBtn = document.getElementById('toggle-password');
      pwdBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (pwd.type === 'password') {
          pwd.type = 'text';
          pwdBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
          pwd.type = 'password';
          pwdBtn.innerHTML = '<i class="fas fa-eye"></i>';
        }
      });
      // Toggle confirm password
      const cpwd = document.getElementById('confirm-password');
      const cpwdBtn = document.getElementById('toggle-confirm-password');
      cpwdBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (cpwd.type === 'password') {
          cpwd.type = 'text';
          cpwdBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
          cpwd.type = 'password';
          cpwdBtn.innerHTML = '<i class="fas fa-eye"></i>';
        }
      });
      // Force du mot de passe
      const strengthDiv = document.getElementById('password-strength');
      pwd.addEventListener('input', function() {
        const val = pwd.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        let msg = 'Faible', color = 'text-red-500';
        if (score >= 4) { msg = 'Fort'; color = 'text-green-600'; }
        else if (score === 3) { msg = 'Moyen'; color = 'text-yellow-600'; }
        strengthDiv.textContent = 'Force du mot de passe : ' + msg;
        strengthDiv.className = 'mt-1 text-xs ' + color;
      });
    });
  </script>
</body>
</html>
