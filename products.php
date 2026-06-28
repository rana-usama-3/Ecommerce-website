<?php $pageTitle = 'Shop'; require_once 'header.php'; 

 $current_cat = isset($_GET['cat']) ? $_GET['cat'] : '';
 $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
 $search = isset($_GET['search']) ? trim($_GET['search']) : '';

 $where = "WHERE 1=1";
 $params = [];

if($current_cat) {
    $where .= " AND c.slug = ?";
    $params[] = $current_cat;
}
if($search) {
    $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

 $orderBy = "ORDER BY ";
switch($sort) {
    case 'price-low': $orderBy .= "p.price ASC"; break;
    case 'price-high': $orderBy .= "p.price DESC"; break;
    case 'name': $orderBy .= "p.name ASC"; break;
    default: $orderBy .= "p.id DESC"; break;
}

// Get category name for header
 $cat_name = 'All Dresses';
if($current_cat) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE slug = ?");
    $stmt->execute([$current_cat]);
    $cn = $stmt->fetch();
    if($cn) $cat_name = $cn['name'];
}

// Count
 $countSql = "SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id $where";
 $stmt = $pdo->prepare($countSql);
 $stmt->execute($params);
 $total_products = $stmt->fetchColumn();

// Products
 $productSql = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id $where $orderBy";
 $stmt = $pdo->prepare($productSql);
 $stmt->execute($params);
 $products = $stmt->fetchAll();

// All categories for filter
 $all_cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<section class="page-header" style="background-image: url('https://picsum.photos/seed/shopbanner/1600/400')">
    <div class="page-header-overlay"></div>
    <div class="container page-header-content">
        <h1><?php echo htmlspecialchars($cat_name); ?></h1>
        <p><?php echo $total_products; ?> products found<?php echo $search ? ' for "' . htmlspecialchars($search) . '"' : ''; ?></p>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span> <a href="categories.php">Categories</a> <span>/</span> <span><?php echo htmlspecialchars($cat_name); ?></span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="shop-layout">
            <!-- Sidebar -->
            <aside class="shop-sidebar">
                <!-- Search -->
                <div class="sidebar-widget">
                    <h3><i class="fas fa-search"></i> Search</h3>
                    <form method="get" class="search-form">
                        <?php if($current_cat): ?><input type="hidden" name="cat" value="<?php echo $current_cat; ?>"><?php endif; ?>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search dresses...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Categories Filter -->
                <div class="sidebar-widget">
                    <h3><i class="fas fa-folder"></i> Categories</h3>
                    <ul class="filter-list">
                        <li>
                            <a href="products.php<?php echo $search ? '?search=' . urlencode($search) : ''; ?>" class="<?php echo !$current_cat ? 'active' : ''; ?>">
                                All Dresses <span>(<?php 
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
                                    echo $stmt->fetchColumn(); 
                                ?>)</span>
                            </a>
                        </li>
                        <?php foreach($all_cats as $cat):
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                            $stmt->execute([$cat['id']]);
                            $cnt = $stmt->fetchColumn();
                        ?>
                        <li>
                            <a href="products.php?cat=<?php echo $cat['slug']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="<?php echo $current_cat == $cat['slug'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?> <span>(<?php echo $cnt; ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Price Range Info -->
                <div class="sidebar-widget">
                    <h3><i class="fas fa-tag"></i> Price Range</h3>
                    <div class="price-info">
                        <span>From: <?php echo formatPrice(1999); ?></span>
                        <span>To: <?php echo formatPrice(24999); ?></span>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="shop-main">
                <div class="shop-toolbar">
                    <p>Showing <?php echo count($products); ?> of <?php echo $total_products; ?> results</p>
                    <form method="get" class="sort-form">
                        <?php if($current_cat): ?><input type="hidden" name="cat" value="<?php echo $current_cat; ?>"><?php endif; ?>
                        <?php if($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                        <select name="sort" onchange="this.form.submit()">
                            <option value="newest" <?php echo $sort=='newest'?'selected':''; ?>>Newest First</option>
                            <option value="price-low" <?php echo $sort=='price-low'?'selected':''; ?>>Price: Low to High</option>
                            <option value="price-high" <?php echo $sort=='price-high'?'selected':''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sort=='name'?'selected':''; ?>>Name: A to Z</option>
                        </select>
                    </form>
                </div>

                <?php if(count($products) > 0): ?>
                <div class="product-grid">
                    <?php foreach($products as $product):
                        $discount = getDiscountPercent($product['price'], $product['original_price']);
                    ?>
                    <div class="product-card">
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-img-wrap">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                            <?php if($discount > 0): ?>
                                <span class="product-badge">-<?php echo $discount; ?>%</span>
                            <?php endif; ?>
                            <?php if($product['stock'] <= 5): ?>
                                <span class="product-badge badge-low">Low Stock</span>
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
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                    <a href="products.php" class="btn btn-primary">View All Products</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>