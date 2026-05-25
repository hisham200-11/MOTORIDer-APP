<?php
session_start();
include "connection.php";

$result = $conn->query("SELECT * FROM ride_requests WHERE status='pending' ORDER BY created_at DESC");

while($ride = $result->fetch_assoc()) {
    echo "
    <div class='ride' data-id='{$ride['id']}'>
        <div class='ride-header'>
            <strong>{$ride['rider_name']}</strong>
            <span class='price'>₱{$ride['price']}</span>
        </div>
        <p>{$ride['distance']} km</p>
        <p>Pickup: {$ride['pickup']}</p>
        <p>Dropoff: {$ride['dropoff']}</p>
        <p>Payment: <strong>{$ride['payment_method']}</strong></p>
    <div class='ride-actions'>
            <button class='btn accept-btn' data-id='{$ride['id']}'>Accept Ride</button>
            <button class='decline-btn' data-id='{$ride['id']}'>Decline</button>
        </div>
    </div>";
}
?>
