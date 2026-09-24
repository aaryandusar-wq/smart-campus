<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Safe null-coalescing session fallbacks to eliminate PHP undefined array key warnings
$user_dept = $_SESSION['department'] ?? 'All';
$user_role = $_SESSION['role'] ?? 'Student';
$user_name = $_SESSION['name'] ?? 'User';

// Fetch non-expired notices for user's department or 'All'
$sql = "SELECT n.*, u.name as author 
        FROM notices n 
        LEFT JOIN users u ON n.posted_by = u.id 
        WHERE (n.department = ? OR n.department = 'All') 
        AND (n.expiry_date >= CURDATE() OR n.expiry_date IS NULL) 
        ORDER BY FIELD(n.priority, 'Emergency', 'High', 'Medium', 'Low'), n.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_dept);
$stmt->execute();
$notices = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Notice Board - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">Smart Campus</a>
    <div class="d-flex text-white align-items-center">
      <a href="events.php" class="btn btn-outline-light btn-sm me-2">Events</a>
      <a href="attendance_qr.php" class="btn btn-outline-info btn-sm me-2">My QR Passes</a>
      <?php if ($user_role === 'Faculty' || $user_role === 'Admin'): ?>
          <a href="admin_panel.php" class="btn btn-warning btn-sm me-2">Admin Panel</a>
      <?php endif; ?>
      <span class="me-3">Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong> (<?php echo htmlspecialchars($user_role); ?>)</span>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Campus Notice Board</h3>
        <span class="badge bg-secondary fs-6">Dept: <?php echo htmlspecialchars($user_dept); ?></span>
    </div>

    <div class="row">
        <?php if ($notices && $notices->num_rows > 0): ?>
            <?php while($row = $notices->fetch_assoc()): ?>
                <?php 
                    $badgeClass = 'bg-secondary';
                    if($row['priority'] == 'Medium') $badgeClass = 'bg-info text-dark';
                    if($row['priority'] == 'High') $badgeClass = 'bg-warning text-dark';
                    if($row['priority'] == 'Emergency') $badgeClass = 'bg-danger';
                ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($row['priority'] ?? 'Low'); ?> Priority</span>
                                <small class="text-muted fw-bold"><?php echo htmlspecialchars($row['category'] ?? 'General'); ?></small>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title'] ?? ''); ?></h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'] ?? '')); ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-muted">
                            <small>Posted by <?php echo htmlspecialchars($row['author'] ?? 'Admin'); ?> | Expires: <?php echo !empty($row['expiry_date']) ? htmlspecialchars($row['expiry_date']) : 'N/A'; ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No active notices found for your department right now.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Emergency Broadcast Modal Overlay -->
<div class="modal fade" id="emergencyModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger border-3 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="emergencyTitle">🚨 EMERGENCY ALERT</h5>
      </div>
      <div class="modal-body bg-light">
        <p id="emergencyContent" class="fs-5 fw-bold text-dark mb-0"></p>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Acknowledge Alert</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function checkEmergencyAlerts() {
    fetch('check_emergency.php')
        .then(response => response.json())
        .then(data => {
            if (data && data.emergency) {
                document.getElementById('emergencyTitle').innerText = '🚨 ' + (data.title || 'EMERGENCY ALERT');
                document.getElementById('emergencyContent').innerText = data.content || '';
                var myModal = new bootstrap.Modal(document.getElementById('emergencyModal'));
                myModal.show();
            }
        })
        .catch(error => console.error('Error fetching emergency updates:', error));
}

// Poll every 5 seconds for real-time emergency broadcasts
setInterval(checkEmergencyAlerts, 5000);
checkEmergencyAlerts();
</script>

</body>
</html>
