<?php
// Database connection for Shrinker (InfinityFree compatible)
 
$host = "sql104.infinityfree.com";   // Replace XXX with your actual server number
$user = "if0_41200607";          // Example: if0_41200607
$pass = "shrinkertest123";          // Your database password
$dbname = "if0_41200607_shrinker_test";            // Example: if0_41200607_shrinker
 
$conn = new mysqli($host, $user, $pass, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>