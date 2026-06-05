<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit();
}

$amount = floatval($_POST['amount'] ?? 0);

if ($amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount.']);
    exit();
}

// Include your DB connection here — adjust path as needed
include 'connection.php'; // or whatever your connection file is called

$customer_id = $_SESSION['customer_id'];

$stmt = $conn->prepare("UPDATE customer_tbl SET gcash_balance = gcash_balance + ? WHERE customer_id = ?");
$stmt->bind_param("di", $amount, $customer_id);

if ($stmt->execute()) {
    // Update session so balance stays in sync
    $_SESSION['gcash_balance'] = ($_SESSION['gcash_balance'] ?? 0) + $amount;

    echo json_encode([
        'success' => true,
        'new_balance' => $_SESSION['gcash_balance']
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}

$stmt->close();