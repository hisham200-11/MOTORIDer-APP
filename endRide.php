<?php
session_start();
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rideId = $_POST['id'];
    $driverId = $_SESSION['driver_id'];
    
    $stmtPrice = $conn->prepare("SELECT * FROM ride_requests WHERE id = ?");
    $stmtPrice->bind_param("i", $rideId);
    $stmtPrice->execute();
    $res = $stmtPrice->get_result();
    $rideData = $res->fetch_assoc();
    
    if (!$rideData) {
        echo json_encode(["status" => "error", "message" => "Ride not found"]);
        exit;
    }
    
    $fare           = $rideData['price'];
    $tax            = round($fare * 0.12, 2); // 5% platform fee
    $driver_earnings = round($fare - $tax, 2); // what driver gets
    
    $conn->begin_transaction();

    try {
        $stmtRide = $conn->prepare("UPDATE ride_requests SET status='completed', end_time=NOW(), tax=?, driver_earnings=? WHERE id=? AND driver_id=?");
        $stmtRide->bind_param("ddii", $tax, $driver_earnings, $rideId, $driverId);
        $stmtRide->execute();

        // Driver only gets earnings after tax
        $stmtDriver = $conn->prepare("UPDATE driver_tbl SET total_earnings = total_earnings + ?, total_rides = total_rides + 1 WHERE driver_id = ?");
        $stmtDriver->bind_param("di", $driver_earnings, $driverId);
        $stmtDriver->execute();

        $conn->commit();

        echo json_encode([
            "status"          => "success",
            "rider_name"      => $rideData['rider_name'],
            "pickup"          => $rideData['pickup'],
            "dropoff"         => $rideData['dropoff'],
            "distance"        => $rideData['distance'],
            "fare"            => $fare,
            "tax"             => $tax,
            "driver_earnings" => $driver_earnings,
            "payment_method"  => $rideData['payment_method'],
            "start_time"      => $rideData['start_time'],
            "end_time"        => date('Y-m-d H:i:s')
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error"]);
    }
}
?>
