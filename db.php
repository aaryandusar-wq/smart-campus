<?php
// Enable PHP error reporting for troubleshooting
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
$conn = mysqli_init();

// Configure SSL options for Aiven connection
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

// Attempt 1: Standard SSL Connection
if (!@$conn->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
    // Attempt 2: Direct Connection Fallback
    if (!@$conn->real_connect($host, $user, $password, $dbname, $port)) {
        die("<div style='font-family:sans-serif; padding:20px; color:#721c24; background-color:#f8d7da; border:1px solid #f5c6cb; border-radius:5px; margin:20px;'>
            <h3>Database Connection Error</h3>
            <p><strong>Error Message:</strong> " . mysqli_connect_error() . "</p>
            <p><strong>Error Code:</strong> " . mysqli_connect_errno() . "</p>
            </div>");
    }
}
?>
