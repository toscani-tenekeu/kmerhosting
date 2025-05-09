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

require_once 'includes/header.php';
?>

    <!-- Main Content -->
    <main class="flex-grow">
        <?php require_once 'includes/hero_section.php'; ?>

        <!-- KamerHosting Trust Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex flex-col items-center justify-center text-center">
                    <img src="assets/images/logo.webp" alt="KamerHosting" class="h-32 md:h-48 mb-8">
                    <h2 class="text-3xl font-bold text-kmerblue mb-4">Votre Hébergeur Camerounais de Confiance</h2>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto mb-6">
                        KmerHosting est fier d'être un hébergeur 100% camerounais, offrant des solutions fiables et
                        sécurisées pour tous vos projets web.
                    </p>
                    <div class="bg-gray-100 p-4 rounded-lg inline-flex items-center">
                        <i class="fas fa-certificate text-kmergreen text-2xl mr-3"></i>
                        <span class="text-gray-800 font-medium">Registrateur de domaines certifié et approuvé par Namecheap et les autorités de certification (CA)
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-kmerblue">Nos Services</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Découvrez notre gamme complète de services d'hébergement
                        web pour répondre à tous vos besoins en ligne.</p>
                </div>

                <?php $offers = getOffers(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($offers as $offer): ?>
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:border-kmergreen transition-all duration-300">
                        <div class="text-kmergreen text-4xl mb-4">
                            <?php
                            $iconType = (strpos($offer['icon'], 'wordpress') !== false || strpos($offer['icon'], 'globe') !== false) ? 'fab' : 'fas';
                            ?>
                            <i class="<?php echo $iconType . ' ' . htmlspecialchars($offer['icon']); ?>"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-kmerblue"><?php echo htmlspecialchars($offer['title']); ?></h3>
                        <p class="text-gray-600"><?php echo htmlspecialchars($offer['description']); ?></p>
                        <ul class="mt-4 space-y-2">
                            <?php foreach ($offer['features'] as $feature): ?>
                            <li class="flex items-center">
                                <i class="fas fa-check text-kmergreen mr-2"></i>
                                <span><?php echo htmlspecialchars($feature); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="<?php
                            $url = '#';
                            if (stripos($offer['title'], 'WordPress') !== false) {
                                $url = '#wordpress-packages';
                            } elseif (stripos($offer['title'], 'Domaine') !== false) {
                                $url = 'domaines.php';
                            } elseif (stripos($offer['title'], 'SSL') !== false) {
                                $url = '#ssl-certificates';
                            } elseif (stripos($offer['title'], 'Hébergement Web') !== false) {
                                $url = '#packages';
                            }
                            echo $url;
                        ?>"
                            class="mt-6 inline-block text-kmergreen hover:text-kmergreen-dark font-medium">
                            <?php echo htmlspecialchars($offer['link_text']); ?> <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Payment Methods Section -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-kmerblue mb-4">Paiement Automatique, Fiable et Sécurisé</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Effectuez vos paiements en toute sécurité avec les
                        méthodes de paiement les plus populaires au Cameroun</p>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-center gap-8 max-w-2xl mx-auto">
                    <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                        <img src="assets/images/orange_money_logo.webp" alt="Orange Money" class="h-16 mb-4">
                        <span class="font-medium text-gray-800">Orange Money</span>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md flex flex-col items-center">
                        <img src="assets/images/mtn_momo_logo.webp" alt="MTN Mobile Money" class="h-16 mb-4">
                        <span class="font-medium text-gray-800">MTN Mobile Money</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technologies Section -->
        <section id="technologies" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-kmerblue">Nos Technologies</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Nous utilisons les technologies les plus récentes et
                        performantes pour vous offrir un hébergement web de qualité supérieure.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <!-- LiteSpeed -->
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 transition-transform duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center mb-4">
                            <img src="assets/images/litespeed.png" alt="LiteSpeed" class="h-16">
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-center text-kmerblue">LiteSpeed Web Server</h3>
                        <p class="text-gray-600">LiteSpeed est jusqu'à 9x plus rapide qu'Apache et 3x plus rapide que
                            Nginx. Il offre une meilleure gestion des connexions simultanées, une consommation de
                            ressources réduite et une sécurité renforcée.</p>
                        <ul class="mt-4 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Cache intégré pour des performances optimales</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Protection anti-DDoS avancée</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Compatible avec les applications PHP populaires</span>
                            </li>
                        </ul>
                    </div>

                    <!-- DirectAdmin -->
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 transition-transform duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center mb-4">
                            <img src="assets/images/directadmin.svg" alt="DirectAdmin" class="h-16">
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-center text-kmerblue">DirectAdmin</h3>
                        <p class="text-gray-600">Interface d'administration simple et puissante qui vous permet de gérer
                            facilement votre hébergement web. Créez des comptes email, gérez vos bases de données et
                            bien plus encore.</p>
                        <ul class="mt-4 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Interface intuitive et conviviale</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Gestion complète de votre hébergement</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Sauvegardes automatiques</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Premium Site Builder -->
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 transition-transform duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-paint-brush text-kmerblue text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-center text-kmerblue">Premium Site Builder</h3>
                        <p class="text-gray-600">Créez facilement votre site web professionnel sans connaissances
                            techniques. Notre constructeur de site premium offre des modèles modernes et des
                            fonctionnalités avancées.</p>
                        <ul class="mt-4 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Interface glisser-déposer intuitive</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Plus de 100 modèles professionnels</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Responsive design pour tous les appareils</span>
                            </li>
                        </ul>
                    </div>

                    <!-- SSD Storage -->
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 transition-transform duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center mb-4">
                            <i class="fas fa-hdd text-kmerblue text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-center text-kmerblue">Stockage SSD</h3>
                        <p class="text-gray-600">Tous nos plans d'hébergement utilisent des disques SSD haute
                            performance pour un chargement ultra-rapide de vos sites web et applications.</p>
                        <ul class="mt-4 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Vitesse de lecture/écriture jusqu'à 10x plus rapide</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Temps de chargement des pages réduit</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Fiabilité accrue et durée de vie prolongée</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Immunify360 -->
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 transition-transform duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center mb-4">
                            <img src="assets/images/imunify360.svg" alt="Immunify360" class="h-16">
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-center text-kmerblue">Immunify360</h3>
                        <p class="text-gray-600">Protection complète contre les menaces de sécurité web avec
                            Immunify360, une solution de sécurité tout-en-un pour votre hébergement.</p>
                        <ul class="mt-4 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Pare-feu adaptatif</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Protection contre les malwares</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-kmergreen mr-2"></i>
                                <span>Détection d'intrusion proactive</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Frameworks Support -->
                    <div
                        class="bg-white p-6 rounded-lg shadow-md border border-gray-200 transition-transform duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center mb-4 flex-wrap gap-4">
                            <i class="fab fa-php text-kmerblue text-3xl mx-2"></i>
                            <i class="fab fa-node-js text-kmerblue text-3xl mx-2"></i>
                            <i class="fab fa-python text-kmerblue text-3xl mx-2"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-center text-kmerblue">Support Multi-Frameworks</h3>
                        <p class="text-gray-600">Nous prenons en charge les frameworks et technologies les plus
                            populaires pour vos projets de développement web, avec les dernières versions disponibles.
                        </p>

                        <div class="mt-4 space-y-4">
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fab fa-php text-kmerblue text-2xl mr-2"></i>
                                    <h4 class="font-semibold">PHP 8.3</h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">Profitez des dernières fonctionnalités de PHP 8.3
                                    pour des performances optimales.</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">PHP 7.4</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">PHP 8.0</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">PHP 8.1</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">PHP 8.2</span>
                                    <span
                                        class="text-xs bg-kmergreen bg-opacity-20 text-kmergreen px-2 py-1 rounded font-medium">PHP
                                        8.3</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Choisissez votre version PHP depuis le panneau
                                    DirectAdmin</p>
                            </div>

                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fab fa-node-js text-kmerblue text-2xl mr-2"></i>
                                    <h4 class="font-semibold">Node.js 22</h4>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">Développez avec la dernière version de Node.js
                                    pour des applications modernes et performantes.</p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">Node.js 16</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">Node.js 18</span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">Node.js 20</span>
                                    <span
                                        class="text-xs bg-kmergreen bg-opacity-20 text-kmergreen px-2 py-1 rounded font-medium">Node.js
                                        24 (Dernière version actuelle)</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Sélectionnez votre version Node.js via notre
                                    interface de gestion</p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <div class="flex flex-col items-center p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-laravel text-red-600 text-2xl mb-1"></i>
                                <span class="text-xs font-medium">Laravel</span>
                            </div>
                            <div class="flex flex-col items-center p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-react text-blue-400 text-2xl mb-1"></i>
                                <span class="text-xs font-medium">React</span>
                            </div>
                            <div class="flex flex-col items-center p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-vuejs text-green-500 text-2xl mb-1"></i>
                                <span class="text-xs font-medium">Vue.js</span>
                            </div>
                            <div class="flex flex-col items-center p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-node text-green-600 text-2xl mb-1"></i>
                                <span class="text-xs font-medium">Next.js</span>
                            </div>
                            <div class="flex flex-col items-center p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-python text-green-500 text-2xl mb-1"></i>
                                <span class="text-xs font-medium">Django</span>
                            </div>
                            <div class="flex flex-col items-center p-2 border border-gray-200 rounded-lg">
                                <i class="fab fa-wordpress text-blue-700 text-2xl mb-1"></i>
                                <span class="text-xs font-medium">WordPress</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CMS Image Section -->
        <section class="py-8 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="flex justify-center">
                    <img src="assets/images/cms.png" alt="CMS Solutions" class="max-w-full h-auto">
                </div>
            </div>
        </section>

        <!-- WordPress Packages Section -->
        <section id="wordpress-packages" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-kmerblue">Nos Packs WordPress Optimisés</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Des solutions d'hébergement spécialement conçues pour
                        WordPress, avec des performances optimales et une sécurité renforcée.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($wordpressPackages as $index => $package): ?>
                    <!-- Pack WordPress <?php echo $package['name']; ?> -->
                    <div
                        class="package-card bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transition-all duration-300 <?php echo $package['recommended'] ? 'ring-2 ring-kmergreen' : ''; ?>">
                        <?php if ($package['recommended']): ?>
                        <div class="bg-kmergreen text-white text-center py-2 font-medium">
                            Pack Recommandé
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center mb-4 text-kmerblue"><?php echo $package['name']; ?></h3>
                            <div class="text-center mb-6">
                                <span class="text-3xl font-bold text-kmerblue"><?php echo number_format($package['price'], 0, ',', ' '); ?></span>
                                <span class="text-gray-600"> FCFA/an</span>
                            </div>

                            <div class="mb-6">
                                <ul class="space-y-3 mb-4">
                                    <li class="flex items-center"><i
                                            class="fas fa-hdd <?php echo getFeatureClass($package['disk_space']); ?> mr-3"></i><span>Espace disque SSD: <?php echo $package['disk_space']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-exchange-alt <?php echo getFeatureClass($package['bandwidth']); ?> mr-3"></i><span>Bande passante: <?php echo $package['bandwidth']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-globe <?php echo getFeatureClass($package['domains']); ?> mr-3"></i><span class="<?php echo isFeatureIncluded($package['domains']) ? '' : 'text-gray-400'; ?>">Domaines: <?php echo $package['domains']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-sitemap <?php echo getFeatureClass($package['subdomains']); ?> mr-3"></i><span>Sous-domaines: <?php echo $package['subdomains']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-database <?php echo getFeatureClass($package['mysql_databases']); ?> mr-3"></i><span>Bases MySQL: <?php echo $package['mysql_databases']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-user <?php echo getFeatureClass($package['ftp_accounts']); ?> mr-3"></i><span>Comptes FTP: <?php echo $package['ftp_accounts']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fab fa-php <?php echo getFeatureClass($package['php_access']); ?> mr-3"></i><span>Accès PHP: <?php echo $package['php_access']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-server <?php echo getFeatureClass($package['litespeed']); ?> mr-3"></i><span>LiteSpeed: <?php echo $package['litespeed']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-cog <?php echo getFeatureClass($package['directadmin']); ?> mr-3"></i><span>DirectAdmin: <?php echo $package['directadmin']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-file <?php echo getFeatureClass($package['inodes']); ?> mr-3"></i><span>Inodes: <?php echo $package['inodes']; ?></span></li>
                                    <li class="flex items-center"><i    
                                            class="fas fa-shield-alt <?php echo getFeatureClass($package['ssl']); ?> mr-3"></i><span>SSL: <?php echo $package['ssl']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-globe <?php echo getFeatureClass($package['sites_count']); ?> mr-3"></i><span>Nombre Sites : <?php echo $package['sites_count']; ?></span>
                                    </li>
                                </ul>

                                <div class="collapse-content hidden">
                                    <ul class="space-y-3 mb-4 border-t border-gray-200 pt-4">
                                        <li class="flex items-center">
                                            <i class="fas fa-envelope <?php echo getFeatureClass($package['email_accounts']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['email_accounts']) ? '' : 'text-gray-400'; ?>">Comptes email: <?php echo $package['email_accounts']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-mail-forward <?php echo getFeatureClass($package['email_redirects']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['email_redirects']) ? '' : 'text-gray-400'; ?>">Redirections email: <?php echo $package['email_redirects']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-mail-bulk <?php echo getFeatureClass($package['mailing_lists']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['mailing_lists']) ? '' : 'text-gray-400'; ?>">Listes de diffusion: <?php echo $package['mailing_lists']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-reply-all <?php echo getFeatureClass($package['auto_responders']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['auto_responders']) ? '' : 'text-gray-400'; ?>">Répondeurs auto: <?php echo $package['auto_responders']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-random <?php echo getFeatureClass($package['domain_pointers']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['domain_pointers']) ? '' : 'text-gray-400'; ?>">Pointeurs domaine: <?php echo $package['domain_pointers']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-code <?php echo getFeatureClass($package['cgi_access']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['cgi_access']) ? '' : 'text-gray-400'; ?>">Accès CGI: <?php echo $package['cgi_access']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-ban <?php echo getFeatureClass($package['spamassassin']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['spamassassin']) ? '' : 'text-gray-400'; ?>">SpamAssassin: <?php echo $package['spamassassin']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-envelope <?php echo getFeatureClass($package['catch_all']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['catch_all']) ? '' : 'text-gray-400'; ?>">Catch-All Email: <?php echo $package['catch_all']; ?></span>
                                        </li>

                                        <li class="flex items-center">
                                            <i class="fas fa-clock <?php echo getFeatureClass($package['cron_jobs']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['cron_jobs']) ? '' : 'text-gray-400'; ?>">Tâches Cron: <?php echo $package['cron_jobs']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-database <?php echo getFeatureClass($package['redis']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['redis']) ? '' : 'text-gray-400'; ?>">Redis: <?php echo $package['redis']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-info-circle <?php echo getFeatureClass($package['system_info']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['system_info']) ? '' : 'text-gray-400'; ?>">System Info: <?php echo $package['system_info']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-key <?php echo getFeatureClass($package['login_keys']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['login_keys']) ? '' : 'text-gray-400'; ?>">Login Keys: <?php echo $package['login_keys']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-network-wired <?php echo getFeatureClass($package['dns_control']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['dns_control']) ? '' : 'text-gray-400'; ?>">DNS Control: <?php echo $package['dns_control']; ?></span>
                                        </li>
                                    
                                        <li class="flex items-center">
                                            <i class="fas fa-file-contract <?php echo getFeatureClass($package['security_txt']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['security_txt']) ? '' : 'text-gray-400'; ?>">Security.txt (RFC9116): <?php echo $package['security_txt']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-paint-brush <?php echo getFeatureClass($package['site_builder']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['site_builder']) ? '' : 'text-gray-400'; ?>">Premium Site Builder: <?php echo $package['site_builder']; ?></span>
                                        </li>
                                     
                                    </ul>
                                </div>

                                <button type="button"
                                    class="collapse-trigger w-full text-kmergreen hover:text-kmergreen-dark text-sm font-medium py-2 flex items-center justify-center">
                                    <span class="show-more">Voir plus de fonctionnalités</span>
                                    <span class="show-less hidden">Voir moins</span>
                                    <i class="fas fa-chevron-down ml-1 show-more"></i>
                                    <i class="fas fa-chevron-up ml-1 show-less hidden"></i>
                                </button>
                            </div>

                            <div class="flex flex-wrap mb-4">
                                <?php 
                                $packageFeatures = explodeFeatures($package['features']);
                                foreach ($packageFeatures as $feature): 
                                ?>
                                <span class="feature-badge">
                                    <?php 
                                    $icon = '';
                                    switch ($feature) {
                                        case 'WordPress': $icon = 'fab fa-wordpress'; break;
                                        case 'Joomla': $icon = 'fab fa-joomla'; break;
                                        case 'Drupal': $icon = 'fab fa-drupal'; break;
                                        case 'Prestashop': $icon = 'fab fa-store'; break;
                                        case 'LiteSpeed': $icon = 'fas fa-bolt'; break;
                                        case 'Optimisation': $icon = 'fas fa-cogs'; break;
                                        case 'Sécurité': $icon = 'fas fa-shield-alt'; break;
                                        case 'WooCommerce': $icon = 'fas fa-shopping-cart'; break;
                                        default: $icon = 'fas fa-check'; break;
                                    }
                                    ?>
                                    <i class="<?php echo $icon; ?> mr-1"></i> <?php echo $feature; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>

                            <button type="button"
                                class="add-to-cart-btn w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300"
                                data-package-id="wp<?php echo $index + 1; ?>" 
                                data-package-name="<?php echo htmlspecialchars($package['name']); ?>" 
                                data-package-price="<?php echo $package['price']; ?>"
                                data-package-type="wordpress">
                                Commander
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- SSL Certificates Section -->
        <section id="ssl-certificates" class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-kmerblue">Certificats SSL</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Sécurisez votre site web avec nos certificats SSL. Protégez les données de vos visiteurs et améliorez votre référencement.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($sslPackages as $index => $package): ?>
                    <!-- SSL <?php echo $package['name']; ?> -->
                    <div class="package-card bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transition-all duration-300 <?php echo $package['recommended'] ? 'ring-2 ring-kmergreen' : ''; ?>">
                        <?php if ($package['recommended']): ?>
                        <div class="bg-kmergreen text-white text-center py-2 font-medium">
                            Recommandé
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center mb-4 text-kmerblue"><?php echo $package['name']; ?></h3>
                            <div class="text-center mb-6">
                                <span class="text-3xl font-bold text-kmerblue"><?php echo number_format($package['price'], 0, ',', ' '); ?></span>
                                <span class="text-gray-600"> FCFA/an</span>
                            </div>

                            <div class="mb-6">
                                <ul class="space-y-3 mb-4">
                                    <?php 
                                    $sslFeatures = explode(',', $package['features']);
                                    foreach ($sslFeatures as $feature): 
                                    ?>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-kmergreen mr-3"></i>
                                        <span><?php echo $feature; ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <button type="button" 
                                class="add-to-cart-btn w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300" 
                                data-package-id="ssl<?php echo $index + 1; ?>" 
                                data-package-name="<?php echo htmlspecialchars($package['name']); ?>" 
                                data-package-price="<?php echo $package['price']; ?>"
                                data-package-type="ssl">
                                Commander
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Packages Section -->
        <section id="packages" class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-kmerblue">Nos Packs d'Hébergement Web</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Choisissez le pack qui correspond le mieux à vos besoins.
                        Tous nos plans incluent un support technique 24/7 et une garantie de disponibilité de 99.9%.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($hostingPackages as $index => $package): ?>
                    <!-- Pack <?php echo $index + 1; ?>: <?php echo $package['name']; ?> -->
                    <div
                        class="package-card bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 transition-all duration-300 <?php echo $package['recommended'] ? 'ring-2 ring-kmergreen' : ''; ?>">
                        <?php if ($package['recommended']): ?>
                        <div class="bg-kmergreen text-white text-center py-2 font-medium">
                            Pack Recommandé
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center mb-4 text-kmerblue"><?php echo $package['name']; ?></h3>
                            <div class="text-center mb-6">
                                <span class="text-3xl font-bold text-kmerblue"><?php echo number_format($package['price'], 0, ',', ' '); ?></span>
                                <span class="text-gray-600"> FCFA/an</span>
                            </div>

                            <div class="mb-6">
                                <ul class="space-y-3 mb-4">
                                    <li class="flex items-center"><i
                                            class="fas fa-hdd <?php echo getFeatureClass($package['disk_space']); ?> mr-3"></i><span>Espace disque SSD: <?php echo $package['disk_space']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-exchange-alt <?php echo getFeatureClass($package['bandwidth']); ?> mr-3"></i><span>Bande passante: <?php echo $package['bandwidth']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-globe <?php echo getFeatureClass($package['domains']); ?> mr-3"></i><span class="<?php echo isFeatureIncluded($package['domains']) ? '' : 'text-gray-400'; ?>">Domaines: <?php echo $package['domains']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-sitemap <?php echo getFeatureClass($package['subdomains']); ?> mr-3"></i><span>Sous-domaines: <?php echo $package['subdomains']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-database <?php echo getFeatureClass($package['mysql_databases']); ?> mr-3"></i><span>Bases MySQL: <?php echo $package['mysql_databases']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-user <?php echo getFeatureClass($package['ftp_accounts']); ?> mr-3"></i><span>Comptes FTP: <?php echo $package['ftp_accounts']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fab fa-php <?php echo getFeatureClass($package['php_access']); ?> mr-3"></i><span>Accès PHP: <?php echo $package['php_access']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fab fa-node <?php echo getFeatureClass($package['nodejs_access']); ?> mr-3"></i><span class="<?php echo isFeatureIncluded($package['nodejs_access']) ? '' : 'text-gray-400'; ?>">Accès Node.js: <?php echo $package['nodejs_access']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fab fa-python <?php echo getFeatureClass($package['python_access']); ?> mr-3"></i><span class="<?php echo isFeatureIncluded($package['python_access']) ? '' : 'text-gray-400'; ?>">Accès Python: <?php echo $package['python_access']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-server <?php echo getFeatureClass($package['litespeed']); ?> mr-3"></i><span>LiteSpeed: <?php echo $package['litespeed']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-cog <?php echo getFeatureClass($package['directadmin']); ?> mr-3"></i><span>DirectAdmin: <?php echo $package['directadmin']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-file <?php echo getFeatureClass($package['inodes']); ?> mr-3"></i><span>Inodes: <?php echo $package['inodes']; ?></span></li>
                                    <li class="flex items-center"><i
                                            class="fas fa-shield-alt <?php echo getFeatureClass($package['ssl']); ?> mr-3"></i><span class="<?php echo isFeatureIncluded($package['ssl']) ? '' : 'text-gray-400'; ?>">SSL: <?php echo $package['ssl']; ?></span>
                                    </li>
                                    <li class="flex items-center"><i
                                            class="fas fa-globe <?php echo getFeatureClass($package['sites_count']); ?> mr-3"></i><span>Nombre Sites : <?php echo $package['sites_count']; ?></span>
                                    </li>
                                </ul>

                                <div class="collapse-content hidden">
                                    <ul class="space-y-3 mb-4 border-t border-gray-200 pt-4">
                                        <li class="flex items-center">
                                            <i class="fas fa-envelope <?php echo getFeatureClass($package['email_accounts']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['email_accounts']) ? '' : 'text-gray-400'; ?>">Comptes email: <?php echo $package['email_accounts']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-mail-forward <?php echo getFeatureClass($package['email_redirects']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['email_redirects']) ? '' : 'text-gray-400'; ?>">Redirections email: <?php echo $package['email_redirects']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-mail-bulk <?php echo getFeatureClass($package['mailing_lists']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['mailing_lists']) ? '' : 'text-gray-400'; ?>">Listes de diffusion: <?php echo $package['mailing_lists']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-reply-all <?php echo getFeatureClass($package['auto_responders']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['auto_responders']) ? '' : 'text-gray-400'; ?>">Répondeurs auto: <?php echo $package['auto_responders']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-random <?php echo getFeatureClass($package['domain_pointers']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['domain_pointers']) ? '' : 'text-gray-400'; ?>">Pointeurs domaine: <?php echo $package['domain_pointers']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-code <?php echo getFeatureClass($package['cgi_access']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['cgi_access']) ? '' : 'text-gray-400'; ?>">Accès CGI: <?php echo $package['cgi_access']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-ban <?php echo getFeatureClass($package['spamassassin']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['spamassassin']) ? '' : 'text-gray-400'; ?>">SpamAssassin: <?php echo $package['spamassassin']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-inbox-in <?php echo getFeatureClass($package['catch_all']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['catch_all']) ? '' : 'text-gray-400'; ?>">Catch-All Email: <?php echo $package['catch_all']; ?></span>
                                        </li>

                                        <li class="flex items-center">
                                            <i class="fas fa-clock <?php echo getFeatureClass($package['cron_jobs']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['cron_jobs']) ? '' : 'text-gray-400'; ?>">Tâches Cron: <?php echo $package['cron_jobs']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-database <?php echo getFeatureClass($package['redis']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['redis']) ? '' : 'text-gray-400'; ?>">Redis: <?php echo $package['redis']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-info-circle <?php echo getFeatureClass($package['system_info']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['system_info']) ? '' : 'text-gray-400'; ?>">System Info: <?php echo $package['system_info']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-key <?php echo getFeatureClass($package['login_keys']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['login_keys']) ? '' : 'text-gray-400'; ?>">Login Keys: <?php echo $package['login_keys']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-network-wired <?php echo getFeatureClass($package['dns_control']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['dns_control']) ? '' : 'text-gray-400'; ?>">DNS Control: <?php echo $package['dns_control']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-file-contract <?php echo getFeatureClass($package['security_txt']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['security_txt']) ? '' : 'text-gray-400'; ?>">Security.txt (RFC9116): <?php echo $package['security_txt']; ?></span>
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-paint-brush <?php echo getFeatureClass($package['site_builder']); ?> mr-3"></i>
                                            <span class="<?php echo isFeatureIncluded($package['site_builder']) ? '' : 'text-gray-400'; ?>">Premium Site Builder: <?php echo $package['site_builder']; ?></span>
                                        </li>
                                    </ul>
                                </div>

                                <button type="button"
                                    class="collapse-trigger w-full text-kmergreen hover:text-kmergreen-dark text-sm font-medium py-2 flex items-center justify-center">
                                    <span class="show-more">Voir plus de fonctionnalités</span>
                                    <span class="show-less hidden">Voir moins</span>
                                    <i class="fas fa-chevron-down ml-1 show-more"></i>
                                    <i class="fas fa-chevron-up ml-1 show-less hidden"></i>
                                </button>
                            </div>

                            <div class="flex flex-wrap mb-4">
                                <?php 
                                $packageFeatures = explodeFeatures($package['features']);
                                foreach ($packageFeatures as $feature): 
                                ?>
                                <span class="feature-badge">
                                    <?php 
                                    $icon = '';
                                    switch ($feature) {
                                        case 'PHP': $icon = 'fab fa-php'; break;
                                        case 'Laravel': $icon = 'fab fa-laravel'; break;
                                        case 'Node.js': $icon = 'fab fa-node-js'; break;
                                        case 'Python': $icon = 'fab fa-python'; break;
                                        case 'Softaculous': $icon = 'fas fa-magic'; break;
                                        case 'phpMyAdmin': $icon = 'fas fa-table'; break;
                                        case 'Sécurité +': $icon = 'fas fa-shield-alt'; break;
                                        case '10': case '30': case '100': $icon = 'fas fa-plus'; break;
                                        default: $icon = 'fas fa-check'; break;
                                    }
                                    ?>
                                    <i class="<?php echo $icon; ?> mr-1"></i> <?php echo $feature; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>

                            <button type="button"
                                class="add-to-cart-btn w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300"
                                data-package-id="<?php echo $index + 1; ?>" 
                                data-package-name="<?php echo htmlspecialchars($package['name']); ?>" 
                                data-package-price="<?php echo $package['price']; ?>"
                                data-package-type="hosting">
                                Commander
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

<!-- Modal de confirmation d'ajout au panier -->
<div id="add-to-cart-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-kmerblue">Ajouter au panier</h3>
            <button id="close-cart-modal" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5">
            <div class="mb-4">
                <p class="text-gray-700 mb-3">Vous êtes sur le point d'ajouter ce service à votre panier :</p>
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-kmerblue" id="modal-package-name">Nom du package</span>
                        <span class="font-bold text-kmergreen" id="modal-package-price">0 FCFA</span>
                    </div>
                    <p class="text-sm text-gray-600" id="modal-package-type">Type de service</p>
                </div>
            </div>
            
            <div class="flex justify-between space-x-3">
                <button type="button" id="cancel-add-to-cart" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                    Annuler
                </button>
                <button type="button" id="confirm-add-to-cart" class="flex-1 bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pas de notification visuelle permanente -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons "Voir plus/moins"
    const collapseTriggers = document.querySelectorAll('.collapse-trigger');
    
    collapseTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const content = this.previousElementSibling;
            const showMore = this.querySelector('.show-more');
            const showLess = this.querySelector('.show-less');
            
            content.classList.toggle('hidden');
            showMore.classList.toggle('hidden');
            showLess.classList.toggle('hidden');
        });
    });

    // Variables pour la modal d'ajout au panier
    const addToCartModal = document.getElementById('add-to-cart-modal');
    const closeCartModal = document.getElementById('close-cart-modal');
    const cancelAddToCart = document.getElementById('cancel-add-to-cart');
    const confirmAddToCart = document.getElementById('confirm-add-to-cart');
    const modalPackageName = document.getElementById('modal-package-name');
    const modalPackagePrice = document.getElementById('modal-package-price');
    const modalPackageType = document.getElementById('modal-package-type');
    const cartNotification = document.getElementById('cart-notification');
    
    // Variables pour stocker les informations du package sélectionné
    let selectedPackageId = '';
    let selectedPackageName = '';
    let selectedPackageType = '';
    
    // Gestion des boutons "Commander"
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Récupérer les informations du package
            selectedPackageId = this.getAttribute('data-package-id');
            selectedPackageName = this.getAttribute('data-package-name');
            selectedPackageType = this.getAttribute('data-package-type');
            const packagePrice = this.getAttribute('data-package-price');
            
            // Mettre à jour la modal avec les informations
            modalPackageName.textContent = selectedPackageName;
            modalPackagePrice.textContent = Number(packagePrice).toLocaleString('fr-FR') + ' FCFA';
            
            // Mettre à jour le type de package
            let packageTypeText = '';
            switch(selectedPackageType) {
                case 'hosting':
                    packageTypeText = 'Hébergement Web';
                    break;
                case 'wordpress':
                    packageTypeText = 'Hébergement WordPress';
                    break;
                case 'ssl':
                    packageTypeText = 'Certificat SSL';
                    break;
                default:
                    packageTypeText = 'Service';
            }
            modalPackageType.textContent = packageTypeText;
            
            // Afficher la modal
            addToCartModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Empêcher le défilement
        });
    });
    
    // Fermer la modal
    function closeModal() {
        addToCartModal.classList.add('hidden');
        document.body.style.overflow = ''; // Réactiver le défilement
    }
    
    if (closeCartModal) {
        closeCartModal.addEventListener('click', closeModal);
    }
    
    if (cancelAddToCart) {
        cancelAddToCart.addEventListener('click', closeModal);
    }
    
    // Fermer la modal en cliquant à l'extérieur
    window.addEventListener('click', function(event) {
        if (event.target === addToCartModal) {
            closeModal();
        }
    });
    
    // Ajouter au panier
    if (confirmAddToCart) {
        confirmAddToCart.addEventListener('click', function() {
            // Vérifier si l'utilisateur est connecté
            <?php if ($isLoggedIn): ?>
                // Désactiver le bouton pendant le traitement
                confirmAddToCart.disabled = true;
                confirmAddToCart.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Traitement...';
                
                // Ajouter au panier via AJAX
                const formData = new FormData();
                formData.append('package_id', selectedPackageId);
                formData.append('package_type', selectedPackageType);
                formData.append('quantity', 1);
                
                fetch('backend/cart/add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Réactiver le bouton
                    confirmAddToCart.disabled = false;
                    confirmAddToCart.innerHTML = 'Confirmer';
                    
                    if (data.success) {
                        // Fermer la modal
                        closeModal();
                        
                        // Mettre à jour le compteur du panier dans le header
                        const cartCountBadges = document.querySelectorAll('.cart-count-badge');
                        if (cartCountBadges.length > 0) {
                            cartCountBadges.forEach(badge => {
                                badge.textContent = data.cart_count;
                                badge.classList.remove('hidden');
                            });
                        }
                        
                        // Afficher une alerte JavaScript
                        alert('Produit ajouté au panier');
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue. Veuillez réessayer.');
                    
                    // Réactiver le bouton
                    confirmAddToCart.disabled = false;
                    confirmAddToCart.innerHTML = 'Confirmer';
                });
            <?php else: ?>
                // Rediriger vers la page de connexion
                window.location.href = 'login.php?redirect=index.php';
            <?php endif; ?>
        });
    }
    
    // Fermer la notification
    document.addEventListener('click', function(e) {
        if (cartNotification.contains(e.target) && !e.target.closest('a')) {
            // Afficher une alerte simple
            alert('Produit ajouté au panier avec succès');
        }
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
