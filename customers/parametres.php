<?php
// Démarrer la session
session_start();

// Définir la page actuelle
$currentPage = 'parametres';

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
require_once '../backend/settings/get_settings.php';

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

// Récupérer les paramètres de l'utilisateur
$userSettings = getUserSettings($_SESSION['user_id']);

// Définir les langues disponibles
$languages = [
    'fr' => 'Français',
    'en' => 'English'
];

// Message de succès ou d'erreur
$message = '';
$messageType = '';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = 'Paramètres mis à jour avec succès.';
    $messageType = 'success';
} elseif (isset($_GET['error'])) {
    $message = 'Erreur lors de la mise à jour des paramètres: ' . htmlspecialchars($_GET['error']);
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - KmerHosting</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Copy the same Tailwind config from other pages
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-dark-900 text-white">
    <!-- Header/Navbar -->
    <header class="bg-dark-800 border-b border-dark-700">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <a href="../index.php" class="mr-6 flex items-center">
                    <img src="../assets/images/logo.png" alt="KmerHosting Logo" class="h-10">
                    <div class="ml-3">
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
                            <img src="<?php echo htmlspecialchars($currentUser['profile_image']); ?>" alt="User" class="w-8 h-8 rounded-full border border-dark-600">
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
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <a href="dashboard.php" class="flex items-center text-gray-400 hover:text-white mr-4">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour au tableau de bord
                        </a>
                        <h1 class="text-2xl font-bold">Paramètres</h1>
                    </div>
                </div>

                <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <form id="settings-form" action="../backend/settings/update_settings.php" method="POST">
                    <!-- Interface Settings -->
                    <div class="bg-dark-800 rounded-lg border border-dark-700 p-6 mb-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Interface</h2>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-300 mb-1">Thème sombre</p>
                                    <p class="text-gray-400 text-sm">Activer/désactiver le thème sombre</p>
                                </div>
                                <div class="relative inline-block w-12 align-middle">
                                    <input type="checkbox" id="toggle-theme" name="theme" value="dark" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:border-kmergreen" <?php echo $userSettings['theme'] === 'dark' ? 'checked' : ''; ?> />
                                    <label for="toggle-theme" class="toggle-label block h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-300 mb-1">Langue</p>
                                    <p class="text-gray-400 text-sm">Choisir la langue de l'interface</p>
                                </div>
                                <select name="language" class="bg-dark-900 border border-dark-700 rounded px-3 py-2 text-white">
                                    <?php foreach ($languages as $code => $name): ?>
                                    <option value="<?php echo $code; ?>" <?php echo $userSettings['language'] === $code ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Settings -->
                    <div class="bg-dark-800 rounded-lg border border-dark-700 p-6 mb-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Notifications</h2>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-300 mb-1">Notifications par email</p>
                                    <p class="text-gray-400 text-sm">Recevoir des notifications par email</p>
                                </div>
                                <div class="relative inline-block w-12 align-middle">
                                    <input type="checkbox" id="notifications-email" name="notifications_email" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:border-kmergreen" <?php echo $userSettings['notifications_email'] ? 'checked' : ''; ?> />
                                    <label for="notifications-email" class="toggle-label block h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-300 mb-1">Notifications par SMS</p>
                                    <p class="text-gray-400 text-sm">Recevoir des notifications par SMS</p>
                                </div>
                                <div class="relative inline-block w-12 align-middle">
                                    <input type="checkbox" id="notifications-sms" name="notifications_sms" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:border-kmergreen" <?php echo $userSettings['notifications_sms'] ? 'checked' : ''; ?> />
                                    <label for="notifications-sms" class="toggle-label block h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                </div>
                            </div>

                            <div class="border-t border-dark-700 pt-6">
                                <h3 class="text-md font-medium text-white mb-4">Types de notifications</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <p class="text-gray-300">Expiration des services</p>
                                        </div>
                                        <div class="relative inline-block w-12 mr-2 align-middle select-none">
                                            <input type="checkbox" id="notifications-expiry" name="notifications_expiry" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:border-kmergreen" <?php echo $userSettings['notifications_expiry'] ? 'checked' : ''; ?> />
                                            <label for="notifications-expiry" class="toggle-label block h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <p class="text-gray-300">Actualités et promotions</p>
                                        <div class="relative inline-block w-12 align-middle">
                                            <input type="checkbox" id="notifications-news" name="notifications_news" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:border-kmergreen" <?php echo $userSettings['notifications_news'] ? 'checked' : ''; ?> />
                                            <label for="notifications-news" class="toggle-label block h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="bg-dark-800 rounded-lg border border-dark-700 p-6 mb-6">
                        <h2 class="text-lg font-semibold text-white mb-6">Confidentialité</h2>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-300 mb-1">Afficher mes services</p>
                                    <p class="text-gray-400 text-sm">Permettre aux autres utilisateurs de voir vos services</p>
                                </div>
                                <div class="relative inline-block w-12 align-middle">
                                    <input type="checkbox" id="privacy-services" name="privacy_show_services" value="1" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:right-0 checked:border-kmergreen" <?php echo $userSettings['privacy_show_services'] ? 'checked' : ''; ?> />
                                    <label for="privacy-services" class="toggle-label block h-6 rounded-full bg-gray-700 cursor-pointer"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Account Section -->
                    <div class="bg-dark-800 rounded-lg border border-red-900 p-6 mb-6">
                        <h2 class="text-lg font-semibold text-red-500 mb-6">Supprimer mon compte</h2>
                        <div class="space-y-6">
                            <div>
                                <p class="text-gray-300 mb-3">Cette action est irréversible et entraînera les conséquences suivantes :</p>
                                <ul class="list-disc pl-5 text-gray-400 space-y-2 mb-4">
                                    <li>Toutes vos données personnelles seront définitivement supprimées</li>
                                    <li>Vos services actifs seront immédiatement résiliés</li>
                                    <li>Vous perdrez l'accès à tous vos domaines, hébergements et certificats SSL</li>
                                    <li>Votre crédit restant sera perdu</li>
                                    <li>Vous ne pourrez plus vous connecter avec ce compte</li>
                                </ul>
                                <div class="bg-red-900/20 p-4 rounded-lg mb-4">
                                    <p class="text-red-400 text-sm">Pour confirmer la suppression, veuillez saisir "Delete my account now" dans le champ ci-dessous.</p>
                                </div>
                                <div class="mb-4">
                                    <input type="text" id="delete-confirmation" placeholder="Delete my account now" class="w-full bg-dark-900 border border-dark-700 rounded px-3 py-2 text-white">
                                </div>
                                <button type="button" id="delete-account-btn" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 opacity-50 cursor-not-allowed">
                                    <i class="fas fa-trash-alt mr-2"></i> Supprimer définitivement mon compte
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-kmergreen hover:bg-kmergreen-dark text-white font-medium rounded-lg transition duration-300">
                            <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
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
                        dropdownIcon.classList.add('rotate-180');
                        setTimeout(() => {
                            dropdownMenu.classList.remove('opacity-0', '-translate-y-2');
                        }, 10);
                    } else {
                        dropdownIcon.classList.remove('rotate-180');
                        dropdownMenu.classList.add('opacity-0', '-translate-y-2');
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                if (dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                    dropdownMenu.classList.add('hidden', 'opacity-0', '-translate-y-2');
                    dropdownIcon.classList.remove('rotate-180');
                }
            });

            // Initialize Typed.js
            var typed = new Typed('#typed-slogan', {
                strings: ['Votre succès en ligne commence ici.', 'Hébergement web de qualité pour tous.'],
                typeSpeed: 50,
                backSpeed: 25,
                backDelay: 2000,
                startDelay: 1000,
                loop: true
            });

            // Form submission with AJAX
            const settingsForm = document.getElementById('settings-form');
            if (settingsForm) {
                settingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    // Add unchecked checkboxes with value 0
                    const checkboxes = ['theme', 'notifications_email', 'notifications_sms', 'notifications_expiry', 'notifications_news', 'privacy_show_services'];
                    checkboxes.forEach(name => {
                        if (!formData.has(name)) {
                            formData.append(name, '0');
                        }
                    });
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.href = 'parametres.php?success=1';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la mise à jour des paramètres.'
                        });
                    });
                });
            }

            // Style for toggle checkboxes
            document.querySelectorAll('.toggle-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    if (this.checked) {
                        label.classList.add('bg-kmergreen-dark');
                        label.classList.remove('bg-gray-700');
                    } else {
                        label.classList.remove('bg-kmergreen-dark');
                        label.classList.add('bg-gray-700');
                    }
                });
                
                // Initialize state
                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });

            // Delete account functionality
            const deleteConfirmationInput = document.getElementById('delete-confirmation');
            const deleteAccountBtn = document.getElementById('delete-account-btn');
            
            if (deleteConfirmationInput && deleteAccountBtn) {
                deleteConfirmationInput.addEventListener('input', function() {
                    if (this.value === 'Delete my account now') {
                        deleteAccountBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        deleteAccountBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                });
                
                deleteAccountBtn.addEventListener('click', function() {
                    if (deleteConfirmationInput.value !== 'Delete my account now') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Confirmation incorrecte',
                            text: 'Veuillez saisir exactement "Delete my account now" pour confirmer la suppression.'
                        });
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Êtes-vous absolument sûr ?',
                        html: `
                            <div class="text-left">
                                <p class="mb-3">Cette action est <strong class="text-red-500">irréversible</strong> et entraînera :</p>
                                <ul class="list-disc pl-5 text-left mb-3">
                                    <li>La suppression définitive de toutes vos données</li>
                                    <li>La résiliation immédiate de tous vos services</li>
                                    <li>La perte de tous vos domaines et hébergements</li>
                                </ul>
                                <p>Vous ne pourrez plus accéder à votre compte après cette action.</p>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Oui, supprimer mon compte',
                        cancelButtonText: 'Annuler',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Suppression en cours...',
                                html: 'Veuillez patienter pendant que nous supprimons votre compte.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Send delete request
                            fetch('../backend/profile/delete_account.php', {
                                method: 'POST'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Compte supprimé',
                                        text: 'Votre compte a été supprimé avec succès. Vous allez être redirigé vers la page d\'accueil.',
                                        showConfirmButton: false,
                                        timer: 3000
                                    }).then(() => {
                                        window.location.href = '../index.php';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur',
                                        text: data.message || 'Une erreur est survenue lors de la suppression de votre compte.'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Erreur:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Une erreur est survenue lors de la suppression de votre compte.'
                                });
                            });
                        }
                    });
                });
            }
        });
    </script>
    <style>
        /* Custom styles for toggle switches */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #10b981;
        }
        .toggle-label {
            transition: background-color 0.3s ease;
        }
    </style>
</body>
</html>
