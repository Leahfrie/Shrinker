<?php
include '../includes/db.php';
include '../includes/auth.php';

$current_week = date('Y-m-d', strtotime('monday this week'));
$previous_week = date('Y-m-d', strtotime('monday last week'));

$matchups = $conn->query("
    SELECT * FROM weekly_matchups
    WHERE week_start = '$current_week'
");
?>

<h2>This Week's Matchups</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>User 1</th>
        <th>% Lost</th>
        <th>User 2</th>
        <th>% Lost</th>
        <th>Winner</th>
    </tr>

<?php
while ($m = $matchups->fetch_assoc()) {

    // Fetch user info
    $u1 = $conn->query("SELECT name FROM users WHERE id = {$m['user1_id']}")->fetch_assoc();
    $u2 = $conn->query("SELECT name FROM users WHERE id = {$m['user2_id']}")->fetch_assoc();

    // Function to calculate weekly % lost
    function weekly_percent($uid, $conn, $previous_week, $current_week) {
        $prev = $conn->query("
            SELECT weight 
            FROM weekly_weights
            WHERE user_id = $uid
            AND week_start = '$previous_week'
            ORDER BY date_logged DESC
            LIMIT 1
        ");

        $prev_last = ($prev->num_rows > 0) ? $prev->fetch_assoc()['weight'] : null;

        $curr = $conn->query("
            SELECT MIN(weight) AS lowest
            FROM weekly_weights
            WHERE user_id = $uid
            AND week_start = '$current_week'
        ");

        $current_lowest = $curr->fetch_assoc()['lowest'];

        if ($prev_last !== null && $current_lowest !== null) {
            return round((($prev_last - $current_lowest) / $prev_last) * 100, 2);
        }

        return 0;
    }

    $p1 = weekly_percent($m['user1_id'], $conn, $previous_week, $current_week);
    $p2 = weekly_percent($m['user2_id'], $conn, $previous_week, $current_week);

    $winner = "TBD";
    if ($m['winner_id']) {
        $winner = $conn->query("SELECT name FROM users WHERE id = {$m['winner_id']}")->fetch_assoc()['name'];
    }
?>

    <tr>
        <td><?= $u1['name'] ?></td>
        <td><?= $p1 ?>%</td>
        <td><?= $u2['name'] ?></td>
        <td><?= $p2 ?>%</td>
        <td><?= $winner ?></td>
    </tr>

<?php } ?>
</table>
