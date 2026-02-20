<?php
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id'];

// Handle weight submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = $_POST['weight'];
    $week_start = date('Y-m-d', strtotime('monday this week'));

    $conn->query("INSERT INTO weekly_weights (user_id, week_start, weight)
                  VALUES ($user_id, '$week_start', $weight)");
}

// Fetch weight history
$data = $conn->query("SELECT week_start, weight 
                      FROM weekly_weights 
                      WHERE user_id=$user_id 
                      ORDER BY week_start ASC");

$dates = [];
$weights = [];

while ($row = $data->fetch_assoc()) {
    $dates[] = $row['week_start'];
    $weights[] = $row['weight'];
}
?>

<h2>Log Weight + History</h2>

<!-- Weight Log Form -->
<form method="POST">
    <label>Weight (kg):</label>
    <input type="number" step="0.1" name="weight" required>
    <button type="submit">Submit</button>
</form>

<hr>

<!-- Chart -->
<canvas id="chart"></canvas>

<script src="../assets/chart.min.js"></script>

<script>
const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
            label: 'Weight (kg)',
            data: <?= json_encode($weights) ?>,
            borderColor: 'blue',
            borderWidth: 2,
            fill: false,
            tension: 0.2
        }]
    }
});
</script>
