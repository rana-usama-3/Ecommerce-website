<?php $pageTitle = 'Checkout'; require_once 'header.php';

// Redirect if cart empty
if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// User ID Check
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
 $user_id = $_SESSION['user_id'];

// Get cart items
 $cart_items = [];
 $cart_total = 0;
 $cart_count = 0;
foreach($_SESSION['cart'] as $pid => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch();
    if($product) {
        $product['qty'] = $qty;
        $product['subtotal'] = $product['price'] * $qty;
        $cart_items[] = $product;
        $cart_total += $product['subtotal'];
        $cart_count += $qty;
    }
}
 $shipping = $cart_total >= 3000 ? 0 : 250;
 $grand_total = $cart_total + $shipping;

 $order_placed = false;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');

    $errors = [];
    if(empty($name)) $errors[] = 'Name is required';
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if(empty($phone) || strlen($phone) < 10) $errors[] = 'Valid phone number is required';
    if(empty($address)) $errors[] = 'Address is required';
    if(empty($city)) $errors[] = 'City is required';

    if(empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Generate Tracking ID
            $tracking_id = 'TRK-' . strtoupper(uniqid()) . '-' . rand(100,999);
            
            // Insert order with User ID and Tracking ID
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, tracking_id, customer_name, email, phone, address, city, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $tracking_id, $name, $email, $phone, $address, $city, $grand_total]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, image) VALUES (?, ?, ?, ?, ?, ?)");
            foreach($cart_items as $item) {
                $stmt->execute([$order_id, $item['id'], $item['name'], $item['qty'], $item['price'], $item['image']]);
                
                // Update stock
                $stmt2 = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $stmt2->execute([$item['qty'], $item['id'], $item['qty']]);
            }

            $pdo->commit();
            
            // --- SEND EMAIL ---
            $to = $email;
            $subject = "Order Confirmed - DressVibe (Tracking: $tracking_id)";
            
            $message = "
            <html>
            <head>
            <title>Order Confirmation</title>
            </head>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
                    <h2 style='color: #e11d48; text-align: center;'>DressVibe</h2>
                    <h3 style='text-align: center;'>Order Confirmed!</h3>
                    <p>Dear $name,</p>
                    <p>Thank you for shopping with us. Your order has been placed successfully.</p>
                    
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='background: #f4f4f5;'>
                            <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Order ID</th>
                            <td style='padding: 10px; border: 1px solid #ddd;'>#$order_id</td>
                        </tr>
                        <tr>
                            <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Tracking ID</th>
                            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold; color: #e11d48;'>$tracking_id</td>
                        </tr>
                        <tr style='background: #f4f4f5;'>
                            <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Total Amount</th>
                            <td style='padding: 10px; border: 1px solid #ddd;'>" . formatPrice($grand_total) . "</td>
                        </tr>
                    </table>

                    <p>You can track your order status using the Tracking ID provided above.</p>
                    <p style='text-align: center; margin-top: 30px;'>
                        <a href='http://localhost/dress_shop/user_dashboard.php' style='background: #e11d48; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Order Details</a>
                    </p>
                    <p style='text-align: center; font-size: 12px; color: #777; margin-top: 40px;'>&copy; 2026 DressVibe. All rights reserved.</p>
                </div>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: DressVibe <no-reply@dressvibe.com>" . "\r\n";

            // Warning: mail() requires SMTP configuration in php.ini
            @mail($to, $subject, $message, $headers);

            // Clear cart
            unset($_SESSION['cart']);
            $order_placed = true;
            $order_id_display = $order_id;
            $tracking_id_display = $tracking_id;

        } catch(PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>

<section class="page-header" style="background-image: url('https://picsum.photos/seed/checkoutbanner/1600/300')">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1>Checkout</h1>
        <p>Complete your order</p>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <a href="cart.php">Cart</a> <span>/</span> <span>Checkout</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if($order_placed): ?>
        <!-- Order Success -->
        <div class="order-success">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h2>Order Placed Successfully! 🎉</h2>
            <p>Your order <strong>#DV-<?php echo str_pad($order_id_display, 5, '0', STR_PAD_LEFT); ?></strong> has been placed.</p>
            <p>We have sent a confirmation email with details.</p>
            <div class="success-details">
                <div><span>Tracking ID:</span> <strong style="color:#e11d48;"><?php echo $tracking_id_display; ?></strong></div>
                <div><span>Total Amount:</span> <strong><?php echo formatPrice($grand_total); ?></strong></div>
                <div><span>Payment:</span> <strong>Cash on Delivery</strong></div>
                <div><span>Status:</span> <strong class="status-pending">Pending</strong></div>
            </div>
            <a href="products.php" class="btn btn-primary btn-lg"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
        </div>
        <?php else: ?>

        <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo implode('<br>', $errors); ?>
        </div>
        <?php endif; ?>

        <div class="checkout-layout">
            <!-- Checkout Form -->
            <div class="checkout-form-section">
                <h3><i class="fas fa-map-marker-alt"></i> Delivery Information</h3>
                <form method="post" class="checkout-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required placeholder="your@email.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required placeholder="03XX XXXXXXX">
                        </div>
                        <div class="form-group">
                            <label>City *</label>
                            <select name="city" required>
                                <option value="">Select City</option>
                                <option value="Lahore" <?php echo (($_POST['city'] ?? '') == 'Lahore') ? 'selected' : ''; ?>>Lahore</option>
                                <option value="Karachi" <?php echo (($_POST['city'] ?? '') == 'Karachi') ? 'selected' : ''; ?>>Karachi</option>
                                <option value="Islamabad" <?php echo (($_POST['city'] ?? '') == 'Islamabad') ? 'selected' : ''; ?>>Islamabad</option>
                                <option value="Rawalpindi" <?php echo (($_POST['city'] ?? '') == 'Rawalpindi') ? 'selected' : ''; ?>>Rawalpindi</option>
                                <option value="Faisalabad" <?php echo (($_POST['city'] ?? '') == 'Faisalabad') ? 'selected' : ''; ?>>Faisalabad</option>
                                <option value="Multan" <?php echo (($_POST['city'] ?? '') == 'Multan') ? 'selected' : ''; ?>>Multan</option>
                                <option value="Peshawar" <?php echo (($_POST['city'] ?? '') == 'Peshawar') ? 'selected' : ''; ?>>Peshawar</option>
                                <option value="Quetta" <?php echo (($_POST['city'] ?? '') == 'Quetta') ? 'selected' : ''; ?>>Quetta</option>
                                <option value="Other" <?php echo (($_POST['city'] ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Complete Address *</label>
                        <textarea name="address" rows="3" required placeholder="House/Flat #, Street, Area, Colony..."><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>

                    <h3 style="margin-top:30px"><i class="fas fa-credit-card"></i> Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-option selected">
                            <input type="radio" name="payment" value="cod" checked>
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Cash on Delivery</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-full" style="margin-top:30px">
                        <i class="fas fa-lock"></i> Place Order - <?php echo formatPrice($grand_total); ?>
                    </button>
                </form>
            </div>

            <!-- Order Summary (Same as before) -->
            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <?php foreach($cart_items as $item): ?>
                <div class="checkout-item">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="checkout-item-info">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <span>Qty: <?php echo $item['qty']; ?> × <?php echo formatPrice($item['price']); ?></span>
                    </div>
                    <span class="checkout-item-total"><?php echo formatPrice($item['subtotal']); ?></span>
                </div>
                <?php endforeach; ?>

                <div class="checkout-totals">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?php echo formatPrice($cart_total); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span><?php echo $shipping === 0 ? '<span class="free-shipping">FREE</span>' : formatPrice($shipping); ?></span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span><?php echo formatPrice($grand_total); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'footer.php'; ?>