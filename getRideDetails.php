<?php
session_start();
include "connection.php";

if (!isset($_GET['id']) || !isset($_SESSION['driver_id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$ride_id = intval($_GET['id']);
$driver_id = $_SESSION['driver_id'];

// Fetch ride details
$query = "SELECT 
    id,
    customer_id,
    pickup_lat,
    pickup_lng,
    pickup_address,
    dropoff_lat,
    dropoff_lng,
    dropoff_address,
    status,
    fare
FROM ride_history 
WHERE id = $ride_id";

$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Ride not found"]);
    exit();
}

$ride = $result->fetch_assoc();

// Return as JSON
header('Content-Type: application/json');
echo json_encode($ride);
?>
