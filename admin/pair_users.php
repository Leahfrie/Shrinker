<?php
include '../includes/db.php';
include '../includes/auth.php';

// Determine week start
$week_start = date('Y-m-d', strtotime('monday this week'));

// Get all users
$users = [];
$res = $conn->query("SELECT id FROM users");
while ($row = $res->fetch_assoc()) {
    $users[] = $row['id'];
}

// Shuffle for randomness
shuffle($users);

// Function: check if two users have faced each other before
function have_faced_before($conn, $u1, $u2) {
    $q = $conn->query("
        SELECT id FROM weekly_matchups
        WHERE (user1_id = $u1 AND user2_id = $u2)
           OR (user1_id = $u2 AND user2_id = $u1)
        LIMIT 1
    ");
    return $q->num_rows > 0;
}

$matchups = [];

while (count($users) > 1) {
    $u1 = array_shift($users);

    // Find someone u1 has NOT faced before
    $found = false;

    foreach ($users as $index => $u2) {
        if (!have_faced_before($conn, $u1, $u2)) {
            $matchups[] = [$u1, $u2];
            unset($users[$index]);
            $users = array_values($users);
            $found = true;
            break;
        }
    }

    // If no valid opponent found → give a bye
    if (!$found) {
        $matchups[] = [$u1, null];
    }
}

// If one user left → bye
if (count($users) == 1) {
    $matchups[] = [$users[0], null];
}

// Insert matchups into DB
foreach ($matchups as $m) {
    $u1 = $m[0];
    $u2 = $m[1];

    $stmt = $conn->prepare("
        INSERT INTO weekly_matchups (week_start, user1_id, user2_id)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sii", $week_start, $u1, $u2);
    $stmt->execute();
}

echo "Matchups created for week $week_start";
