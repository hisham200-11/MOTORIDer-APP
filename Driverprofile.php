<?php 
session_start(); 

if (!isset($_SESSION['driver_id'])) {
    header("Location: Driverlogin.html");
    exit();
}

include "connection.php";

// Fetch latest driver data from DB
$id = $_SESSION['driver_id'];
$stmt = $conn->prepare("SELECT * FROM driver_tbl WHERE driver_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Profile</title>
    <link rel="stylesheet" href="Driverhome.css">
    <link href='https://fonts.googleapis.com/css?family=Zen Dots' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="logo">
  <i class="fa-solid fa-motorcycle"></i> MotoRide
</div>
  <div class="user-actions">
    <span>Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<!-- PROFILE CONTENT -->
<div class="container column">

  <!-- PERSONAL INFO -->
  <div class="card">
    <h3>Personal Information</h3>
    <p class="subtitle">Manage your account details</p>

    <div class="input-group">
      <label>Full Name</label>
      <input type="text" id="name" value="<?php echo htmlspecialchars($driver['name']); ?>">
    </div>
    <div class="input-group">
      <label>Username</label>
      <input type="text" id="username" value="<?php echo htmlspecialchars($driver['username']); ?>">
    </div>
    <div class="input-group">
      <label>Phone Number</label>
      <input type="text" id="contactNo" value="<?php echo htmlspecialchars($driver['contact_no']); ?>">
    </div>
  </div>

  <!-- VEHICLE INFO -->
  <div class="card">
    <h3>Vehicle Information</h3>
    <p class="subtitle">Your registered vehicle details</p>

    <div class="row">
      <div class="input-group">
        <label>Vehicle Type</label>
        <input type="text" id="vehicle_type" value="<?php echo htmlspecialchars($driver['brand'] ?? ''); ?>">
      </div>
      <div class="input-group">
        <label>Vehicle Model</label>
        <input type="text" id="vehicle_model" value="<?php echo htmlspecialchars($driver['model'] ?? ''); ?>">
      </div>
    </div>
    <div class="input-group">
      <label>Vehicle Color</label>
      <input type="text" id="vehicle_color" value="<?php echo htmlspecialchars($driver['color'] ?? ''); ?>">
    </div>
    <div class="input-group">
      <label>License Plate</label>
      <input type="text" id="vehicle_plate" value="<?php echo htmlspecialchars($driver['plate_no'] ?? ''); ?>">
    </div>
  </div>

  <!-- DRIVER VERIFICATION -->
  <div class="card">
    <h3>Driver Verification</h3>
    <p class="subtitle">Your license details</p>

    <div class="row">
      <div class="input-group">
        <label>Driver License Number</label>
        <input type="text" id="driver_license" value="<?php echo htmlspecialchars($driver['driver_license'] ?? ''); ?>">
      </div>
      <div class="input-group">
        <label>License Expiry</label>
        <input type="date" id="license_expiry" value="<?php echo htmlspecialchars($driver['license_expiry'] ?? ''); ?>">
      </div>
    </div>
  </div>

  <!-- PAYMENT -->
  <div class="card">
    <h3>Payment Details</h3>
    <p class="subtitle">Your GCash information</p>

    <div class="input-group">
      <label>GCash Number</label>
      <input type="text" id="gcash" value="<?php echo htmlspecialchars($driver['gcash'] ?? ''); ?>">
    </div>
  </div>

  <!-- SAVE -->
  <div class="card">
    <button class="btn" onclick="updateDriver()">Save Changes</button>
    <p id="status" class="status"></p>
  </div>

</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
  <div class="nav-item">
    <i class="fa-solid fa-house"></i>
    <a href="Driverhome.php" style="text-decoration: none; color: inherit;"><span>Home</span></a>
  </div>

  <div class="nav-item">
    <i class="fa-solid fa-receipt"></i>
    <a href="Driverhistory.php" style="text-decoration: none; color: inherit;"><span>History</span></a>
  </div>

  <div class="nav-item active">
    <i class="fa-solid fa-user"></i>
    <span>Profile</span>
  </div>
</div>

<script src="javafolder/updateProfile.js"></script>
</body>
</html>
