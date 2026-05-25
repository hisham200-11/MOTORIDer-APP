<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rideId = $_POST['id'];
    $status = $_POST['status'];
    $driverId = $_SESSION['driver_id'] ?? null;

    // Only assign driver if accepting
    if ($status === 'accepted' && $driverId) {
        $stmt = $conn->prepare("UPDATE ride_requests SET status=?, driver_id=? WHERE id=?");
        $stmt->bind_param("sii", $status, $driverId, $rideId);
    } else {
        $stmt = $conn->prepare("UPDATE ride_requests SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $rideId);
    }

    $stmt->execute();
    $stmt->close();

    echo "success";
}
?>
