<?php
// Enable PHP error reporting for clear diagnostics on Render
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Aiven MySQL Cloud Credentials
$host     = "smart-campus-db-kunalgotawade47-cf91.g.aivencloud.com";
$user     = "avnadmin";
$password = "AVNS_uq3y3aWJvGPdoe4KHPC";
$dbname   = "defaultdb";
$port     = 19435;

mysqli_report(MYSQLI_REPORT_OFF);

// Function to establish secure MySQLi connection
function connect_cloud_db($host, $user, $password, $dbname, $port) {
    $conn = mysqli_init();
    
    // Set connection timeout (seconds)
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 15);
    
    // Disable strict SSL verification for cloud compatibility inside Docker
    $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    
    // Primary Connection Attempt (Using SSL flags)
    if (@$conn->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
        return $conn;
    }
    
    // Secondary Fallback Connection Attempt (Standard TCP)
    if (@$conn->real_connect($host, $user, $password, $dbname, $port)) {
        return $conn;
    }
    
    return false;
}

$conn = connect_cloud_db($host, $user, $password, $dbname, $port);

if (!$conn) {
    die("
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;'>
        <h3 style='margin-top:0;'>Cloud Database Connection Failed</h3>
        <p><strong>Error Details:</strong> " . htmlspecialchars(mysqli_connect_error()) . "</p>
        <p><strong>Error Code:</strong> " . mysqli_connect_errno() . "</p>
        <hr style='border-top: 1px solid #f5c6cb;'>
        <small>Target: " . htmlspecialchars($host) . ":" . $port . "</small>
    </div>");
}
?>
