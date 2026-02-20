<?php
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = $user_id";
$user = $conn->query($sql)->fetch_assoc();
?>

<h2>Your Profile</h2>

<p>Name: <?= $user['name'] ?></p>
<p>Starting Weight: <?= $user['starting_weight'] ?></p>
<p>Points: <?= $user['points'] ?></p>
