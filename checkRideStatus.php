<?php
// ============================================
// PROPER SESSION HANDLING FOR INCLUSION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "connection.php";

$rider_name = $_SESSION['name'] ?? '';
if (!$rider_name) {
    echo "<p>No current ride</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT rr.*, d.name AS driver_name, d.brand, d.model, d.color, 
           d.plate_no, d.contact_no AS driver_contact, d.gcash AS driver_gcash
    FROM ride_requests rr
    LEFT JOIN driver_tbl d ON rr.driver_id = d.driver_id
    WHERE rr.rider_name = ? AND (
        rr.status IN ('pending','accepted','started')
        OR (rr.status = 'completed' AND rr.end_time > DATE_SUB(NOW(), INTERVAL 5 MINUTE))
    )
    ORDER BY rr.created_at DESC
    LIMIT 1
");
$stmt->bind_param("s", $rider_name);
$stmt->execute();
$result = $stmt->get_result();
$ride = $result->fetch_assoc();
$stmt->close();

if (!$ride) {
    echo "<p>No current ride</p>";
    exit;
}

if ($ride['status'] === 'pending') {
    echo "
    <div style='text-align:center; padding: 20px;'>
        <p style='font-size:16px; color:#7c3aed;'>⏳ Waiting for a driver...</p>
        <p style='color:#888; font-size:13px;'>Your ride request is pending</p>
        <p><strong>Pickup:</strong> {$ride['pickup']}</p>
        <p><strong>Dropoff:</strong> {$ride['dropoff']}</p>
        <p><strong>Fare:</strong> ₱{$ride['price']}</p>
        <p><strong>Payment:</strong> {$ride['payment_method']}</p>
    </div>";

} elseif ($ride['status'] === 'accepted') {
    echo "
    <div style='padding: 10px;'>
        <span id='activeRidePickup' style='display:none;'>{$ride['pickup']}</span>
        <span id='activeRideDropoff' style='display:none;'>{$ride['dropoff']}</span>
        <p style='font-size:15px; color:#16a34a; font-weight:bold;'>✅ Driver is on the way!</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🧑 Driver Information</p>
        <p><strong>Name:</strong> {$ride['driver_name']}</p>
        <p><strong>Contact:</strong> {$ride['driver_contact']}</p>
        <p><strong>GCash:</strong> {$ride['driver_gcash']}</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🏍️ Vehicle Information</p>
        <p><strong>Brand:</strong> {$ride['brand']}</p>
        <p><strong>Model:</strong> {$ride['model']}</p>
        <p><strong>Color:</strong> {$ride['color']}</p>
        <p><strong>Plate No:</strong> {$ride['plate_no']}</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🗺️ Ride Details</p>
        <p><strong>Pickup:</strong> {$ride['pickup']}</p>
        <p><strong>Dropoff:</strong> {$ride['dropoff']}</p>
        <p><strong>Fare:</strong> ₱{$ride['price']}</p>
        <p><strong>Payment:</strong> {$ride['payment_method']}</p>
    </div>";

} elseif ($ride['status'] === 'started') {
    echo "
    <div style='padding: 10px;'>
        <span id='activeRidePickup' style='display:none;'>{$ride['pickup']}</span>
        <span id='activeRideDropoff' style='display:none;'>{$ride['dropoff']}</span>
        <p style='font-size:15px; color:#9333ea; font-weight:bold;'>🚀 Ride in Progress!</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🧑 Driver Information</p>
        <p><strong>Name:</strong> {$ride['driver_name']}</p>
        <p><strong>Contact:</strong> {$ride['driver_contact']}</p>
        <p><strong>GCash:</strong> {$ride['driver_gcash']}</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🏍️ Vehicle Information</p>
        <p><strong>Brand:</strong> {$ride['brand']}</p>
        <p><strong>Model:</strong> {$ride['model']}</p>
        <p><strong>Color:</strong> {$ride['color']}</p>
        <p><strong>Plate No:</strong> {$ride['plate_no']}</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🗺️ Ride Details</p>
        <p><strong>Pickup:</strong> {$ride['pickup']}</p>
        <p><strong>Dropoff:</strong> {$ride['dropoff']}</p>
        <p><strong>Fare:</strong> ₱{$ride['price']}</p>
        <p><strong>Payment:</strong> {$ride['payment_method']}</p>
    </div>";

} elseif ($ride['status'] === 'completed') {
    echo "
    <div style='padding: 10px;'>
        <span id='completedRideId' style='display:none;'>{$ride['id']}</span>
        <p style='font-size:15px; color:#16a34a; font-weight:bold; text-align:center;'>🎉 Ride Completed!</p>
        <p style='text-align:center; color:#888; font-size:13px; margin-bottom:15px;'>
            " . date('M d, Y • h:i A', strtotime($ride['end_time'])) . "
        </p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🧑 Driver Information</p>
        <p><strong>Name:</strong> {$ride['driver_name']}</p>
        <p><strong>Contact:</strong> {$ride['driver_contact']}</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🏍️ Vehicle Information</p>
        <p><strong>Brand:</strong> {$ride['brand']}</p>
        <p><strong>Model:</strong> {$ride['model']}</p>
        <p><strong>Color:</strong> {$ride['color']}</p>
        <p><strong>Plate No:</strong> {$ride['plate_no']}</p>

        <hr style='border:none; border-top:1px solid #eee; margin: 10px 0;'>

        <p style='font-weight:bold; margin-bottom:8px;'>🗺️ Ride Details</p>
        <p><strong>Pickup:</strong> {$ride['pickup']}</p>
        <p><strong>Dropoff:</strong> {$ride['dropoff']}</p>
        <p><strong>Distance:</strong> {$ride['distance']} km</p>
        <p><strong>Payment:</strong> {$ride['payment_method']}</p>

        <hr style='border:none; border-top:2px solid #9333ea; margin: 15px 0;'>

        <div style='display:flex; justify-content:space-between; align-items:center;'>
            <span style='font-size:16px; font-weight:bold;'>Total Fare</span>
            <span style='font-size:22px; font-weight:bold; color:#9333ea;'>₱{$ride['price']}</span>
        </div>

        <button onclick='dismissReceipt()'
            style='width:100%; margin-top:20px; padding:12px;
                background:linear-gradient(90deg,#9333ea,#7c3aed); color:white;
                border:none; border-radius:10px; font-size:15px; cursor:pointer;
                font-family: Zen Dots, sans-serif;'>
            Done
        </button>
    </div>";
}
?>
