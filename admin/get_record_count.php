<?php
// get_record_count.php

// Database connection

include('connect/connect.php');

$conn = new mysqli($servername, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Fetch record count from the database
$sql = "SELECT COUNT(*) as count FROM dane_klienci";
$result = $conn->query($sql);

if ($result === false) {
    echo "Error in SQL query: " . $conn->error;
} else {
    $row = $result->fetch_assoc();
    echo $row['count'];
}

$conn->close();
?>
