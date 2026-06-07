<?php
session_start();
include "connection.php";

// Set JSON response header
header('Content-Type: application/json');

// Handle JSON POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON from POST body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Invalid JSON data"]);
        exit;
    }
    
    // Validate required fields
    if (!isset($_SESSION['customer_id']) || !isset($_SESSION['name'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "Session expired. Please login again."]);
        exit;
    }
    
    $rider_name = $_SESSION['name'];
    $pickup = $data['pickup'] ?? '';
    $dropoff = $dropoff = isset($data['dropoff']) && !empty($data['dropoff']) ? $data['dropoff'] : 'Unknown Location';
    error_log("Dropoff value in submitRide: " . $dropoff);
    $pickup_lat = $data['pickup_lat'] ?? null;
    $pickup_lng = $data['pickup_lng'] ?? null;
    $dropoff_lat = $data['dropoff_lat'] ?? null;
    $dropoff_lng = $data['dropoff_lng'] ?? null;
    $distance = $data['distance'] ?? 0;
    $fare = $data['fare'] ?? 0;
    $payment_method = $data['payment_method'] ?? 'Cash';
    
    if (!$pickup || !$dropoff) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Pickup and dropoff are required"]);
        exit;
    }
    
    // Insert ride request with coordinates
    $stmt = $conn->prepare("INSERT INTO ride_requests (rider_name, pickup, dropoff, pickup_lat, pickup_lng, dropoff_lat, dropoff_lng, distance, price, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("sssdddddds", $rider_name, $pickup, $dropoff, $pickup_lat, $pickup_lng, $dropoff_lat, $dropoff_lng, $distance, $fare, $payment_method);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Ride booked successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Failed to book ride: " . $stmt->error]);
    }
    
    $stmt->close();
    exit;
}

// Non-POST request
http_response_code(405);
echo json_encode(["success" => false, "error" => "Method not allowed"]);
?>
