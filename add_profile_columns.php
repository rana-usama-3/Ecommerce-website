<?php
require_once 'database.php';

echo "<h3>Adding Profile Columns...</h3>";

// 1. Full Name
try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN full_name VARCHAR(255) DEFAULT NULL");
    echo "✅ <strong>full_name</strong> column added.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') === false) {
        echo "Error adding full_name: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>full_name</strong> already exists.<br>";
    }
}

// 2. Phone
try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN phone VARCHAR(20) DEFAULT NULL");
    echo "✅ <strong>phone</strong> column added.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') === false) {
        echo "Error adding phone: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>phone</strong> already exists.<br>";
    }
}

// 3. Address
try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN address TEXT DEFAULT NULL");
    echo "✅ <strong>address</strong> column added.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') === false) {
        echo "Error adding address: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>address</strong> already exists.<br>";
    }
}

// 4. Profile Image
try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN profile_image VARCHAR(500) DEFAULT NULL");
    echo "✅ <strong>profile_image</strong> column added.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate') === false) {
        echo "Error adding profile_image: " . $e->getMessage() . "<br>";
    } else {
        echo "ℹ️ <strong>profile_image</strong> already exists.<br>";
    }
}

// 5. Set Default Name for Safety (Agar null hai to)
 $stmt = $pdo->prepare("UPDATE admins SET full_name = 'Admin' WHERE full_name IS NULL OR full_name = ''");
 $stmt->execute();
echo "✅ Default name set for empty profiles.<br>";

echo "<br><a href='admin_profile.php' style='font-size:18px; font-weight:bold; color:#e11d48;'>Go to My Profile &rarr;</a>";
?>