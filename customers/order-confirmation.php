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

// Vérifier si l'ID de commande est fourni
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: dashboard.php');
    exit;
}

$order_id = intval($_GET['order_id']);
$order = getOrderDetails($order_id);

// Vérifier si la commande existe et appartient à l'utilisateur
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit;
}

// Récupérer le crédit disponible de l'utilisateur
$user_id = $_SESSION['user_id'];
$userCredit = getUserCredit($user_id);

// Récupérer les articles du panier et calculer le total
$cartCount = getCartItemCount($user_id);

// Vérifier si la facture a déjà été générée
$invoice_filename = '../invoices/invoice_' . $order_id . '.pdf';
// Utiliser un chemin absolu au lieu d'un chemin relatif
$invoice_filename = dirname(__DIR__) . '/invoices/invoice_' . $order_id . '.pdf';
$invoice_generated = false;
$invoice_sent = false;

// Si la facture n'existe pas encore, la générer
if (!file_exists($invoice_filename)) {
    // Générer la facture PDF avec TCPDF
    require_once '../vendor/autoload.php';

    // Créer un nouveau document PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Définir les informations du document
    $pdf->SetCreator('KmerHosting');
    $pdf->SetAuthor('KmerHosting');
    $pdf->SetTitle('Facture #' . $order_id);
    $pdf->SetSubject('Facture KmerHosting');
    $pdf->SetKeywords('Facture, KmerHosting, Commande');

    // Supprimer les en-têtes et pieds de page par défaut
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Définir les marges
    $pdf->SetMargins(15, 15, 15);

    // Ajouter une page
    $pdf->AddPage();

    // Logo
    $pdf->Image('../assets/images/logo.png', 15, 15, 50, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Titre
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 20, '', 0, 1, 'R');
    $pdf->Cell(0, 10, 'FACTURE', 0, 1, 'R');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'N° ' . $order_id . ' - ' . date('d/m/Y', strtotime($order['created_at'])), 0, 1, 'R');
    $pdf->Ln(10);

    // Informations client et entreprise
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(90, 10, 'FACTURÉ À:', 0, 0);
    $pdf->Cell(90, 10, 'ÉMIS PAR:', 0, 1);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(90, 6, $order['fullname'], 0, 0);
    $pdf->Cell(90, 6, 'KmerHosting', 0, 1);
    $pdf->Cell(90, 6, $order['email'], 0, 0);
    $pdf->Cell(90, 6, 'Douala, Cameroun', 0, 1);
    $pdf->Cell(90, 6, '', 0, 0);
    $pdf->Cell(90, 6, 'Email: contact@kmerhosting.site', 0, 1);
    $pdf->Cell(90, 6, '', 0, 0);
    $pdf->Cell(90, 6, 'Web: www.kmerhosting.site', 0, 1);
    $pdf->Ln(10);

    // Tableau des articles
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(80, 8, 'PRODUIT', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'PRIX', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'QUANTITÉ', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'TOTAL', 1, 1, 'C', true);

    $pdf->SetFont('helvetica', '', 10);
    foreach ($order['items'] as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $pdf->Cell(80, 8, $item['product_name'], 1, 0, 'L');
        $pdf->Cell(30, 8, number_format($item['price'], 0, ',', ' ') . ' FCFA', 1, 0, 'R');
        $pdf->Cell(30, 8, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(40, 8, number_format($itemTotal, 0, ',', ' ') . ' FCFA', 1, 1, 'R');
    }

    // Total
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(140, 8, 'TOTAL', 1, 0, 'R');
    $pdf->Cell(40, 8, number_format($order['total_amount'], 0, ',', ' ') . ' FCFA', 1, 1, 'R');
    $pdf->Ln(10);

    // Informations de paiement
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, 'INFORMATIONS DE PAIEMENT', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 8, 'Méthode de paiement:', 0, 0);
    $pdf->Cell(0, 8, ucfirst($order['payment_method']), 0, 1);
    $pdf->Cell(40, 8, 'Statut:', 0, 0);
    $pdf->Cell(0, 8, ucfirst($order['status']), 0, 1);
    $pdf->Cell(40, 8, 'Date:', 0, 0);
    $pdf->Cell(0, 8, date('d/m/Y H:i', strtotime($order['created_at'])), 0, 1);
    $pdf->Ln(10);

    // Note de remerciement
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 8, 'Merci pour votre confiance. Pour toute question concernant cette facture, veuillez nous contacter.', 0, 1, 'C');
    $pdf->Cell(0, 8, 'KmerHosting - Votre partenaire d\'hébergement web au Cameroun', 0, 1, 'C');

    // Générer le PDF et l'enregistrer
    $pdf->Output($invoice_filename, 'F');
    // S'assurer que le dossier invoices existe
    $invoices_dir = dirname(__DIR__) . '/invoices';
    if (!is_dir($invoices_dir)) {
        mkdir($invoices_dir, 0755, true);
    }
    $pdf->Output($invoice_filename, 'F');
    $invoice_generated = true;

    // Envoyer la facture par email
    require_once '../vendor/autoload.php';
    
    // Créer une nouvelle instance de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configuration du serveur
        $mail->isSMTP();
        $mail->Host       = 'mail.kmerhosting.site'; // Remplacez par votre serveur SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@kmerhosting.site'; // Remplacez par votre adresse email
        $mail->Password   = 'password4321go'; // Remplacez par votre mot de passe
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Destinataires
        $mail->setFrom('noreply@kmerhosting.site', 'KmerHosting');
        $mail->addAddress($order['email'], $order['fullname']);
        
        // Pièce jointe
        $mail->addAttachment($invoice_filename, 'facture_' . $order_id . '.pdf');
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Votre facture KmerHosting #' . $order_id;
        $mail->Body    = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="text-align: center; padding: 20px 0;">
                    <img src="https://kmerhosting.site/assets/images/logo.png" alt="KmerHosting Logo" style="max-width: 200px;">
                </div>
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px;">
                    <h2 style="color: #0e7490;">Merci pour votre commande!</h2>
                    <p>Bonjour ' . htmlspecialchars($order['fullname']) . ',</p>
                    <p>Nous vous remercions pour votre commande. Votre facture est jointe à cet email.</p>
                    <p><strong>Numéro de commande:</strong> #' . $order_id . '</p>
                    <p><strong>Date:</strong> ' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</p>
                    <p><strong>Total:</strong> ' . number_format($order['total_amount'], 0, ',', ' ') . ' FCFA</p>
                    <p>Vous pouvez gérer vos services depuis votre <a href="https://kmerhosting.site/customers/dashboard.php" style="color: #0e7490;">tableau de bord</a>.</p>
                </div>
                <div style="text-align: center; padding: 20px 0; color: #6c757d; font-size: 12px;">
                    <p>© ' . date('Y') . ' KmerHosting. Tous droits réservés.</p>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        ';
        
        $mail->send();
        $invoice_sent = true;
    } catch (Exception $e) {
        // Enregistrer l'erreur mais continuer l'exécution
        error_log("Erreur d'envoi d'email: " . $mail->ErrorInfo);
    }
} else {
    // La facture existe déjà
    $invoice_generated = true;
    $invoice_sent = true;
}

// Pour le menu actif
$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande - KmerHosting</title>
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
                    <h1 class="text-2xl font-bold text-white mb-2">Confirmation de commande</h1>
                    <p class="text-gray-400">Votre commande a été traitée avec succès.</p>
                </div>

                <!-- Confirmation Message -->
                <div class="bg-green-900/20 border border-green-700/30 rounded-lg p-5 mb-6">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                        <h2 class="text-xl font-semibold text-white">Commande confirmée</h2>
                    </div>
                    <p class="text-gray-300 mb-2">Votre commande #<?php echo $order_id; ?> a été traitée avec succès. Merci pour votre achat!</p>
                    <p class="text-gray-400">Date: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    <?php if ($invoice_sent): ?>
                    <p class="text-gray-300 mt-2"><i class="fas fa-envelope text-kmergreen mr-2"></i> Une facture a été envoyée à votre adresse email.</p>
                    <?php endif; ?>
                    <?php if ($invoice_generated): ?>
                    <div class="mt-4">
                        <a href="../invoices/invoice_<?php echo $order_id; ?>.pdf" target="_blank" class="inline-flex items-center px-4 py-2 bg-kmergreen hover:bg-kmergreen-dark text-white rounded-lg transition-colors duration-300">
                            <a href="/kmerhosting/invoices/invoice_<?php echo $order_id; ?>.pdf" target="_blank" class="inline-flex items-center px-4 py-2 bg-kmergreen hover:bg-kmergreen-dark text-white rounded-lg transition-colors duration-300">
                            <i class="fas fa-file-pdf mr-2"></i> Télécharger la facture
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Order Details -->
                <div class="bg-dark-800 rounded-lg border border-dark-700 mb-6">
                    <div class="p-5 border-b border-dark-700">
                        <h2 class="text-lg font-semibold text-white">Détails de la commande</h2>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-400 mb-2">Informations client</h3>
                                <p class="text-white"><?php echo htmlspecialchars($order['fullname']); ?></p>
                                <p class="text-gray-300"><?php echo htmlspecialchars($order['email']); ?></p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-400 mb-2">Informations de paiement</h3>
                                <p class="text-white">Méthode: <?php echo ucfirst($order['payment_method']); ?></p>
                                <p class="text-gray-300">Statut: <?php echo ucfirst($order['status']); ?></p>
                            </div>
                        </div>

                        <h3 class="text-sm font-medium text-gray-400 mb-3">Articles commandés</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-gray-400 text-sm">
                                        <th class="pb-3 font-medium">Produit</th>
                                        <th class="pb-3 font-medium">Type</th>
                                        <th class="pb-3 font-medium">Prix</th>
                                        <th class="pb-3 font-medium">Quantité</th>
                                        <th class="pb-3 font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): 
                                        $itemTotal = $item['price'] * $item['quantity'];
                                    ?>
                                        <tr class="border-t border-dark-700 text-sm">
                                            <td class="py-4">
                                                <div class="font-medium text-white"><?php echo htmlspecialchars($item['product_name'] ?? 'Produit inconnu'); ?></div>
                                            </td>
                                            <td class="py-4 text-gray-300"><?php echo ucfirst(htmlspecialchars($item['product_type'])); ?></td>
                                            <td class="py-4 text-white"><?php echo number_format($item['price'] ?? 0, 0, ',', ' '); ?> FCFA</td>
                                            <td class="py-4 text-white"><?php echo $item['quantity']; ?></td>
                                            <td class="py-4 text-white"><?php echo number_format($itemTotal ?? 0, 0, ',', ' '); ?> FCFA</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="border-t border-dark-700">
                                        <td colspan="4" class="py-4 text-right font-medium">Total:</td>
                                        <td class="py-4 text-white font-bold"><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> FCFA</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-dark-800 rounded-lg border border-dark-700">
                    <div class="p-5 border-b border-dark-700">
                        <h2 class="text-lg font-semibold text-white">Prochaines étapes</h2>
                    </div>
                    <div class="p-5">
                        <p class="text-gray-300 mb-4">Vos services ont été activés et sont maintenant disponibles. Vous pouvez les gérer depuis votre tableau de bord.</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="services.php" class="inline-block bg-kmergreen hover:bg-kmergreen-dark text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                                <i class="fas fa-server mr-2"></i> Gérer mes services
                            </a>
                            <a href="dashboard.php" class="inline-block bg-dark-700 hover:bg-dark-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i> Tableau de bord
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
        });
    </script>
</body>
</html>
