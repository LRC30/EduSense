<?php
// config.php - DB connection (put in project root)
$DB_HOST = 'sql308.ezyro.com';
$DB_USER = 'ezyro_40678371';
$DB_PASS = '2129315b1';       // XAMPP default is empty
$DB_NAME = 'ezyro_40678371_edusense_db';
$DB_PORT = 3306;     // default

// create mysqli connection and expose both $mysqli and $conn for compatibility
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($mysqli->connect_errno) {
    // stop early so files show a clear message
    die("DB connect error ({$mysqli->connect_errno}): {$mysqli->connect_error}");
}

// Also set $conn alias (some of your files may expect $conn)
$conn = $mysqli;

// set charset
$mysqli->set_charset('utf8mb4');
