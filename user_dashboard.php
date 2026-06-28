<?php 
 $pageTitle = 'My Account'; 
require_once 'header.php'; 
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

 $user_id = $_SESSION['user_id'];
// Get User Info
 $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
 $stmt->execute([$user_id]);
 $user = $stmt->fetch();

// Get Orders
 $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
 $stmt->execute([$user_id]);
 $orders = $stmt->fetchAll();
?>

<section class="page-header" style="background-image: url('https://picsum.photos/seed/userbanner/1600/300')">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1>My Account</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user['name']); ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="admin-card">
            <div style="display:grid; grid-template-columns: 250px 1fr; gap: 30px;">
                <!-- Sidebar -->
                <div style="border-right: 1px solid #f4f4f5; padding-right: 20px;">
                    <h3 style="font-size: 16px; margin-bottom: 20px; color: #e11d48;">Account Menu</h3>
                    <ul style="list-style:none; line-height: 2.5;">
                        <li style="font-weight:600; color:#18181b;"><i class="fas fa-list" style="width:20px;"></i> Order History</li>
                        <li><a href="logout.php" style="color:#ef4444; text-decoration:none;"><i class="fas fa-sign-out-alt" style="width:20px;"></i> Logout</a></li>
                    </ul>
                </div>

                <!-- Content -->
                <div>
                    <h3 style="font-size: 18px; margin-bottom: 20px;">Profile Details</h3>
                    <div style="background:#fafafa; padding:20px; border-radius:10px; margin-bottom:30px;">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                    </div>

                    <h3 style="font-size: 18px; margin-bottom: 20px;">Order History</h3>
                    <?php if(count($orders) > 0): ?>
                    <div style="overflow-x:auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Tracking ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                <tr>
                                    <td>#DV-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td style="font-family:monospace; color:#e11d48; font-weight:700;"><?php echo $order['tracking_id']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo formatPrice($order['total']); ?></td>
                                    <td><span class="admin-badge admin-badge-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p>You have no orders yet. <a href="products.php" style="color:#e11d48;">Start Shopping</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>