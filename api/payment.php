<?php
// Include configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON for all responses
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette opération.']);
    exit;
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Handle payment link generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_payment_link') {
    // Validate inputs
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
    
    // Basic validation
    if (empty($phone) || strlen($phone) !== 9 || !is_numeric($phone)) {
        echo json_encode(['success' => false, 'message' => 'Numéro de téléphone invalide. Veuillez entrer un numéro à 9 chiffres.']);
        exit;
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
        exit;
    }
    
    if ($amount < 100) {
        echo json_encode(['success' => false, 'message' => 'Le montant minimum est de 100 FCFA.']);
        exit;
    }
    
    try {
        // Generate a unique external ID for this transaction
        $external_id = 'kmerhosting_' . time() . '_' . $user_id;
        
        // Create callback URL for payment verification
        $callback_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . BASE_URL . "/api/payment_callback.php";
        
        // Create redirect URLs after payment
        $success_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . BASE_URL . "/payment_success.php";
        $failure_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . BASE_URL . "/payment_failure.php";
        
        // Store redirect URLs in session
        $_SESSION['payment_success_url'] = $success_url;
        $_SESSION['payment_failure_url'] = $failure_url;
        
        // Prepare data for Fapshi API
        $data = [
            'amount' => $amount,
            'email' => $email,
            'redirectUrl' => $success_url,
            'userId' => (string)$user_id,
            'externalId' => $external_id,
            'message' => 'Recharge de crédit KmerHosting',
            'webhook' => $callback_url
        ];
        
        // Log the request data
        error_log('Fapshi payment request: ' . json_encode($data));
        
        // Call Fapshi API to generate payment link
        $payment_response = generateFapshiPaymentLink($data);

        if ($payment_response && isset($payment_response['link'])) {
            // Save transaction details to database
            $sql = "INSERT INTO payment_transactions (user_id, amount, phone, email, external_id, trans_id, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 'PENDING', NOW())";
            
            $status = 'PENDING';
            $trans_id = $payment_response['transId'];
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iissss", $user_id, $amount, $phone, $email, $external_id, $trans_id);
            $stmt->execute();
            $transaction_id = $conn->insert_id;
            
            // Store transaction ID in session
            $_SESSION['current_transaction_id'] = $transaction_id;
            $_SESSION['current_external_id'] = $external_id;
            $_SESSION['current_trans_id'] = $trans_id;
            
            // Return success response with payment link
            echo json_encode([
                'success' => true, 
                'payment_link' => $payment_response['link'],
                'transaction_id' => $transaction_id,
                'external_id' => $external_id,
                'trans_id' => $trans_id
            ]);
            exit;
        } else {
            // Return error response
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la génération du lien de paiement. Veuillez réessayer.']);
            exit;
        }
    } catch (Exception $e) {
        // Log the error
        error_log('Payment error: ' . $e->getMessage());
        
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check_status') {
    // Check transaction status
    $transaction_id = isset($_GET['transaction_id']) ? intval($_GET['transaction_id']) : 0;
    $external_id = isset($_GET['external_id']) ? trim($_GET['external_id']) : '';
    
    if ($transaction_id <= 0 && empty($external_id)) {
        echo json_encode(['success' => false, 'message' => 'ID de transaction manquant.']);
        exit;
    }
    
    try {
        // Get transaction from database
        $transaction = null;
        
        if ($transaction_id > 0) {
            $transaction = getTransactionById($transaction_id);
        } else if (!empty($external_id)) {
            $transaction = getTransactionByExternalId($external_id);
        }
        
        if (!$transaction) {
            echo json_encode(['success' => false, 'message' => 'Transaction non trouvée.']);
            exit;
        }
        
        // If we have a Fapshi transaction ID, check status with Fapshi
        if (!empty($transaction['trans_id'])) {
            $payment_status = check_payment_status($transaction['trans_id']);
            
            if ($payment_status && isset($payment_status['status'])) {
                // Update transaction status in database
                updateTransactionStatus($transaction['id'], $payment_status['status']);
                
                // If payment is successful, process it
                if ($payment_status['status'] === 'SUCCESSFUL' && $transaction['status'] !== 'SUCCESSFUL') {
                    processSuccessfulPayment($transaction['id']);
                }
                
                echo json_encode([
                    'success' => true, 
                    'status' => $payment_status['status'],
                    'transaction' => $transaction,
                    'fapshi_data' => $payment_status
                ]);
                exit;
            }
        }
        
        // Return current status from database
        echo json_encode([
            'success' => true, 
            'status' => $transaction['status'],
            'transaction' => $transaction
        ]);
        exit;
    } catch (Exception $e) {
        // Log the error
        error_log('Check status error: ' . $e->getMessage());
        
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la vérification du statut.']);
        exit;
    }
} else {
    // Invalid request
    echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
    exit;
}

// Function to generate Fapshi payment link
function generateFapshiPaymentLink($data) {
    global $conn;
    $external_id = $data['externalId'];
    $url = FAPSHI_BASE_URL . "/initiate-pay";
    
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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
    
    // Log the raw response
    error_log('Fapshi API Response: ' . $response);
    
    // Decode the response
    $decoded = json_decode($response, true);
    
    // Check if JSON parsing failed
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON parsing error: ' . json_last_error_msg() . ' - Raw response: ' . $response);
        return false;
    }
    
    // Check if the response contains a payment link and transaction ID
    if (isset($decoded['link']) && isset($decoded['transId'])) {
        
        // Log the response for debugging
        error_log('Fapshi payment link response: ' . json_encode($decoded));

        // Si la réponse contient un transId, l'enregistrer dans la base de données
        if (isset($decoded['transId'])) {
            $trans_id = $decoded['transId'];
            
            // Mettre à jour la transaction avec le transId
            $sql = "UPDATE payment_transactions SET trans_id = ? WHERE external_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $trans_id, $external_id);
            $stmt->execute();
            
            // Log la mise à jour
            error_log("Updated transaction with transId: $trans_id for externalId: $external_id");
        }
        
        return [
            'link' => $decoded['link'],
            'transId' => $decoded['transId']
        ];
    }
    
    return false;
}
