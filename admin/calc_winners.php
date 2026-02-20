<?php
include '../includes/db.php';

$week_start = date('Y-m-d', strtotime('monday this week'));

$matchups = $conn->query("SELECT * FROM weekly_matchups WHERE week_start='$week_start'");

while ($m = $matchups->fetch_assoc()) {
    $u1 = $m['user1_id'];
    $u2 = $m['user2_id'];

    $w1 = $conn->query("SELECT weight FROM weekly_weights WHERE user_id=$u1 AND week_start='$week_start' ORDER BY id DESC LIMIT 1")->fetch_assoc()['weight'];
    $w2 = $conn->query("SELECT weight FROM weekly_weights WHERE user_id=$u2 AND week_start='$week_start' ORDER BY id DESC LIMIT 1")->fetch_assoc()['weight'];

    $s1 = $conn->query("SELECT starting_weight FROM users WHERE id=$u1")->fetch_assoc()['starting_weight'];
    $s2 = $conn->query("SELECT starting_weight FROM users WHERE id=$u2")->fetch_assoc()['starting_weight'];

    $p1 = ($s1 - $w1) / $s1;
    $p2 = ($s2 - $w2) / $s2;

    $winner = ($p1 > $p2) ? $u1 : $u2;

    $conn->query("UPDATE weekly_matchups SET winner_id=$winner WHERE id=" . $m['id']);
    $conn->query("UPDATE users SET points = points + 1 WHERE id=$winner");
}

echo "Winners calculated!";
