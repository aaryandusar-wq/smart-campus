<?php
// Enable full PHP error reporting for troubleshooting on Render
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cloud Database Credentials (Aiven MySQL)
$host     = "smart-campus-db-kunalgotawade47-cf91.g.aivencloud.com";
$user     = "avnadmin";
$password = "AVNS_uq3y3aWJvGPdoe4KHPC";
$dbname   = "defaultdb";
$port     = 19435;

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();

// Disable strict SSL certificate verification to allow Docker to connect to Aiven safely
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

// Establish connection with SSL fallback
if (!@$conn->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT)) {
    // Fallback connection attempt if strict client SSL flags fail
    if (!@$conn->real_connect($host, $user, $password, $dbname, $port)) {
        die("<div style='font-family:sans-serif; padding:20px; color:red; border:1px solid red; background:#ffe6e6; border-radius:5px;'>
            <h2>Database Connection Error</h2>
            <p><strong>Message:</strong> " . mysqli_connect_error() . "</p>
            </div>");
    }
}
?>1``
