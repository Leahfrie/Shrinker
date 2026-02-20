<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $start = $_POST['starting_weight'];

    $sql = "INSERT INTO users (name, email, password, starting_weight)
            VALUES ('$name', '$email', '$pass', $start)";
    $conn->query($sql);

    echo "Account created. <a href='login.php'>Login</a>";
    exit;
}
?>

<h2>Register</h2>

<form method="POST">
    Name: <input name="name"><br>
    Email: <input name="email"><br>
    Password: <input type="password" name="password"><br>
    Starting Weight: <input type="number" step="0.1" name="starting_weight"><br>
    <button>Register</button>
</form>
