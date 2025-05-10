<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - KmerHosting.site</title>
    <meta name="description"
        content="Contactez l'équipe de KmerHosting.site pour toute question concernant nos services d'hébergement web ou pour obtenir de l'aide technique.">

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

    <!-- Custom styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #004a6e 80%);
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
                    <a href="index.php#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="index.php#technologies" class="text-kmerblue hover:text-kmergreen font-medium">Technologies</a>
                    <a href="index.php#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="domaines.php" class="text-kmerblue hover:text-kmergreen font-medium">Nom de Domaines</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                </nav>

                <!-- CTA Button -->
                <a href="login.php" class="hidden md:inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    Se connecter / S'inscrire
                </a>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden mt-4 pb-2">
                <div class="flex flex-col space-y-3">
                    <a href="index.php#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="index.php#technologies" class="text-kmerblue hover:text-kmergreen font-medium">Technologies</a>
                    <a href="index.php#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="domaines.php" class="text-kmerblue hover:text-kmergreen font-medium">Nom de Domaines</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                    <a href="login.php" class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-center">
                        Se connecter / S'inscrire
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <!-- Page Header -->
        <section class="gradient-bg text-white py-12">
            <div class="container mx-auto px-4 text-center">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Contactez-nous</h1>
                <p class="text-xl max-w-2xl mx-auto">Nous sommes là pour répondre à toutes vos questions et vous aider à trouver la solution d'hébergement parfaite pour votre projet.</p>
            </div>
        </section>

        <!-- Contact Information -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Contact Form -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold mb-6 text-kmerblue">Envoyez-nous un message</h2>
                        <form id="contact-form">
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 font-medium mb-2">Nom complet</label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
                            </div>

                            <div class="mb-4">
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
                            </div>

                            <div class="mb-4">
                                <label for="subject" class="block text-gray-700 font-medium mb-2">Sujet</label>
                                <input type="text" id="subject" name="subject" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen">
                            </div>

                            <div class="mb-6">
                                <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                                <textarea id="message" name="message" rows="5" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kmergreen"></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                                Envoyer le message
                            </button>
                        </form>
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <h2 class="text-2xl font-bold mb-6 text-kmerblue">Nos coordonnées</h2>
                        
                        <div class="bg-kmergreen bg-opacity-10 p-4 rounded-lg mb-6 border-l-4 border-kmergreen">
                            <p class="font-bold text-kmergreen mb-2">Support disponible 24h/24</p>
                            <p class="text-gray-700">Réponse garantie en moins de 2 heures</p>
                            <p class="text-gray-700 mt-2">Si vous n'avez pas de réponse dans les 2 heures, contactez-nous directement sur WhatsApp au <a href="https://wa.me/237694193493" class="text-kmergreen font-bold">+237 694 193 493</a> ou <a href="https://wa.me/237650500018" class="text-kmergreen font-bold">+237 650 500 018</a></p>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="bg-kmergreen bg-opacity-10 p-3 rounded-full mr-4">
                                    <i class="fas fa-map-marker-alt text-kmergreen"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Adresse</h3>
                                    <p class="text-gray-600">Yaoundé, Cameroun</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-kmergreen bg-opacity-10 p-3 rounded-full mr-4">
                                    <i class="fas fa-envelope text-kmergreen"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                                    <p class="text-gray-600">contact@toscanisoft.cm</p>
                                    <p class="text-gray-600">contact@kmerhosting.site</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-kmergreen bg-opacity-10 p-3 rounded-full mr-4">
                                    <i class="fas fa-phone-alt text-kmergreen"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Téléphone</h3>
                                    <p class="text-gray-600">+237 694 193 493</p>
                                    <p class="text-gray-600">+237 650 500 018</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-kmergreen bg-opacity-10 p-3 rounded-full mr-4">
                                    <i class="fas fa-clock text-kmergreen"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Heures d'ouverture</h3>
                                    <p class="text-gray-600">Lundi - Samedi: 8h - 18h</p>
                                    <p class="text-gray-600">Support technique disponible en permanence (24h/24)</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-kmergreen bg-opacity-10 p-3 rounded-full mr-4">
                                    <i class="fab fa-whatsapp text-kmergreen"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">WhatsApp</h3>
                                    <p class="text-gray-600">+237 694 193 493</p>
                                    <a href="https://wa.me/237694193493" class="text-kmergreen hover:text-kmergreen-dark font-medium">
                                        Discuter sur WhatsApp <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="mt-8">
                            <h3 class="font-semibold text-gray-800 mb-3">Suivez-nous</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="bg-kmerblue text-white p-3 rounded-full hover:bg-kmerblue-light transition duration-300">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="bg-kmerblue text-white p-3 rounded-full hover:bg-kmerblue-light transition duration-300">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="bg-kmerblue text-white p-3 rounded-full hover:bg-kmerblue-light transition duration-300">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="bg-kmerblue text-white p-3 rounded-full hover:bg-kmerblue-light transition duration-300">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-kmerblue">Questions fréquentes</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Trouvez rapidement des réponses aux questions les plus courantes sur nos services d'hébergement.</p>
                </div>

                <div class="max-w-3xl mx-auto">
                    <div class="space-y-6">
                        <!-- FAQ Item 1 -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-3 text-kmerblue">Comment puis-je migrer mon site vers KmerHosting?</h3>
                            <p class="text-gray-600">Nous offrons un service de migration gratuit pour tous les nouveaux clients. Notre équipe technique se chargera de transférer votre site web, vos emails et vos bases de données vers nos serveurs sans temps d'arrêt.</p>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-3 text-kmerblue">Quels sont les moyens de paiement acceptés?</h3>
                            <p class="text-gray-600">Nous acceptons les paiements via Orange Money et MTN Mobile Money. Les paiements sont automatiques, fiables et sécurisés.</p>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-3 text-kmerblue">Comment fonctionne le support technique?</h3>
                            <p class="text-gray-600">Notre support technique est disponible 24h/24 et 7j/7. Vous pouvez nous contacter par email, téléphone ou WhatsApp. Nous garantissons une réponse en moins de 2 heures. Si vous n'avez pas de réponse dans ce délai, contactez-nous directement sur WhatsApp au +237 694 193 493.</p>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-3 text-kmerblue">Quelle est la garantie de disponibilité de vos serveurs?</h3>
                            <p class="text-gray-600">Nous offrons une garantie de disponibilité de 99.9% pour tous nos services d'hébergement. En cas de non-respect de cette garantie, nous vous offrons un crédit proportionnel à la durée de l'indisponibilité.</p>
                        </div>

                        <!-- FAQ Item 5 -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-3 text-kmerblue">Comment installer WordPress sur mon hébergement?</h3>
                            <p class="text-gray-600">L'installation de WordPress est très simple avec notre outil d'installation en 1 clic disponible dans votre panneau de contrôle DirectAdmin. Vous pouvez également opter pour nos packs WordPress optimisés qui incluent une installation pré-configurée pour des performances optimales.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Success Modal -->
    <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <div class="text-center">
                <div class="text-kmergreen text-5xl mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 text-kmerblue">Message Envoyé!</h3>
                <p class="text-gray-600 mb-6">Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.</p>
                <button id="close-success-modal"
                    class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-kmerblue text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and About -->
                <div>
                    <img src="assets/images/logo.png" alt="KmerHosting.site" class="h-16 mb-4">
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
                        <li><a href="index.php#services" class="text-gray-300 hover:text-white">Nos Services</a></li>
                        <li><a href="index.php#technologies" class="text-gray-300 hover:text-white">Technologies</a></li>
                        <li><a href="index.php#packages" class="text-gray-300 hover:text-white">Nos Packs</a></li>
                        <li><a href="domaines.php" class="text-gray-300 hover:text-white">Nom de Domaines</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Nos Services</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php#services" class="text-gray-300 hover:text-white">Hébergement Web</a></li>
                        <li><a href="index.php#services" class="text-gray-300 hover:text-white">Hébergement WordPress</a></li>
                        <li><a href="domaines.php" class="text-gray-300 hover:text-white">Noms de Domaine</a></li>
                        <li><a href="index.php#services" class="text-gray-300 hover:text-white">Certificats SSL</a></li>
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
                <p>&copy; 2025 KmerHosting.site. Un produit de <a class="text-kmergreen font-bold" href="https://toscanisoft.site">Toscanisoft</a>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Form submission
            const contactForm = document.getElementById('contact-form');
            const successModal = document.getElementById('success-modal');
            const closeSuccessModal = document.getElementById('close-success-modal');

            if (contactForm) {
                contactForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // In a real application, you would send this data to a server
                    // For this static version, we'll just show a success message
                    successModal.classList.remove('hidden');
                    this.reset();
                });
            }

            // Close success modal
            if (closeSuccessModal) {
                closeSuccessModal.addEventListener('click', function () {
                    successModal.classList.add('hidden');
                });
            }

            // Newsletter form
            const newsletterForm = document.getElementById('newsletter-form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // In a real application, you would send this data to a server
                    // For this static version, we'll just show an alert
                    alert('Merci de vous être inscrit à notre newsletter!');
                    this.reset();
                });
            }
        });
    </script>
</body>

</html>
