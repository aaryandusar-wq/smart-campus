<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Faculty' && $_SESSION['role'] !== 'Admin')) {
    header("Location: dashboard.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'post_notice') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $department = $_POST['department'];
    $expiry_date = $_POST['expiry_date'];

    $stmt = $conn->prepare("INSERT INTO notices (title, content, category, priority, department, posted_by, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $title, $content, $category, $priority, $department, $user_id, $expiry_date);
    $stmt->execute();

    header("Location: admin_panel.php?msg=Notice Published Successfully");
    exit();
} 

elseif ($action === 'post_event') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $guest_limit = (int)$_POST['guest_limit'];

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location, guest_limit, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $title, $description, $event_date, $location, $guest_limit, $user_id);
    $stmt->execute();

    header("Location: admin_panel.php?msg=Event Published Successfully");
    exit();
}
?>