<?php
include "connection.php";

$role = $_POST['role'];

if ($role === 'rider') {

    $name     = $_POST['name'];
    $contact  = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $gcash    = $_POST['gcash'];

    if (empty($name) || empty($contact) || empty($username) || empty($password) || empty($gcash)) { 
        echo "Please fill all fields!";
        exit();
    }
    if (!preg_match('/^[0-9]+$/', $contact)) {
        echo "Error: Contact number must contain only numbers!";
        exit();
    }

    $check_query = "SELECT * FROM customer_tbl WHERE contact_no='$contact' OR username='$username'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "User already exists!";
    } else {
        $sql = "INSERT INTO customer_tbl (name, contact_no, username, password, gcash) 
                VALUES ('$name', '$contact', '$username', '$password', '$gcash')"; 

        if ($conn->query($sql) === TRUE) {
            echo "Registered successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

} else if ($role === 'driver') {

    $name     = $_POST['name'];
    $contact  = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $plate_no = $_POST['plate_no'];
    $model    = $_POST['model'];
    $color    = $_POST['color'];
    $license  = $_POST['driver_license'];
    $expiry   = $_POST['license_expiry'];
    $gcash    = $_POST['gcash'];
    $brand    = $_POST['driver_brand'];

    if (empty($name) || empty($contact) || empty($username) || empty($password) || 
        empty($plate_no) || empty($brand) || empty($model) || empty($color) || 
        empty($license) || empty($expiry) || empty($gcash)) {
        echo "Please fill all fields!";
        exit();
    }
    if (!preg_match('/^[0-9]+$/', $contact)) {
        echo "Error: Contact number must contain only numbers!";
        exit();
    }

    $check_query = "SELECT * FROM driver_tbl WHERE contact_no='$contact' OR username='$username' OR plate_no='$plate_no'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "User already exists!";
    } else {
        $sql = "INSERT INTO driver_tbl (name, contact_no, username, password, plate_no, brand, model, color, driver_license, license_expiry, gcash) 
                VALUES ('$name', '$contact', '$username', '$password', '$plate_no', '$brand', '$model', '$color', '$license', '$expiry', '$gcash')";

        if ($conn->query($sql) === TRUE) {
            echo "Registered successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }

} else {
    echo "Error: Invalid user role!";
}
?>
