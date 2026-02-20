<?php
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id'];

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<h2>Welcome, <?= $user['name'] ?></h2>

<ul>
    <li><a href="weight.php">Weight</a></li>
    <li><a href="matchups.php">Weekly Matchups</a></li>
    <li><a href="weekly_leaderboard.php">Weekly Leaderboard</a></li>
    <li><a href="season_rankings.php">Season Rankings</a></li>
    <li><a href="profile.php">Your Profile</a></li>
</ul>
