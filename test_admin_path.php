<?php
echo 'Current dir: ' . __DIR__ . "<br>";
echo 'Login file exists? ';
echo file_exists(__DIR__ . '/admin/login.php') ? 'YES' : 'NO';
