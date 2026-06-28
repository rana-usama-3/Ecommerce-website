<?php
require_once 'database.php';

echo "<h3>Updating Admin Database...</h3>";

try {
    // 1. Email column add karein
    $pdo->exec("ALTER TABLE admins ADD COLUMN email VARCHAR(255) DEFAULT NULL AFTER username");
    echo "✅ <strong>Email column added successfully.</strong><br>";
} catch (PDOException $e) {
    // Agar column pehle se bana ho to error ignore karein
    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>Email column already exists.</strong><br>";
    }
}

// 2. Default admin ka email set karein
 $stmt = $pdo->prepare("UPDATE admins SET email = ? WHERE username = 'admin'");
if($stmt->execute(['admin@dressvibe.com'])) {
    echo "✅ <strong>Admin email set to: admin@dressvibe.com</strong><br>";
} else {
    echo "⚠️ Could not update email (Check username 'admin').<br>";
}

echo "<br><a href='admin_login.php' style='font-size:18px; font-weight:bold; color:#e11d48;'>Go to Admin Login &rarr;</a>";
?>