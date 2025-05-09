<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../backend/config/db.php';
require_once __DIR__ . '/../backend/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the callback data
error_log('Fapshi callback received: ' . json_encode($_POST));

// Log the callback
$raw_post_data = file_get_contents('php://input');
error_log('Fapshi Callback received: ' . $raw_post_data);

// Parse the JSON data
$data = json_decode($raw_post_data, true);

// Verify the callback
if ($data && isset($data['transId']) && isset($data['status'])) {
    $transId = $data['transId'];
    $status = $data['status'];
    $externalId = $data['externalId'] ?? '';
    
    // Log the transaction details
    error_log("Processing payment: transId=$transId, status=$status, externalId=$externalId");
    
    // Find the transaction in our database
    $sql = "SELECT * FROM payment_transactions WHERE external_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $externalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        $user_id = $transaction['user_id'];
        $amount = $transaction['amount'];
        
        // Update transaction status
        $sql = "UPDATE payment_transactions SET status = ?, trans_id = ?, updated_at = NOW() WHERE external_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $status, $transId, $externalId);
        $stmt->execute();
        
        // If payment is successful, add credit to user account
        if ($status === 'SUCCESSFUL' && $transaction['status'] !== 'SUCCESSFUL') {
            // Get current user credit
            $sql = "SELECT credit FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $current_credit = $user['credit'];
                $new_credit = $current_credit + $amount;
                
                // Update user credit
                $sql = "UPDATE users SET credit = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("di", $new_credit, $user_id);
                $stmt->execute();
                
                // Log the credit update
                $sql = "INSERT INTO credit_logs (user_id, amount, type, description, created_at) 
                        VALUES (?, ?, 'recharge', ?, NOW())";
                $description = "Recharge via Fapshi (TransID: $transId)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ids", $user_id, $amount, $description);
                $stmt->execute();
                
                error_log("Credit updated for user $user_id: $current_credit -> $new_credit");
            }
        }

        // Si le statut est SUCCESSFUL, mettre à jour la base de données
        if ($status === 'SUCCESSFUL') {
            // Vérifier si la transaction a déjà été traitée
            $sql = "SELECT status FROM payment_transactions WHERE external_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $externalId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $transaction = $result->fetch_assoc();
                
                // Ne traiter que si le statut n'est pas déjà SUCCESSFUL
                if ($transaction['status'] !== 'SUCCESSFUL') {
                    // Mettre à jour le statut dans la base de données
                    $sql = "UPDATE payment_transactions SET status = ?, updated_at = NOW() WHERE external_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $status, $externalId);
                    $stmt->execute();
                    
                    // Traiter le paiement réussi
                    process_successful_payment($externalId, $transId);
                    
                    // Log le succès
                    error_log("Payment successful for transaction $transId (externalId: $externalId)");
                } else {
                    error_log("Transaction $externalId already processed. Skipping.");
                }
            }
        }
        
        // Return success response
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } else {
        // Transaction not found
        error_log("Transaction not found for externalId: $externalId");
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Transaction not found']);
    }
} else {
    // Invalid data
    error_log("Invalid callback data received");
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
