<?php $pageTitle = 'Home'; require_once 'header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg" style="background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?w=1600&q=80')"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <span class="hero-badge">New Collection 2026</span>
        <h1 class="hero-title">Discover Your <br><em>Perfect Dress</em></h1>
        <p class="hero-subtitle">Explore our curated collection of elegant dresses for every occasion. From casual to couture, find your style.</p>
        <div class="hero-buttons">
            <a href="products.php" class="btn btn-primary btn-lg">Shop Now <i class="fas fa-arrow-right"></i></a>
            <a href="categories.php" class="btn btn-outline btn-lg">Browse Categories</a>
        </div>
    </div>
</section>

<!-- Features Bar -->
<section class="features-bar">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-truck"></i>
                <div>
                    <strong>Free Shipping</strong>
                    <span>On orders above Rs. 3,000</span>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-undo"></i>
                <div>
                    <strong>Easy Returns</strong>
                    <span>7-day return policy</span>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <strong>Secure Payment</strong>
                    <span>100% secure checkout</span>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <div>
                    <strong>24/7 Support</strong>
                    <span>Always here to help</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Browse</span>
            <h2 class="section-title">Shop by Category</h2>
            <p class="section-subtitle">Find the perfect dress for every occasion</p>
        </div>
        <div class="category-grid">
            <?php
            $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name");
            while($cat = $stmt->fetch()):
            ?>
            <a href="products.php?cat=<?php echo $cat['slug']; ?>" class="category-card">
                <div class="category-img">
                    <img src="<?php echo $cat['image']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" loading="lazy">
                    <div class="category-overlay">
                        <span class="category-count"><?php echo $cat['product_count']; ?> Items</span>
                    </div>
                </div>
                <div class="category-info">
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <span>Shop Now <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Trending</span>
            <h2 class="section-title">Featured Dresses</h2>
            <p class="section-subtitle">Our most popular picks this season</p>
        </div>
        <div class="product-grid">
            <?php
            $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 ORDER BY RAND() LIMIT 8");
            while($product = $stmt->fetch()):
                $discount = getDiscountPercent($product['price'], $product['original_price']);
            ?>
            <div class="product-card">
                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-img-wrap">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                    <?php if($discount > 0): ?>
                        <span class="product-badge">-<?php echo $discount; ?>%</span>
                    <?php endif; ?>
                    <div class="product-hover">
                        <span>View Details <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                <div class="product-info">
                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h3 class="product-name"><a href="product-detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                        <?php if($product['original_price'] > $product['price']): ?>
                            <span class="original-price"><?php echo formatPrice($product['original_price']); ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="cart.php" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-cart"><i class="fas fa-shopping-bag"></i> Add to Cart</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center" style="margin-top:40px">
            <a href="products.php" class="btn btn-primary btn-lg">View All Dresses <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- Banner Section -->
<section class="promo-banner" style="background-image: url('https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1600&q=80')">
    <div class="promo-overlay"></div>
    <div class="container promo-content">
        <span class="promo-tag">Limited Time Offer</span>
        <h2>Summer Sale - Up to 50% Off</h2>
        <p>Don't miss out on our biggest sale of the season. Shop now and save big!</p>
        <a href="products.php" class="btn btn-primary btn-lg">Shop the Sale <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<!-- New Arrivals -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Just In</span>
            <h2 class="section-title">New Arrivals</h2>
            <p class="section-subtitle">Fresh styles just added to our collection</p>
        </div>
        <div class="product-grid">
            <?php
            $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.featured = 0 ORDER BY RAND() LIMIT 4");
            while($product = $stmt->fetch()):
                $discount = getDiscountPercent($product['price'], $product['original_price']);
            ?>
            <div class="product-card">
                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-img-wrap">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                    <?php if($discount > 0): ?>
                        <span class="product-badge">-<?php echo $discount; ?>%</span>
                    <?php endif; ?>
                    <div class="product-hover">
                        <span>View Details <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                <div class="product-info">
                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h3 class="product-name"><a href="product-detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                    <div class="product-price">
                        <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                        <?php if($product['original_price'] > $product['price']): ?>
                            <span class="original-price"><?php echo formatPrice($product['original_price']); ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="cart.php" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-cart"><i class="fas fa-shopping-bag"></i> Add to Cart</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section section-light">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Reviews</span>
            <h2 class="section-title">What Our Customers Say</h2>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p>"Absolutely love the quality! The floral casual dress is my favorite. Will order again for sure."</p>
                <div class="testimonial-author">
                    <img src="https://picsum.photos/seed/user1face/80/80" alt="Customer">
                    <div>
                        <strong>Ayesha Khan</strong>
                        <span>Lahore</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p>"The bridal gown was exactly as shown. Amazing craftsmanship. My wedding day was perfect!"</p>
                <div class="testimonial-author">
                    <img src="https://picsum.photos/seed/user2face/80/80" alt="Customer">
                    <div>
                        <strong>Fatima Noor</strong>
                        <span>Karachi</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                </div>
                <p>"Fast delivery and great prices. The party wear dress was a showstopper at my friend's birthday."</p>
                <div class="testimonial-author">
                    <img src="https://picsum.photos/seed/user3face/80/80" alt="Customer">
                    <div>
                        <strong>Sana Ali</strong>
                        <span>Islamabad</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>