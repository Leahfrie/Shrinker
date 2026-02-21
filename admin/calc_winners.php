<?php
include '../includes/db.php';
include '../includes/auth.php';

$current_week = date('Y-m-d', strtotime('monday this week'));
$previous_week = date('Y-m-d', strtotime('monday last week'));

$matchups = $conn->query("
    SELECT * FROM weekly_matchups
    WHERE week_start = '$current_week'
");

// Function to get latest weight for a given week
function latest_weight($conn, $uid, $week) {
    $q = $conn->query("
        SELECT weight 
        FROM weekly_weights
        WHERE user_id = $uid
        AND week_start = '$week'
        ORDER BY date_logged DESC
        LIMIT 1
    ");

    return ($q->num_rows > 0) ? $q->fetch_assoc()['weight'] : null;
}

while ($m = $matchups->fetch_assoc()) {

    $u1 = $m['user1_id'];
    $u2 = $m['user2_id'];

    // Handle bye week
    if ($u2 === null) {
        continue;
    }

    // Get previous week's last weights
    $prev1 = latest_weight($conn, $u1, $previous_week);
    $prev2 = latest_weight($conn, $u2, $previous_week);

    // Get current week's last weights
    $curr1 = latest_weight($conn, $u1, $current_week);
    $curr2 = latest_weight($conn, $u2, $current_week);

    // If either user has no weigh-ins, skip matchup
    if ($prev1 === null || $curr1 === null || $prev2 === null || $curr2 === null) {
        continue;
    }

    // Calculate weekly % lost
    $p1 = (($prev1 - $curr1) / $prev1) * 100;
    $p2 = (($prev2 - $curr2) / $prev2) * 100;

    // Determine winner
    if ($p1 > $p2) {
        $winner = $u1;
    } else {
        $winner = $u2;
    }

    // Save winner
    $conn->query("
        UPDATE weekly_matchups 
        SET winner_id = $winner 
        WHERE id = {$m['id']}
    ");

    // Award 1 point
    $conn->query("
        UPDATE users 
        SET points = points + 1 
        WHERE id = $winner
    ");
}

echo "Weekly winners calculated!";
