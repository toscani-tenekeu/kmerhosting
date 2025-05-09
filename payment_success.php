<?php
// Include necessary files
require_once __DIR__ . '/backend/config/db.php';
require_once __DIR__ . '/backend/functions.php';
require_once __DIR__ . '/api/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Get transaction details from session or query parameters
$external_id = isset($_SESSION['current_external_id']) ? $_SESSION['current_external_id'] : (isset($_GET['external_id']) ? $_GET['external_id'] : '');
$trans_id = isset($_SESSION['current_trans_id']) ? $_SESSION['current_trans_id'] : (isset($_GET['trans_id']) ? $_GET['trans_id'] : '');

// Initialize variables
$transaction = null;
$payment_status = null;

// Si nous avons des détails de transaction, vérifier le statut
if (!empty($external_id) || !empty($trans_id)) {
    try {
        if (!empty($trans_id)) {
            // Vérifier le statut du paiement avec l'API Fapshi
            $payment_status = check_payment_status($trans_id);
            
            if ($payment_status && isset($payment_status['status'])) {
                $status = $payment_status['status'];
                $external_id = $payment_status['externalId'] ?? $external_id;
                
                // Mettre à jour la transaction dans la base de données si trouvée
                if (!empty($external_id)) {
                    // Vérifier d'abord si la transaction existe et si elle a déjà été traitée
                    $sql = "SELECT * FROM payment_transactions WHERE external_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $external_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $transaction = $result->fetch_assoc();
                        
                        // Ne mettre à jour le statut que s'il est différent
                        if ($transaction['status'] !== $status) {
                            $sql = "UPDATE payment_transactions SET status = ?, trans_id = ?, updated_at = NOW() WHERE external_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sss", $status, $trans_id, $external_id);
                            $stmt->execute();
                            
                            // Si le paiement est réussi et n'a pas déjà été traité, ajouter du crédit au compte de l'utilisateur
                            if ($status === 'SUCCESSFUL' && $transaction['status'] !== 'SUCCESSFUL') {
                                process_successful_payment($external_id, $trans_id);
                            }
                        }
                    } else {
                        // Si la transaction n'existe pas encore, l'ajouter
                        $sql = "UPDATE payment_transactions SET status = ?, trans_id = ?, updated_at = NOW() WHERE external_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $status, $trans_id, $external_id);
                        $stmt->execute();
                    }
                }
            }
        }
        
        // Obtenir les détails de la transaction depuis la base de données
        if (empty($transaction) && !empty($external_id)) {
            $sql = "SELECT * FROM payment_transactions WHERE external_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $external_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $transaction = $result->fetch_assoc();
            }
        }
    } catch (Exception $e) {
        error_log('Payment success page error: ' . $e->getMessage());
    }
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_info = [];
try {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_info = $result->fetch_assoc();
    }
} catch (Exception $e) {
    error_log('Error fetching user info: ' . $e->getMessage());
}

// No header or footer included
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - KmerHosting</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --text-color: #1f2937;
            --light-bg: #f9fafb;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        
        .success-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            padding: 0;
            overflow: hidden;
        }
        
        .success-header {
            background-color: var(--primary-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .success-icon {
            background-color: white;
            color: var(--success-color);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .success-amount {
            font-size: 48px;
            font-weight: 700;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .currency {
            font-size: 24px;
            margin-left: 8px;
            font-weight: 600;
        }
        
        .success-body {
            padding: 30px;
        }
        
        .transaction-details {
            background-color: var(--light-bg);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
        }
        
        .detail-label {
            color: #6b7280;
            font-weight: 500;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
        }
        
        .btn-warning {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: white;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #f59e0b;
            border-radius: 0;
            animation: confetti 5s ease-in-out infinite;
        }
        
        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
        
        @media (max-width: 576px) {
            .success-container {
                margin: 15px;
                max-width: 100%;
            }
            
            .success-amount {
                font-size: 36px;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <?php if ($transaction && ($transaction['status'] === 'SUCCESSFUL' || ($payment_status && $payment_status['status'] === 'SUCCESSFUL'))): ?>
            <div class="success-header">
                <!-- Confetti animation -->
                <div id="confetti-container"></div>
                
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Paiement Réussi!</h2>
                <div class="success-amount">
                    <?= number_format($transaction['amount'], 0, ',', ' ') ?> <span class="currency">FCFA</span>
                </div>
                <p>Votre compte a été rechargé avec succès</p>
            </div>
            
            <div class="success-body">
                <div class="transaction-details">
                    <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Détails de la transaction</h5>
                    
                    <div class="detail-item">
                        <span class="detail-label">ID Transaction</span>
                        <span class="detail-value"><?= htmlspecialchars($trans_id) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Date</span>
                        <span class="detail-value"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Méthode</span>
                        <span class="detail-value">
                            <?php 
                            $desc = isset($transaction['description']) ? $transaction['description'] : '';
                            if ($desc && strpos(strtolower($desc), 'orange') !== false): ?>
                                <i class="fas fa-mobile-alt text-warning me-1"></i> Orange Money
                            <?php elseif ($desc && strpos(strtolower($desc), 'mtn') !== false): ?>
                                <i class="fas fa-mobile-alt text-warning me-1"></i> MTN Mobile Money
                            <?php else: ?>
                                <i class="fas fa-credit-card me-1"></i> Paiement Mobile
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Statut</span>
                        <span class="detail-value text-success">
                            <i class="fas fa-check-circle me-1"></i> Confirmé
                        </span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <a href="customers/dashboard.php" class="btn btn-success">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="customers/cart.php" class="btn btn-warning">
                        <i class="fas fa-shopping-cart"></i> Panier
                    </a>
                </div>
            </div>
            
            <!-- JavaScript to check payment status and create confetti -->
            <script>
                // Create confetti effect
                function createConfetti() {
                    const confettiContainer = document.getElementById('confetti-container');
                    const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                    
                    for (let i = 0; i < 100; i++) {
                        const confetti = document.createElement('div');
                        confetti.className = 'confetti';
                        confetti.style.left = Math.random() * 100 + '%';
                        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                        confetti.style.width = Math.random() * 10 + 5 + 'px';
                        confetti.style.height = Math.random() * 10 + 5 + 'px';
                        confetti.style.animationDuration = Math.random() * 3 + 2 + 's';
                        confetti.style.animationDelay = Math.random() * 5 + 's';
                        
                        confettiContainer.appendChild(confetti);
                    }
                }
                
                // Run confetti on page load
                document.addEventListener('DOMContentLoaded', createConfetti);
            </script>
        <?php elseif ($transaction && $transaction['status'] === 'PENDING'): ?>
            <div class="success-header" style="background-color: #f59e0b;">
                <div class="success-icon" style="color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
                <h2>Paiement en Cours</h2>
                <div class="success-amount">
                    <?= number_format($transaction['amount'], 0, ',', ' ') ?> <span class="currency">FCFA</span>
                </div>
                <p>Votre paiement est en cours de traitement</p>
            </div>
            
            <div class="success-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3">Veuillez patienter pendant que nous vérifions votre paiement...</p>
                </div>
                
                <div class="transaction-details">
                    <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Détails de la transaction</h5>
                    
                    <div class="detail-item">
                        <span class="detail-label">ID Transaction</span>
                        <span class="detail-value"><?= htmlspecialchars($trans_id) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Date</span>
                        <span class="detail-value"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Statut</span>
                        <span class="detail-value text-warning">
                            <i class="fas fa-clock me-1"></i> En attente
                        </span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <a href="customers/dashboard.php" class="btn btn-success">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="customers/cart.php" class="btn btn-warning">
                        <i class="fas fa-shopping-cart"></i> Panier
                    </a>
                </div>
            </div>
            
            <!-- JavaScript to check payment status -->
            <script>
                // Function to check payment status
                function checkPaymentStatus() {
                    const externalId = '<?= htmlspecialchars($external_id) ?>';
                    const transId = '<?= htmlspecialchars($trans_id) ?>';
                    
                    if (!externalId && !transId) return;
                    
                    fetch(`api/check_payment.php?${transId ? 'transId=' + transId : ''}&${externalId ? 'externalId=' + externalId : ''}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.status === 'SUCCESSFUL') {
                                // Reload page to show success message
                                window.location.reload();
                            } else if (data.success && (data.status === 'FAILED' || data.status === 'EXPIRED')) {
                                // Redirect to failure page
                                window.location.href = 'payment_failure.php';
                            }
                        })
                        .catch(error => {
                            console.error('Error checking payment status:', error);
                        });
                }
                
                // Check status every 5 seconds
                const statusInterval = setInterval(checkPaymentStatus, 5000);
                
                // Initial check
                checkPaymentStatus();
                
                // Clear interval when page is unloaded
                window.addEventListener('beforeunload', () => {
                    clearInterval(statusInterval);
                });
            </script>
        <?php else: ?>
            <div class="success-header" style="background-color: #ef4444;">
                <div class="success-icon" style="color: #ef4444;">
                    <i class="fas fa-times"></i>
                </div>
                <h2>Paiement Non Trouvé</h2>
                <p>Nous n'avons pas pu trouver les détails de votre paiement</p>
            </div>
            
            <div class="success-body">
                <p class="text-center mb-4">Veuillez vérifier votre compte pour voir si le crédit a été ajouté ou contacter notre support.</p>
                
                <div class="action-buttons">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <a href="customers/dashboard.php" class="btn btn-success">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="customers/cart.php" class="btn btn-warning">
                        <i class="fas fa-shopping-cart"></i> Panier
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
/**
 * Function to check payment status with Fapshi API
 */
function check_payment_status($transaction_id) {
    $url = FAPSHI_BASE_URL . "/payment-status/" . $transaction_id;
    
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "apiuser: " . FAPSHI_API_USER,
        "apikey: " . FAPSHI_API_KEY
    ));
    
    // Disable SSL verification for development
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        error_log('Fapshi API cURL Error: ' . curl_error($ch));
        return false;
    }
    
    curl_close($ch);
    
    // Decode the response
    $decoded = json_decode($response, true);
    
    // Check if JSON parsing failed
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON parsing error: ' . json_last_error_msg() . ' - Raw response: ' . $response);
        return false;
    }
    
    // If status is SUCCESSFUL, update the database
    if (isset($decoded['status']) && $decoded['status'] === 'SUCCESSFUL') {
        global $conn;
        $externalId = $decoded['externalId'] ?? '';
        
        if (!empty($externalId)) {
            // Update transaction status to SUCCESSFUL
            $status = 'SUCCESSFUL';
            $sql = "UPDATE payment_transactions SET status = ?, updated_at = NOW() WHERE external_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $status, $externalId);
            $stmt->execute();
            
            // Process the successful payment
            process_successful_payment($externalId, $transaction_id);
        }
    }
    
    return $decoded;
}

/**
 * Process a successful payment
 */
function process_successful_payment($externalId, $transId) {
    global $conn;
    
    // Find transaction in database
    $sql = "SELECT * FROM payment_transactions WHERE external_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $externalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        $user_id = $transaction['user_id'];
        $amount = $transaction['amount'];
        
        // IMPORTANT: Check if this transaction has already been processed
        // If status is already SUCCESSFUL, don't process it again
        if ($transaction['status'] === 'SUCCESSFUL') {
            error_log("Transaction $externalId already processed. Skipping credit update.");
            return false;
        }
        
        // Update transaction status to SUCCESSFUL
        $status = 'SUCCESSFUL';
        $sql = "UPDATE payment_transactions SET status = ?, updated_at = NOW() WHERE external_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $status, $externalId);
        $stmt->execute();
        
        // Get current user credit
        $current_credit = getUserCredit($user_id);
        $new_credit = $current_credit + $amount;
        
        // Update user credit
        updateUserCredit($user_id, $new_credit);
        
        // Log the credit addition
        $sql = "INSERT INTO credit_logs (user_id, amount, type, description, created_at) 
                VALUES (?, ?, 'recharge', ?, NOW())";
        $description = "Recharge via Fapshi (TransID: $transId)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ids", $user_id, $amount, $description);
        $stmt->execute();
        
        error_log("Credit updated for user $user_id: $current_credit -> $new_credit");
        
        return true;
    }
    
    return false;
}
?>
