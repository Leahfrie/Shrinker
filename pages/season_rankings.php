<?php
include '../includes/db.php';
include '../includes/auth.php';

// Get all users
$users = $conn->query("SELECT id, name, starting_weight, points FROM users");

// Build leaderboard array
$leaderboard = [];

while ($u = $users->fetch_assoc()) {
    $uid = $u['id'];

    // Get latest logged weight
    $w = $conn->query("
        SELECT weight 
        FROM weekly_weights 
        WHERE user_id = $uid 
        ORDER BY week_start DESC 
        LIMIT 1
    ");

    if ($w->num_rows > 0) {
        $latest = $w->fetch_assoc()['weight'];

        // Calculate percentage lost
        $percent = (($u['starting_weight'] - $latest) / $u['starting_weight']) * 100;
    } else {
        // No weight logged yet
        $percent = 0;
    }

    $leaderboard[] = [
        'name' => $u['name'],
        'points' => $u['points'],
        'percent' => round($percent, 2)
    ];
}

// Sort by percentage lost first, then points
usort($leaderboard, function($a, $b) {
    if ($a['percent'] == $b['percent']) {
        return $b['points'] <=> $a['points']; // tiebreaker
    }
    return $b['percent'] <=> $a['percent']; // highest % first
});
?>

<h2>Season Rankings</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Rank</th>
        <th>Name</th>
        <th>% Lost</th>
        <th>Points</th>
    </tr>

    <?php $rank = 1; ?>
    <?php foreach ($leaderboard as $row): ?>
        <tr>
            <td><?= $rank++ ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['percent'] ?>%</td>
            <td><?= $row['points'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
