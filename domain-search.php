<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration de la base de données et les fonctions
require_once 'backend/config/db.php';
require_once 'backend/functions.php';

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$cartItemCount = $isLoggedIn ? getCartItemCount($_SESSION['user_id']) : 0;

// Récupérer toutes les extensions de domaine
$sql = "SELECT * FROM domain_packages ORDER BY price ASC";
$result = $conn->query($sql);
$allDomains = [];
while ($row = $result->fetch_assoc()) {
    $allDomains[] = $row;
}

// Sélectionner quelques domaines populaires (les 5 premiers)
$popularDomains = array_slice($allDomains, 0, 5);

// Titre de la page
$pageTitle = "Recherche de Domaines - KmerHosting";

require_once 'includes/domain_search_header.php';
?>

<!-- Main Content -->
<main class="flex-grow">
    <!-- Hero Section avec animation futuriste -->
    <section class="relative bg-gradient-to-r from-kmerblue to-kmerblue-dark text-white py-16 overflow-hidden">
        <!-- Particules d'arrière-plan -->
        <div id="particles-js" class="absolute inset-0 z-0"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white">Trouvez le Domaine Parfait</h1>
                <p class="text-xl mb-8 text-blue-100">Vérifiez la disponibilité de votre nom de domaine et sécurisez-le dès maintenant.</p>
                
                <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-lg p-8 mb-8 border border-white/20">
                    <form id="domain-search-form" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-grow relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-blue-300"></i>
                            </div>
                            <input type="text" id="domain-name" name="domain" 
                                placeholder="Entrez votre nom de domaine" 
                                class="w-full pl-12 pr-4 py-4 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-kmergreen focus:border-transparent transition-all duration-300">
                            <div class="domain-input-animation"></div>
                        </div>
                        <button type="submit" class="cyber-button bg-kmergreen hover:bg-kmergreen-dark text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center group">
                            <span class="mr-2">Rechercher</span>
                            <i class="fas fa-arrow-right transition-transform duration-300 group-hover:translate-x-1"></i>
                        </button>
                    </form>
                </div>
                
                <div class="flex flex-wrap justify-center gap-3">
                    <?php foreach ($popularDomains as $domain): ?>
                    <span class="bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer domain-tag" data-extension="<?php echo $domain['extension']; ?>">
                        <?php echo $domain['extension']; ?> - <?php echo number_format($domain['price'], 0, ',', ' '); ?> FCFA
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Domain Extensions Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 text-kmerblue">Extensions de Domaine Disponibles</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Choisissez parmi une large gamme d'extensions de domaine pour votre site web.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($allDomains as $domain): ?>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-kmergreen domain-card">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-kmerblue"><?php echo $domain['extension']; ?></h3>
                            <span class="text-kmergreen font-bold"><?php echo number_format($domain['price'], 0, ',', ' '); ?> FCFA</span>
                        </div>
                        
                        <p class="text-gray-600 mb-4"><?php echo $domain['description']; ?></p>
                        
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-700 mb-2">Caractéristiques :</h4>
                            <ul class="space-y-2">
                                <?php 
                                $features = explode(',', $domain['features']);
                                foreach ($features as $feature): 
                                ?>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-kmergreen mr-2"></i>
                                    <span class="text-gray-600"><?php echo $feature; ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm text-gray-500">Renouvellement : <?php echo number_format($domain['renewal_price'], 0, ',', ' '); ?> FCFA</span>
                            </div>
                            <button type="button" class="search-with-extension bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 transform hover:scale-105" data-extension="<?php echo $domain['extension']; ?>">
                                Rechercher
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4 text-kmerblue">Pourquoi Choisir KmerHosting pour Votre Domaine ?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Nous offrons des services de domaine fiables et abordables avec un support client exceptionnel.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-kmergreen transition-all duration-300 transform hover:scale-105">
                    <div class="text-kmergreen text-4xl mb-4">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-kmerblue">Protection de la Vie Privée</h3>
                    <p class="text-gray-600">Nous protégeons vos informations personnelles avec notre service de protection WHOIS gratuit inclus avec chaque domaine.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-kmergreen transition-all duration-300 transform hover:scale-105">
                    <div class="text-kmergreen text-4xl mb-4">
                        <i class="fas fa-server"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-kmerblue">DNS Management</h3>
                    <p class="text-gray-600">Gérez facilement vos enregistrements DNS avec notre interface intuitive et nos serveurs DNS rapides et fiables.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-kmergreen transition-all duration-300 transform hover:scale-105">
                    <div class="text-kmergreen text-4xl mb-4">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-kmerblue">Redirection Email</h3>
                    <p class="text-gray-600">Créez des adresses email professionnelles avec votre domaine et redirigez-les vers votre boîte de réception existante.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-kmergreen transition-all duration-300 transform hover:scale-105">
                    <div class="text-kmergreen text-4xl mb-4">
                        <i class="fas fa-sync"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-kmerblue">Renouvellement Automatique</h3>
                    <p class="text-gray-600">Ne perdez jamais votre domaine grâce à notre service de renouvellement automatique qui garantit que votre domaine reste actif.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-kmergreen transition-all duration-300 transform hover:scale-105">
                    <div class="text-kmergreen text-4xl mb-4">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-kmerblue">Support 24/7</h3>
                    <p class="text-gray-600">Notre équipe de support client est disponible 24/7 pour vous aider avec toutes vos questions concernant votre domaine.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-kmergreen transition-all duration-300 transform hover:scale-105">
                    <div class="text-kmergreen text-4xl mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-kmerblue">Sécurité Avancée</h3>
                    <p class="text-gray-600">Protégez votre domaine contre les transferts non autorisés et le vol de domaine avec nos fonctionnalités de sécurité avancées.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Results Modal avec design futuriste -->
<div id="results-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto border border-white/20 text-white">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-white">Résultats pour <span id="search-domain-name" class="text-kmergreen"></span></h3>
            <button id="close-modal" class="text-white/70 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Loading Spinner -->
        <div id="loading-spinner" class="hidden">
            <div class="flex justify-center items-center py-8">
                <div class="cyber-spinner"></div>
            </div>
        </div>

        <!-- Results Container -->
        <div id="results-container" class="space-y-4">
            <!-- Results will be inserted here -->
        </div>
    </div>
</div>

<!-- Ajout des scripts nécessaires -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>

<style>
.cyber-button {
    position: relative;
    overflow: hidden;
}

.cyber-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
}

.cyber-button:hover::before {
    left: 100%;
}

.domain-input-animation {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    width: 0;
    background: linear-gradient(90deg, #10b981, #004a6e);
    transition: width 0.3s ease;
}

#domain-name:focus + .domain-input-animation {
    width: 100%;
}

.domain-card {
    transform: perspective(1000px) rotateY(0deg);
    transition: transform 0.5s ease;
}

.domain-card:hover {
    transform: perspective(1000px) rotateY(5deg) translateY(-5px);
}

.cyber-spinner {
    width: 50px;
    height: 50px;
    border: 3px solid transparent;
    border-top-color: #10b981;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    position: relative;
}

.cyber-spinner::before, .cyber-spinner::after {
    content: '';
    position: absolute;
    border: 3px solid transparent;
    border-radius: 50%;
}

.cyber-spinner::before {
    top: 5px;
    left: 5px;
    right: 5px;
    bottom: 5px;
    border-top-color: #004a6e;
    animation: spin 2s linear infinite;
}

.cyber-spinner::after {
    top: 15px;
    left: 15px;
    right: 15px;
    bottom: 15px;
    border-top-color: #10b981;
    animation: spin 1.5s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Initialisation des particules
    particlesJS('particles-js', {
        "particles": {
            "number": {
                "value": 80,
                "density": {
                    "enable": true,
                    "value_area": 800
                }
            },
            "color": {
                "value": "#ffffff"
            },
            "shape": {
                "type": "circle",
                "stroke": {
                    "width": 0,
                    "color": "#000000"
                },
                "polygon": {
                    "nb_sides": 5
                }
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {
                    "enable": false,
                    "speed": 1,
                    "opacity_min": 0.1,
                    "sync": false
                }
            },
            "size": {
                "value": 3,
                "random": true,
                "anim": {
                    "enable": false,
                    "speed": 40,
                    "size_min": 0.1,
                    "sync": false
                }
            },
            "line_linked": {
                "enable": true,
                "distance": 150,
                "color": "#ffffff",
                "opacity": 0.4,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 6,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {
                    "enable": false,
                    "rotateX": 600,
                    "rotateY": 1200
                }
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "repulse"
                },
                "onclick": {
                    "enable": true,
                    "mode": "push"
                },
                "resize": true
            },
            "modes": {
                "grab": {
                    "distance": 400,
                    "line_linked": {
                        "opacity": 1
                    }
                },
                "bubble": {
                    "distance": 400,
                    "size": 40,
                    "duration": 2,
                    "opacity": 8,
                    "speed": 3
                },
                "repulse": {
                    "distance": 200,
                    "duration": 0.4
                },
                "push": {
                    "particles_nb": 4
                },
                "remove": {
                    "particles_nb": 2
                }
            }
        },
        "retina_detect": true
    });

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Domain search form handling
    const domainSearchForm = document.getElementById('domain-search-form');
    const domainSearchInput = document.getElementById('domain-name');
    const resultsModal = document.getElementById('results-modal');
    const closeModal = document.getElementById('close-modal');
    const loadingSpinner = document.getElementById('loading-spinner');
    const resultsContainer = document.getElementById('results-container');
    const searchDomainName = document.getElementById('search-domain-name');

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
        <?php foreach ($allDomains as $domain): ?>
        { ext: '<?php echo $domain['extension']; ?>', price: <?php echo $domain['price']; ?> },
        <?php endforeach; ?>
    ];
    const currency = 'FCFA';
    const period = 'an';

    // Fonction pour créer une carte de résultat avec prix
    function createResultCard(domain, available, price = null) {
        return `
            <div class="bg-white/10 backdrop-blur-sm p-6 rounded-xl border border-white/20 transition-all duration-300 hover:bg-white/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-xl font-bold text-white">${domain}</h4>
                        <p class="text-blue-200">
                            ${available ? 'Ce domaine est disponible !' : 'Ce domaine n\'est pas disponible.'}
                        </p>
                    </div>
                    ${price !== null ? `<div class="text-right">
                        <div class="text-kmergreen font-bold text-xl">${formatPrice(price)} ${currency}</div>
                        <div class="text-blue-200">/${period}</div>
                    </div>` : ''}
                </div>
                ${available ? `
                    <button onclick="addDomainToCart('${domain}', ${price})" 
                            class="add-to-cart-btn mt-4 w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-3 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-cart-plus mr-2"></i>
                        Ajouter au panier
                    </button>
                ` : ''}
            </div>
        `;
    }

    // Ajouter cette fonction JavaScript pour gérer l'ajout au panier
    window.addDomainToCart = function(domain, price) {
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
                    timer: 1500,
                    background: 'rgba(255, 255, 255, 0.9)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        title: 'text-kmerblue',
                        content: 'text-gray-700'
                    }
                });
                
                // Mettre à jour le compteur du panier dans le header
                const cartCountBadges = document.querySelectorAll('.cart-count-badge');
                if (cartCountBadges.length > 0) {
                    cartCountBadges.forEach(badge => {
                        badge.textContent = data.cart_count;
                        badge.classList.remove('hidden');
                    });
                }
                
                // Fermer la modal après l'ajout
                setTimeout(() => {
                    resultsModal.classList.add('hidden');
                    resultsModal.classList.remove('flex');
                }, 1500);
            } else {
                throw new Error(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message || 'Une erreur est survenue lors de l\'ajout au panier.',
                background: 'rgba(255, 255, 255, 0.9)',
                backdrop: 'rgba(0, 0, 0, 0.4)',
                customClass: {
                    title: 'text-red-600',
                    content: 'text-gray-700'
                }
            });
        });
    };

    // Gestionnaire de soumission du formulaire
    if (domainSearchForm) {
        domainSearchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            let domain = domainSearchInput.value.trim();
            if (!domain) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention',
                    text: 'Veuillez entrer un nom de domaine',
                    background: 'rgba(255, 255, 255, 0.9)',
                    backdrop: 'rgba(0, 0, 0, 0.4)',
                    customClass: {
                        title: 'text-yellow-600',
                        content: 'text-gray-700'
                    }
                });
                return;
            }
            
            // Nettoyer le nom (enlever extension si présente)
            domain = domain.replace(/\.[a-zA-Z0-9]+$/, '');

            // Afficher le modal et le spinner
            resultsModal.classList.remove('hidden');
            resultsModal.classList.add('flex');
            loadingSpinner.classList.remove('hidden');
            resultsContainer.innerHTML = '';
            
            // Mettre à jour le titre de la modal
            if (searchDomainName) {
                searchDomainName.textContent = domain;
            }

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
                        <div class="bg-yellow-500/20 border border-yellow-500/30 text-yellow-200 px-4 py-3 rounded-lg">
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
                    
                    // Trier les résultats : d'abord les disponibles, puis par prix
                    statusResults.sort((a, b) => {
                        if (a.available !== b.available) {
                            return a.available ? -1 : 1;
                        }
                        return (a.price || 0) - (b.price || 0);
                    });
                    
                    resultsContainer.innerHTML = statusResults.map(res => createResultCard(res.domain, res.available, res.price)).join('');
                }
            } catch (error) {
                resultsContainer.innerHTML = `
                    <div class="bg-red-500/20 border border-red-500/30 text-red-200 px-4 py-3 rounded-lg">
                        <p>Une erreur est survenue lors de la recherche: ${error.message}. Veuillez réessayer.</p>
                    </div>
                `;
                console.error('Error:', error);
            } finally {
                loadingSpinner.classList.add('hidden');
            }
        });
    }

    // Fermer le modal
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            resultsModal.classList.add('hidden');
            resultsModal.classList.remove('flex');
        });
    }

    // Fermer le modal en cliquant en dehors
    if (resultsModal) {
        resultsModal.addEventListener('click', (e) => {
            if (e.target === resultsModal) {
                resultsModal.classList.add('hidden');
                resultsModal.classList.remove('flex');
            }
        });
    }
    
    // Gestionnaire d'événement pour les boutons de recherche avec extension
    const searchWithExtensionButtons = document.querySelectorAll('.search-with-extension');
    if (searchWithExtensionButtons.length > 0) {
        searchWithExtensionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const extension = this.getAttribute('data-extension');
                
                // Faire défiler jusqu'au formulaire de recherche
                if (domainSearchForm) {
                    domainSearchForm.scrollIntoView({ behavior: 'smooth' });
                }
                
                // Mettre le focus sur le champ de recherche
                setTimeout(() => {
                    if (domainSearchInput) {
                        domainSearchInput.focus();
                        
                        // Si un domaine est déjà saisi, lancer la recherche
                        const domain = domainSearchInput.value.trim();
                        if (domain) {
                            // Simuler la soumission du formulaire
                            domainSearchForm.dispatchEvent(new Event('submit'));
                        }
                    }
                }, 500);
            });
        });
    }
    
    // Gestionnaire d'événement pour les tags de domaine
    const domainTags = document.querySelectorAll('.domain-tag');
    if (domainTags.length > 0) {
        domainTags.forEach(tag => {
            tag.addEventListener('click', function() {
                const extension = this.getAttribute('data-extension');
                
                // Faire défiler jusqu'au formulaire de recherche
                if (domainSearchForm) {
                    domainSearchForm.scrollIntoView({ behavior: 'smooth' });
                }
                
                // Mettre le focus sur le champ de recherche
                setTimeout(() => {
                    if (domainSearchInput) {
                        domainSearchInput.focus();
                        
                        // Suggérer un exemple avec cette extension
                        domainSearchInput.value = "exemple" + extension.replace('.', '');
                        
                        // Simuler la soumission du formulaire
                        domainSearchForm.dispatchEvent(new Event('submit'));
                    }
                }, 500);
            });
        });
    }
});
</script>

<div class="text-center mt-8">
    <a href="../index.php#packages" class="inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
        Découvrir nos offres
    </a>
</div>

<?php
require_once 'includes/footer.php';
?>
