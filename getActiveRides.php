<?php
// 1. Fix Session Notice: Only start if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "connection.php";

// 2. Security Check: Ensure session variable exists
if (!isset($_SESSION['driver_id'])) {
    echo "<p>Error: Driver not logged in.</p>";
    exit;
}

$driver_id = $_SESSION['driver_id'];

// 3. Use Prepared Statements to prevent SQL injection and errors
$sql = "SELECT rr.*, c.name AS rider_name 
        FROM ride_requests rr 
        JOIN customer_tbl c ON rr.rider_name = c.name 
        WHERE rr.driver_id = ? AND rr.status IN ('accepted','started') 
        ORDER BY rr.created_at DESC 
        LIMIT 1";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($ride = $result->fetch_assoc()) {
        ?>
        <div class='active-ride' data-id='<?php echo $ride['id']; ?>'>
            <div class='ride-header'>
                <strong><?php echo htmlspecialchars($ride['rider_name']); ?></strong>
                <span class='price'>₱<?php echo number_format($ride['price'], 2); ?></span>
            </div>
            
            <p><strong>Status:</strong> 
                <span class='status-<?php echo $ride['status']; ?>'>
                    <?php echo ucfirst($ride['status']); ?>
                </span>
            </p>

            <?php if ($ride['status'] == 'started' && !empty($ride['start_time'])): ?>
                <p><strong>Started at:</strong> <?php echo date('H:i', strtotime($ride['start_time'])); ?></p>
            <?php endif; ?>

            <p><strong>Pickup:</strong> <?php echo htmlspecialchars($ride['pickup']); ?></p>
            <p><strong>Dropoff:</strong> <?php echo htmlspecialchars($ride['dropoff']); ?></p>
            <p><strong>Payment:</strong> <?php echo htmlspecialchars($ride['payment_method']); ?></p>
            
            <!-- Hidden inputs for map coordinates -->
            <input type='hidden' class='ride-pickup-lat' value='<?php echo htmlspecialchars($ride['pickup_lat'] ?? ''); ?>'>
            <input type='hidden' class='ride-pickup-lng' value='<?php echo htmlspecialchars($ride['pickup_lng'] ?? ''); ?>'>
            <input type='hidden' class='ride-dropoff-lat' value='<?php echo htmlspecialchars($ride['dropoff_lat'] ?? ''); ?>'>
            <input type='hidden' class='ride-dropoff-lng' value='<?php echo htmlspecialchars($ride['dropoff_lng'] ?? ''); ?>'>

            <?php if ($ride['status'] == 'accepted'): ?>
                <button class='btn start-btn' data-id='<?php echo $ride['id']; ?>'>🚀 Start Ride</button>
            <?php else: ?>
                <button class='btn end-btn' data-id='<?php echo $ride['id']; ?>' style='background: #16a34a;'>✅ Complete Ride</button>
            <?php endif; ?>
        </div>
        <?php
    } else {
        echo "<p style='text-align:center; color:#666; padding:20px;'>No active ride<br><small>Accept a request to get started</small></p>";
    }
    $stmt->close();
} else {
    echo "Query Error: " . $conn->error;
}
?>
