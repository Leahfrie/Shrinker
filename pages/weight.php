<?php
include '../includes/db.php';
include '../includes/auth.php';

$user_id = $_SESSION['user_id'];

// Handle weight + photo submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = $_POST['weight'];
    $week_start = date('Y-m-d', strtotime('monday this week'));

    // Handle photo upload
    $photo_name = null;

    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_name = "photo_" . $user_id . "_" . time() . "." . $ext;
        $target = "../uploads/" . $photo_name;

        move_uploaded_file($_FILES['photo']['tmp_name'], $target);
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO weekly_weights (user_id, week_start, weight, photo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $week_start, $weight, $photo_name);
    $stmt->execute();
}

// Fetch weight history
$data = $conn->query("SELECT week_start, weight, photo 
                      FROM weekly_weights 
                      WHERE user_id=$user_id 
                      ORDER BY week_start ASC");

$dates = [];
$weights = [];
$photos = [];

while ($row = $data->fetch_assoc()) {
    $dates[] = $row['week_start'];
    $weights[] = $row['weight'];
    $photos[] = $row['photo'];
}
?>

<h2>Log Weight + Progress Photo</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Weight (kg):</label>
    <input type="number" step="0.1" name="weight" required>

    <br><br>

    <label>Progress Photo (optional):</label>
    <input type="file" name="photo" accept="image/*">

    <br><br>

    <button type="submit">Log Entry</button>
</form>

<hr>

<h3>Your Weight History</h3>

<div style="width:40%; min-width:250px; margin:auto;">
    <canvas id="chart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
            label: 'Weight (kg)',
            data: <?= json_encode($weights) ?>,
            borderColor: 'blue',
            borderWidth: 2,
            fill: false
        }]
    }
});
</script>

<hr>

<h3>Progress Photos</h3>

<?php foreach ($photos as $p): ?>
    <?php if ($p): ?>
        <img src="../uploads/<?= $p ?>" width="150" style="margin:10px; border:1px solid #ccc;">
    <?php endif; ?>
<?php endforeach; ?>
