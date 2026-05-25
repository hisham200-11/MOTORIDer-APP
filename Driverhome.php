<?php
session_start();
include "connection.php";
// FIX: Check for driver_id
if (!isset($_SESSION['driver_id'])) {
    header("Location: Driverlogin.html");
    exit();
}

// Default value before JS kicks in
$driver_id = $_SESSION['driver_id'];
$result = $conn->query("SELECT status FROM driver_tbl WHERE driver_id = '$driver_id'");
$row = $result->fetch_assoc();

$driver_status = ($row['status'] === 'on') ? 'Online' : 'Offline';
$checked = ($row['status'] === 'on') ? 'checked' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MotoRide Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Zen+Dots&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="Driverhome.css">
</head>
<body>

    <div class="header">
        <div class="logo">
            <i class="fa-solid fa-motorcycle"></i> MotoRide
        </div>

        <div class="user-actions">
            <span>Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <i class="fa-solid fa-arrow-right-from-bracket"></i> 
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="card">
            <h2>Driver Status</h2>
            <p>Toggle your availability to receive ride requests</p>

            <div class="status-header">
                <span id="statusText"><?php echo $driver_status; ?></span>
                <input type="checkbox" id="statusToggle" <?php echo $checked; ?>>
            </div>
        </div>

        <div id="onlineContent">
            
            <div class="stats">
                <div class="stat-box">
                    <h2 id="totalRidesDisplay" style="color:#9333ea;">--</h2>
                    <p>Total rides</p>
                </div>
                <div class="stat-box">
                    <h2 id="totalEarningsDisplay" style="color:#16a34a;">₱--</h2>
                    <p>Total earnings</p>
                </div>
            </div>

            <div class="card" id="activeRideSection">
                <h3>Active Ride</h3>
                <div id="activeRide">
                    <?php include "getActiveRides.php"; ?>
                </div>
            </div>

            <div class="card" id="pendingRequestsSection" style="display:none; margin-top: 20px;">
                <h3>Incoming Requests</h3>
                <div id="rideRequests">
                    </div>
            </div>

        </div> <div id="offlineContent" style="display:none;">
            <div class="card offline" style="text-align: center; padding: 50px;">
                <i class="fa-regular fa-user" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                <h2>You're Offline</h2>
                <p>Toggle the switch above to go online and start receiving ride requests.</p>
            </div>
        </div>

    </div> <div class="bottom-nav">
        <div class="nav-item active">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </div>

        <div class="nav-item">
            <i class="fa-solid fa-receipt"></i>
            <a href="Driverhistory.php" style="text-decoration: none; color: inherit;"><span>History</span></a>
        </div>

        <div class="nav-item">
            <i class="fa-solid fa-user"></i>
            <a href="Driverprofile.php" style="text-decoration: none; color: inherit;">Profile</a>
        </div>
    </div>

    <script src="javafolder/driverDashboard.js"></script>

</body>
</html>
