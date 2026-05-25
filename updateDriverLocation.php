<?php
session_start();
include "connection.php";
$name = $_SESSION['name'];
$stmt = $conn->prepare("SELECT driver_lat, driver_lng FROM ride_requests WHERE rider_name=? AND status IN ('accepted','started') LIMIT 1");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
header('Content-Type: application/json');
echo json_encode($row ?? ["driver_lat" => null, "driver_lng" => null]);
?>