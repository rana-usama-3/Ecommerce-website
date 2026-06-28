<?php 
 $pageTitle = 'Product Detail'; 
require_once 'header.php';

 $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
 $stmt->execute([$id]);
 $product = $stmt->fetch();

if(!$product) {
    echo '<div class="container" style="padding:100px 20px;text-align:center"><h2>Product not found</h2><a href="products.php" class="btn btn-primary">Back to Shop</a></div>';
    require_once 'footer.php';
    exit;
}

 $discount = getDiscountPercent($product['price'], $product['original_price']);
 $sizes = explode(',', $product['sizes']);
 $colors = explode(',', $product['colors']);

// Related products
 $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 4");
 $stmt->execute([$product['category_id'], $id]);
 $related = $stmt->fetchAll();

// Handle add to cart
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    addToCart($product['id']);
    $added = true;
}
?>

<section class="page-header" style="background-image: url('https://picsum.photos/seed/detailbanner/1600/300')">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> 
            <a href="products.php?cat=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a> 
            <span>/</span> <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if(isset($added)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Product added to cart! <a href="cart.php">View Cart</a>
        </div>
        <?php endif; ?>

        <div class="product-detail-layout">
            <!-- Images -->
            <div class="product-detail-images">
                <div class="main-image">
                    <img id="mainProductImg" src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php if($discount > 0): ?>
                        <span class="product-badge badge-large">-<?php echo $discount; ?>% OFF</span>
                    <?php endif; ?>
                </div>
                <div class="thumb-images">
                    <div class="thumb active" onclick="changeImage('<?php echo $product['image']; ?>', this)">
                        <img src="<?php echo $product['image']; ?>" alt="Thumb 1">
                    </div>
                    <?php if($product['image2']): ?>
                    <div class="thumb" onclick="changeImage('<?php echo $product['image2']; ?>', this)">
                        <img src="<?php echo $product['image2']; ?>" alt="Thumb 2">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info -->
            <div class="product-detail-info">
                <span class="product-category-tag"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <h1 class="product-detail-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="product-detail-rating">
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <span>(4.5) - <?php echo rand(10, 99); ?> Reviews</span>
                </div>

                <div class="product-detail-price">
                    <span class="detail-current-price"><?php echo formatPrice($product['price']); ?></span>
                    <?php if($product['original_price'] > $product['price']): ?>
                        <span class="detail-original-price"><?php echo formatPrice($product['original_price']); ?></span>
                        <span class="detail-save">You save: <?php echo formatPrice($product['original_price'] - $product['price']); ?></span>
                    <?php endif; ?>
                </div>

                <p class="product-detail-desc"><?php echo htmlspecialchars($product['description']); ?></p>

                <!-- Sizes -->
                <div class="option-group">
                    <label>Size:</label>
                    <div class="size-options">
                        <?php foreach($sizes as $size): ?>
                        <button type="button" class="size-btn" onclick="selectSize(this)"><?php echo trim($size); ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Colors -->
                <div class="option-group">
                    <label>Color:</label>
                    <div class="color-options">
                        <?php foreach($colors as $color): ?>
                        <button type="button" class="color-btn" onclick="selectColor(this)" title="<?php echo trim($color); ?>">
                            <?php echo trim($color); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Stock -->
                <div class="stock-info">
                    <?php if($product['stock'] > 10): ?>
                        <span class="in-stock"><i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php elseif($product['stock'] > 0): ?>
                        <span class="low-stock"><i class="fas fa-exclamation-circle"></i> Only <?php echo $product['stock']; ?> left!</span>
                    <?php else: ?>
                        <span class="out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="product-actions">
                    <form method="post" style="flex:1">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-primary btn-lg btn-full" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-bag"></i> <?php echo $product['stock'] <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                        </button>
                    </form>
                    <a href="cart.php" class="btn btn-outline btn-lg"><i class="fas fa-eye"></i> View Cart</a>
                </div>

                <!-- Features -->
                <div class="product-features">
                    <div><i class="fas fa-truck"></i> Free Delivery</div>
                    <div><i class="fas fa-undo"></i> 7 Days Return</div>
                    <div><i class="fas fa-shield-alt"></i> Genuine Product</div>
                    <div><i class="fas fa-box"></i> Cash on Delivery</div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if(count($related) > 0): ?>
        <div class="related-section">
            <h2 class="section-title">You May Also Like</h2>
            <div class="product-grid">
                <?php foreach($related as $rp):
                    $rd = getDiscountPercent($rp['price'], $rp['original_price']);
                ?>
                <div class="product-card">
                    <a href="product-detail.php?id=<?php echo $rp['id']; ?>" class="product-img-wrap">
                        <img src="<?php echo $rp['image']; ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>" loading="lazy">
                        <?php if($rd > 0): ?><span class="product-badge">-<?php echo $rd; ?>%</span><?php endif; ?>
                        <div class="product-hover"><span>View Details <i class="fas fa-arrow-right"></i></span></div>
                    </a>
                    <div class="product-info">
                        <span class="product-category"><?php echo htmlspecialchars($rp['category_name'] ?? $product['category_name']); ?></span>
                        <h3 class="product-name"><a href="product-detail.php?id=<?php echo $rp['id']; ?>"><?php echo htmlspecialchars($rp['name']); ?></a></h3>
                        <div class="product-price">
                            <span class="current-price"><?php echo formatPrice($rp['price']); ?></span>
                            <?php if($rp['original_price'] > $rp['price']): ?>
                                <span class="original-price"><?php echo formatPrice($rp['original_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <form method="post" action="cart.php" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $rp['id']; ?>">
                            <input type="hidden" name="action" value="add">
                            <button type="submit" class="btn btn-cart"><i class="fas fa-shopping-bag"></i> Add to Cart</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function changeImage(src, el) {
    document.getElementById('mainProductImg').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
function selectSize(el) {
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
}
function selectColor(el) {
    document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
}
</script>

<?php require_once 'footer.php'; ?>