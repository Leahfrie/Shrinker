<?php
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = $_POST['weight'];
    $week_start = date('Y-m-d', strtotime('monday this week'));

    $conn->query("INSERT INTO weekly_weights (user_id, week_start, weight)
                  VALUES ($user_id, '$week_start', $weight)");

    echo "Weight logged!";
}
?>

<h2>Log Weight</h2>

<form method="POST">
    Weight (kg): <input type="number" step="0.1" name="weight" required><br>
    <button>Submit</button>
</form>
