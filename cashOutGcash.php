<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['driver_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Check current balance first
$check = $conn->prepare("SELECT gcash_balance FROM driver_tbl WHERE driver_id = ?");
$check->bind_param("i", $driver_id);
$check->execute();
$row = $check->get_result()->fetch_assoc();
$check->close();

if (!$row || floatval($row['gcash_balance']) <= 0) {
    echo json_encode(['success' => false, 'error' => 'No balance to cash out.']);
    exit;
}

$stmt = $conn->prepare("UPDATE driver_tbl SET gcash_balance = 0 WHERE driver_id = ?");
$stmt->bind_param("i", $driver_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
$stmt->close();