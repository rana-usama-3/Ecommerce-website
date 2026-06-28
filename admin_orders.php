<?php require_once 'database.php'; require_once 'functions.php';
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: admin_login.php"); exit; }

if(isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id']; $new_status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    header("Location: admin_orders.php?msg=updated"); exit;
}
 $filter_status = isset($_GET['status']) ? $_GET['status'] : '';
 $where = "WHERE 1=1";
if($filter_status) $where .= " AND status = '".$filter_status."'";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Orders - DressVibe Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-body">
    <div class="admin-sidebar">
        <div class="admin-sidebar-logo"><i class="fas fa-gem"></i> <span>DressVibe</span></div>
        <div class="admin-sidebar-nav">
            <a href="admin_index.php" class="admin-sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="admin_orders.php" class="admin-sidebar-link active"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="admin_products.php" class="admin-sidebar-link"><i class="fas fa-tshirt"></i> Products</a>
            <a href="admin_categories.php" class="admin-sidebar-link"><i class="fas fa-folder"></i> Categories</a>
            <a href="admin_logout.php" class="admin-sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="admin-main">
        <div class="admin-page-title">
            <span>Manage Orders</span>
            <div style="display:flex;gap:8px;">
                <a href="admin_orders.php" class="admin-btn admin-btn-primary">All</a>
                <a href="admin_orders.php?status=pending" class="admin-btn admin-btn-primary">Pending</a>
                <a href="admin_orders.php?status=processing" class="admin-btn admin-btn-primary">Processing</a>
                <a href="admin_orders.php?status=delivered" class="admin-btn admin-btn-primary">Delivered</a>
            </div>
        </div>
        <?php if(isset($_GET['msg'])): ?><div style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:16px;">Status updated successfully!</div><?php endif; ?>
        <div class="admin-card" style="overflow-x:auto;">
            <table class="admin-table">
                <thead><tr><th>ID</th><th>Customer</th><th>City</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php $stmt = $pdo->query("SELECT * FROM orders $where ORDER BY id DESC"); while($order = $stmt->fetch()): ?>
                    <tr>
                        <td><strong>#DV-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?><br><small style="color:#a1a1aa;"><?php echo $order['phone']; ?></small></td>
                        <td><?php echo htmlspecialchars($order['city']); ?></td>
                        <td><strong><?php echo formatPrice($order['total']); ?></strong></td>
                        <td><span class="admin-badge admin-badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <td>
                            <form method="post" style="display:flex;gap:6px;align-items:center;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" style="padding:6px;border:1px solid #e4e4e7;border-radius:4px;font-size:12px;">
                                    <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $order['status']=='confirmed'?'selected':''; ?>>Confirmed</option>
                                    <option value="processing" <?php echo $order['status']=='processing'?'selected':''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status']=='delivered'?'selected':''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="admin-btn admin-btn-edit"><i class="fas fa-check"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>