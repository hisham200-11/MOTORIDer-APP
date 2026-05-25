<?php
// Force the browser to NEVER cache this file so it updates instantly
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header('Content-Type: application/json');

session_start();
include "connection.php";

if (!isset($_SESSION['driver_id'])) {
    echo json_encode(['total_rides' => 0, 'total_earnings' => 0]);
    exit;
}

$driver_id = $_SESSION['driver_id'];

// Dynamically count ONLY this specific driver's completed rides.
// We use SUM(price * 0.95) to automatically deduct the 5% platform fee for their true earnings!
// IFNULL prevents it from crashing if they have 0 rides.
$query = "
    SELECT 
        COUNT(id) AS total_rides, 
        IFNULL(SUM(price * 0.88), 0) AS total_earnings 
    FROM ride_requests 
    WHERE driver_id = ? AND status = 'completed'
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Format it cleanly and return to the JavaScript
echo json_encode([
    'total_rides' => (int)$data['total_rides'],
    'total_earnings' => number_format((float)$data['total_earnings'], 2, '.', '')
]);

$stmt->close();
$conn->close();
?>