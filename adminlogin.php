<?php
session_start();

// Already logged in
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Simple hardcoded credentials — change these!
    $admin_username = 'admin';
    $admin_password = 'admin12345';

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MotoRide Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminlogin.css">

</head>
<body>

<div class="login-wrap">
    <div class="login-logo">
        <div class="icon"><i class="fa-solid fa-motorcycle"></i></div>
        <h1>MotoRide</h1>
        <p>Admin Panel — Sign in to continue</p>
    </div>

    <div class="login-card">
        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Enter username" required autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In
            </button>
        </form>
    </div>
</div>

</body>
</html>
