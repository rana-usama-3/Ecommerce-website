<?php $pageTitle = 'Cart'; require_once 'header.php';

// Handle cart actions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if($action === 'add' && $product_id > 0) {
        addToCart($product_id);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
    if($action === 'remove' && $product_id > 0) {
        removeFromCart($product_id);
    }
    if($action === 'update' && $product_id > 0) {
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        updateCartQty($product_id, $qty);
    }
    if($action === 'clear') {
        unset($_SESSION['cart']);
    }
}

// Get cart items
 $cart_items = [];
 $cart_total = 0;
 $cart_count = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
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
}
?>

<section class="page-header" style="background-image: url('https://picsum.photos/seed/cartbanner/1600/300')">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1>Shopping Cart</h1>
        <p><?php echo $cart_count; ?> item(s) in your cart</p>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Cart</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if(empty($cart_items)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything yet</p>
            <a href="products.php" class="btn btn-primary btn-lg"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
        </div>
        <?php else: ?>
        <div class="cart-layout">
            <!-- Cart Items -->
            <div class="cart-items">
                <div class="cart-header-row">
                    <span>Product</span>
                    <span>Price</span>
                    <span>Quantity</span>
                    <span>Total</span>
                    <span>Action</span>
                </div>
                <?php foreach($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="cart-product">
                        <a href="product-detail.php?id=<?php echo $item['id']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </a>
                        <div>
                            <h4><a href="product-detail.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h4>
                            <span class="cart-item-cat"><?php echo htmlspecialchars($item['category_name'] ?? ''); ?></span>
                        </div>
                    </div>
                    <div class="cart-price"><?php echo formatPrice($item['price']); ?></div>
                    <div class="cart-qty">
                        <form method="post" style="display:flex;align-items:center;gap:5px">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="button" class="qty-btn" onclick="changeQty(this, -1)">-</button>
                            <input type="number" name="qty" value="<?php echo $item['qty']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="qty-input" onchange="this.form.submit()">
                            <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                        </form>
                    </div>
                    <div class="cart-total"><?php echo formatPrice($item['subtotal']); ?></div>
                    <div class="cart-action">
                        <form method="post">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="remove-btn" title="Remove"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="cart-actions-bar">
                    <form method="post">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-trash"></i> Clear Cart</button>
                    </form>
                    <a href="products.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal (<?php echo $cart_count; ?> items)</span>
                    <span><?php echo formatPrice($cart_total); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $cart_total >= 3000 ? '<span class="free-shipping">FREE</span>' : formatPrice(250); ?></span>
                </div>
                <?php if($cart_total < 3000): ?>
                <div class="shipping-note">
                    <i class="fas fa-info-circle"></i> Add <?php echo formatPrice(3000 - $cart_total); ?> more for free shipping!
                </div>
                <?php endif; ?>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span><?php echo formatPrice($cart_total + ($cart_total >= 3000 ? 0 : 250)); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary btn-lg btn-full"><i class="fas fa-lock"></i> Proceed to Checkout</a>
                <div class="trust-badges">
                    <span><i class="fas fa-shield-alt"></i> Secure Checkout</span>
                    <span><i class="fas fa-undo"></i> Easy Returns</span>
                    <span><i class="fas fa-money-bill-wave"></i> COD Available</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function changeQty(btn, change) {
    let input = btn.parentElement.querySelector('.qty-input');
    let newQty = parseInt(input.value) + change;
    let max = parseInt(input.max);
    if(newQty < 1) newQty = 1;
    if(newQty > max) newQty = max;
    input.value = newQty;
    input.form.submit();
}
</script>

<?php require_once 'footer.php'; ?>