<?php
session_start();
include "connection.php";

$driver_id = $_SESSION['driver_id'];

// FIX: Changed WHERE id= to WHERE driver_id=
$query = mysqli_query($conn, "SELECT status FROM driver_tbl WHERE driver_id='$driver_id'");
$row = mysqli_fetch_assoc($query);

$current = $row['status'];
$newStatus = ($current == 'on') ? 'off' : 'on';

// FIX: Changed WHERE id= to WHERE driver_id=
mysqli_query($conn, "UPDATE driver_tbl SET status='$newStatus' WHERE driver_id='$driver_id'");

echo ($newStatus == 'on') ? "ONLINE" : "OFFLINE";
?>
