<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration de la base de données et les fonctions
require_once 'backend/config/db.php';
require_once 'backend/functions.php';

// Récupérer les données depuis la base de données
$wordpressPackages = getWordpressPackages();
$sslPackages = getSslPackages();
$hostingPackages = getHostingPackages();

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$cartItemCount = $isLoggedIn ? getCartItemCount($_SESSION['user_id']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KmerHosting.site - Hébergement Web Fiable</title>
    <meta name="description"
        content="KmerHosting.site offre des solutions d'hébergement web fiables et abordables avec un support 24/7, propulsées par LiteSpeed pour des performances optimales.">

    <!-- Favicon -->
    <link rel="icon" href="./assets/images/favicon.png">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        kmerblue: {
                            DEFAULT: '#004a6e', // Bleu foncé du logo
                            light: '#005d8a',
                            dark: '#003a57'
                        },
                        kmergreen: {
                            DEFAULT: '#10b981', // Vert du logo
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

    <!-- Typed.js pour l'animation de texte -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #004a6e 80%);
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .typed-cursor {
            color: white;
            font-size: 1.5rem;
        }

        @media (min-width: 768px) {
            .typed-cursor {
                font-size: 2rem;
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(-20px) translateX(10px);
            }

            100% {
                transform: translateY(0) translateX(0);
            }
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            pointer-events: none;
        }

        .tech-icon {
            transition: all 0.3s ease;
        }

        .tech-icon:hover {
            transform: translateY(-5px);
        }

        .feature-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center">
                    <!-- Logo grand dans la nav -->
                    <img src="assets/images/logo.png" alt="KmerHosting.site" class="h-24">
                </a>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-kmerblue focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="#technologies" class="text-kmerblue hover:text-kmergreen font-medium">Technologies</a>
                    <a href="#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="domaines.php" class="text-kmerblue hover:text-kmergreen font-medium">Nom de Domaines</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                </nav>

                <!-- CTA Button or User Profile -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Panier avec badge -->
                        <a href="customers/cart.php" class="text-kmerblue hover:text-kmergreen relative p-2">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <?php if ($cartItemCount > 0): ?>
                            <span class="cart-count-badge absolute top-0 right-0 bg-kmergreen text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                <?php echo $cartItemCount; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="customers/dashboard.php" class="flex items-center space-x-2 text-kmerblue hover:text-kmergreen">
                            <span class="font-medium">Mon Compte</span>
                            <i class="fas fa-user-circle text-xl"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php"
                        class="hidden md:inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        Se connecter / S'inscrire
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden mt-4 pb-2">
                <div class="flex flex-col space-y-3">
                    <a href="#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="#technologies" class="text-kmerblue hover:text-kmergreen font-medium">Technologies</a>
                    <a href="#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="domaines.php" class="text-kmerblue hover:text-kmergreen font-medium">Nom de Domaines</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="customers/dashboard.php"
                            class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-center">
                            Mon Compte
                        </a>
                    <?php else: ?>
                        <a href="login.php"
                            class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-center">
                            Se connecter / S'inscrire
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
</body>
</html>
