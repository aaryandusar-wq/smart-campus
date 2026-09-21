<!-- <?php
// Cloud Database Credentials (Aiven MySQL)
$host     = "smart-campus-db-kunalgotawade47-cf91.g.aivencloud.com";
$user     = "avnadmin";
$password = "AVNS_uq3y3aWJvGPdoe4KHPC";
$dbname   = "defaultdb";
$port     = 19435; // Updated to match your screenshot

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

if (!@$conn->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Cloud Database Connection Failed: " . $conn->connect_error);
}
?> -->