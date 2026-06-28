<?php
require_once 'database.php';

try {
    // Full Name column add karein
    $pdo->exec("ALTER TABLE admins ADD COLUMN full_name VARCHAR(255) DEFAULT NULL AFTER id");
    echo "✅ Full Name column added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') === false) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "ℹ️ Column already exists.";
    }
}
echo "<br><a href='admin_register.php?key=supersecure123'>Go to Admin Signup &rarr;</a>";
?>