<?php
include '../includes/db.php';

$week_start = date('Y-m-d', strtotime('monday this week'));

$sql = "
SELECT u.name,
       ((u.starting_weight - w.weight) / u.starting_weight) * 100 AS percent_lost
FROM users u
JOIN weekly_weights w ON u.id = w.user_id
WHERE w.week_start = '$week_start'
ORDER BY percent_lost DESC
";

$result = $conn->query($sql);
?>

<h2>Weekly Leaderboard</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <p>
        <?= $row['name'] ?> — <?= number_format($row['percent_lost'], 2) ?>%
    </p>
<?php endwhile; ?>
