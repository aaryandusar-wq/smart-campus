<?php
session_start();
include 'db.php';

// Access Control: Restrict panel access strictly to Faculty and Admin roles
$user_role = $_SESSION['role'] ?? 'Student';
$user_id   = $_SESSION['user_id'] ?? 0;

if (!isset($_SESSION['user_id']) || ($user_role !== 'Faculty' && $user_role !== 'Admin')) {
    header("Location: dashboard.php");
    exit();
}

$message = '';
$error = '';

// Handle Notice Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_notice') {
    $title       = trim($_POST['title'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $category    = $_POST['category'] ?? 'General';
    $priority    = $_POST['priority'] ?? 'Low';
    $department  = $_POST['department'] ?? 'All';
    $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : NULL;

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO notices (title, content, category, priority, department, posted_by, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssis", $title, $content, $category, $priority, $department, $user_id, $expiry_date);
        
        if ($stmt->execute()) {
            $message = "Notice published successfully!";
        } else {
            $error = "Failed to publish notice: " . $conn->error;
        }
    } else {
        $error = "Notice title and content cannot be empty.";
    }
}

// Handle Event Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_event') {
    $event_title = trim($_POST['event_title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date  = $_POST['event_date'] ?? '';
    $location    = trim($_POST['location'] ?? '');
    $guest_limit = intval($_POST['guest_limit'] ?? 100);

    if (!empty($event_title) && !empty($description) && !empty($event_date) && !empty($location)) {
        $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location, guest_limit, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $event_title, $description, $event_date, $location, $guest_limit, $user_id);

        if ($stmt->execute()) {
            $message = "Campus event created successfully!";
        } else {
            $error = "Failed to create event: " . $conn->error;
        }
    } else {
        $error = "All event details are required.";
    }
}

// Fetch total stats for admin overview
$total_notices = $conn->query("SELECT COUNT(*) AS count FROM notices")->fetch_assoc()['count'] ?? 0;
$total_events  = $conn->query("SELECT COUNT(*) AS count FROM events")->fetch_assoc()['count'] ?? 0;
$total_rsvps   = $conn->query("SELECT COUNT(*) AS count FROM rsvps")->fetch_assoc()['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus - Admin Control Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">← Smart Campus Dashboard</a>
    <span class="navbar-text text-white">Admin Control Panel</span>
  </div>
</nav>

<div class="container my-4">

    <!-- QR Scanner Quick Access Card -->
    <div class="card shadow-sm border-primary mb-4 bg-white">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-1 fw-bold text-primary">📷 Event Attendance Scanner</h4>
                <p class="text-muted mb-0">Use device camera to scan student QR passes and verify live attendance.</p>
            </div>
            <a href="scan_attendance.php" class="btn btn-primary btn-lg fw-bold my-2">
                Launch QR Scanner
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- System Metrics Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-dark mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fs-6">Total Notices</h5>
                    <p class="card-text fs-2 fw-bold mb-0"><?php echo $total_notices; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fs-6">Active Events</h5>
                    <p class="card-text fs-2 fw-bold mb-0"><?php echo $total_events; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fs-6">Total RSVPs Issued</h5>
                    <p class="card-text fs-2 fw-bold mb-0"><?php echo $total_rsvps; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Post New Notice Form -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">Post Campus Notice</div>
                <div class="card-body">
                    <form method="POST" action="admin_panel.php">
                        <input type="hidden" name="action" value="post_notice">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Notice Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content Description</label>
                            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="General">General</option>
                                    <option value="Timetable Alert">Timetable Alert</option>
                                    <option value="Classroom Update">Classroom Update</option>
                                    <option value="Emergency">Emergency</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority Level</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Emergency">Emergency Broadcast</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Target Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="All">All Departments</option>
                                    <option value="Computer Science">Computer Science</option>
                                    <option value="Information Technology">Information Technology</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Mechanical">Mechanical</option>
                                    <option value="Civil">Civil</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Publish Notice</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Event Form -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white fw-bold">Create Campus Event</div>
                <div class="card-body">
                    <form method="POST" action="admin_panel.php">
                        <input type="hidden" name="action" value="create_event">

                        <div class="mb-3">
                            <label for="event_title" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event_title" name="event_title" placeholder="e.g. Annual Tech Symposium" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Event Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="event_date" class="form-label">Event Date & Time</label>
                            <input type="datetime-local" class="form-control" id="event_date" name="event_date" required>
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label for="location" class="form-label">Venue / Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="e.g. Main Auditorium" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="guest_limit" class="form-label">Guest Capacity</label>
                                <input type="number" class="form-control" id="guest_limit" name="guest_limit" value="100" min="1" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold">Publish Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
