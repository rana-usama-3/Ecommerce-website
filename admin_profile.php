<?php require_once 'database.php';
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: admin_login.php"); exit; }

 $admin_id = $_SESSION['admin_id'];
 $error = '';
 $success = '';

// --- UPDATE PROFILE LOGIC ---
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $profile_image = trim($_POST['profile_image']);

    if(empty($full_name) || empty($email)) {
        $error = "Name and Email are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE admins SET full_name=?, email=?, phone=?, address=?, profile_image=? WHERE id=?");
        if($stmt->execute([$full_name, $email, $phone, $address, $profile_image, $admin_id])) {
            $success = "Profile updated successfully!";
            $_SESSION['admin_name'] = $full_name; 
            $_SESSION['admin_email'] = $email;
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// --- FETCH CURRENT DATA ---
 $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
 $stmt->execute([$admin_id]);
 $admin = $stmt->fetch();

// --- FETCH LOGIN HISTORY ---
 $logs_stmt = $pdo->prepare("SELECT * FROM admin_logs WHERE admin_id = ? ORDER BY login_time DESC LIMIT 20");
 $logs_stmt->execute([$admin_id]);
 $logs = $logs_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>My Profile - DressVibe Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
    <div class="admin-sidebar">
        <div class="admin-sidebar-logo"><i class="fas fa-gem"></i> <span>DressVibe</span></div>
        <div class="admin-sidebar-nav">
            <a href="admin_index.php" class="admin-sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="admin_orders.php" class="admin-sidebar-link"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="admin_products.php" class="admin-sidebar-link"><i class="fas fa-tshirt"></i> Products</a>
            <a href="admin_categories.php" class="admin-sidebar-link"><i class="fas fa-folder"></i> Categories</a>
            <a href="admin_profile.php" class="admin-sidebar-link active"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="admin_logout.php" class="admin-sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="admin-main">
        <div class="admin-page-title"><span>My Profile</span></div>

        <?php if($error): ?><div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:16px;"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?><div style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:16px;"><?php echo $success; ?></div><?php endif; ?>

        <div class="admin-card" style="margin-bottom: 30px;">
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; border-bottom:1px solid #f4f4f5; padding-bottom:20px;">
                <div style="width:100px; height:100px; border-radius:50%; overflow:hidden; background:#f4f4f5; display:flex; align-items:center; justify-content:center;">
                    <?php if($admin['profile_image']): ?>
                        <img src="<?php echo $admin['profile_image']; ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <i class="fas fa-user" style="font-size:40px; color:#a1a1aa;"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 style="margin:0; font-size:24px;"><?php echo htmlspecialchars($admin['full_name'] ?? 'Admin User'); ?></h2>
                    <p style="margin:5px 0 0; color:#71717a;"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email'] ?? ''); ?></p>
                </div>
            </div>

            <form method="post" class="admin-form">
                <div><label>Full Name</label><input type="text" name="full_name" value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>" required></div>
                <div><label>Email Address</label><input type="email" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required></div>
                <div><label>Phone Number</label><input type="text" name="phone" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>"></div>
                <div><label>Profile Image URL</label><input type="text" name="profile_image" value="<?php echo htmlspecialchars($admin['profile_image'] ?? ''); ?>" placeholder="https://example.com/image.jpg"></div>
                <div class="full-width"><label>Address</label><textarea name="address" rows="3"><?php echo htmlspecialchars($admin['address'] ?? ''); ?></textarea></div>
                <div class="full-width"><button type="submit" class="admin-btn admin-btn-primary">Update Profile</button></div>
            </form>
        </div>

        <!-- Login History Section -->
        <div class="admin-card">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-history"></i> Login History (Recent Activity)</h3>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>IP Address</th>
                            <th>Device / Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($logs) > 0): ?>
                            <?php foreach($logs as $log): ?>
                            <tr>
                                <td><?php echo date('M d, Y - h:i A', strtotime($log['login_time'])); ?></td>
                                <td style="font-family:monospace; font-weight:bold; color:#e11d48;"><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td style="font-size:12px; color:#52525b; max-width:300px;" title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                    <?php echo htmlspecialchars(substr($log['user_agent'], 0, 50)) . '...'; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center; padding:20px;">No login history found. <br><small>Log out and log back in to see history.</small></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>