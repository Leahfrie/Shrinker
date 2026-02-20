<?php
include '../includes/db.php';

$week_start = date('Y-m-d', strtotime('monday this week'));

$conn->query("DELETE FROM weekly_matchups WHERE week_start='$week_start'");

$users = [];
$res = $conn->query("SELECT id FROM users");
while ($row = $res->fetch_assoc()) $users[] = $row['id'];

shuffle($users);

for ($i = 0; $i < count($users); $i += 2) {
    if (!isset($users[$i+1])) break;

    $u1 = $users[$i];
    $u2 = $users[$i+1];

    $conn->query("INSERT INTO weekly_matchups (week_start, user1_id, user2_id)
                  VALUES ('$week_start', $u1, $u2)");
}

echo "Matchups created!";
