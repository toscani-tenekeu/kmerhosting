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
            <p>&copy; <?php echo date('Y'); ?> KmerHosting.site. Un produit de <a class="text-kmergreen font-bold"
                    href="https://toscanisoft.site">Toscanisoft</a>. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Animation de texte avec Typed.js
        const typedText = new Typed('#typed-text', {
            strings: [
                'Des solutions d\'hébergement optimisées pour la performance et la sécurité de votre site web.',
                'Propulsez votre présence en ligne avec nos services d\'hébergement haute performance.',
                'Hébergement web rapide, sécurisé et fiable pour tous vos projets.'
            ],
            typeSpeed: 40,
            backSpeed: 20,
            backDelay: 3000,
            startDelay: 500,
            loop: true
        });

        // Animation de slogan
        const typedSlogan = new Typed('#typed-slogan', {
            strings: [
                'Votre succès, notre priorité',
                'Performance et fiabilité',
                'Sécurité et disponibilité 24/7',
                'L\'hébergement web à prix abordable',
                'Support technique réactif'
            ],
            typeSpeed: 50,
            backSpeed: 30,
            backDelay: 2000,
            startDelay: 1000,
            loop: true
        });

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Newsletter form
        const newsletterForm = document.getElementById('newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                alert('Merci de vous être inscrit à notre newsletter!');
                this.reset();
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80, // Adjust for header height
                        behavior: 'smooth'
                    });

                    // Close mobile menu if open
                    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
    });
</script>
</body>
</html> 