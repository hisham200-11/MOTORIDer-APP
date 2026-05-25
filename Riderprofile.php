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

    <link rel="stylesheet" href="Riderhome.css">

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
      <span>
      <span>Hi, <?php echo $_SESSION['name']; ?></span>
      <a href="logout.php" class="logout">Logout</a>
      </span>
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
        <input type="text" id="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>">
      </div>

      <div class="input-group">
        <label>Username</label>
        <input type="email" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
      </div>

      <div class="input-group">
        <label>Phone Number</label>
        <input type="text" id="contactNo" value="<?php echo htmlspecialchars($_SESSION['contact_no']); ?>">
      </div>

      <div class="input-group">
        <label>GCash Number</label>
        <input type="text" id="gcash" value="<?php echo htmlspecialchars($_SESSION['gcash'] ?? ''); ?>"> 
      </div>

      <button class="btn" id="saveChangesBtn" onclick="updateRider()">Save Changes</button>
      <p id="status" class="status"></p>
    </div>

  </div>

  <!-- BOTTOM NAV  -->
  <div class="bottom-nav">
    <div class="nav-item">
        <i class="fa-solid fa-house"></i>
        <a href="Riderhome.php" style="text-decoration: none; color: inherit;"><span>Home</span></a>
    </div>

    <div class="nav-item">
        <i class="fa-solid fa-receipt"></i>
        <a href="Riderhistory.php" style="text-decoration: none; color: inherit;"><span>History</span></a>
    </div>

    <div class="nav-item active">
        <i class="fa-solid fa-user"></i>
        <span>Profile</span>
    </div>
</div>

<script src="javafolder/updateProfile.js"></script>

</body>
</html>
