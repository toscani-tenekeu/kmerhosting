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

// If we have transaction details, get transaction info
if (!empty($external_id) || !empty($trans_id)) {
    try {
        // Get transaction details from database
        if (!empty($external_id)) {
            $sql = "SELECT * FROM payment_transactions WHERE external_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $external_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $transaction = $result->fetch_assoc();
            }
        }
        
        // If we have a transaction ID, check with Fapshi
        if (!empty($trans_id)) {
            $payment_status = check_payment_status($trans_id);
        }
    } catch (Exception $e) {
        error_log('Payment failure page error: ' . $e->getMessage());
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
    <title>Paiement Échoué - KmerHosting</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #10b981;
            --danger-color: #ef4444;
            --accent-color: #f59e0b;
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
        
        .failure-container {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            padding: 0;
            overflow: hidden;
        }
        
        .failure-header {
            background-color: var(--danger-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .failure-icon {
            background-color: white;
            color: var(--danger-color);
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
        
        .failure-amount {
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
        
        .failure-body {
            padding: 30px;
        }
        
        .reasons-list {
            background-color: var(--light-bg);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .reason-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 15px;
        }
        
        .reason-item i {
            color: var(--danger-color);
            margin-right: 10px;
            font-size: 16px;
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
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
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
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        @media (max-width: 576px) {
            .failure-container {
                margin: 15px;
                max-width: 100%;
            }
            
            .failure-amount {
                font-size: 36px;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="failure-container">
        <div class="failure-header">
            <div class="failure-icon">
                <i class="fas fa-times"></i>
            </div>
            <h2>Paiement Échoué</h2>
            <?php if ($transaction): ?>
                <div class="failure-amount">
                    <?= number_format($transaction['amount'], 0, ',', ' ') ?> <span class="currency">FCFA</span>
                </div>
                <p>Votre paiement n'a pas pu être traité</p>
            <?php else: ?>
                <p>Votre paiement n'a pas pu être traité</p>
            <?php endif; ?>
        </div>
        
        <div class="failure-body">
            <div class="reasons-list">
                <h5 class="mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Raisons possibles</h5>
                
                <div class="reason-item">
                    <i class="fas fa-times-circle"></i>
                    <span>Solde insuffisant sur votre compte mobile</span>
                </div>
                
                <div class="reason-item">
                    <i class="fas fa-times-circle"></i>
                    <span>Problème de connexion réseau</span>
                </div>
                
                <div class="reason-item">
                    <i class="fas fa-times-circle"></i>
                    <span>Transaction expirée ou annulée</span>
                </div>
                
                <div class="reason-item">
                    <i class="fas fa-times-circle"></i>
                    <span>Problème technique avec le service de paiement</span>
                </div>
            </div>
            
            <p class="text-center mb-4">Vous pouvez réessayer le paiement ou contacter notre support si le problème persiste.</p>
            
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
            
            <div class="text-center mt-4">
                <a href="javascript:void(0);" onclick="window.location.href='customers/cart.php?recharge=1'" class="btn btn-danger">
                    <i class="fas fa-redo"></i> Réessayer le paiement
                </a>
            </div>
        </div>
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
    
    return $decoded;
}
?>
