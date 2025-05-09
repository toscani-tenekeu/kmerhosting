<?php
// Démarrer la session
session_start();

// Définir la page courante
$currentPage = 'services';

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
$cartCount = getCartItemCount($_SESSION['user_id']);

// Récupérer les services de l'utilisateur
$userServices = getUserServices($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Services - KmerHosting</title>
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
                            <a href="services.php" class="flex items-center px-4 py-2.5 text-white bg-dark-700">
                                <i class="fas fa-server w-5 mr-3 text-kmergreen"></i>
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
                    <h1 class="text-2xl font-bold text-white mb-2">Mes Services</h1>
                    <p class="text-gray-400">Gérez vos services d'hébergement et solutions web.</p>
                </div>

                <?php if (empty($userServices)): ?>
                    <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                        <div class="p-5 text-center">
                            <div class="text-gray-400 text-5xl mb-4">
                                <i class="fas fa-server"></i>
                            </div>
                            <p class="text-gray-300 mb-4">Vous n'avez pas encore de services actifs</p>
                            <a href="../index.php#packages" class="inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                                Découvrir nos offres
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($userServices as $service): ?>
                        <?php 
                        // Parse connection info if available
                        $connectionInfo = null;
                        if (!empty($service['connection_info'])) {
                            $connectionInfo = json_decode($service['connection_info'], true);
                        }
                        
                        // Check if service is active
                        $isActive = ($service['status'] === 'active');
                        
                        // Get service details based on service type
                        $serviceDetails = null;
                        $usageStats = [];
                        $features = [];
                        
                        if ($service['service_type'] === 'hosting') {
                            $hostingPackage = getHostingPackageById($service['service_id']);
                            if ($hostingPackage) {
                                $serviceDetails = $hostingPackage;
                                
                                // Extract disk space value and unit
                                $diskSpace = $hostingPackage['disk_space'];
                                $diskSpaceValue = preg_replace('/[^0-9.]/', '', $diskSpace);
                                $diskSpaceUnit = preg_replace('/[0-9.]/', '', $diskSpace);
                                $diskSpaceUnit = trim($diskSpaceUnit);
                                
                                // Extract bandwidth value and unit
                                $bandwidth = $hostingPackage['bandwidth'];
                                $bandwidthValue = preg_replace('/[^0-9.]/', '', $bandwidth);
                                $bandwidthUnit = preg_replace('/[0-9.]/', '', $bandwidth);
                                $bandwidthUnit = trim($bandwidthUnit);
                                
                                // Set usage stats with real data from the package
                                $usageStats = [
                                    [
                                        'name' => 'Espace disque',
                                        'current' => '2.1', // This would come from actual usage data
                                        'max' => $diskSpaceValue,
                                        'unit' => $diskSpaceUnit,
                                        'percent' => 21 // This would be calculated from actual usage
                                    ],
                                    [
                                        'name' => 'Bande passante',
                                        'current' => '45', // This would come from actual usage data
                                        'max' => $bandwidthValue,
                                        'unit' => $bandwidthUnit,
                                        'percent' => 45 // This would be calculated from actual usage
                                    ],
                                    [
                                        'name' => 'Bases de données',
                                        'current' => '3', // This would come from actual usage data
                                        'max' => $hostingPackage['mysql_databases'],
                                        'unit' => '',
                                        'percent' => 30 // This would be calculated from actual usage
                                    ],
                                    [
                                        'name' => 'Comptes FTP',
                                        'current' => '2', // This would come from actual usage data
                                        'max' => $hostingPackage['ftp_accounts'],
                                        'unit' => '',
                                        'percent' => 40 // This would be calculated from actual usage
                                    ]
                                ];
                                
                                // Set features with real data from the package
                                $features = [
                                    ['name' => 'Espace disque SSD', 'value' => $hostingPackage['disk_space']],
                                    ['name' => 'Bande passante', 'value' => $hostingPackage['bandwidth']],
                                    ['name' => 'Domaines', 'value' => $hostingPackage['domains']],
                                    ['name' => 'Sous-domaines', 'value' => $hostingPackage['subdomains']],
                                    ['name' => 'Bases MySQL', 'value' => $hostingPackage['mysql_databases']],
                                    ['name' => 'Comptes FTP', 'value' => $hostingPackage['ftp_accounts']],
                                    ['name' => 'Accès PHP', 'value' => $hostingPackage['php_access']],
                                    ['name' => 'LiteSpeed', 'value' => $hostingPackage['litespeed']],
                                    ['name' => 'DirectAdmin', 'value' => $hostingPackage['directadmin']],
                                    ['name' => 'Inodes', 'value' => $hostingPackage['inodes']],
                                    ['name' => 'SSL', 'value' => $hostingPackage['ssl']],
                                    ['name' => 'Nombre Sites', 'value' => $hostingPackage['sites_count']]
                                ];
                            }
                        } elseif ($service['service_type'] === 'wordpress') {
                            $wordpressPackage = getWordpressPackageById($service['service_id']);
                            if ($wordpressPackage) {
                                $serviceDetails = $wordpressPackage;
                                
                                // Extract disk space value and unit
                                $diskSpace = $wordpressPackage['disk_space'];
                                $diskSpaceValue = preg_replace('/[^0-9.]/', '', $diskSpace);
                                $diskSpaceUnit = preg_replace('/[0-9.]/', '', $diskSpace);
                                $diskSpaceUnit = trim($diskSpaceUnit);
                                
                                // Extract bandwidth value and unit
                                $bandwidth = $wordpressPackage['bandwidth'];
                                $bandwidthValue = preg_replace('/[^0-9.]/', '', $bandwidth);
                                $bandwidthUnit = preg_replace('/[0-9.]/', '', $bandwidth);
                                $bandwidthUnit = trim($bandwidthUnit);
                                
                                // Set usage stats with real data from the package
                                $usageStats = [
                                    [
                                        'name' => 'Espace disque',
                                        'current' => '2.1', // This would come from actual usage data
                                        'max' => $diskSpaceValue,
                                        'unit' => $diskSpaceUnit,
                                        'percent' => 21 // This would be calculated from actual usage
                                    ],
                                    [
                                        'name' => 'Bande passante',
                                        'current' => '45', // This would come from actual usage data
                                        'max' => $bandwidthValue,
                                        'unit' => $bandwidthUnit,
                                        'percent' => 45 // This would be calculated from actual usage
                                    ],
                                    [
                                        'name' => 'Bases de données',
                                        'current' => '3', // This would come from actual usage data
                                        'max' => $wordpressPackage['mysql_databases'],
                                        'unit' => '',
                                        'percent' => 30 // This would be calculated from actual usage
                                    ],
                                    [
                                        'name' => 'Comptes FTP',
                                        'current' => '2', // This would come from actual usage data
                                        'max' => $wordpressPackage['ftp_accounts'],
                                        'unit' => '',
                                        'percent' => 40 // This would be calculated from actual usage
                                    ]
                                ];
                                
                                // Set features with real data from the package
                                $features = [
                                    ['name' => 'Espace disque SSD', 'value' => $wordpressPackage['disk_space']],
                                    ['name' => 'Bande passante', 'value' => $wordpressPackage['bandwidth']],
                                    ['name' => 'Domaines', 'value' => $wordpressPackage['domains']],
                                    ['name' => 'Sous-domaines', 'value' => $wordpressPackage['subdomains']],
                                    ['name' => 'Bases MySQL', 'value' => $wordpressPackage['mysql_databases']],
                                    ['name' => 'Comptes FTP', 'value' => $wordpressPackage['ftp_accounts']],
                                    ['name' => 'Accès PHP', 'value' => $wordpressPackage['php_access']],
                                    ['name' => 'LiteSpeed', 'value' => $wordpressPackage['litespeed']],
                                    ['name' => 'DirectAdmin', 'value' => $wordpressPackage['directadmin']],
                                    ['name' => 'Inodes', 'value' => $wordpressPackage['inodes']],
                                    ['name' => 'SSL', 'value' => $wordpressPackage['ssl']],
                                    ['name' => 'Nombre Sites', 'value' => $wordpressPackage['sites_count']]
                                ];
                            }
                        } elseif ($service['service_type'] === 'ssl') {
                            $sslPackage = getSSLPackageById($service['service_id']);
                            if ($sslPackage) {
                                $serviceDetails = $sslPackage;
                                
                                // Parse features from the features field
                                $sslFeatures = explodeFeatures($sslPackage['features']);
                                
                                // SSL packages have different features
                                $features = [
                                    ['name' => 'Type de SSL', 'value' => $sslPackage['name']],
                                    ['name' => 'Cryptage', 'value' => 'Cryptage 256-bit'],
                                    ['name' => 'Domaines couverts', 'value' => strpos($sslPackage['name'], 'Business') !== false ? 'Wildcard (*.votredomaine.com)' : (strpos($sslPackage['name'], 'Pro') !== false ? 'Multi-domaines (jusqu\'à 5)' : 'Protection pour 1 domaine')],
                                    ['name' => 'Installation', 'value' => 'Installation gratuite'],
                                    ['name' => 'Support technique', 'value' => strpos($sslPackage['name'], 'Business') !== false ? 'Support technique 24/7' : (strpos($sslPackage['name'], 'Pro') !== false ? 'Support technique prioritaire' : 'Support technique')],
                                    ['name' => 'Compatibilité', 'value' => 'Compatibilité tous navigateurs'],
                                    ['name' => 'Sceau de sécurité', 'value' => strpos($sslPackage['name'], 'Basic') !== false ? 'Non inclus' : (strpos($sslPackage['name'], 'Business') !== false ? 'Sceau de sécurité premium' : 'Sceau de sécurité')],
                                    ['name' => 'Validation', 'value' => strpos($sslPackage['name'], 'Business') !== false ? 'Validation d\'organisation' : 'Validation de domaine'],
                                    
                                ];
                            }
                        }
                        ?>
                        
                        <!-- Service Card -->
                        <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                            <div class="p-5 border-b border-dark-700">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <h2 class="text-lg font-semibold text-white mb-1"><?php echo htmlspecialchars($service['service_name']); ?></h2>
                                        <div class="text-sm text-gray-400">ID: <?php echo htmlspecialchars($service['id']); ?></div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full <?php echo $isActive ? 'bg-green-900/30 text-green-400' : 'bg-yellow-900/30 text-yellow-400'; ?>">
                                            <?php echo $isActive ? 'Actif' : ucfirst($service['status']); ?>
                                        </span>
                                        <span class="text-sm text-gray-400">Expire le: <?php echo date('d/m/Y', strtotime($service['expiry_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="p-5 border-b border-dark-700 bg-dark-900/50">
                                <div class="flex flex-wrap gap-3">
                                    <?php if ($service['service_type'] === 'hosting' || $service['service_type'] === 'wordpress'): ?>
                                        <?php if ($isActive && $connectionInfo): ?>
                                            <a href="<?php echo htmlspecialchars($connectionInfo['directadmin_url']); ?>" target="_blank" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-md transition-colors flex items-center">
                                                <i class="fas fa-server mr-2"></i>
                                                DirectAdmin
                                            </a>
                                            <a href="<?php echo htmlspecialchars($connectionInfo['phpmyadmin_url']); ?>" target="_blank" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-md transition-colors flex items-center">
                                                <i class="fas fa-database mr-2"></i>
                                                PHPMyAdmin
                                            </a>
                                            <button onclick="showConnectionInfo(<?php echo htmlspecialchars(json_encode($connectionInfo)); ?>)" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-md transition-colors flex items-center">
                                                <i class="fas fa-key mr-2"></i>
                                                Identifiants
                                            </button>
                                        <?php else: ?>
                                            <button disabled class="px-4 py-2 bg-dark-800 text-gray-500 rounded-md cursor-not-allowed flex items-center opacity-50">
                                                <i class="fas fa-server mr-2"></i>
                                                DirectAdmin
                                            </button>
                                            <button disabled class="px-4 py-2 bg-dark-800 text-gray-500 rounded-md cursor-not-allowed flex items-center opacity-50">
                                                <i class="fas fa-database mr-2"></i>
                                                PHPMyAdmin
                                            </button>
                                            <button disabled class="px-4 py-2 bg-dark-800 text-gray-500 rounded-md cursor-not-allowed flex items-center opacity-50">
                                                <i class="fas fa-key mr-2"></i>
                                                Identifiants
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($service['service_type'] === 'ssl'): ?>
                                        <button class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-md transition-colors flex items-center">
                                            <i class="fas fa-shield-alt mr-2"></i>
                                            Détails du certificat
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Usage Statistics -->
                            <div class="p-5 grid gap-6">
                                <!-- Display usage statistics if available -->
                                <?php if (!empty($usageStats) && $service['status'] === 'active'): ?>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-dark-900 rounded-lg p-4 flex items-center">
                                        <div class="bg-dark-800 p-3 rounded-lg mr-3">
                                            <i class="fas fa-hdd text-kmergreen text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-400 mb-1">Espace disque</div>
                                            <div class="text-lg text-white font-medium">
                                                <?php echo htmlspecialchars($serviceDetails['disk_space']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-dark-900 rounded-lg p-4 flex items-center">
                                        <div class="bg-dark-800 p-3 rounded-lg mr-3">
                                            <i class="fas fa-network-wired text-kmergreen text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-400 mb-1">Bande passante</div>
                                            <div class="text-lg text-white font-medium">
                                                <?php echo htmlspecialchars($serviceDetails['bandwidth']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-dark-900 rounded-lg p-4 flex items-center">
                                        <div class="bg-dark-800 p-3 rounded-lg mr-3">
                                            <i class="fas fa-database text-kmergreen text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-400 mb-1">Bases de données</div>
                                            <div class="text-lg text-white font-medium">
                                                <?php echo htmlspecialchars($serviceDetails['mysql_databases']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-dark-900 rounded-lg p-4 flex items-center">
                                        <div class="bg-dark-800 p-3 rounded-lg mr-3">
                                            <i class="fas fa-users text-kmergreen text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-400 mb-1">Comptes FTP</div>
                                            <div class="text-lg text-white font-medium">
                                                <?php echo htmlspecialchars($serviceDetails['ftp_accounts']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php elseif ($service['status'] === 'pending'): ?>
                                <div class="bg-yellow-900/20 border border-yellow-700/30 rounded-lg p-4 mb-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-base font-medium text-yellow-400">Service en cours de création</h3>
                                            <div class="mt-2 text-sm text-gray-300">
                                                <p>Votre espace est en cours de création. Ce processus prend généralement moins de 15 minutes.</p>
                                                <p class="mt-2">Si ce n'est pas fait après 15 minutes, veuillez contacter notre support:</p>
                                                <ul class="list-disc list-inside mt-2 space-y-1 text-gray-400">
                                                    <li>WhatsApp: <a href="https://wa.me/237694193493" target="_blank" class="text-yellow-400 hover:underline">+237 6 94 19 34 93</a></li>
                                                    <li>Email: <a href="mailto:support@kmerhosting.site" class="text-yellow-400 hover:underline">support@kmerhosting.site</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Display service features if available -->
                                <?php if (!empty($features)): ?>
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold text-white mb-4">Caractéristiques du service</h3>
                                    <div class="bg-dark-900 rounded-lg p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <?php foreach ($features as $feature): ?>
                                            <div class="flex items-center">
                                                <?php 
                                                $isIncluded = isFeatureIncluded($feature['value']);
                                                $iconClass = $isIncluded ? 'text-kmergreen' : 'text-gray-500';
                                                $icon = $isIncluded ? 'fa-check-circle' : 'fa-times-circle';
                                                ?>
                                                <i class="fas <?php echo $icon; ?> <?php echo $iconClass; ?> mr-2"></i>
                                                <span class="text-gray-300"><?php echo htmlspecialchars($feature['name']); ?>: </span>
                                                <span class="text-white ml-1 font-medium"><?php echo htmlspecialchars($feature['value']); ?></span>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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

            // Show connection info modal
            window.showConnectionInfo = function(connectionInfo) {
                let html = `
                <div class="bg-dark-800 p-4 rounded-lg">
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 mb-1">URL DirectAdmin</p>
                        <div class="flex items-center">
                            <input type="text" value="${connectionInfo.directadmin_url}" readonly class="bg-dark-900 text-white p-2 rounded w-full mr-2">
                            <button onclick="copyToClipboard('${connectionInfo.directadmin_url}')" class="p-2 bg-dark-700 hover:bg-dark-600 rounded">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 mb-1">Nom d'utilisateur</p>
                        <div class="flex items-center">
                            <input type="text" value="${connectionInfo.directadmin_username}" readonly class="bg-dark-900 text-white p-2 rounded w-full mr-2">
                            <button onclick="copyToClipboard('${connectionInfo.directadmin_username}')" class="p-2 bg-dark-700 hover:bg-dark-600 rounded">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 mb-1">Mot de passe</p>
                        <div class="flex items-center">
                            <input type="password" value="${connectionInfo.directadmin_password}" id="password-field" readonly class="bg-dark-900 text-white p-2 rounded w-full mr-2">
                            <button onclick="togglePassword()" class="p-2 bg-dark-700 hover:bg-dark-600 rounded mr-2">
                                <i class="fas fa-eye" id="eye-icon"></i>
                            </button>
                            <button onclick="copyToClipboard('${connectionInfo.directadmin_password}')" class="p-2 bg-dark-700 hover:bg-dark-600 rounded">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 mb-1">Adresse IP du serveur</p>
                        <div class="flex items-center">
                            <input type="text" value="${connectionInfo.server_ip}" readonly class="bg-dark-900 text-white p-2 rounded w-full mr-2">
                            <button onclick="copyToClipboard('${connectionInfo.server_ip}')" class="p-2 bg-dark-700 hover:bg-dark-600 rounded">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                `;
                
                Swal.fire({
                    title: 'Informations de connexion',
                    html: html,
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'bg-dark-800 text-white',
                        title: 'text-white',
                        closeButton: 'text-white'
                    }
                });
            };

            // Toggle password visibility
            window.togglePassword = function() {
                const passwordField = document.getElementById('password-field');
                const eyeIcon = document.getElementById('eye-icon');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordField.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            };

            // Copy to clipboard function
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).then(function() {
                    Swal.fire({
                        title: 'Copié!',
                        text: 'Texte copié dans le presse-papiers',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'bg-dark-800 text-white',
                            title: 'text-white'
                        }
                    });
                }, function(err) {
                    console.error('Erreur lors de la copie: ', err);
                });
            };
        });
    </script>
</body>
</html>
