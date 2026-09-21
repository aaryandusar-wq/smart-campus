<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch user's event RSVPs with generated QR hashes
$sql = "SELECT e.title, e.event_date, e.location, r.qr_code_hash, r.attended 
        FROM rsvps r 
        JOIN events e ON r.event_id = e.id 
        WHERE r.user_id = ? 
        ORDER BY e.event_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_rsvps = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Event Passes - Smart Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Smart Campus</a>
    <div class="d-flex text-white">
      <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Notice Board</a>
      <a href="events.php" class="btn btn-outline-light btn-sm me-2">Events</a>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <h3 class="mb-4 text-center">My Digital Event Passes</h3>
    
    <?php if ($my_rsvps->num_rows > 0): ?>
        <?php while($pass = $my_rsvps->fetch_assoc()): ?>
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo htmlspecialchars($pass['title']); ?></h5>
                    <p class="text-muted small mb-2">📍 <?php echo htmlspecialchars($pass['location']); ?> | 📅 <?php echo date('M d, Y h:i A', strtotime($pass['event_date'])); ?></p>
                    
                    <!-- Dynamic Free QR Generator API -->
                    <div class="my-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($pass['qr_code_hash']); ?>" alt="QR Code" class="img-fluid border p-2 bg-white rounded">
                    </div>

                    <small class="text-muted d-block">Present this QR code at venue entrance for attendance check-in</small>
                    
                    <span class="badge <?php echo $pass['attended'] ? 'bg-success' : 'bg-warning text-dark'; ?> mt-2">
                        <?php echo $pass['attended'] ? 'Attendance Verified ✓' : 'Registration Active'; ?>
                    </span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">No active event RSVPs found.</div>
    <?php endif; ?>
</div>

</body>
</html>