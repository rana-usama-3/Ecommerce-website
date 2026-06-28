<?php
require_once 'database.php';

try {
    // 1. Profile Columns add karein
    $pdo->exec("ALTER TABLE admins ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER password");
    echo "✅ Phone column added.<br>";
} catch (PDOException $e) { if(strpos($e->getMessage(), 'Duplicate')===false) echo $e->getMessage()."<br>"; }

try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN address TEXT DEFAULT NULL AFTER phone");
    echo "✅ Address column added.<br>";
} catch (PDOException $e) { if(strpos($e->getMessage(), 'Duplicate')===false) echo $e->getMessage()."<br>"; }

try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN profile_image VARCHAR(500) DEFAULT NULL AFTER address");
    echo "✅ Profile Image column added.<br>";
} catch (PDOException $e) { if(strpos($e->getMessage(), 'Duplicate')===false) echo $e->getMessage()."<br>"; }

// 2. Login History Table create karein
try {
    $sql = "CREATE TABLE IF NOT EXISTS admin_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_agent TEXT,
        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "✅ Admin Logs table created.<br>";
} catch (PDOException $e) { echo "Error creating logs: " . $e->getMessage(); }

echo "<br><strong>Database Updated Successfully!</strong><br>";
echo "<a href='admin_login.php'>Go to Admin Login</a>";
?>