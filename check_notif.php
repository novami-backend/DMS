<?php
// Simple column check
try {
    $db = mysqli_connect('localhost', 'root', '', 'dms_management');
    $result = mysqli_query($db, "SHOW COLUMNS FROM notifications");
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
