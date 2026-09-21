<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch all upcoming events with calculated RSVP counts
$sql = "SELECT e.*, u.name as organizer, 
        (SELECT COUNT(*) FROM rsvps WHERE event_id = e.id) as total_rsvps,
        (SELECT COUNT(*) FROM rsvps WHERE event_id = e.id AND user_id = ?) as user_rsvp
        FROM events e 
        LEFT JOIN users u ON e.created_by = u.id 
        ORDER BY e.event_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Campus Events - Smart Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Smart Campus</a>
    <div class="d-flex text-white">
      <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Notice Board</a>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3 class="mb-4">Upcoming Campus Events</h3>
    <div class="row">
        <?php if ($events->num_rows > 0): ?>
            <?php while($event = $events->fetch_assoc()): ?>
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="text-muted mb-1">📍 <?php echo htmlspecialchars($event['location']); ?> | 📅 <?php echo date('M d, Y h:i A', strtotime($event['event_date'])); ?></p>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            
                            <div class="progress mb-3" style="height: 10px;">
                                <?php $percent = ($event['total_rsvps'] / $event['guest_limit']) * 100; ?>
                                <div class="progress-bar bg-info" style="width: <?php echo $percent; ?>%;"></div>
                            </div>
                            <small class="text-muted"><?php echo $event['total_rsvps']; ?> / <?php echo $event['guest_limit']; ?> Seats Reserved</small>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <small class="text-muted">By <?php echo htmlspecialchars($event['organizer']); ?></small>
                            <?php if ($event['user_rsvp'] > 0): ?>
                                <span class="badge bg-success">RSVP'd ✓</span>
                            <?php elseif ($event['total_rsvps'] >= $event['guest_limit']): ?>
                                <span class="badge bg-secondary">Event Full</span>
                            <?php else: ?>
                                <a href="rsvp_action.php?event_id=<?php echo $event['id']; ?>" class="btn btn-primary btn-sm">RSVP Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-info">No upcoming events scheduled.</div></div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>