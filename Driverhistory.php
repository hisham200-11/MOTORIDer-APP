<?php
session_start();
include "connection.php";

if (!isset($_SESSION['driver_id'])) {
    header("Location: Driverlogin.html");
    exit();
}

$driver_id = $_SESSION['driver_id'];

$stmt = $conn->prepare("SELECT * FROM ride_requests WHERE driver_id = ? AND status = 'completed' ORDER BY end_time DESC");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$rides = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Summary totals
$total_rides    = count($rides);
$total_earnings = array_sum(array_column($rides, 'driver_earnings'));
$total_tax      = array_sum(array_column($rides, 'tax'));
$total_fare     = array_sum(array_column($rides, 'price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Dots&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- DARK MODE ADDITION START -->
    <link rel="stylesheet" href="dark-mode.css">
    <script src="dark-mode.js"></script>
    <!-- DARK MODE ADDITION END -->
    <link rel="stylesheet" href="Driverhome.css">
    <link rel="stylesheet" href="Driverhistory.css">
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <i class="fa-solid fa-motorcycle"></i> MotoRide
        </div>
        <div class="user-actions">
            <span>Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <!-- DARK MODE ADDITION START -->
            <button class="theme-toggle" onclick="toggleDarkMode()"></button>
            <!-- DARK MODE ADDITION END -->
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="history-container">

        <!-- SUMMARY CARD -->
        <div class="summary-card">
            <h3>Your Earnings Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="value"><?php echo $total_rides; ?></div>
                    <div class="label">Total Rides</div>
                </div>
                <div class="divider"></div>
                <div class="summary-item">
                    <div class="value">₱<?php echo number_format($total_earnings, 2); ?></div>
                    <div class="label">Total Earnings</div>
                </div>
                <div class="divider"></div>
                <div class="summary-item">
                    <div class="value">₱<?php echo number_format($total_tax, 2); ?></div>
                    <div class="label">Platform Fees</div>
                </div>
            </div>
        </div>

        <!-- RIDE LIST -->
        <div class="section-title">Transaction History</div>

        <?php if (empty($rides)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-receipt"></i>
                <p>No completed rides yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rides as $ride): ?>
                <div class="ride-card" onclick="showReceipt(<?php echo htmlspecialchars(json_encode($ride)); ?>)">
                    <div class="ride-card-header">
                        <span class="rider"><?php echo htmlspecialchars($ride['rider_name']); ?></span>
                        <span class="earnings">₱<?php echo number_format($ride['driver_earnings'], 2); ?></span>
                    </div>
                    <div class="ride-card-date">
                        <i class="fa-regular fa-clock"></i>
                        <?php echo date('M d, Y • h:i A', strtotime($ride['end_time'])); ?>
                    </div>
                    <div class="ride-card-route">
                        <i class="fa-solid fa-circle-dot"></i>
                        <?php echo htmlspecialchars($ride['pickup']); ?>
                        <i class="fa-solid fa-arrow-right"></i>
                        <i class="fa-solid fa-location-dot"></i>
                        <?php echo htmlspecialchars($ride['dropoff']); ?>
                    </div>
                    <div class="ride-card-footer">
                        <span class="payment-badge">
                            <i class="fa-solid fa-<?php echo $ride['payment_method'] === 'GCash' ? 'mobile-screen' : 'money-bill'; ?>"></i>
                            <?php echo htmlspecialchars($ride['payment_method']); ?>
                        </span>
                        <span class="distance-badge"><?php echo $ride['distance']; ?> km</span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- RECEIPT MODAL -->
    <div class="modal-overlay" id="receiptModal">
        <div class="modal-box">
            <div class="modal-title">🧾 Ride Receipt</div>
            <div class="modal-subtitle" id="modal-date"></div>

            <div class="modal-row">
                <span class="modal-label">Passenger</span>
                <span class="modal-value" id="modal-rider"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Pickup</span>
                <span class="modal-value" id="modal-pickup"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Dropoff</span>
                <span class="modal-value" id="modal-dropoff"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Distance</span>
                <span class="modal-value" id="modal-distance"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Payment</span>
                <span class="modal-value" id="modal-payment"></span>
            </div>

            <hr class="modal-divider">

            <div class="modal-row">
                <span class="modal-label">Gross Fare</span>
                <span class="modal-value" id="modal-fare"></span>
            </div>
            <div class="modal-tax-row">
                <span>Platform Fee (5%)</span>
                <span id="modal-tax"></span>
            </div>

            <hr class="modal-divider thick">

            <div class="modal-total">
                <span class="total-label">Your Earnings</span>
                <span class="total-value" id="modal-earnings"></span>
            </div>

            <button class="modal-close-btn" onclick="closeReceipt()">Close</button>
        </div>
    </div>

    <!-- BOTTOM NAV -->
    <div class="bottom-nav">
        <div class="nav-item">
            <i class="fa-solid fa-house"></i>
            <a href="Driverhome.php" style="text-decoration: none; color: inherit;"><span>Home</span></a>
        </div>
        <div class="nav-item active">
            <i class="fa-solid fa-receipt"></i>
            <span>History</span>
        </div>
        <div class="nav-item">
            <i class="fa-solid fa-user"></i>
            <a href="Driverprofile.php" style="text-decoration: none; color: inherit;"><span>Profile</span></a>
        </div>
    </div>

    <script src="javafolder/historyfunc.js"></script>

</body>
</html>
```
