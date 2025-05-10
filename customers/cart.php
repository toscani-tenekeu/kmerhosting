<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

// Inclure les fonctions
require_once '../backend/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php?error=Vous devez être connecté pour accéder à cette page.');
    exit;
}

// Récupérer les informations de l'utilisateur
$currentUser = getCurrentUser();
if (!$currentUser) {
    session_destroy();
    header('Location: ../login.php?error=Une erreur est survenue. Veuillez vous reconnecter.');
    exit;
}

// Récupérer les articles du panier et calculer le total
$user_id = $_SESSION['user_id'];
$cartItems = getUserCartItems($user_id);
$cartCount = getCartItemCount($user_id);

// Calculer le total du panier
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += floatval($item['price']) * intval($item['quantity']);
}

// Récupérer le crédit disponible de l'utilisateur
$userCredit = getUserCredit($user_id);

// Pour le menu actif
$currentPage = 'cart';

// Traiter le paiement si le formulaire est soumis
$paymentMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Vérifier si le panier n'est pas vide
    if (empty($cartItems)) {
        $paymentMessage = '<div class="bg-red-900/20 border border-red-700/30 rounded-lg p-3 mb-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                <span class="text-red-400">Votre panier est vide. Impossible de procéder au paiement.</span>
            </div>
        </div>';
    } else {
        // Vérifier si l'utilisateur a suffisamment de crédit
        if ($userCredit >= $totalAmount) {
            // Déduire le montant du crédit de l'utilisateur
            $newCredit = $userCredit - $totalAmount;
            if (updateUserCredit($user_id, $newCredit)) {
                // Créer une commande à partir du panier
                $order_id = createOrderFromCart($user_id, 'credit');
                
                if ($order_id) {
                    // Créer les services à partir de la commande
                    try {
                        $services_created = createServicesFromOrder($order_id);
                        if (!$services_created) {
                            // Log the error but still proceed with the checkout
                            error_log("Error creating services for order ID: " . $order_id);
                        }
                    } catch (Exception $e) {
                        // Log the exception but still proceed with the checkout
                        error_log("Exception creating services for order ID: " . $order_id . ". Error: " . $e->getMessage());
                    }
                    
                    // Vider le panier
                    clearCart($user_id);
                    
                    // Rediriger vers la page de confirmation
                    header('Location: order-confirmation.php?order_id=' . $order_id);
                    exit;
                } else {
                    $paymentMessage = '<div class="bg-red-900/20 border border-red-700/30 rounded-lg p-3 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <span class="text-red-400">Une erreur est survenue lors de la création de la commande. Veuillez réessayer.</span>
                        </div>
                    </div>';
                }
            } else {
                $paymentMessage = '<div class="bg-red-900/20 border border-red-700/30 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <span class="text-red-400">Une erreur est survenue lors de la mise à jour de votre crédit. Veuillez réessayer.</span>
                    </div>
                </div>';
            }
        } else {
            $paymentMessage = '<div class="bg-red-900/20 border border-red-700/30 rounded-lg p-3 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    <span class="text-red-400">Crédit insuffisant. Veuillez recharger votre compte ou choisir un autre mode de paiement.</span>
                </div>
            </div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - KmerHosting</title>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-dark-900 text-white">
    <!-- Header/Navbar -->
    <header class="bg-dark-800 border-b border-dark-700">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center">
                <a href="../index.php" class="mr-6">
                    <img src="../assets/images/logo.png" alt="KmerHosting Logo" class="h-10">
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
                            <a href="cart.php" class="flex items-center px-4 py-2.5 text-white bg-dark-700">
                                <i class="fas fa-shopping-cart w-5 mr-3 text-kmergreen"></i>
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
                    <h1 class="text-2xl font-bold text-white mb-2">Mon Panier</h1>
                    <p class="text-gray-400">Gérez les articles de votre panier et procédez au paiement.</p>
                </div>

                <!-- Afficher le message de paiement s'il existe -->
                <?php echo $paymentMessage; ?>

                <!-- Crédit disponible -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                    <div class="p-5 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-white">Crédit disponible</h2>
                        <span class="text-kmergreen font-bold text-xl"><?php echo number_format($userCredit, 0, ',', ' '); ?> FCFA</span>
                    </div>
                    <div class="p-5">
                        <p class="text-gray-400 mb-3">Vous pouvez utiliser votre crédit pour payer vos commandes ou recharger votre compte.</p>
                        <button id="recharge-btn" class="inline-block bg-dark-700 hover:bg-dark-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                            <i class="fas fa-plus-circle mr-2"></i> Recharger mon compte
                        </button>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                    <div class="p-5 border-b border-dark-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-white">Articles dans votre panier</h2>
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
                                            <th class="pb-3 font-medium">Prix</th>
                                            <th class="pb-3 font-medium">Quantité</th>
                                            <th class="pb-3 font-medium">Total</th>
                                            <th class="pb-3 font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartItems as $item): 
                                            $itemTotal = $item['price'] * $item['quantity'];
                                        ?>
                                            <tr class="border-t border-dark-700 text-sm">
                                                <td class="py-4">
                                                    <div class="font-medium text-white"><?php echo htmlspecialchars($item['product_name'] ?? 'Produit inconnu'); ?></div>
                                                    <div class="text-gray-400 text-xs mt-1"><?php echo ucfirst(htmlspecialchars($item['product_type'])); ?></div>
                                                </td>
                                                <td class="py-4 text-white"><?php echo number_format($item['price'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                                                <td class="py-4">
                                                    <div class="flex items-center">
                                                        <button class="quantity-btn minus bg-dark-700 hover:bg-dark-600 text-white w-8 h-8 rounded-l flex items-center justify-center" data-id="<?php echo $item['id']; ?>">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="quantity-input bg-dark-900 border-y border-dark-600 text-white w-12 h-8 text-center" value="<?php echo $item['quantity']; ?>" min="1" max="10" data-id="<?php echo $item['id']; ?>">
                                                        <button class="quantity-btn plus bg-dark-700 hover:bg-dark-600 text-white w-8 h-8 rounded-r flex items-center justify-center" data-id="<?php echo $item['id']; ?>">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="py-4 text-white"><?php echo number_format($itemTotal ?? 0, 0, ',', ' '); ?> FCFA</td>
                                                <td class="py-4">
                                                    <button class="remove-item p-2 bg-red-900/30 hover:bg-red-800/50 text-red-400 rounded-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50" data-id="<?php echo $item['id']; ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 flex flex-col md:flex-row justify-between items-start md:items-center">
                                <div>
                                    <a href="../index.php#packages" class="text-kmergreen hover:text-kmergreen-light">
                                        <i class="fas fa-arrow-left mr-2"></i> Continuer mes achats
                                    </a>
                                </div>
                                <div class="mt-4 md:mt-0 bg-dark-700 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-300">Total:</span>
                                        <span class="text-white font-bold"><?php echo number_format($totalAmount, 0, ',', ' '); ?> FCFA</span>
                                    </div>
                                    
                                    <button id="confirm-payment-btn" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 mt-2">
                                        Payer avec mon crédit
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Recharge Modal -->
    <div id="recharge-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-dark-800 rounded-lg shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <div class="p-5 border-b border-dark-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">Recharger mon compte</h3>
                <button id="close-modal" class="text-gray-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-5">
                <div class="mb-4">
                    <p class="text-gray-300 mb-3">Choisissez votre méthode de paiement préférée:</p>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-dark-700 p-3 rounded-lg border border-dark-600 hover:border-kmergreen cursor-pointer payment-method active" data-method="orange">
                            <img src="../assets/images/orange_money_logo.webp" alt="Orange Money" class="h-12 mx-auto mb-2">
                            <p class="text-center text-sm text-white">Orange Money</p>
                        </div>
                        <div class="bg-dark-700 p-3 rounded-lg border border-dark-600 hover:border-kmergreen cursor-pointer payment-method" data-method="mtn">
                            <img src="../assets/images/mtn_momo_logo.webp" alt="MTN Mobile Money" class="h-12 mx-auto mb-2">
                            <p class="text-center text-sm text-white">MTN Mobile Money</p>
                        </div>
                    </div>
                </div>
                
                <form id="recharge-form" class="space-y-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Numéro de téléphone (9 chiffres)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-gray-300 bg-dark-700 border border-r-0 border-dark-600 rounded-l-md">
                                <span id="phone-prefix">+237</span>
                            </span>
                            <input type="text" id="phone" name="phone" class="bg-dark-900 border border-dark-600 text-white rounded-r-md block w-full p-2.5 focus:outline-none focus:ring-1 focus:ring-kmergreen focus:border-kmergreen" placeholder="6XXXXXXXX" maxlength="9" required>
                        </div>
                        <p id="phone-error" class="mt-1 text-sm text-red-400 hidden">Veuillez entrer un numéro à 9 chiffres.</p>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="bg-dark-900 border border-dark-600 text-white rounded-md block w-full p-2.5 focus:outline-none focus:ring-1 focus:ring-kmergreen focus:border-kmergreen" placeholder="votre@email.com" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                    </div>
                    
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-300 mb-1">Montant (FCFA)</label>
                        <input type="number" id="amount" name="amount" class="bg-dark-900 border border-dark-600 text-white rounded-md block w-full p-2.5 focus:outline-none focus:ring-1 focus:ring-kmergreen focus:border-kmergreen" placeholder="Minimum 1 000 FCFA" min="1000" step="100" value="1000" required>
                        <p class="mt-1 text-sm text-gray-400">Montant minimum: 1 000 FCFA</p>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" id="generate-payment-btn" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-3 px-4 rounded-lg transition duration-300">
                            Démarrer le processus de paiement
                        </button>
                    </div>
                </form>
                
                <!-- Payment Link Section (Hidden initially) -->
                <div id="payment-link-section" class="hidden mt-4 space-y-4">
                    <div class="bg-dark-700 p-4 rounded-lg">
                        <p class="text-gray-300 mb-2">Votre lien de paiement a été généré. Cliquez sur le bouton ci-dessous pour procéder au paiement:</p>
                        <a id="payment-link-btn" href="#" target="_blank" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                            <i class="fas fa-external-link-alt mr-2"></i> Procéder au paiement
                        </a>
                        <p class="text-sm text-gray-400 mt-2">Une nouvelle fenêtre va s'ouvrir pour finaliser votre paiement.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de paiement -->
    <div id="confirm-payment-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-dark-800 rounded-lg shadow-lg max-w-md w-full mx-4 overflow-hidden">
            <div class="p-5 border-b border-dark-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">Confirmation de paiement</h3>
                <button id="close-payment-modal" class="text-gray-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-5">
                <div class="mb-4">
                    <p class="text-gray-300 mb-3">Vous êtes sur le point de payer les services suivants:</p>
                    <div class="bg-dark-700 p-3 rounded-lg border border-dark-600 mb-4">
                        <div id="services-summary" class="space-y-2">
                            <!-- Les services seront ajoutés ici dynamiquement -->
                        </div>
                        
                        <div class="border-t border-dark-600 mt-3 pt-3 flex justify-between">
                            <span class="font-medium text-white">Total:</span>
                            <span id="total-amount" class="font-bold text-kmergreen">0 FCFA</span>
                        </div>
                    </div>
                    <!-- Message d'avertissement -->
                    <div class="mt-4 bg-yellow-900/30 border border-yellow-700/50 rounded-lg p-3">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                <span class="text-yellow-400 text-sm">Ne quittez pas cette page et n'effectuez aucune autre action pendant le processus de paiement. Celui-ci peut prendre entre 20 et 45 secondes. Toute interruption pourrait entraîner des problèmes avec votre commande.</span>
                            </div>
                        </div>  
                    
                </div>
                
                
                <form id="payment-confirmation-form" method="post" action="" class="space-y-4">
                    <div>
                        <label for="confirmation-code" class="block text-sm font-medium text-gray-300 mb-1">Pour confirmer, veuillez saisir "kmerhosting"</label>
                        <input type="text" id="confirmation-code" name="confirmation_code" class="bg-dark-900 border border-dark-600 text-white rounded-md block w-full p-2.5 focus:outline-none focus:ring-1 focus:ring-kmergreen focus:border-kmergreen" placeholder="kmerhosting" required>
                        <p id="confirmation-error" class="mt-1 text-sm text-red-400 hidden">Veuillez saisir exactement "kmerhosting" pour confirmer.</p>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" name="checkout" id="confirm-checkout-btn" class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-3 px-4 rounded-lg transition duration-300">
                            Confirmer le paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

            // Gestion du panier
            const quantityBtns = document.querySelectorAll('.quantity-btn');
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const removeItemBtns = document.querySelectorAll('.remove-item');

            // Mise à jour de la quantité
            if (quantityBtns.length > 0) {
                quantityBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const input = document.querySelector(`.quantity-input[data-id="${id}"]`);
                        let value = parseInt(input.value);

                        if (this.classList.contains('plus')) {
                            value = Math.min(value + 1, 10);
                        } else {
                            value = Math.max(value - 1, 1);
                        }

                        input.value = value;
                        updateCartItemQuantity(id, value);
                    });
                });
            }

            // Mise à jour manuelle de la quantité
            if (quantityInputs.length > 0) {
                quantityInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const id = this.getAttribute('data-id');
                        let value = parseInt(this.value);

                        // Limiter entre 1 et 10
                        value = Math.max(1, Math.min(value, 10));
                        this.value = value;

                        updateCartItemQuantity(id, value);
                    });
                });
            }

            // Supprimer un article
            if (removeItemBtns.length > 0) {
                removeItemBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        removeCartItem(id);
                    });
                });
            }

            // Fonction pour mettre à jour la quantité
            function updateCartItemQuantity(id, quantity) {
                fetch('../backend/auth/cart/update_quantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `cart_item_id=${id}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour mettre à jour les totaux
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

       
            // Supprimer un article du panier
            function removeCartItem(id) {
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
                body: `item_id=${id}`
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

            // Recharge Modal
            const rechargeBtn = document.getElementById('recharge-btn');
            const rechargeModal = document.getElementById('recharge-modal');
            const closeModal = document.getElementById('close-modal');
            const paymentMethods = document.querySelectorAll('.payment-method');
            const phoneInput = document.getElementById('phone');
            const phoneError = document.getElementById('phone-error');
            const rechargeForm = document.getElementById('recharge-form');
            const paymentLinkSection = document.getElementById('payment-link-section');
            const paymentLinkBtn = document.getElementById('payment-link-btn');
            const generatePaymentBtn = document.getElementById('generate-payment-btn');
           
            // Open modal
            if (rechargeBtn) {
                rechargeBtn.addEventListener('click', function() {
                    rechargeModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                });
            }

            // Close modal
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    rechargeModal.classList.add('hidden');
                    document.body.style.overflow = ''; // Enable scrolling
                    // Reset form
                    rechargeForm.reset();
                    paymentLinkSection.classList.add('hidden');
                    rechargeForm.classList.remove('hidden');
                    generatePaymentBtn.disabled = false;
                    generatePaymentBtn.innerHTML = 'Démarrer le processus de paiement';
                });
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === rechargeModal) {
                    rechargeModal.classList.add('hidden');
                    document.body.style.overflow = ''; // Enable scrolling
                    // Reset form
                    rechargeForm.reset();
                    paymentLinkSection.classList.add('hidden');
                    rechargeForm.classList.remove('hidden');
                    generatePaymentBtn.disabled = false;
                    generatePaymentBtn.innerHTML = 'Démarrer le processus de paiement';
                }
            });

            // Toggle payment method
            if (paymentMethods.length > 0) {
                paymentMethods.forEach(method => {
                    method.addEventListener('click', function() {
                        // Remove active class from all methods
                        paymentMethods.forEach(m => m.classList.remove('active', 'border-kmergreen'));
                        // Add active class to clicked method
                        this.classList.add('active', 'border-kmergreen');
                        
                        // Update phone prefix based on selected method
                        const phonePrefix = document.getElementById('phone-prefix');
                        if (this.getAttribute('data-method') === 'orange') {
                            phonePrefix.textContent = '+237';
                        } else {
                            phonePrefix.textContent = '+237';
                        }
                    });
                });
            }

            // Validate phone number
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    // Remove non-numeric characters
                    this.value = this.value.replace(/\D/g, '');
                    
                    // Check if valid (9 digits)
                    if (this.value.length === 9) {
                        phoneError.classList.add('hidden');
                    } else {
                        phoneError.classList.remove('hidden');
                    }
                });
            }

            // Handle form submission
            if (rechargeForm) {
                rechargeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Validate phone number
                    if (phoneInput.value.length !== 9) {
                        phoneError.classList.remove('hidden');
                        return;
                    }
                    
                    // Disable button and show loading state
                    generatePaymentBtn.disabled = true;
                    generatePaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Génération du lien...';
                    
                    // Get form data
                    const formData = new FormData(rechargeForm);
                    formData.append('action', 'generate_payment_link');
                    
                    // Send request to generate payment link
                    fetch('../api/payment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // Check if response is ok
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show payment link section
                            rechargeForm.classList.add('hidden');
                            paymentLinkSection.classList.remove('hidden');
                            
                            // Set payment link
                            paymentLinkBtn.href = data.payment_link;
                        } else {
                            // Show error
                            alert('Erreur: ' + data.message);
                            
                            // Reset button
                            generatePaymentBtn.disabled = false;
                            generatePaymentBtn.innerHTML = 'Démarrer le processus de paiement';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue. Veuillez réessayer.');
                        
                        // Reset button
                        generatePaymentBtn.disabled = false;
                        generatePaymentBtn.innerHTML = 'Démarrer le processus de paiement';
                    });
                });
            }

            // Modal de confirmation de paiement
            const confirmPaymentBtn = document.getElementById('confirm-payment-btn');
            const confirmPaymentModal = document.getElementById('confirm-payment-modal');
            const closePaymentModal = document.getElementById('close-payment-modal');
            const confirmationCode = document.getElementById('confirmation-code');
            const confirmationError = document.getElementById('confirmation-error');
            const paymentConfirmationForm = document.getElementById('payment-confirmation-form');
            const confirmCheckoutBtn = document.getElementById('confirm-checkout-btn');
            const servicesSummary = document.getElementById('services-summary');
            const totalAmount = document.getElementById('total-amount');

            // Ouvrir la modal de confirmation
            if (confirmPaymentBtn) {
                confirmPaymentBtn.addEventListener('click', function() {
                    // Récupérer les services du panier et les afficher dans la modal
                    servicesSummary.innerHTML = '';
                    let total = 0;
                    
                    <?php 
                    // Construire un tableau JavaScript des éléments du panier
                    echo "const cartItems = [";
                    foreach ($cartItems as $index => $item) {
                        $itemTotal = $item['price'] * $item['quantity'];
                        echo ($index > 0 ? "," : "") . "{";
                        echo "name: '" . addslashes(htmlspecialchars($item['product_name'])) . "',";
                        echo "quantity: " . $item['quantity'] . ",";
                        echo "total: '" . number_format($itemTotal, 0, ',', ' ') . " FCFA',";
                        echo "totalValue: " . $itemTotal;
                        echo "}";
                    }
                    echo "];";
                    ?>
                    
                    // Ajouter tous les éléments du panier à la liste
                    cartItems.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'flex justify-between';
                        div.innerHTML = `
                            <span class="text-gray-300">${item.name} (${item.quantity})</span>
                            <span class="text-white">${item.total}</span>
                        `;
                        servicesSummary.appendChild(div);
                        total += item.totalValue;
                    });
                    
                    // Mettre à jour le total
                    totalAmount.textContent = total.toLocaleString('fr-FR') + ' FCFA';
                    
                    // Afficher la modal
                    confirmPaymentModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Empêcher le défilement
                });
            }

            // Fermer la modal
            if (closePaymentModal) {
                closePaymentModal.addEventListener('click', function() {
                    confirmPaymentModal.classList.add('hidden');
                    document.body.style.overflow = ''; // Réactiver le défilement
                    // Réinitialiser le formulaire
                    paymentConfirmationForm.reset();
                    confirmationError.classList.add('hidden');
                });
            }

            // Fermer la modal en cliquant à l'extérieur
            window.addEventListener('click', function(event) {
                if (event.target === confirmPaymentModal) {
                    confirmPaymentModal.classList.add('hidden');
                    document.body.style.overflow = ''; // Réactiver le défilement
                    // Réinitialiser le formulaire
                    paymentConfirmationForm.reset();
                    confirmationError.classList.add('hidden');
                }
            });

            // Valider le code de confirmation
            if (paymentConfirmationForm) {
                
             
                paymentConfirmationForm.addEventListener('submit', function(e) {
                    
                    if (confirmationCode.value !== 'kmerhosting') {
                        e.preventDefault();
                        confirmationError.classList.remove('hidden');
                        
                        return false;
                    }
                    
                    // Si le code est correct, soumettre le formulaire
                    confirmationError.classList.add('hidden');
                    confirmCheckoutBtn.addEventListener('click', ()=> {
                        confirmCheckoutBtn.disabled = true;
                        confirmCheckoutBtn.classList.add("opacity-50", "cursor-not-allowed");
                        confirmCheckoutBtn.textContent = "Traitement en cours...";
                    });
                    return true;
                    
                });
            }
        });
    </script>
</body>
</html>
