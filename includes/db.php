<?php
// Database connection settings
$host = "sql104.infinityfree.com";
$user = "if0_41200607";
$pass = "shrinkertest123";
$dbname = "if0_41200607_shrinker_test";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/*
|--------------------------------------------------------------------------
| TIMEZONE FIX (InfinityFree Safe Version)
|--------------------------------------------------------------------------
| InfinityFree does NOT support named MySQL timezones.
| Using 'Pacific/Auckland' in MySQL causes ERR_EMPTY_RESPONSE.
|
| PHP timezone is safe.
| MySQL timezone MUST use numeric offset.
|--------------------------------------------------------------------------
*/

// PHP timezone (safe)
date_default_timezone_set('Pacific/Auckland');

// MySQL timezone (safe)
$conn->query("SET time_zone = '+13:00'");   // NZDT (summer)
// If it's winter, switch to:
// $conn->query("SET time_zone = '+12:00'");

?>
