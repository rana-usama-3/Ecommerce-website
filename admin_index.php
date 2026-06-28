<?php require_once 'database.php'; require_once 'functions.php';
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: admin_login.php"); exit; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Dashboard - DressVibe Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
 <div class="admin-sidebar">
        <div class="admin-sidebar-logo"><i class="fas fa-gem"></i> <span>DressVibe</span></div>
        <div class="admin-sidebar-nav">
            <a href="admin_profile.php" class="admin-sidebar-link"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="admin_index.php" class="admin-sidebar-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="admin_orders.php" class="admin-sidebar-link"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="admin_products.php" class="admin-sidebar-link"><i class="fas fa-tshirt"></i> Products</a>
            <a href="admin_categories.php" class="admin-sidebar-link"><i class="fas fa-folder"></i> Categories</a>
            <a href="index.php" class="admin-sidebar-link" target="_blank"><i class="fas fa-external-link-alt"></i> View Shop</a>
            <a href="admin_logout.php" class="admin-sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="admin-main">
        <div class="admin-page-title"><span>Dashboard Overview</span></div>
        <div class="admin-stat-grid">
            <?php
            $total_revenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
            $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
            $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
            ?>
            <div class="admin-stat-card"><span>Total Revenue</span><h2 style="color: #16a34a;"><?php echo formatPrice($total_revenue); ?></h2></div>
            <div class="admin-stat-card"><span>Total Orders</span><h2><?php echo $total_orders; ?></h2></div>
            <div class="admin-stat-card"><span>Pending Orders</span><h2 style="color: #f59e0b;"><?php echo $pending_orders; ?></h2></div>
            <div class="admin-stat-card"><span>Total Products</span><h2><?php echo $total_products; ?></h2></div>
        </div>
        <div class="admin-card">
            <h3 style="margin-bottom: 16px;">Recent Orders</h3>
            <table class="admin-table">
                <thead><tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5"); while($order = $stmt->fetch()): ?>
                    <tr>
                        <td><strong>#DV-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo formatPrice($order['total']); ?></td>
                        <td><span class="admin-badge admin-badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>