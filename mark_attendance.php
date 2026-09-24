<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Ensure requester has administrative permission
$user_role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($user_role !== 'Faculty' && $user_role !== 'Admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$qr_hash = trim($_POST['qr_hash'] ?? '');

if (empty($qr_hash)) {
    echo json_encode(['status' => 'error', 'message' => 'No QR hash received.']);
    exit();
}

// Locate matching RSVP record along with Student & Event details
$stmt = $conn->prepare("
    SELECT r.id, r.attended, u.name AS student_name, e.title AS event_title 
    FROM rsvps r
    JOIN users u ON r.user_id = u.id
    JOIN events e ON r.event_id = e.id
    WHERE r.qr_code_hash = ?
");
$stmt->bind_param("s", $qr_hash);
$stmt->execute();
$result = $stmt->get_result();

if ($rsvp = $result->fetch_assoc()) {
    if ($rsvp['attended'] == 1) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Pass already scanned for ' . htmlspecialchars($rsvp['student_name']) . ' (' . htmlspecialchars($rsvp['event_title']) . ').'
        ]);
    } else {
        // Mark attendance as verified
        $update_stmt = $conn->prepare("UPDATE rsvps SET attended = 1 WHERE id = ?");
        $update_stmt->bind_param("i", $rsvp['id']);
        
        if ($update_stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Attendance verified for ' . htmlspecialchars($rsvp['student_name']) . ' for event "' . htmlspecialchars($rsvp['event_title']) . '".'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or unrecognised QR Pass.']);
}
?>