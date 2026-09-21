<?php
include 'db.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $department = $_POST['department'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $department);

    if ($stmt->execute()) {
        $message = "Account registered successfully! <a href='login.php'>Login here</a>";
    } else {
        $message = "Registration failed: Email already exists.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - Smart Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 450px;">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-3">Campus Registration</h4>
        <?php if($message) echo "<div class='alert alert-info'>$message</div>"; ?>
        <form method="POST">
            <input type="text" name="name" class="form-control mb-2" placeholder="Full Name" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Email Address" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
            <select name="role" class="form-select mb-2">
                <option value="Student">Student</option>
                <option value="Faculty">Faculty</option>
                <option value="Admin">Admin</option>
            </select>
            <input type="text" name="department" class="form-control mb-3" placeholder="Department (e.g., Computer)" required>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>
</body>
</html>