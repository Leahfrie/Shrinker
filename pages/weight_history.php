<?php
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id'];

$data = $conn->query("SELECT week_start, weight FROM weekly_weights WHERE user_id=$user_id ORDER BY week_start ASC");

$dates = [];
$weights = [];

while ($row = $data->fetch_assoc()) {
    $dates[] = $row['week_start'];
    $weights[] = $row['weight'];
}
?>

<h2>Your Weight History</h2>

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
            fill: false
        }]
    }
});
</script>
