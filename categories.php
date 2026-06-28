<?php $pageTitle = 'Categories'; require_once 'header.php'; ?>

<section class="page-header" style="background-image: url('https://picsum.photos/seed/catbanner/1600/400')">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1>All Categories</h1>
        <p>Explore our complete range of dress collections</p>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <span>Categories</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="category-full-grid">
            <?php
            $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count, MIN(p.price) as min_price, MAX(p.price) as max_price FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name");
            while($cat = $stmt->fetch()):
            ?>
            <a href="products.php?cat=<?php echo $cat['slug']; ?>" class="category-full-card">
                <div class="category-full-img">
                    <img src="<?php echo $cat['image']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" loading="lazy">
                    <div class="category-full-overlay">
                        <div class="category-full-info">
                            <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                            <p><?php echo htmlspecialchars($cat['description']); ?></p>
                            <div class="category-meta">
                                <span><i class="fas fa-tshirt"></i> <?php echo $cat['product_count']; ?> Products</span>
                                <span><i class="fas fa-tag"></i> Starting from <?php echo $cat['min_price'] ? formatPrice($cat['min_price']) : 'N/A'; ?></span>
                            </div>
                            <span class="btn btn-primary btn-sm">Shop Now <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>