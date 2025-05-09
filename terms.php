<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions d'Utilisation - KmerHosting.site</title>
    <meta name="description" content="Conditions d'utilisation de KmerHosting.site - Les règles et conditions régissant l'utilisation de nos services.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">
    
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
    
    <!-- Custom styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center">
                    <img src="assets/images/logo.png" alt="KmerHosting.site" class="h-16">
                </a>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-kmerblue focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="/#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="/#features" class="text-kmerblue hover:text-kmergreen font-medium">Pourquoi Nous</a>
                    <a href="/#testimonials" class="text-kmerblue hover:text-kmergreen font-medium">Témoignages</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                </nav>
                
                <!-- CTA Button -->
                <a href="/#packages" class="hidden md:inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                    Commander
                </a>
            </div>
            
            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden mt-4 pb-2">
                <div class="flex flex-col space-y-3">
                    <a href="/#services" class="text-kmerblue hover:text-kmergreen font-medium">Nos Services</a>
                    <a href="/#packages" class="text-kmerblue hover:text-kmergreen font-medium">Nos Packs</a>
                    <a href="/#features" class="text-kmerblue hover:text-kmergreen font-medium">Pourquoi Nous</a>
                    <a href="/#testimonials" class="text-kmerblue hover:text-kmergreen font-medium">Témoignages</a>
                    <a href="contact.php" class="text-kmerblue hover:text-kmergreen font-medium">Contact</a>
                    <a href="/#packages" class="bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300 text-center">
                        Commander
                    </a>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-12">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
                <h1 class="text-3xl font-bold mb-6 text-kmerblue">Conditions d'Utilisation</h1>
                
                <div class="prose max-w-none">
                    <p class="mb-4">Dernière mise à jour : 7 Mai 2023</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">1. Acceptation des conditions</h2>
                    <p class="mb-4">En accédant à ou en utilisant les services de KmerHosting.site, vous acceptez d'être lié par ces conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">2. Description des services</h2>
                    <p class="mb-4">KmerHosting.site fournit des services d'hébergement web, d'enregistrement de noms de domaine et des services connexes. Nous nous réservons le droit de modifier, suspendre ou interrompre tout aspect de nos services à tout moment.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">3. Compte utilisateur</h2>
                    <p class="mb-4">Pour utiliser certains de nos services, vous devrez créer un compte. Vous êtes responsable de maintenir la confidentialité de vos informations de compte et de toutes les activités qui se produisent sous votre compte. Vous devez nous informer immédiatement de toute utilisation non autorisée de votre compte.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">4. Paiement et renouvellement</h2>
                    <p class="mb-4">Les frais pour nos services sont indiqués sur notre site web. Le paiement est dû au moment de la commande. Pour les services récurrents, votre abonnement sera automatiquement renouvelé à la fin de chaque période, sauf si vous annulez avant la date de renouvellement.</p>
                    <p class="mb-4">Nous nous réservons le droit de modifier nos tarifs à tout moment, mais les changements n'affecteront pas les abonnements déjà payés.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">5. Politique de remboursement</h2>
                    <p class="mb-4">Nous offrons une garantie de remboursement de 30 jours pour nos services d'hébergement. Si vous n'êtes pas satisfait de nos services dans les 30 premiers jours suivant votre achat, vous pouvez demander un remboursement complet.</p>
                    <p class="mb-4">Les frais d'enregistrement de nom de domaine ne sont pas remboursables.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">6. Utilisation acceptable</h2>
                    <p class="mb-4">Vous acceptez de ne pas utiliser nos services pour :</p>
                    <ul class="list-disc pl-6 mb-4">
                        <li class="mb-2">Violer les lois applicables ou les droits d'autrui</li>
                        <li class="mb-2">Distribuer des logiciels malveillants ou des virus</li>
                        <li class="mb-2">Envoyer des spams ou des communications non sollicitées</li>
                        <li class="mb-2">Héberger du contenu illégal, obscène, diffamatoire ou menaçant</li>
                        <li class="mb-2">Surcharger nos systèmes ou réseaux</li>
                    </ul>
                    <p class="mb-4">Nous nous réservons le droit de suspendre ou de résilier votre accès à nos services si vous violez ces conditions.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">7. Disponibilité et support</h2>
                    <p class="mb-4">Nous nous efforçons de maintenir une disponibilité de 99,9% pour nos services d'hébergement. Cependant, nous ne pouvons pas garantir que nos services seront ininterrompus ou sans erreur.</p>
                    <p class="mb-4">Nous fournissons un support technique 24/7 pour tous les problèmes liés à nos services.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">8. Propriété intellectuelle</h2>
                    <p class="mb-4">Tout le contenu présent sur notre site web, y compris les textes, graphiques, logos, icônes et images, est la propriété de KmerHosting.site et est protégé par les lois sur la propriété intellectuelle.</p>
                    <p class="mb-4">Vous conservez tous les droits sur le contenu que vous hébergez sur nos serveurs.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">9. Limitation de responsabilité</h2>
                    <p class="mb-4">Dans toute la mesure permise par la loi, KmerHosting.site ne sera pas responsable des dommages indirects, spéciaux, accessoires ou consécutifs résultant de l'utilisation ou de l'impossibilité d'utiliser nos services.</p>
                    <p class="mb-4">Notre responsabilité totale pour toute réclamation ne dépassera pas le montant que vous avez payé pour nos services au cours des 12 mois précédant la réclamation.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">10. Modifications des conditions</h2>
                    <p class="mb-4">Nous pouvons modifier ces conditions d'utilisation à tout moment. Les modifications entreront en vigueur dès leur publication sur notre site web. Votre utilisation continue de nos services après la publication des modifications constitue votre acceptation des nouvelles conditions.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">11. Loi applicable</h2>
                    <p class="mb-4">Ces conditions sont régies par les lois du Cameroun, sans égard aux principes de conflits de lois.</p>
                    
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">12. Nous contacter</h2>
                    <h2 class="text-xl font-semibold mt-8 mb-4 text-kmerblue">8. Nous contacter</h2>
                    <p class="mb-4">Si vous avez des questions concernant cette politique de confidentialité, veuillez nous contacter à :</p>
                    <p class="mb-4">
                        Email : <a href="mailto:contact@kmerhosting.site" class="text-kmergreen hover:underline">contact@kmerhosting.site</a><br>
                        Adresse : Yaoundé, Cameroun<br>
                        Téléphone : +237 6 94 19 34 93

                    </p>
                    <p class="mb-4">Pour toute question, n'hésitez pas à nous contacter.</p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-kmerblue text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <!-- Legal Links -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <div class="flex flex-wrap justify-center gap-4 mb-4">
                    <a href="privacy.php" class="text-gray-400 hover:text-white text-sm">Politique de Confidentialité</a>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>