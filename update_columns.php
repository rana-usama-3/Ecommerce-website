<?php
require_once 'database.php';

echo "<h3>Updating Database Columns...</h3>";

try {
    // 1. Add user_id column to orders table
    // Agar column pehle se hai to error skip ho jayegi
    $sql1 = "ALTER TABLE orders ADD COLUMN user_id INT DEFAULT NULL AFTER id";
    $pdo->exec($sql1);
    echo "✅ <strong>user_id</strong> column added successfully.<br>";
} catch (PDOException $e) {
    // Agar column already exists to ignore karein
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "❌ Error adding user_id: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>user_id</strong> column already exists.<br>";
    }
}

try {
    // 2. Add tracking_id column to orders table
    $sql2 = "ALTER TABLE orders ADD COLUMN tracking_id VARCHAR(50) DEFAULT NULL AFTER user_id";
    $pdo->exec($sql2);
    echo "✅ <strong>tracking_id</strong> column added successfully.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "❌ Error adding tracking_id: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>tracking_id</strong> column already exists.<br>";
    }
}

echo "<br><strong style='color:#16a34a;'>✅ Database Structure Updated!</strong><br>";
echo "<a href='user_dashboard.php' style='color:#0ea5e9; font-weight:bold;'>Go to User Dashboard &rarr;</a>";
?>