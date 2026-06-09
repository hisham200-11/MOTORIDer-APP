<?php 
session_start(); 

if (!isset($_SESSION['customer_id'])) {
    header("Location: Riderlogin.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- DARK MODE ADDITION START -->
    <link rel="stylesheet" href="dark-mode.css">
    <!-- DARK MODE ADDITION END -->

    <link rel="stylesheet" href="Riderhome.css">

    <link href='https://fonts.googleapis.com/css?family=Zen Dots' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Leaflet Map Library -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- DARK MODE ADDITION START -->
    <script src="dark-mode.js"></script>
    <!-- DARK MODE ADDITION END -->

</head>
<body>
    
<!-- HEADER -->
  <div class="header">
        <div class="logo">
            <i class="fa-solid fa-motorcycle"></i> MotoRide
        </div>

        <!-- THE FLOATING CENTER BUTTON -->
        <button class="theme-toggle" onclick="toggleDarkMode()"></button>

        <div class="user-actions">
            <span>Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="logout.php" class="logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
        </div>
    </div>

  <!-- MAIN CONTENT -->
  <div class="split-layout">
    <div class="info-side">
      <div class="card booking-card">
        <h2>Request a Ride</h2>
        <p>Click on map to pin pickup & dropoff</p>

        <div class="input-group">
          <label>Pickup Location</label>
          <input type="text" id="pickup" placeholder="Search pickup location or click on map">
        </div>

        <div class="input-group">
          <label>Dropoff Location</label>
          <input type="text" id="dropoff" placeholder="Search dropoff location or click on map">
        </div>

        <div class="input-group">
          <label>Payment Method</label>
          <select id="paymentMethod">
            <option disabled selected value="">Select payment method</option>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
          </select>
        </div>

        <button class="btn-clear" onclick="clearAllAndRecenter()">
          <i class="fa-solid fa-redo"></i> Clear & Reset
        </button>

        <button class="btn-locate" onclick="locateUser()">
          <i class="fa-solid fa-location-dot"></i> Find My Location
        </button>

        <button class="btn" onclick="calculateFare()">Find Available Drivers</button>
      </div>

      <div id="rideStatus">
        <!-- Ride status will dynamically appear here -->
      </div>

      <div id="driversPanel" class="drivers-panel hidden">
        <h3>Available Drivers Nearby</h3>
        <p class="subtitle">Choose your preferred driver</p>
        <div id="driverList"></div>
      </div>
    </div>

    <div class="map-side">
      <div id="map" class="map-display"></div>
    </div>
  </div>

  <!-- BOTTOM NAV -->
  <div class="bottom-nav">
    <div class="nav-item active">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </div>

    <div class="nav-item">
        <i class="fa-solid fa-receipt"></i>
        <a href="Riderhistory.php" style="text-decoration: none; color: inherit;"><span>History</span></a>
    </div>

    <div class="nav-item">
        <i class="fa-solid fa-user"></i>
        <a href="Riderprofile.php" style="text-decoration: none; color: inherit;"><span>Profile</span></a>
    </div>
</div>

  <!-- Leaflet Map & Ride Booking Script -->

  <script src="javafolder/riderRegistration.js"></script>
</body>
</html>
