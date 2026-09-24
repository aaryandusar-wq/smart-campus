<?php
session_start();
include 'db.php';

// Restrict access so only Faculty or Admin can scan
$user_role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($user_role !== 'Faculty' && $user_role !== 'Admin')) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus - Admin QR Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 QR Code Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin_panel.php">← Back to Admin Panel</a>
    <span class="navbar-text text-white">Event Attendance Scanner</span>
  </div>
</nav>

<div class="container" style="max-width: 600px;">
    <div class="card shadow border-0 text-center">
        <div class="card-body p-4">
            <h4 class="card-title mb-3 fw-bold">Scan Student Event Pass</h4>
            <p class="text-muted">Point your camera at the student's QR code to verify attendance.</p>
            
            <!-- Camera Viewfinder Box -->
            <div id="reader" style="width: 100%;" class="rounded overflow-hidden border"></div>

            <!-- Real-time Scan Result Alert -->
            <div id="scan-result" class="mt-4 d-none"></div>
        </div>
    </div>
</div>

<script>
function onScanSuccess(decodedText, decodedResult) {
    // Stop scanning temporarily after finding a code
    html5QrcodeScanner.clear();

    const resultDiv = document.getElementById('scan-result');
    resultDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
    resultDiv.classList.add('alert', 'alert-info');
    resultDiv.innerHTML = "Processing QR Pass...";

    // Send the decoded QR hash to backend for verification
    fetch('mark_attendance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'qr_hash=' + encodeURIComponent(decodedText)
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.classList.remove('alert-info');
        if (data.status === 'success') {
            resultDiv.classList.add('alert-success');
            resultDiv.innerHTML = '<strong>Success!</strong> ' + data.message;
        } else {
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = '<strong>Error!</strong> ' + data.message;
        }
        
        // Re-enable camera scanner after 3 seconds for next student
        setTimeout(() => {
            resultDiv.classList.add('d-none');
            startScanner();
        }, 3000);
    })
    .catch(error => {
        resultDiv.classList.remove('alert-info');
        resultDiv.classList.add('alert-danger');
        resultDiv.innerHTML = '<strong>Error:</strong> Verification request failed.';
    });
}

let html5QrcodeScanner;
function startScanner() {
    html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false);
    html5QrcodeScanner.render(onScanSuccess);
}

startScanner();
</script>

</body>
</html>