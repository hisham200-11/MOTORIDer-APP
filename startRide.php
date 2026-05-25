<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rideId = $_POST['id'];
    $driverId = $_SESSION['driver_id'];
    
    $stmt = $conn->prepare("UPDATE ride_requests SET status='started', start_time=NOW() WHERE id=? AND driver_id=?");
    $stmt->bind_param("ii", $rideId, $driverId);
    $success = $stmt->execute();
    $stmt->close();
    
    echo $success ? "success" : "error";
}
?>
