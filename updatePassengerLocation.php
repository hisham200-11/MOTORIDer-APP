<?php
session_start();
include "connection.php";
$lat = floatval($_POST['lat']);
$lng = floatval($_POST['lng']);
$name = $_SESSION['name'];
$stmt = $conn->prepare("UPDATE ride_requests SET passenger_lat=?, passenger_lng=? WHERE rider_name=? AND status IN ('accepted','started')");
$stmt->bind_param("dds", $lat, $lng, $name);
$stmt->execute();
echo "ok";
?>