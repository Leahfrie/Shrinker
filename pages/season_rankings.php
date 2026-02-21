<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/nav.php';

$users = $conn->query("SELECT id, name, starting_weight FROM users");

$rankings = [];

// Get latest weight for any user
function latest_weight_any($conn, $uid) {
    $q = $conn->query("
        SELECT weight
        FROM weekly_weights
        WHERE user_id = $uid
        ORDER BY date_logged DESC
        LIMIT 1
    ");

    return ($q->num_rows > 0) ? $q->fetch_assoc()['weight'] : null;
}

while ($u = $users->fetch_assoc()) {
    $uid = $u['id'];
    $start = $u['starting_weight'];

    // Latest weight
    $current = latest_weight_any($conn, $uid);

    // If no weigh-ins yet → skip
    if ($current === null) {
        continue;
    }

    // Overall % lost
    $percent = (($start - $current) / $start) * 100;
    $percent = round($percent, 2);

    $rankings[] = [
        'name' => $u['name'],
        'percent' => $percent
    ];
}

// Sort by overall % lost
usort($rankings, function($a, $b) {
    return $b['percent'] <=> $a['percent'];
});
?>

<h2>Season Rankings (Overall % Lost)</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Rank</th>
        <th>Name</th>
        <th>Overall % Lost</th>
    </tr>

    <?php $rank = 1; ?>
    <?php foreach ($rankings as $row): ?>
        <tr>
            <td><?= $rank++ ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['percent'] ?>%</td>
        </tr>
    <?php endforeach; ?>
</table>
