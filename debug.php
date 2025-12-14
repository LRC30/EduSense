<?php
// debug.php - run once to print environment & DB info (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Project debug - Student-sentiment</h2>";
echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

$root = __DIR__;
echo "<p><strong>Project root:</strong> {$root}</p>";

echo "<h3>PHP & Server</h3>";
echo "<ul>";
echo "<li>PHP SAPI: " . php_sapi_name() . "</li>";
echo "<li>PHP version: " . phpversion() . "</li>";
echo "<li>Loaded php.ini: " . php_ini_loaded_file() . "</li>";
echo "<li>Display errors: " . ini_get('display_errors') . "</li>";
echo "</ul>";

echo "<h3>Check key files (existence)</h3>";
$files = [
    'config.php',
    'submit_feedback.php',
    'index.php',
    'frontend/feedback.html',
    'frontend/index.html',
    'frontend/style.css',
    'admin/dashboard.php',
    'admin/feedback.php',
    'admin/export_csv.php',
];
echo "<ul>";
foreach($files as $f){
    $p = $root . DIRECTORY_SEPARATOR . $f;
    echo "<li>{$f} -> " . (file_exists($p) ? "<span style='color:green'>FOUND</span>" : "<span style='color:red'>MISSING</span>") . "</li>";
}
echo "</ul>";

// Try to include config.php by both common paths
echo "<h3>DB connection test</h3>";
$tried = [];
$connected = false;
foreach (['config.php','./config.php','../config.php'] as $path) {
    $tried[] = $path;
    $full = realpath($root . DIRECTORY_SEPARATOR . $path);
    echo "<p>Trying include: <code>{$path}</code> -> " . ($full ? $full : 'realpath not found') . "</p>";
    if ($full && is_readable($full)) {
        echo "<p style='color:green'>Including $full ...</p>";
        try {
            include_once $full;
            if (isset($conn) && $conn instanceof mysqli) {
                if ($conn->connect_error) {
                    echo "<p style='color:red'>mysqli connect error: " . htmlspecialchars($conn->connect_error) . "</p>";
                } else {
                    echo "<p style='color:green'>Connected to DB via \$conn (mysqli).</p>";
                    $connected = true;
                    break;
                }
            } elseif (isset($pdo) && $pdo instanceof PDO) {
                echo "<p style='color:green'>Connected to DB via \$pdo (PDO).</p>";
                $connected = true;
                break;
            } else {
                echo "<p style='color:orange'>config included but no \$conn or \$pdo found.</p>";
            }
        } catch (Throwable $e) {
            echo "<p style='color:red'>Include error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color:gray'>Can't read include file.</p>";
    }
}

if (!$connected) {
    echo "<p style='color:red'><strong>NOT CONNECTED:</strong> Please ensure config.php exists in the project root and defines either <code>\$conn = new mysqli(...)</code> or <code>\$pdo = new PDO(...)</code>.</p>";
} else {
    // if connected, list DB tables and some rows
    echo "<h4>DB schema & sample data</h4>";
    try {
        if (isset($conn) && $conn instanceof mysqli) {
            $res = $conn->query("SHOW TABLES");
            if ($res) {
                echo "<p>Tables:</p><ul>";
                while ($r = $res->fetch_row()) {
                    echo "<li>" . htmlspecialchars($r[0]) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color:orange'>SHOW TABLES failed: " . htmlspecialchars($conn->error) . "</p>";
            }

            // check feedback table
            $check = $conn->query("SHOW COLUMNS FROM feedback");
            if ($check && $check->num_rows) {
                echo "<p><strong>feedback table columns:</strong></p><ul>";
                while ($c = $check->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($c['Field']) . " (" . htmlspecialchars($c['Type']) . ")</li>";
                }
                echo "</ul>";

                $r2 = $conn->query("SELECT id, student_name, course, subject, comment, sentiment, created_at FROM feedback ORDER BY created_at DESC LIMIT 5");
                if ($r2 && $r2->num_rows) {
                    echo "<p><strong>Recent feedback rows (up to 5):</strong></p><ol>";
                    while ($row = $r2->fetch_assoc()) {
                        echo "<li><strong>" . htmlspecialchars($row['student_name']) . "</strong> [" . htmlspecialchars($row['sentiment']) . "] - " . htmlspecialchars(substr($row['comment'],0,120)) . " ... <em>(" . $row['created_at'] . ")</em></li>";
                    }
                    echo "</ol>";
                } else {
                    echo "<p>No feedback rows found (or query failed).</p>";
                }
            } else {
                echo "<p style='color:red'>No feedback table found or can't access columns.</p>";
            }

            // check users table too
            $r3 = $conn->query("SHOW COLUMNS FROM users");
            if ($r3 && $r3->num_rows) {
                echo "<p><strong>users table columns:</strong></p><ul>";
                while ($c = $r3->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($c['Field']) . " (" . htmlspecialchars($c['Type']) . ")</li>";
                }
                echo "</ul>";
                $r4 = $conn->query("SELECT id, username FROM users LIMIT 5");
                if ($r4 && $r4->num_rows) {
                    echo "<p>Users (sample):</p><ul>";
                    while ($u = $r4->fetch_assoc()) {
                        echo "<li>" . (int)$u['id'] . " - " . htmlspecialchars($u['username']) . "</li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "<p style='color:orange'>users table not found (ok kung wala).</p>";
            }
        } elseif (isset($pdo) && $pdo instanceof PDO) {
            echo "<p>PDO connection present — running table list (PDO)</p>";
            $stmt = $pdo->query("SHOW TABLES");
            $rows = $stmt->fetchAll(PDO::FETCH_NUM);
            echo "<ul>";
            foreach ($rows as $r) echo "<li>" . htmlspecialchars($r[0]) . "</li>";
            echo "</ul>";
        }
    } catch (Throwable $e) {
        echo "<p style='color:red'>DB query error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<h3>Filesystem listing (frontend & admin)</h3>";
$dirs = ['frontend', 'admin', 'asset/css'];
foreach ($dirs as $d) {
    $p = $root . DIRECTORY_SEPARATOR . $d;
    echo "<h4>{$d}</h4>";
    if (is_dir($p)) {
        echo "<ul>";
        foreach (scandir($p) as $f) {
            if ($f[0]=='.') continue;
            echo "<li>" . htmlspecialchars($f) . (is_dir($p.DIRECTORY_SEPARATOR.$f) ? " (dir)" : "") . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>Directory not found: {$p}</p>";
    }
}

echo "<hr><p>After you paste the debug output here I will give the exact file(s) to replace/fix.</p>";
