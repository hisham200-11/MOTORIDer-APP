<?php
session_start();
include "connection.php";

// ✅ Ensure user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([]);
    exit();
}

// ✅ Set JSON header
header('Content-Type: application/json');

// ✅ Run query
$query = mysqli_query($conn, "
    SELECT driver_id, name, brand, model, color, status 
    FROM driver_tbl 
    WHERE status = 'on'
");

// ✅ Handle query error
if (!$query) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit();
}

// ✅ Fetch results
$drivers = [];

while ($row = mysqli_fetch_assoc($query)) {
    $drivers[] = $row;
}

// ✅ Return JSON
echo json_encode($drivers);
?>
