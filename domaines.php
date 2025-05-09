<?php
session_start();
require_once 'backend/functions.php';

$currentUser = null;
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $currentUser = getCurrentUser();
    $cartCount = getCartItemCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Domaines - KmerHosting.site</title>
    <meta name="description" content="Recherchez et enregistrez votre nom de domaine avec KmerHosting.site - Des prix compétitifs et un processus simple.">

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

    <!-- Custom styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #004a6e 80%);
        }

        .domain-card {
            transition: all 0.3s ease;
        }

        .domain-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center">
                    <img src="assets/images/logo.png" alt="KmerHosting.site" class="h-24">
                </a>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-kmerblue focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="index.php#technologies" class="text-kmerblue hover:text-kmergreen font-medium">Technologies</a>
                    <a href="index.php#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="domaines.php" class="text-kmerblue hover:text-kmergreen font-medium">Nom de Domaines</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                </nav>

                <!-- CTA Button -->
                <?php if ($currentUser): ?>
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Panier avec badge -->
                        <a href="customers/cart.php" class="text-kmerblue hover:text-kmergreen relative p-2">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <?php if ($cartCount > 0): ?>
                            <span class="absolute top-0 right-0 bg-kmergreen text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                <?php echo $cartCount; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="customers/dashboard.php" class="flex items-center space-x-2 text-kmerblue hover:text-kmergreen">
                            <span class="font-medium">Mon Compte</span>
                            <i class="fas fa-user-circle text-xl"></i>
                        </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="hidden md:inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        Se connecter / S'inscrire
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden mt-4 pb-2">
                <div class="flex flex-col space-y-3">
                    <a href="index.php#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="index.php#technologies" class="text-kmerblue hover:text-kmergreen font-medium">Technologies</a>
                    <a href="index.php#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="domaines.php" class="text-kmerblue hover:text-kmergreen font-medium">Nom de Domaines</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                    <a href="login.php"
                        class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-center">
                        Se connecter / S'inscrire
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="gradient-bg text-white py-16 md:py-24">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto text-center">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">Trouvez votre nom de domaine parfait</h1>
                    <p class="text-xl mb-8">Recherchez et enregistrez votre nom de domaine en quelques clics</p>
                    
                    <!-- Search Form -->
                    <form id="domain-search-form" class="flex flex-col md:flex-row gap-4 max-w-2xl mx-auto">
                        <div class="flex-grow">
                            <input type="text" id="domain-search" 
                                class="w-full px-6 py-4 rounded-lg text-gray-900 text-lg focus:outline-none focus:ring-2 focus:ring-kmergreen"
                                placeholder="Entrez votre nom de domaine...">
                        </div>
                        <button type="submit" 
                            class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-4 px-8 rounded-lg text-lg transition duration-300">
                            Rechercher
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Domain Extensions Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center text-kmerblue mb-12">Extensions de domaine populaires</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                    <div class="domain-card bg-white p-6 rounded-xl shadow-md text-center">
                        <div class="text-2xl font-bold text-kmerblue mb-2">.com</div>
                        <div class="text-kmergreen font-semibold">11,000 FCFA/an</div>
                    </div>
                    <div class="domain-card bg-white p-6 rounded-xl shadow-md text-center">
                        <div class="text-2xl font-bold text-kmerblue mb-2">.cm</div>
                        <div class="text-kmergreen font-semibold">12,000 FCFA/an</div>
                    </div>
                    <div class="domain-card bg-white p-6 rounded-xl shadow-md text-center">
                        <div class="text-2xl font-bold text-kmerblue mb-2">.org</div>
                        <div class="text-kmergreen font-semibold">11,000 FCFA/an</div>
                    </div>
                    <div class="domain-card bg-white p-6 rounded-xl shadow-md text-center">
                        <div class="text-2xl font-bold text-kmerblue mb-2">.net</div>
                        <div class="text-kmergreen font-semibold">10,000 FCFA/an</div>
                    </div>
                    <div class="domain-card bg-white p-6 rounded-xl shadow-md text-center">
                        <div class="text-2xl font-bold text-kmerblue mb-2">.fr</div>
                        <div class="text-kmergreen font-semibold">10,500 FCFA/an</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center text-kmerblue mb-12">Pourquoi choisir KmerHosting pour votre domaine ?</h2>
                <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-kmergreen text-3xl mb-4">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="text-xl font-bold text-kmerblue mb-2">Protection WHOIS</h3>
                        <p class="text-gray-600">Protégez vos informations personnelles avec notre service de protection WHOIS gratuit.</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-kmergreen text-3xl mb-4">
                            <i class="fas fa-sync"></i>
                        </div>
                        <h3 class="text-xl font-bold text-kmerblue mb-2">Renouvellement automatique</h3>
                        <p class="text-gray-600">Ne perdez jamais votre domaine grâce à notre système de renouvellement automatique.</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-kmergreen text-3xl mb-4">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="text-xl font-bold text-kmerblue mb-2">Support 24/7</h3>
                        <p class="text-gray-600">Notre équipe de support est disponible 24/7 pour vous aider avec votre domaine.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-kmerblue text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and About -->
                <div>
                    <img src="assets/images/logo.png" alt="KmerHosting.site" class="h-24 mb-4">
                    <p class="text-gray-300 mb-4">
                        Solutions d'hébergement web fiables et abordables pour tous vos projets en ligne.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Liens Rapides</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white">Accueil</a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-white">Nos Services</a></li>
                        <li><a href="#technologies" class="text-gray-300 hover:text-white">Technologies</a></li>
                        <li><a href="#packages" class="text-gray-300 hover:text-white">Nos Packs</a></li>
                        <li><a href="domaines.php" class="text-gray-300 hover:text-white">Nom de Domaines</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Nos Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#services" class="text-gray-300 hover:text-white">Hébergement Web</a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-white">Hébergement WordPress</a></li>
                        <li><a href="domaines.php" class="text-gray-300 hover:text-white">Noms de Domaine</a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-white">Certificats SSL</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-kmergreen"></i>
                            <span class="text-gray-300">Yaoundé, Cameroun</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-kmergreen"></i>
                            <span class="text-gray-300">contact@toscanisoft.cm</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-kmergreen"></i>
                            <span class="text-gray-300">contact@kmerhosting.site</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-3 text-kmergreen"></i>
                            <span class="text-gray-300">+237 694 193 493</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-3 text-kmergreen"></i>
                            <span class="text-gray-300">+237 650 500 018</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold mb-4">Moyens de Paiement</h3>
                    <div class="flex justify-center space-x-8">
                        <img src="assets/images/orange_money_logo.webp" alt="Orange Money" class="h-12">
                        <img src="assets/images/mtn_momo_logo.webp" alt="MTN Mobile Money" class="h-12">
                    </div>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="max-w-md mx-auto">
                    <h3 class="text-lg font-semibold mb-4 text-center">Abonnez-vous à notre newsletter</h3>
                    <form id="newsletter-form" class="flex">
                        <input type="email" name="email" placeholder="Votre adresse email" required
                            class="flex-grow px-4 py-2 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
                        <button type="submit"
                            class="bg-kmergreen hover:bg-kmergreen-dark text-white px-4 py-2 rounded-r-lg transition duration-300">
                            S'abonner
                        </button>
                    </form>
                </div>
            </div>

            <!-- Toscanisoft -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <div class="flex flex-col items-center justify-center">
                    <p class="text-gray-400 mb-2">Un produit de Toscanisoft</p>
                    <img src="assets/images/toscanisoft_logo.webp" alt="toscanisoft_logo" class="h-24 mb-4">
                </div>
            </div>

            <!-- Legal Links -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <div class="flex flex-wrap justify-center gap-4 mb-4">
                    <a href="privacy.php" class="text-gray-400 hover:text-white text-sm">Politique de
                        Confidentialité</a>
                    <a href="terms.php" class="text-gray-400 hover:text-white text-sm">Conditions d'Utilisation</a>
                    <a href="cookies.php" class="text-gray-400 hover:text-white text-sm">Politique de Cookies</a>
                    <a href="legal.php" class="text-gray-400 hover:text-white text-sm">Mentions Légales</a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
                <p>&copy; 2023 KmerHosting.site. Un produit de <a class="text-kmergreen font-bold"
                        href="https://toscanisoft.site">Toscanisoft</a>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Results Modal -->
    <div id="results-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-kmerblue">Résultats de la recherche</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Loading Spinner -->
            <div id="loading-spinner" class="hidden">
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-kmergreen"></div>
                </div>
            </div>

            <!-- Results Container -->
            <div id="results-container" class="space-y-4">
                <!-- Results will be inserted here -->
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Domain search form handling
        const domainSearchForm = document.getElementById('domain-search-form');
        const domainSearchInput = document.getElementById('domain-search');
        const resultsModal = document.getElementById('results-modal');
        const closeModal = document.getElementById('close-modal');
        const loadingSpinner = document.getElementById('loading-spinner');
        const resultsContainer = document.getElementById('results-container');

        // Animation du placeholder
        const placeholders = [
            "exemple.com",
            "monsite.cm",
            "monentreprise.org",
            "monblog.net"
        ];
        let currentPlaceholderIndex = 0;
        let currentPlaceholderChar = 0;
        let isDeleting = false;
        let typingSpeed = 100;

        function animatePlaceholder() {
            const currentPlaceholder = placeholders[currentPlaceholderIndex];
            
            if (isDeleting) {
                domainSearchInput.placeholder = currentPlaceholder.substring(0, currentPlaceholderChar - 1);
                currentPlaceholderChar--;
                typingSpeed = 50;
            } else {
                domainSearchInput.placeholder = currentPlaceholder.substring(0, currentPlaceholderChar + 1);
                currentPlaceholderChar++;
                typingSpeed = 100;
            }

            if (!isDeleting && currentPlaceholderChar === currentPlaceholder.length) {
                isDeleting = true;
                typingSpeed = 1000;
            } else if (isDeleting && currentPlaceholderChar === 0) {
                isDeleting = false;
                currentPlaceholderIndex = (currentPlaceholderIndex + 1) % placeholders.length;
                typingSpeed = 500;
            }

            setTimeout(animatePlaceholder, typingSpeed);
        }

        // Démarrer l'animation du placeholder
        animatePlaceholder();

        // Fonction pour formater le prix
        function formatPrice(price) {
            return new Intl.NumberFormat('fr-FR').format(price);
        }

        // Extensions populaires et leurs prix (en FCFA)
        const domainExtensions = [
            { ext: '.com', price: 6000 },
            { ext: '.net', price: 9000 },
            { ext: '.org', price: 5000 },
            { ext: '.cm', price: 9000 }
        ];
        const currency = 'FCFA';
        const period = 'an';

        // Fonction pour créer une carte de résultat avec prix
        function createResultCard(domain, available, price = null) {
            return `
                <div class="bg-white p-6 rounded-xl shadow-md border ${available ? 'border-kmergreen' : 'border-red-500'}">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-xl font-bold text-kmerblue">${domain}</h4>
                            <p class="text-gray-600">
                                ${available ? 'Ce domaine est disponible !' : 'Ce domaine n\'est pas disponible.'}
                            </p>
                        </div>
                        ${price !== null ? `<div class="text-right">
                            <div class="text-kmergreen font-bold text-xl">${formatPrice(price)} ${currency}</div>
                            <div class="text-gray-500">/${period}</div>
                        </div>` : ''}
                    </div>
                    ${available ? `
                        <button onclick="addDomainToCart('${domain}', ${price})" 
                                class="add-to-cart-btn mt-4 w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                            Ajouter au panier
                        </button>
                    ` : ''}
                </div>
            `;
        }

        // Ajouter cette fonction JavaScript pour gérer l'ajout au panier
        function addDomainToCart(domain, price) {
            fetch('backend/cart/add_domain.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `domain=${encodeURIComponent(domain)}&price=${price}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès!',
                        text: data.message || `Le domaine ${domain} a été ajouté à votre panier.`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    throw new Error(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Une erreur est survenue lors de l\'ajout au panier.'
                });
            });
        }

        // Gestionnaire de soumission du formulaire
        domainSearchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            let domain = domainSearchInput.value.trim();
            if (!domain) {
                alert('Veuillez entrer un nom de domaine');
                return;
            }
            // Nettoyer le nom (enlever extension si présente)
            domain = domain.replace(/\.[a-zA-Z0-9]+$/, '');

            // Afficher le modal et le spinner
            resultsModal.classList.remove('hidden');
            resultsModal.classList.add('flex');
            loadingSpinner.classList.remove('hidden');
            resultsContainer.innerHTML = '';

            try {
                // Générer la liste des extensions à suggérer (pour le paramètre defaults)
                const defaults = domainExtensions.map(ext => ext.ext.replace('.', '')).join('%2C');
                // Endpoint /v2/search
                const url = `https://domainr.p.rapidapi.com/v2/search?query=${encodeURIComponent(domain)}&defaults=${defaults}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'x-rapidapi-key': 'e449dd5a69msh37bea78c9b5939bp1ecee2jsn13ed9c7e40bf',
                        'x-rapidapi-host': 'domainr.p.rapidapi.com'
                    }
                });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (!data.results || !Array.isArray(data.results) || data.results.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <p>Aucun résultat trouvé pour "${domain}". Essayez un autre nom de domaine.</p>
                        </div>
                    `;
                } else {
                    // Pour chaque résultat, on va vérifier la disponibilité via /v2/status
                    const statusResults = await Promise.all(data.results.map(async (result) => {
                        const statusUrl = `https://domainr.p.rapidapi.com/v2/status?domain=${encodeURIComponent(result.domain)}`;
                        const statusResp = await fetch(statusUrl, {
                            method: 'GET',
                            headers: {
                                'x-rapidapi-key': 'e449dd5a69msh37bea78c9b5939bp1ecee2jsn13ed9c7e40bf',
                                'x-rapidapi-host': 'domainr.p.rapidapi.com'
                            }
                        });
                        let available = false;
                        if (statusResp.ok) {
                            const statusData = await statusResp.json();
                            if (statusData.status && Array.isArray(statusData.status) && statusData.status.length > 0) {
                                const st = statusData.status[0];
                                available = st.status.includes('undelegated') || st.status.includes('inactive') || st.status.includes('available');
                            }
                        }
                        // Trouver le prix selon l'extension
                        const ext = '.' + result.domain.split('.').pop();
                        const extObj = domainExtensions.find(e => e.ext === ext);
                        const price = extObj ? extObj.price : null;
                        return { domain: result.domain, available, price };
                    }));
                    resultsContainer.innerHTML = statusResults.map(res => createResultCard(res.domain, res.available, res.price)).join('');
                }
            } catch (error) {
                resultsContainer.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p>Une erreur est survenue lors de la recherche: ${error.message}. Veuillez réessayer.</p>
                    </div>
                `;
                console.error('Error:', error);
            } finally {
                loadingSpinner.classList.add('hidden');
            }
        });

        // Fermer le modal
        closeModal.addEventListener('click', () => {
            resultsModal.classList.add('hidden');
            resultsModal.classList.remove('flex');
        });

        // Fermer le modal en cliquant en dehors
        resultsModal.addEventListener('click', (e) => {
            if (e.target === resultsModal) {
                resultsModal.classList.add('hidden');
                resultsModal.classList.remove('flex');
            }
        });
    </script>
</body>

</html>
</html>