<?php
// define('DB_SERVER', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'vms');
define('DB_SERVER', 'db'); // Use 'db' which is the service name in docker-compose.yml
 define('DB_USER', 'user'); // Set to the username defined in docker-compose.yml
 define('DB_PASS', 'password'); // Set to the password defined in docker-compose.yml
 define('DB_NAME', 'vehicle_management'); // Update to match the database name in docker-compose.yml
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>
