<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['balance' => 0]);
    exit();
}

echo json_encode([
    'balance' => floatval($_SESSION['gcash_balance'] ?? 0)
]);