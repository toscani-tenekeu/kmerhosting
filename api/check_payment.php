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

// Get transaction ID from request
$transId = isset($_GET['transId']) ? trim($_GET['transId']) : '';
$externalId = isset($_GET['externalId']) ? trim($_GET['externalId']) : '';

if (empty($transId) && empty($externalId)) {
    echo json_encode(['success' => false, 'message' => 'ID de transaction manquant.']);
    exit;
}

try {
    if (!empty($transId)) {
        // Check payment status with Fapshi API
        $payment_status = check_payment_status($transId);
        
        if ($payment_status && isset($payment_status['status'])) {
            $status = $payment_status['status'];
            $externalId = $payment_status['externalId'] ?? '';
            
            // Update transaction in database if found
            if (!empty($externalId)) {
                // Log the status for debugging
                error_log("Payment status for transaction $transId (externalId: $externalId): $status");
                
                // Update transaction status
                $sql = "UPDATE payment_transactions SET status = ?, trans_id = ?, updated_at = NOW() WHERE external_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $status, $transId, $externalId);
                $stmt->execute();
                
                // If payment is successful, add credit to user account
                if ($status === 'SUCCESSFUL') {
                    process_successful_payment($externalId, $transId);
                }
            }
            
            echo json_encode(['success' => true, 'status' => $status, 'data' => $payment_status]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Impossible de vérifier le statut du paiement.']);
            exit;
        }
    } else if (!empty($externalId)) {
        // Find transaction in database
        $sql = "SELECT * FROM payment_transactions WHERE external_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $externalId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $transaction = $result->fetch_assoc();
            $transId = $transaction['trans_id'];
            
            // If we have a transaction ID, check with Fapshi
            if (!empty($transId)) {
                $payment_status = check_payment_status($transId);
                
                if ($payment_status && isset($payment_status['status'])) {
                    $status = $payment_status['status'];
                    
                    // Update transaction status
                    $sql = "UPDATE payment_transactions SET status = ?, updated_at = NOW() WHERE external_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $status, $externalId);
                    $stmt->execute();
                    
                    // If payment is successful, add credit to user account
                    if ($status === 'SUCCESSFUL' && $transaction['status'] !== 'SUCCESSFUL') {
                        process_successful_payment($externalId, $transId);
                    }
                    
                    echo json_encode(['success' => true, 'status' => $status, 'data' => $payment_status]);
                    exit;
                }
            }
            
            // Return current status from database
            echo json_encode(['success' => true, 'status' => $transaction['status'], 'data' => $transaction]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Transaction non trouvée.']);
            exit;
        }
    }
} catch (Exception $e) {
    error_log('Check payment error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue: ' . $e->getMessage()]);
    exit;
}

/**
 * Function to check payment status
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

// Update the process_successful_payment function to ensure it properly updates the status
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
