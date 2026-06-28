<?php require_once 'database.php'; require_once 'functions.php';
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: admin_login.php"); exit; }

if(isset($_GET['delete'])) { $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([(int)$_GET['delete']]); header("Location: admin_products.php?msg=deleted"); exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $category_id = (int)$_POST['category_id']; $price = (float)$_POST['price']; $original_price = (float)($_POST['original_price'] ?? 0); $description = trim($_POST['description']); $image = trim($_POST['image']); $image2 = trim($_POST['image2'] ?? ''); $stock = (int)$_POST['stock']; $sizes = trim($_POST['sizes']); $colors = trim($_POST['colors']); $featured = isset($_POST['featured']) ? 1 : 0; $edit_id = (int)($_POST['edit_id'] ?? 0);
    if($edit_id > 0) { $pdo->prepare("UPDATE products SET name=?, category_id=?, price=?, original_price=?, description=?, image=?, image2=?, stock=?, sizes=?, colors=?, featured=? WHERE id=?")->execute([$name, $category_id, $price, $original_price, $description, $image, $image2, $stock, $sizes, $colors, $featured, $edit_id]); }
    else { $pdo->prepare("INSERT INTO products (name, category_id, price, original_price, description, image, image2, stock, sizes, colors, featured) VALUES (?,?,?,?,?,?,?,?,?,?,?)")->execute([$name, $category_id, $price, $original_price, $description, $image, $image2, $stock, $sizes, $colors, $featured]); }
    header("Location: admin_products.php?msg=saved"); exit;
}
 $edit_prod = null; if(isset($_GET['edit'])) { $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $edit_prod = $stmt->fetch(); }
 $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Products - DressVibe Admin</title>
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
            <a href="admin_products.php" class="admin-sidebar-link active"><i class="fas fa-tshirt"></i> Products</a>
            <a href="admin_categories.php" class="admin-sidebar-link"><i class="fas fa-folder"></i> Categories</a>
            <a href="admin_logout.php" class="admin-sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="admin-main">
        <div class="admin-page-title"><span>Manage Products</span> <a href="admin_products.php" class="admin-btn admin-btn-primary"><i class="fas fa-plus"></i> Add New</a></div>
        <?php if(isset($_GET['msg'])): ?><div style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:16px;">Success!</div><?php endif; ?>

        <?php if($edit_prod || !isset($_GET['edit'])): ?>
        <div class="admin-card" style="margin-bottom:24px;">
            <h3 style="margin-bottom:16px;"><?php echo $edit_prod ? 'Edit Product' : 'Add New Product'; ?></h3>
            <form method="post" class="admin-form">
                <?php if($edit_prod): ?><input type="hidden" name="edit_id" value="<?php echo $edit_prod['id']; ?>"><?php endif; ?>
                <div><label>Product Name *</label><input type="text" name="name" value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['name']) : ''; ?>" required></div>
                <div><label>Category *</label><select name="category_id" required><option value="">Select</option><?php foreach($categories as $cat): ?><option value="<?php echo $cat['id']; ?>" <?php echo ($edit_prod && $edit_prod['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div>
                <div><label>Price (Rs.) *</label><input type="number" name="price" step="0.01" value="<?php echo $edit_prod ? $edit_prod['price'] : ''; ?>" required></div>
                <div><label>Original Price</label><input type="number" name="original_price" step="0.01" value="<?php echo $edit_prod ? $edit_prod['original_price'] : ''; ?>"></div>
                <div><label>Stock *</label><input type="number" name="stock" value="<?php echo $edit_prod ? $edit_prod['stock'] : '50'; ?>" required></div>
                <div><label>Sizes</label><input type="text" name="sizes" value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['sizes']) : 'S,M,L,XL'; ?>"></div>
                <div><label>Colors</label><input type="text" name="colors" value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['colors']) : 'Black,White'; ?>"></div>
                <div style="display:flex;align-items:center;gap:8px;padding-top:22px;"><input type="checkbox" name="featured" value="1" id="feat" <?php echo ($edit_prod && $edit_prod['featured']) ? 'checked' : ''; ?>><label for="feat" style="margin:0;">Featured</label></div>
                <div class="full-width"><label>Image URL *</label><input type="text" name="image" value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['image']) : ''; ?>" required></div>
                <div class="full-width"><label>Image 2 URL</label><input type="text" name="image2" value="<?php echo $edit_prod ? htmlspecialchars($edit_prod['image2'] ?? '') : ''; ?>"></div>
                <div class="full-width"><label>Description *</label><textarea name="description" rows="3" required><?php echo $edit_prod ? htmlspecialchars($edit_prod['description']) : ''; ?></textarea></div>
                <div class="full-width"><button type="submit" class="admin-btn admin-btn-primary" style="padding:12px 24px;"><?php echo $edit_prod ? 'Update' : 'Add'; ?> Product</button> <?php if($edit_prod): ?><a href="admin_products.php" style="margin-left:12px;color:#71717a;font-size:13px;">Cancel</a><?php endif; ?></div>
            </form>
        </div>
        <?php endif; ?>

        <div class="admin-card" style="overflow-x:auto;">
            <table class="admin-table">
                <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC"); while($prod = $stmt->fetch()): ?>
                    <tr>
                        <td><?php echo $prod['id']; ?></td>
                        <td><img src="<?php echo $prod['image']; ?>" alt=""></td>
                        <td><strong><?php echo htmlspecialchars($prod['name']); ?></strong></td>
                        <td><?php echo formatPrice($prod['price']); ?></td>
                        <td style="color:<?php echo $prod['stock'] <= 5 ? '#ef4444' : '#16a34a'; ?>;"><?php echo $prod['stock']; ?></td>
                        <td>
                            <a href="admin_products.php?edit=<?php echo $prod['id']; ?>" class="admin-btn admin-btn-edit"><i class="fas fa-edit"></i></a>
                            <a href="admin_products.php?delete=<?php echo $prod['id']; ?>" class="admin-btn admin-btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>