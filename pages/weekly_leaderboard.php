<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/nav.php';

$current_week = date('Y-m-d', strtotime('monday this week'));
$previous_week = date('Y-m-d', strtotime('monday last week'));

$users = $conn->query("SELECT id, name, starting_weight FROM users");

$leaderboard = [];

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

while ($u = $users->fetch_assoc()) {
    $uid = $u['id'];

    // Previous week's last weight
    $prev_last = latest_weight($conn, $uid, $previous_week);

    // If no previous week weight → use starting weight
    if ($prev_last === null) {
        $prev_last = $u['starting_weight'];
    }

    // Current week's latest weight
    $curr_last = latest_weight($conn, $uid, $current_week);

    // If no weigh-in this week → skip user
    if ($curr_last === null) {
        continue;
    }

    // Calculate % lost
    $percent = (($prev_last - $curr_last) / $prev_last) * 100;
    $percent = round($percent, 2);

    $leaderboard[] = [
        'name' => $u['name'],
        'percent' => $percent
    ];
}

// Sort by % lost DESC
usort($leaderboard, function($a, $b) {
    return $b['percent'] <=> $a['percent'];
});
?>

<h2>Weekly Leaderboard</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Rank</th>
        <th>Name</th>
        <th>% Lost</th>
    </tr>

    <?php $rank = 1; ?>
    <?php foreach ($leaderboard as $row): ?>
        <tr>
            <td><?= $rank++ ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['percent'] ?>%</td>
        </tr>
    <?php endforeach; ?>
</table>
