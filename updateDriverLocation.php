<?php
session_start();
include "connection.php";

// Handle POST request to update driver location
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['driver_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "Not authenticated"]);
        exit();
    }

    if (!isset($_POST['lat']) || !isset($_POST['lng'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing lat/lng"]);
        exit();
    }

    $driver_id = $_SESSION['driver_id'];
    $lat = floatval($_POST['lat']);
    $lng = floatval($_POST['lng']);

    // Update driver location in database
    $query = "UPDATE driver_tbl SET driver_lat = $lat, driver_lng = $lng WHERE driver_id = '$driver_id'";
    
    if ($conn->query($query) === TRUE) {
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update location"]);
    }
    exit();
}

// Handle GET request to fetch driver location (existing functionality)
$name = $_SESSION['name'];
$stmt = $conn->prepare("SELECT driver_lat, driver_lng FROM ride_requests WHERE rider_name=? AND status IN ('accepted','started') LIMIT 1");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
header('Content-Type: application/json');
echo json_encode($row ?? ["driver_lat" => null, "driver_lng" => null]);
?>
