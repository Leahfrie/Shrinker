<?php
include '../includes/db.php';

$sql = "SELECT name, points FROM users ORDER BY points DESC";
$result = $conn->query($sql);
?>

<h2>Season Rankings</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <p>
        <?= $row['name'] ?> — <?= $row['points'] ?> points
    </p>
<?php endwhile; ?>
