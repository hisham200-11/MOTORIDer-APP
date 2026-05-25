<?php
session_start();
include "connection.php";

$driver_id = $_SESSION['driver_id'];

// FIX: Changed WHERE id= to WHERE driver_id=
$query = mysqli_query($conn, "SELECT driver_id, name, contact_no, model, plate_no, color, brand, status FROM driver_tbl WHERE driver_id='$driver_id'");
$drivers = [];
while ($row = mysqli_fetch_assoc($query)) {
    $drivers[] = $row;
}

// Return JSON
echo json_encode($drivers);
?>
