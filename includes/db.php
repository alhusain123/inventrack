<?php
define('DB_HOST', 'zephyr.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'zJxjQnnaXONmHIcZiDYmChsMynbtCsjN');
define('DB_NAME', 'railway');
define('DB_PORT', 32725);

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
?>