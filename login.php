<?php
session_start();
include "connection.php";

// ====================================================================
// STEP 1: GET ROLE (rider or driver)
// ====================================================================
$role = $_POST['role'];


// ====================================================================
// PATH A: RIDER LOGIN
// ====================================================================
if ($role === 'rider') {

    // 1. GET INPUTS
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 2. VALIDATE INPUTS
    if (empty($username) || empty($password)) {
        echo "Please fill all fields!";
        exit();
    }

    // 3. CHECK DATABASE
    $sql = "SELECT * FROM customer_tbl WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();
        $_SESSION['customer_id'] = $row['customer_id']; // Store customer ID in session
        $_SESSION['contact_no'] = $row['contact_no']; // Store contact number in session
        $_SESSION['name'] = $row['name']; // Store name in session
        $_SESSION['username'] = $username; // Store username in session
        $_SESSION['gcash'] = $row['gcash']; // Store gcash in session
        $_SESSION['gcash_balance'] = $row['gcash_balance']; // Store gcash balance in session
        
        echo "Login successful!";
        // OPTIONAL: session_start(); then store user data
    } else {
        echo "Invalid username or password!";
    }
}


// ====================================================================
// PATH B: DRIVER LOGIN
// ====================================================================
else if ($role === 'driver') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "Please fill all fields!";
        exit();
    }

    $sql = "SELECT * FROM driver_tbl WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // THE FIX: Save the driver's info into the server's memory!
        $_SESSION['contact_no'] = $row['contact_no']; 
        $_SESSION['driver_id'] = $row['driver_id']; 
        $_SESSION['name'] = $row['name']; 
        $_SESSION['username'] = $username; 
        
        echo "Login successful!";
    } else {
        echo "Invalid username or password!";
    }
}

// ====================================================================
// PATH C: INVALID ROLE
// ====================================================================
else {
    echo "Error: Invalid user role!";
}
?>
