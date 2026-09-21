<?php
session_start();
include 'db.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role, department FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department'];

            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Invalid Email or Password!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Smart Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 400px;">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-3">Campus Login</h4>
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <input type="email" name="email" class="form-control mb-2" placeholder="Email Address" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html> 