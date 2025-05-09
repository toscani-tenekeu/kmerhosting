<?php
// Démarrer la session
session_start();

// Inclure les fonctions
require_once '../backend/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Stocker l'URL actuelle pour redirection après connexion
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php?error=Vous devez être connecté pour accéder à cette page.');
    exit;
}

// Récupérer les informations de l'utilisateur
$currentUser = getCurrentUser();
if (!$currentUser) {
    // Si l'utilisateur n'existe pas dans la base de données
    session_destroy();
    header('Location: ../login.php?error=Une erreur est survenue. Veuillez vous reconnecter.');
    exit;
}

// Récupérer les articles du panier et le nombre d'articles
$user_id = $_SESSION['user_id'];
$cartItems = getUserCartItems($user_id);
$cartCount = getCartItemCount($user_id);

// Récupérer les services de l'utilisateur
$userServices = getUserServices($user_id);

// Récupérer le crédit de l'utilisateur
$userCredit = getUserCredit($user_id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Client - KmerHosting</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.png">
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Assurer que le menu est visible */
        #sidebar {
            background-color: #1e293b;
            color: white;
            z-index: 40;
        }
        
        /* Style pour le menu actif */
        .active-menu-item {
            background-color: #334155;
        }
        
        /* Scrollbar personnalisée */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #475569;
            border-radius: 20px;
        }

        #dropdown-menu {
            position: absolute;
            right: 0;
            margin-top: 0.5rem;
            width: 12rem;
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            z-index: 50;
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        #dropdown-menu.hidden {
            display: none;
            opacity: 0;
            transform: translateY(-0.5rem);
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
                    <?php if ($cartCount > 0): ?>
                    <span class="absolute top-0 right-0 bg-kmergreen text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        <?php echo $cartCount; ?>
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
                    <div class="absolute right-0 mt-2 w-48 bg-dark-800 border border-dark-700 rounded-md shadow-lg py-1 z-50 hidden group-hover:block" id="dropdown-menu">
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
                            <a href="dashboard.php" class="flex items-center px-4 py-2.5 text-white bg-dark-700">
                                <i class="fas fa-tachometer-alt w-5 mr-3 text-kmergreen"></i>
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
                <!-- Welcome Message -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-white mb-2">Bienvenue, <?php echo htmlspecialchars($currentUser['username']); ?>!</h1>
                    <p class="text-gray-400">Gérez vos services et factures depuis votre tableau de bord.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-dark-800 rounded-lg p-5 border border-dark-700 hover:border-kmergreen transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">Services Actifs</p>
                                <h3 class="text-3xl font-bold text-white mt-1"><?php echo count($userServices); ?></h3>
                            </div>
                            <div class="bg-dark-700/50 p-3 rounded-full">
                                <i class="fas fa-server text-kmergreen"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-dark-800 rounded-lg p-5 border border-dark-700 hover:border-kmergreen transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">Articles dans le panier</p>
                                <h3 class="text-3xl font-bold text-white mt-1"><?php echo $cartCount; ?></h3>
                            </div>
                            <div class="bg-dark-700/50 p-3 rounded-full">
                                <i class="fas fa-shopping-cart text-kmergreen"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-dark-800 rounded-lg p-5 border border-dark-700 hover:border-kmergreen transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm">Crédit Disponible</p>
                                <h3 class="text-3xl font-bold text-white mt-1"><?php echo number_format($userCredit, 0, ',', ' '); ?> FCFA</h3>
                            </div>
                            <div class="bg-dark-700/50 p-3 rounded-full">
                                <i class="fas fa-wallet text-kmergreen"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                    <div class="p-5 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-white">Votre Panier (<?php echo $cartCount; ?> articles)</h2>
                        <i class="fas fa-shopping-cart text-kmergreen"></i>
                    </div>
                    <div class="p-5">
                        <?php if (empty($cartItems)): ?>
                            <div class="text-center py-8">
                                <div class="text-gray-400 text-5xl mb-4">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <p class="text-gray-300 mb-4">Votre panier est vide</p>
                                <a href="../index.php#packages" class="inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                                    Découvrir nos offres
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-left text-gray-400 text-sm">
                                            <th class="pb-3 font-medium">Produit</th>
                                            <th class="pb-3 font-medium">Type</th>
                                            <th class="pb-3 font-medium">Prix</th>
                                            <th class="pb-3 font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartItems as $item): ?>
                                            <tr class="border-t border-dark-700 text-sm">
                                                <td class="py-4">
                                                    <div class="font-medium text-white"><?php echo htmlspecialchars($item['product_name'] ?? 'Produit inconnu'); ?></div>
                                                </td>
                                                <td class="py-4">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo htmlspecialchars($item['product_type'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 text-white"><?php echo number_format($item['price'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                                                <td class="py-4 text-right">
                                                    <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="p-2 bg-red-900/30 hover:bg-red-800/50 text-red-400 rounded-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- DNS Information -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                    <div class="p-5 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-white">Informations Serveurs</h2>
                        <i class="fas fa-network-wired text-kmergreen"></i>
                    </div>
                    <div class="p-5">
                        <p class="text-gray-300 mb-4">Utilisez ces serveurs pour configurer vos services :</p>
                        <div class="bg-dark-900 rounded-lg p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center mb-3 last:mb-0">
                                <span class="text-gray-400 font-medium min-w-[180px]">DNS 1 :</span>
                                <code class="bg-dark-950 px-3 py-1 rounded text-kmergreen mt-1 sm:mt-0">ns1.kmerhosting.site</code>
                                <button class="ml-2 text-gray-400 hover:text-white" onclick="copyToClipboard('panel.kmerhosting.site')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center">
                                <span class="text-gray-400 font-medium min-w-[180px]">DNS 2 :</span>
                                <code class="bg-dark-950 px-3 py-1 rounded text-kmergreen mt-1 sm:mt-0">ns2.kmerhosting.site</code>
                                <button class="ml-2 text-gray-400 hover:text-white" onclick="copyToClipboard('sql.kmerhosting.site')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                    } else {
                        dropdownIcon.classList.remove('rotate-180');
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                if (dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                    dropdownMenu.classList.add('hidden');
                    dropdownIcon.classList.remove('rotate-180');
                }
            });
        });

        // Initialize Typed.js
        document.addEventListener('DOMContentLoaded', function() {
            var typed = new Typed('#typed-slogan', {
                strings: ['Votre succès en ligne commence ici.', 'Hébergement web de qualité pour tous.'],
                typeSpeed: 50,
                backSpeed: 25,
                backDelay: 2000,
                startDelay: 1000,
                loop: true
            });
        });

        // Function to copy text to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copié!',
                        text: 'Le serveur de noms a été copié dans le presse-papiers.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(err => {
                    console.error('Erreur lors de la copie: ', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Une erreur est survenue lors de la copie du serveur de noms.',
                    });
                });
        }

        // Fonction pour supprimer un article du panier
        function removeFromCart(itemId) {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: "Voulez-vous supprimer cet article de votre panier?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#475569',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        background: '#1e293b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            // Afficher un indicateur de chargement
            Swal.fire({
                title: 'Suppression en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#1e293b',
                color: '#fff'
            });
            
            fetch('../backend/cart/remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `item_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Afficher un message de succès
                    Swal.fire({
                        title: 'Supprimé!',
                        text: 'L\'article a été supprimé de votre panier',
                        icon: 'success',
                        confirmButtonColor: '#10b981',
                        background: '#1e293b',
                        color: '#fff',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Erreur!',
                        text: data.message || 'Erreur lors de la suppression de l\'article',
                        icon: 'error',
                        confirmButtonColor: '#10b981',
                        background: '#1e293b',
                        color: '#fff'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Erreur!',
                    text: 'Une erreur est survenue lors de la suppression',
                    icon: 'error',
                    confirmButtonColor: '#10b981',
                    background: '#1e293b',
                    color: '#fff'
                });
            });
        }
    });
}
    </script>
</body>
</html>
