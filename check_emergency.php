<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['emergency' => false]));
}

$user_dept = $_SESSION['department'];

// Check for unexpired active Emergency notices matching user's department
$sql = "SELECT title, content FROM notices 
        WHERE priority = 'Emergency' 
        AND (department = ? OR department = 'All') 
        AND (expiry_date >= CURDATE() OR expiry_date IS NULL) 
        ORDER BY created_at DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_dept);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['emergency' => true, 'title' => $row['title'], 'content' => $row['content']]);
} else {
    echo json_encode(['emergency' => false]);
}
?>