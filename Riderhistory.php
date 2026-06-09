<?php
session_start();
include "connection.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: Riderlogin.html");
    exit();
}

$rider_name = $_SESSION['name'];

$stmt = $conn->prepare("
    SELECT rr.*, d.name AS driver_name, d.brand, d.model, d.color, d.plate_no, d.contact_no AS driver_contact
    FROM ride_requests rr
    LEFT JOIN driver_tbl d ON rr.driver_id = d.driver_id
    WHERE rr.rider_name = ? AND rr.status = 'completed'
    ORDER BY rr.end_time DESC
");
$stmt->bind_param("s", $rider_name);
$stmt->execute();
$result = $stmt->get_result();
$rides = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_rides = count($rides);
$total_spent = array_sum(array_column($rides, 'price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ride History</title>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Dots&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- DARK MODE ADDITION START -->
    <link rel="stylesheet" href="dark-mode.css">
    <script src="dark-mode.js"></script>
    <!-- DARK MODE ADDITION END -->
    <link rel="stylesheet" href="Riderhome.css">
    <style>
        body {
            padding-bottom: 70px;
        }

        .history-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px 20px 100px 20px;
        }

        .summary-card {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            border-radius: 16px;
            padding: 25px;
            color: white;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(124,58,237,0.3);
        }

        .summary-card h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            opacity: 0.85;
        }

        .summary-grid {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .summary-item {
            text-align: center;
            flex: 1;
        }

        .summary-item .value {
            font-size: 22px;
            font-weight: bold;
        }

        .summary-item .label {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 4px;
        }

        .summary-divider {
            width: 1px;
            background: rgba(255,255,255,0.3);
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            color: #444;
            margin-bottom: 15px;
        }

        .ride-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            cursor: pointer;
            transition: 0.2s;
        }

        .ride-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .ride-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .ride-card-header .driver {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        .ride-card-header .fare {
            font-size: 18px;
            font-weight: bold;
            color: #9333ea;
        }

        .ride-card-date {
            font-size: 12px;
            color: #aaa;
            margin-bottom: 12px;
        }

        .ride-card-route {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #555;
            margin-bottom: 12px;
        }

        .ride-card-route i {
            color: #9333ea;
            width: 14px;
        }

        .ride-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }

        .payment-badge {
            background: #f3e8ff;
            color: #7c3aed;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .distance-badge {
            font-size: 12px;
            color: #888;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 16px;
            padding: 30px;
            width: 90%;
            max-width: 380px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .modal-subtitle {
            text-align: center;
            color: #888;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .modal-section {
            font-size: 13px;
            font-weight: bold;
            color: #9333ea;
            margin: 15px 0 8px 0;
        }

        .modal-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .modal-label {
            color: #666;
        }

        .modal-value {
            font-weight: bold;
            color: #333;
            text-align: right;
        }

        .modal-divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 15px 0;
        }

        .modal-divider.thick {
            border-top: 2px solid #9333ea;
        }

        .modal-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-total .total-label {
            font-size: 16px;
            font-weight: bold;
        }

        .modal-total .total-value {
            font-size: 22px;
            font-weight: bold;
            color: #9333ea;
        }

        .modal-close-btn {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(90deg, #9333ea, #7c3aed);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            cursor: pointer;
            font-family: 'Zen Dots', sans-serif;
        }

        /* BOTTOM NAV */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
        }
    </style>
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
            <h3>Your Ride Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="value"><?php echo $total_rides; ?></div>
                    <div class="label">Total Rides</div>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-item">
                    <div class="value">₱<?php echo number_format($total_spent, 2); ?></div>
                    <div class="label">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- RIDE LIST -->
        <div class="section-title">Ride History</div>

        <?php if (empty($rides)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-route"></i>
                <p>No completed rides yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rides as $ride): ?>
                <div class="ride-card" onclick="showReceipt(<?php echo htmlspecialchars(json_encode($ride)); ?>)">
                    <div class="ride-card-header">
                        <span class="driver">
                            <i class="fa-solid fa-user" style="color:#9333ea;"></i>
                            <?php echo htmlspecialchars($ride['driver_name'] ?? 'Unknown Driver'); ?>
                        </span>
                        <span class="fare">₱<?php echo number_format($ride['price'], 2); ?></span>
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

            <div class="modal-section">🧑 Driver Information</div>
            <div class="modal-row">
                <span class="modal-label">Name</span>
                <span class="modal-value" id="modal-driver"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Contact</span>
                <span class="modal-value" id="modal-driver-contact"></span>
            </div>

            <hr class="modal-divider">

            <div class="modal-section">🏍️ Vehicle Information</div>
            <div class="modal-row">
                <span class="modal-label">Brand</span>
                <span class="modal-value" id="modal-brand"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Model</span>
                <span class="modal-value" id="modal-model"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Color</span>
                <span class="modal-value" id="modal-color"></span>
            </div>
            <div class="modal-row">
                <span class="modal-label">Plate No.</span>
                <span class="modal-value" id="modal-plate"></span>
            </div>

            <hr class="modal-divider">

            <div class="modal-section">🗺️ Ride Details</div>
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

            <hr class="modal-divider thick">

            <div class="modal-total">
                <span class="total-label">Total Fare</span>
                <span class="total-value" id="modal-fare"></span>
            </div>

            <button class="modal-close-btn" onclick="closeReceipt()">Close</button>
        </div>
    </div>

    <!-- BOTTOM NAV -->
    <div class="bottom-nav">
        <div class="nav-item">
            <i class="fa-solid fa-house"></i>
            <a href="Riderhome.php" style="text-decoration: none; color: inherit;"><span>Home</span></a>
        </div>
        <div class="nav-item active">
            <i class="fa-solid fa-receipt"></i>
            <span>History</span>
        </div>
        <div class="nav-item">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </div>
    </div>

    <script>
        function showReceipt(ride) {
            document.getElementById("modal-date").textContent           = formatDate(ride.end_time);
            document.getElementById("modal-driver").textContent         = ride.driver_name ?? "Unknown";
            document.getElementById("modal-driver-contact").textContent = ride.driver_contact ?? "N/A";
            document.getElementById("modal-brand").textContent          = ride.brand ?? "N/A";
            document.getElementById("modal-model").textContent          = ride.model ?? "N/A";
            document.getElementById("modal-color").textContent          = ride.color ?? "N/A";
            document.getElementById("modal-plate").textContent          = ride.plate_no ?? "N/A";
            document.getElementById("modal-pickup").textContent         = ride.pickup;
            document.getElementById("modal-dropoff").textContent        = ride.dropoff;
            document.getElementById("modal-distance").textContent       = ride.distance + " km";
            document.getElementById("modal-payment").textContent        = ride.payment_method;
            document.getElementById("modal-fare").textContent           = "₱" + parseFloat(ride.price).toFixed(2);
            document.getElementById("receiptModal").classList.add("active");
        }

        function closeReceipt() {
            document.getElementById("receiptModal").classList.remove("active");
        }

        function formatDate(datetime) {
            const d = new Date(datetime);
            return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) +
                   ' • ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }

        document.getElementById("receiptModal").addEventListener("click", function(e) {
            if (e.target === this) closeReceipt();
        });
    </script>

</body>
</html>
