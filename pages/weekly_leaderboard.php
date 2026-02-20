<?php
include '../includes/db.php';
include '../includes/auth.php';

$current_week = date('Y-m-d', strtotime('monday this week'));
$previous_week = date('Y-m-d', strtotime('monday last week'));

$users = $conn->query("SELECT id, name FROM users");

$leaderboard = [];

while ($u = $users->fetch_assoc()) {
    $uid = $u['id'];

    // Previous week's last weight
    $prev = $conn->query("
        SELECT weight 
        FROM weekly_weights
        WHERE user_id = $uid
        AND week_start = '$previous_week'
        ORDER BY date_logged DESC
        LIMIT 1
    ");

    $prev_last = ($prev->num_rows > 0) ? $prev->fetch_assoc()['weight'] : null;

    // Current week's lowest weight
    $curr = $conn->query("
        SELECT MIN(weight) AS lowest
        FROM weekly_weights
        WHERE user_id = $uid
        AND week_start = '$current_week'
    ");

    $current_lowest = $curr->fetch_assoc()['lowest'];

    // Calculate % lost
    if ($prev_last !== null && $current_lowest !== null) {
        $percent = (($prev_last - $current_lowest) / $prev_last) * 100;
        $percent = round($percent, 2);

        $leaderboard[] = [
            'name' => $u['name'],
            'percent' => $percent
        ];
    }
}

// Sort by % lost
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
