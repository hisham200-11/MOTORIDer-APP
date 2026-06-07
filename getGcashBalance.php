<?php
session_start();
include "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['balance' => 0]);
    exit();
}

$customer_id = $_SESSION['customer_id'];

$stmt = $conn->prepare("SELECT gcash_balance FROM customer_tbl WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$balance = $row['gcash_balance'] ?? 0;

echo json_encode(['balance' => floatval($balance)]);
?>