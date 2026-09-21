<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['event_id'])) {
    header("Location: events.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];
$event_id = (int)$_GET['event_id'];

// Prevent duplicate RSVP
$check = $conn->prepare("SELECT id FROM rsvps WHERE event_id = ? AND user_id = ?");
$check->bind_param("ii", $event_id, $user_id);
$check->execute();

if ($check->get_result()->num_rows == 0) {
    // Generate unique hash token for QR Attendance verification
    $qr_hash = md5($user_id . '_' . $event_id . '_' . time());
    
    $stmt = $conn->prepare("INSERT INTO rsvps (event_id, user_id, qr_code_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $event_id, $user_id, $qr_hash);
    $stmt->execute();
}

header("Location: events.php");
exit();
?>