<?php
include '../includes/db.php';

$week_start = date('Y-m-d', strtotime('monday this week'));

$sql = "SELECT m.*, u1.name AS user1, u2.name AS user2
        FROM weekly_matchups m
        JOIN users u1 ON m.user1_id = u1.id
        JOIN users u2 ON m.user2_id = u2.id
        WHERE m.week_start = '$week_start'";

$result = $conn->query($sql);
?>

<h2>Weekly Matchups</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <p>
        <?= $row['user1'] ?> vs <?= $row['user2'] ?>
    </p>
<?php endwhile; ?>
