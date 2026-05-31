<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$ride_id = intval($_POST['ride_id'] ?? 0);
if (!$ride_id) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("UPDATE ride_requests SET passenger_dismissed = 1 WHERE id = ? AND rider_name = ?");
$stmt->bind_param("is", $ride_id, $_SESSION['name']);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);