<?php
// Démarrer la session
session_start();

// Définir la page actuelle
$currentPage = 'profil';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Stocker l'URL actuelle pour redirection après connexion
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php?error=Vous devez être connecté pour accéder à cette page.');
    exit;
}

// Inclure la configuration de la base de données et les fonctions
require_once '../backend/config/db.php';
require_once '../backend/functions.php';

// Récupérer les informations de l'utilisateur
$currentUser = getCurrentUser();
if (!$currentUser) {
    // Si l'utilisateur n'existe pas dans la base de données
    session_destroy();
    header('Location: ../login.php?error=Une erreur est survenue. Veuillez vous reconnecter.');
    exit;
}

// Récupérer le nombre d'articles dans le panier
$cartItemCount = getCartItemCount($_SESSION['user_id']);
$cartCount = $cartItemCount;

// Extraire le prénom et le nom
$nameParts = explode(' ', $currentUser['fullname'], 2);
$firstName = $nameParts[0];
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';

// Déterminer depuis quand l'utilisateur est client
$registrationDate = new DateTime($currentUser['created_at']);
$now = new DateTime();
$interval = $registrationDate->diff($now);
$clientSince = $registrationDate->format('F Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - KmerHosting</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <!-- Tailwind CSS -->
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
                        },
                        primary: {
                            DEFAULT: '#0e7490',
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        dark: {
                            DEFAULT: '#1e293b',
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #10b981;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #10b981;
        }
    </style>
</head>
<body class="bg-dark-900 text-white">
    <!-- Header/Navbar -->
    <header class="bg-dark-800 border-b border-dark-700">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <a href="../index.php" class="mr-6 flex items-center">
                    <img src="../assets/images/logo.png" alt="KmerHosting Logo" class="h-10">
                    <div class="ml-3 hidden md:block">
                        <span class="text-white text-xl font-bold">KmerHosting</span>
                        <div class="text-sm text-gray-400">
                            <span id="typed-slogan"></span>
                        </div>
                    </div>
                </a>
                <button id="sidebar-toggle" class="lg:hidden text-white p-2 rounded-md hover:bg-dark-700 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Panier avec badge -->
                <a href="cart.php" class="text-white hover:text-kmergreen-light relative p-2">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <?php if ($cartItemCount > 0): ?>
                    <span class="absolute top-0 right-0 bg-kmergreen text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        <?php echo $cartItemCount; ?>
                    </span>
                    <?php endif; ?>
                </a>
                
                <div class="relative group" id="profile-dropdown">
                    <button class="flex items-center space-x-2 text-white hover:text-kmergreen-light focus:outline-none">
                        <span class="hidden md:inline-block"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                        <?php if (!empty($currentUser['profile_image'])): ?>
                            <img src="../<?php echo htmlspecialchars($currentUser['profile_image']); ?>" alt="User" class="w-8 h-8 rounded-full border border-dark-600">
                        <?php else: ?>
                            <div class="w-8 h-8 rounded-full bg-dark-600 flex items-center justify-center">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="dropdown-icon"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-dark-800 border border-dark-700 rounded-md shadow-lg py-1 z-50 hidden transition-all duration-300 opacity-0 transform -translate-y-2" id="dropdown-menu">
                    <a href="../index.php" class="block px-4 py-2 text-sm text-white hover:bg-dark-700">
                            <i class="fas fa-home mr-2"></i> Accueil
                        </a>        
                    <a href="profil.php" class="block px-4 py-2 text-sm text-white hover:bg-dark-700">
                            <i class="fas fa-user mr-2"></i> Mon profil
                        </a>
                        <a href="parametres.php" class="block px-4 py-2 text-sm text-white hover:bg-dark-700">
                            <i class="fas fa-cog mr-2"></i> Paramètres
                        </a>
                        <div class="border-t border-dark-700 my-1"></div>
                        <a href="../backend/auth/logout.php" class="block px-4 py-2 text-sm text-white hover:bg-dark-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 z-50 w-64 bg-dark-800 border-r border-dark-700 pt-16 transition-transform duration-300 ease-in-out lg:static lg:w-64">
            <div class="h-full overflow-y-auto scrollbar-thin">
                <div class="px-4 py-4 border-b border-dark-700">
                    <div class="flex flex-col">
                        <span class="font-semibold text-kmergreen"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($currentUser['fullname'] ?? ''); ?></span>
                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                        <span class="text-sm text-gray-400"><?php echo htmlspecialchars($currentUser['tel'] ?? ''); ?></span>
                    </div>
                    <div class="flex mt-3 space-x-2">
                        <a href="profil.php" class="px-3 py-1 bg-kmergreen hover:bg-kmergreen-dark text-white text-xs rounded flex items-center">
                            <i class="fas fa-edit mr-1"></i> Modifier
                        </a>
                        <a href="../backend/auth/logout.php" class="px-3 py-1 bg-dark-600 hover:bg-dark-500 text-white text-xs rounded flex items-center">
                            <i class="fas fa-sign-out-alt mr-1"></i> Déconnexion
                        </a>
                    </div>
                </div>
                
                <nav class="mt-4">
                    <div class="px-4 mb-3">
                        <h3 class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Menu</h3>
                    </div>
                    <ul>
                        
                        <li>
                            <a href="dashboard.php" class="flex items-center px-4 py-2.5 text-white hover:bg-dark-700">
                                <i class="fas fa-tachometer-alt w-5 mr-3 text-gray-400"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>
                        <li>
                            <a href="cart.php" class="flex items-center px-4 py-2.5 text-white hover:bg-dark-700">
                                <i class="fas fa-shopping-cart w-5 mr-3 text-gray-400"></i>
                                <span>Mon Panier</span>
                                <?php if ($cartCount > 0): ?>
                                <span class="ml-auto bg-kmergreen text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    <?php echo $cartCount; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="services.php" class="flex items-center px-4 py-2.5 text-white hover:bg-dark-700">
                                <i class="fas fa-server w-5 mr-3 text-gray-400"></i>
                                <span>Mes Services</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8 pt-4 pb-8 overflow-x-hidden">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-white mb-2">Mon Profil</h1>
                    <p class="text-gray-400">Gérez vos informations personnelles et préférences de compte.</p>
                </div>

                <!-- Profile Information -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Left Column - Profile Picture and Basic Info -->
                    <div class="lg:col-span-1">
                        <div class="bg-dark-800 rounded-lg border border-dark-700 p-6">
                            <div class="flex flex-col items-center mb-6">
                                <div class="relative mb-4">
                                    <?php if (!empty($currentUser['profile_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($currentUser['profile_image']); ?>" alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-dark-600 object-cover">
                                    <?php else: ?>
                                        <div class="w-32 h-32 rounded-full border-4 border-dark-600 bg-dark-700 flex items-center justify-center">
                                            <i class="fas fa-user text-4xl text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <label for="profile-image-upload" class="absolute bottom-0 right-0 bg-kmergreen hover:bg-kmergreen-dark text-white p-2 rounded-full cursor-pointer">
                                        <i class="fas fa-camera"></i>
                                        <input type="file" id="profile-image-upload" class="hidden" accept="image/*">
                                    </label>
                                </div>
                                <h2 class="text-xl font-bold text-white"><?php echo htmlspecialchars($currentUser['fullname']); ?></h2>
                                <p class="text-gray-400">Client depuis <?php echo $clientSince; ?></p>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-400 mb-1">Email</h3>
                                    <p class="text-white"><?php echo htmlspecialchars($currentUser['email']); ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-400 mb-1">Téléphone</h3>
                                    <p class="text-white"><?php echo htmlspecialchars($currentUser['tel']); ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-400 mb-1">Entreprise</h3>
                                    <p class="text-white"><?php echo !empty($currentUser['company']) ? htmlspecialchars($currentUser['company']) : 'Non spécifié'; ?></p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-400 mb-1">Pays</h3>
                                    <p class="text-white"><?php echo !empty($currentUser['country']) ? htmlspecialchars($currentUser['country']) : 'Non spécifié'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Profile Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-dark-800 rounded-lg border border-dark-700 p-6">
                            <h2 class="text-lg font-semibold text-white mb-6">Informations personnelles</h2>
                            
                            <form id="profile-form">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label for="firstName" class="block text-sm font-medium text-gray-400 mb-1">Prénom</label>
                                        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="lastName" class="block text-sm font-medium text-gray-400 mb-1">Nom</label>
                                        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-400 mb-1">Téléphone</label>
                                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['tel']); ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="company" class="block text-sm font-medium text-gray-400 mb-1">Entreprise</label>
                                        <input type="text" id="company" name="company" value="<?php echo !empty($currentUser['company']) ? htmlspecialchars($currentUser['company']) : ''; ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="website" class="block text-sm font-medium text-gray-400 mb-1">Site web</label>
                                        <input type="url" id="website" name="website" value="<?php echo !empty($currentUser['website']) ? htmlspecialchars($currentUser['website']) : ''; ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                </div>
                                
                                <h3 class="text-md font-semibold text-white mb-4">Adresse</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div class="md:col-span-2">
                                        <label for="address" class="block text-sm font-medium text-gray-400 mb-1">Adresse</label>
                                        <input type="text" id="address" name="address" value="<?php echo !empty($currentUser['address']) ? htmlspecialchars($currentUser['address']) : ''; ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-400 mb-1">Ville</label>
                                        <input type="text" id="city" name="city" value="<?php echo !empty($currentUser['city']) ? htmlspecialchars($currentUser['city']) : ''; ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="region" class="block text-sm font-medium text-gray-400 mb-1">Région</label>
                                        <input type="text" id="region" name="region" value="<?php echo !empty($currentUser['region']) ? htmlspecialchars($currentUser['region']) : ''; ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="postalCode" class="block text-sm font-medium text-gray-400 mb-1">Code postal</label>
                                        <input type="text" id="postalCode" name="postalCode" value="<?php echo !empty($currentUser['postal_code']) ? htmlspecialchars($currentUser['postal_code']) : ''; ?>" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                    </div>
                                    <div>
                                        <label for="country" class="block text-sm font-medium text-gray-400 mb-1">Pays</label>
                                        <select id="country" name="country" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                            <option value="">Sélectionnez un pays</option>
                                            <option value="Cameroun" <?php echo ($currentUser['country'] == 'Cameroun') ? 'selected' : ''; ?>>Cameroun</option>
                                            <option value="Sénégal" <?php echo ($currentUser['country'] == 'Sénégal') ? 'selected' : ''; ?>>Sénégal</option>
                                            <option value="Côte d'Ivoire" <?php echo ($currentUser['country'] == "Côte d'Ivoire") ? 'selected' : ''; ?>>Côte d'Ivoire</option>
                                            <option value="Bénin" <?php echo ($currentUser['country'] == 'Bénin') ? 'selected' : ''; ?>>Bénin</option>
                                            <option value="Togo" <?php echo ($currentUser['country'] == 'Togo') ? 'selected' : ''; ?>>Togo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="button" id="cancel-profile-btn" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-md transition-colors mr-3">
                                        Annuler
                                    </button>
                                    <button type="submit" id="save-profile-btn" class="px-4 py-2 bg-kmergreen hover:bg-kmergreen-dark text-white rounded-md transition-colors">
                                        Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-white mb-6">Sécurité</h2>
                    
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-white mb-4">Changer le mot de passe</h3>
                        <form id="password-form" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="currentPassword" class="block text-sm font-medium text-gray-400 mb-1">Mot de passe actuel</label>
                                <input type="password" id="currentPassword" name="currentPassword" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                            </div>
                            <div>
                                <label for="newPassword" class="block text-sm font-medium text-gray-400 mb-1">Nouveau mot de passe</label>
                                <input type="password" id="newPassword" name="newPassword" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                            </div>
                            <div>
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-400 mb-1">Confirmer le mot de passe</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="w-full bg-dark-900 border border-dark-700 rounded-md px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-kmergreen">
                            </div>
                            <div class="md:col-span-3 flex justify-end">
                                <button type="submit" id="update-password-btn" class="px-4 py-2 bg-kmergreen hover:bg-kmergreen-dark text-white rounded-md transition-colors">
                                    Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="border-t border-dark-700 pt-6">
                        <h3 class="text-md font-semibold text-white mb-4">Authentification à deux facteurs</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-300 mb-1">Protégez votre compte avec l'authentification à deux facteurs</p>
                                <p class="text-gray-400 text-sm">L'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte.</p>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                <input type="checkbox" name="toggle" id="toggle-2fa" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                                <label for="toggle-2fa" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 p-6">
                    <h2 class="text-lg font-semibold text-white mb-6">Préférences</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-300 mb-1">Notifications par email</p>
                                <p class="text-gray-400 text-sm">Recevoir des emails concernant vos services et promotions</p>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                <input type="checkbox" name="toggle" id="toggle-email" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked />
                                <label for="toggle-email" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-300 mb-1">Notifications de renouvellement</p>
                                <p class="text-gray-400 text-sm">Recevoir des rappels avant l'expiration de vos services</p>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                <input type="checkbox" name="toggle" id="toggle-renewal" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" checked />
                                <label for="toggle-renewal" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-300 mb-1">Newsletter</p>
                                <p class="text-gray-400 text-sm">Recevoir des informations sur les nouveaux produits et offres</p>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                <input type="checkbox" name="toggle" id="toggle-newsletter" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                                <label for="toggle-newsletter" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-300 mb-1">Langue préférée</p>
                                <p class="text-gray-400 text-sm">Choisissez la langue d'affichage de votre interface</p>
                            </div>
                            <select class="bg-dark-900 border border-dark-700 rounded-md px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-kmergreen">
                                <option value="fr" selected>Français</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="button" id="save-preferences-btn" class="px-4 py-2 bg-kmergreen hover:bg-kmergreen-dark text-white rounded-md transition-colors">
                            Enregistrer les préférences
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-4 right-4 bg-dark-800 border border-dark-700 rounded-lg shadow-lg p-4 transform translate-y-full opacity-0 transition-all duration-300 z-50 hidden">
        <div class="flex items-center">
            <div id="toast-icon" class="mr-3 text-kmergreen">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p id="toast-message" class="text-white"></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                }
            });

            // Profile dropdown animation
            const profileDropdown = document.getElementById('profile-dropdown');
            const dropdownMenu = document.getElementById('dropdown-menu');
            const dropdownIcon = document.getElementById('dropdown-icon');
            
            if (profileDropdown) {
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                    
                    // If menu is now visible
                    if (!dropdownMenu.classList.contains('hidden')) {
                        // Show animation
                        setTimeout(() => {
                            dropdownMenu.classList.remove('opacity-0', '-translate-y-2');
                            dropdownIcon.classList.add('rotate-180');
                        }, 10);
                    } else {
                        // Hide animation
                        dropdownMenu.classList.add('opacity-0', '-translate-y-2');
                        dropdownIcon.classList.remove('rotate-180');
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!profileDropdown.contains(e.target)) {
                        dropdownMenu.classList.add('hidden', 'opacity-0', '-translate-y-2');
                        dropdownIcon.classList.remove('rotate-180');
                    }
                });
            }

            // Initialisation de Typed.js
            new Typed('#typed-slogan', {
                strings: [
                    'Votre hébergeur web fiable',
                    'Performance et sécurité garanties',
                    'Support technique 24/7',
                    'Solutions d\'hébergement sur mesure'
                ],
                typeSpeed: 50,
                backSpeed: 30,
                backDelay: 2000,
                loop: true,
                showCursor: true,
                cursorChar: '|'
            });

            // Gestion du formulaire de profil
            const profileForm = document.getElementById('profile-form');
            const saveProfileBtn = document.getElementById('save-profile-btn');
            const cancelProfileBtn = document.getElementById('cancel-profile-btn');

            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(profileForm);
                    
                    fetch('../backend/profile/update_profile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast('Une erreur est survenue lors de la mise à jour du profil.', 'error');
                        console.error('Error:', error);
                    });
                });
            }

            if (cancelProfileBtn) {
                cancelProfileBtn.addEventListener('click', function() {
                    window.location.reload();
                });
            }

            // Gestion du formulaire de mot de passe
            const passwordForm = document.getElementById('password-form');
            const updatePasswordBtn = document.getElementById('update-password-btn');

            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(passwordForm);
                    
                    fetch('../backend/profile/update_password.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            passwordForm.reset();
                        } else {
                            showToast(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showToast('Une erreur est survenue lors de la mise à jour du mot de passe.', 'error');
                        console.error('Error:', error);
                    });
                });
            }

            // Gestion de l'upload d'image de profil
            const profileImageUpload = document.getElementById('profile-image-upload');

            if (profileImageUpload) {
                profileImageUpload.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        
                        // Vérifier le type de fichier
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            showToast('Le type de fichier n\'est pas autorisé. Seuls les formats JPEG, PNG, GIF et WEBP sont acceptés.', 'error');
                            return;
                        }
                        
                        // Vérifier la taille du fichier (max 2MB)
                        const maxFileSize = 2 * 1024 * 1024; // 2MB en octets
                        if (file.size > maxFileSize) {
                            showToast('Le fichier est trop volumineux. La taille maximale autorisée est de 2MB.', 'error');
                            return;
                        }
                        
                        // Créer un FormData pour l'upload
                        const formData = new FormData();
                        formData.append('profileImage', file);
                        
                        // Envoyer la requête
                        fetch('../backend/profile/upload_profile_image.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast(data.message, 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                showToast(data.message, 'error');
                            }
                        })
                        .catch(error => {
                            showToast('Une erreur est survenue lors de l\'upload de l\'image.', 'error');
                            console.error('Error:', error);
                        });
                    }
                });
            }

            // Gestion des préférences
            const savePreferencesBtn = document.getElementById('save-preferences-btn');

            if (savePreferencesBtn) {
                savePreferencesBtn.addEventListener('click', function() {
                    showToast('Vos préférences ont été enregistrées avec succès.', 'success');
                });
            }

            // Fonction pour afficher un toast
            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast');
                const toastMessage = document.getElementById('toast-message');
                const toastIcon = document.getElementById('toast-icon');
                
                toastMessage.textContent = message;
                
                if (type === 'success') {
                    toastIcon.innerHTML = '<i class="fas fa-check-circle text-xl"></i>';
                    toastIcon.className = 'mr-3 text-kmergreen';
                } else if (type === 'error') {
                    toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-xl"></i>';
                    toastIcon.className = 'mr-3 text-red-500';
                } else if (type === 'warning') {
                    toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-xl"></i>';
                    toastIcon.className = 'mr-3 text-yellow-500';
                } else if (type === 'info') {
                    toastIcon.innerHTML = '<i class="fas fa-info-circle text-xl"></i>';
                    toastIcon.className = 'mr-3 text-blue-500';
                }
                
                toast.classList.remove('hidden');
                
                // Animation d'entrée
                setTimeout(() => {
                    toast.classList.remove('translate-y-full', 'opacity-0');
                }, 10);
                
                // Masquer après 5 secondes
                setTimeout(() => {
                    toast.classList.add('translate-y-full', 'opacity-0');
                    
                    // Cacher complètement après l'animation
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>
