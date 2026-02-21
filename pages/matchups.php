<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/nav.php';

$current_week = date('Y-m-d', strtotime('monday this week'));
$previous_week = date('Y-m-d', strtotime('monday last week'));

$matchups = $conn->query("
    SELECT * FROM weekly_matchups
    WHERE week_start = '$current_week'
");

// Function to calculate weekly % lost
function weekly_percent($uid, $conn, $previous_week, $current_week) {

    // 1. Get previous week's last weight
    $prev = $conn->query("
        SELECT weight
        FROM weekly_weights
        WHERE user_id = $uid
        AND week_start = '$previous_week'
        ORDER BY date_logged DESC
        LIMIT 1
    ");

    if ($prev->num_rows > 0) {
        $prev_last = $prev->fetch_assoc()['weight'];
    } else {
        // No previous week weight → use starting weight
        $s = $conn->query("SELECT starting_weight FROM users WHERE id = $uid")->fetch_assoc();
        $prev_last = $s['starting_weight'];
    }

    // 2. Get current week's last weight
    $curr = $conn->query("
        SELECT weight
        FROM weekly_weights
        WHERE user_id = $uid
        AND week_start = '$current_week'
        ORDER BY date_logged DESC
        LIMIT 1
    ");

    if ($curr->num_rows > 0) {
        $curr_last = $curr->fetch_assoc()['weight'];
    } else {
        // No weigh-in this week → cannot calculate
        return 0;
    }

    // 3. Calculate % lost
    return round((($prev_last - $curr_last) / $prev_last) * 100, 2);
}

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

    $u1 = $conn->query("SELECT name FROM users WHERE id = {$m['user1_id']}")->fetch_assoc()['name'];

    $p1 = weekly_percent($m['user1_id'], $conn, $previous_week, $current_week);

    if ($m['user2_id']) {
        $u2 = $conn->query("SELECT name FROM users WHERE id = {$m['user2_id']}")->fetch_assoc()['name'];
        $p2 = weekly_percent($m['user2_id'], $conn, $previous_week, $current_week);
    } else {
        $u2 = "BYE";
        $p2 = "-";
    }

    $winner = "TBD";
    if ($m['winner_id']) {
        $winner = $conn->query("SELECT name FROM users WHERE id = {$m['winner_id']}")->fetch_assoc()['name'];
    }
?>
    <tr>
        <td><?= $u1 ?></td>
        <td><?= $p1 ?>%</td>
        <td><?= $u2 ?></td>
        <td><?= $p2 ?>%</td>
        <td><?= $winner ?></td>
    </tr>
<?php } ?>
</table>
