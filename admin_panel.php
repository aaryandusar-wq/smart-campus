<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Faculty' && $_SESSION['role'] !== 'Admin')) {
    header("Location: dashboard.php");
    exit();
}

include 'db.php';
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel - Smart Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Smart Campus Admin</a>
    <div class="d-flex text-white">
      <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Notice Board</a>
      <a href="events.php" class="btn btn-outline-light btn-sm me-2">Events</a>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <?php if($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Post Notice Form -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Post Campus Notice</h5>
                </div>
                <div class="card-body">
                    <form action="post_action.php" method="POST">
                        <input type="hidden" name="action" value="post_notice">
                        <input type="text" name="title" class="form-control mb-2" placeholder="Notice Title" required>
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="Notice details..." required></textarea>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label mb-1">Category</label>
                                <select name="category" class="form-select">
                                    <option value="General">General</option>
                                    <option value="Timetable Alert">Timetable Alert</option>
                                    <option value="Classroom Update">Classroom Update</option>
                                    <option value="Emergency">Emergency</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1">Priority Level</label>
                                <select name="priority" class="form-select">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Emergency">Emergency Override</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label mb-1">Target Department</label>
                                <select name="department" class="form-select">
                                    <option value="All">All Departments</option>
                                    <option value="Computer">Computer</option>
                                    <option value="IT">IT</option>
                                    <option value="ENTC">ENTC</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-1">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Publish Notice</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Schedule Event Form -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Publish New Event</h5>
                </div>
                <div class="card-body">
                    <form action="post_action.php" method="POST">
                        <input type="hidden" name="action" value="post_event">
                        <input type="text" name="title" class="form-control mb-2" placeholder="Event Name" required>
                        <textarea name="description" class="form-control mb-2" rows="3" placeholder="Event description..." required></textarea>
                        <input type="datetime-local" name="event_date" class="form-control mb-2" required>
                        <input type="text" name="location" class="form-control mb-2" placeholder="Location / Hall" required>
                        <input type="number" name="guest_limit" class="form-control mb-3" placeholder="Max Attendees Limit" value="100" required>
                        <button type="submit" class="btn btn-success w-100">Publish Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>