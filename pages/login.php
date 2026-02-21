<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $email = $conn->real_escape_string($email);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit;
        }
    }

    echo "Invalid login.";
}
?>

<h2>Login</h2>

<form method="POST">
    Email: <input name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button>Login</button>
</form>
