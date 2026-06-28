<?php require_once 'database.php'; require_once 'functions.php';
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: admin_login.php"); exit; }

if(isset($_GET['delete'])) { $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([(int)$_GET['delete']]); header("Location: admin_categories.php?msg=deleted"); exit; }
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)); $image = trim($_POST['image']); $description = trim($_POST['description']); $edit_id = (int)($_POST['edit_id'] ?? 0);
    if($edit_id > 0) { $pdo->prepare("UPDATE categories SET name=?, slug=?, image=?, description=? WHERE id=?")->execute([$name, $slug, $image, $description, $edit_id]); }
    else { $pdo->prepare("INSERT INTO categories (name, slug, image, description) VALUES (?, ?, ?, ?)")->execute([$name, $slug, $image, $description]); }
    header("Location: admin_categories.php?msg=saved"); exit;
}
 $edit_cat = null; if(isset($_GET['edit'])) { $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?"); $stmt->execute([(int)$_GET['edit']]); $edit_cat = $stmt->fetch(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Categories - DressVibe Admin</title>
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
            <a href="admin_products.php" class="admin-sidebar-link"><i class="fas fa-tshirt"></i> Products</a>
            <a href="admin_categories.php" class="admin-sidebar-link active"><i class="fas fa-folder"></i> Categories</a>
            <a href="admin_logout.php" class="admin-sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="admin-main">
        <div class="admin-page-title"><span>Manage Categories</span></div>
        <?php if(isset($_GET['msg'])): ?><div style="background:#dcfce7;color:#166534;padding:12px;border-radius:8px;margin-bottom:16px;">Success!</div><?php endif; ?>
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;">
            <div class="admin-card">
                <h3 style="margin-bottom:16px;"><?php echo $edit_cat ? 'Edit Category' : 'Add New Category'; ?></h3>
                <form method="post" class="admin-form" style="grid-template-columns:1fr;">
                    <?php if($edit_cat): ?><input type="hidden" name="edit_id" value="<?php echo $edit_cat['id']; ?>"><?php endif; ?>
                    <div><label>Category Name</label><input type="text" name="name" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['name']) : ''; ?>" required></div>
                    <div><label>Image URL</label><input type="text" name="image" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['image']) : ''; ?>" required></div>
                    <div><label>Description</label><textarea name="description" rows="3" required><?php echo $edit_cat ? htmlspecialchars($edit_cat['description']) : ''; ?></textarea></div>
                    <button type="submit" class="admin-btn admin-btn-primary" style="padding:12px;"><?php echo $edit_cat ? 'Update' : 'Add'; ?> Category</button>
                    <?php if($edit_cat): ?><a href="admin_categories.php" style="display:block;text-align:center;margin-top:8px;color:#71717a;font-size:13px;">Cancel Edit</a><?php endif; ?>
                </form>
            </div>
            <div class="admin-card" style="overflow-x:auto;">
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Products</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as prod_count FROM categories c ORDER BY c.id DESC"); while($cat = $stmt->fetch()): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><img src="<?php echo $cat['image']; ?>" alt=""></td>
                            <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                            <td><?php echo $cat['prod_count']; ?></td>
                            <td>
                                <a href="admin_categories.php?edit=<?php echo $cat['id']; ?>" class="admin-btn admin-btn-edit"><i class="fas fa-edit"></i></a>
                                <a href="admin_categories.php?delete=<?php echo $cat['id']; ?>" class="admin-btn admin-btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>