<?php
// Database configuration
$servername = "localhost";
$db_name = "my_ecommerce";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//echo"Connected successfully";
?>