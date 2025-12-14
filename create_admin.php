<?php
// create_admin.php (run once in browser - then delete)
require_once __DIR__ . '/config.php';

$username = 'admin'; // change if needed
$password = 'admin123'; // change to desired password (remember it)

$hash = password_hash($password, PASSWORD_DEFAULT);
echo "<p>Generated password hash for user '<strong>".htmlspecialchars($username)."</strong>' (password was '<strong>".htmlspecialchars($password)."</strong>'):</p>";
echo "<pre>" . htmlspecialchars($hash) . "</pre>";
echo "<p>Copy the following SQL and run it in phpMyAdmin -> sentiment_db -> SQL tab (this will remove existing admin rows and insert a fresh one):</p>";

$sql = "DELETE FROM users WHERE username = 'admin';\n";
$sql .= "INSERT INTO users (username, password_hash) VALUES ('admin', '" . addslashes($hash) . "');";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";
