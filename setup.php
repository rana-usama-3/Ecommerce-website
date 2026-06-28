<?php
require_once 'database.php';

 $pdo->exec("CREATE DATABASE IF NOT EXISTS dress_shop");
 $pdo->exec("USE dress_shop");

// 1. Create Users Table (Naya)
 $pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 2. Update Categories Table (Purana)
 $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    image VARCHAR(500) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 3. Update Products Table (Purana)
 $pdo->exec("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) DEFAULT NULL,
    description TEXT,
    image VARCHAR(500) NOT NULL,
    image2 VARCHAR(500) DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    stock INT DEFAULT 50,
    sizes VARCHAR(200) DEFAULT 'S,M,L,XL',
    colors VARCHAR(200) DEFAULT 'Black,White,Red',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 4. Update Orders Table (User ID aur Tracking ID add kiye)
 $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    tracking_id VARCHAR(50) DEFAULT NULL,
    customer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 5. Order Items Table (Purana)
 $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(500),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 6. Admins Table (Purana)
 $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Default Admin Insert
 $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
 $stmt = $pdo->prepare("INSERT IGNORE INTO admins (username, password) VALUES (?, ?)");
 $stmt->execute(['admin', $admin_pass]);

// Insert Categories (Agar nahi hain to)
 $categories = [
    ['Casual Dresses', 'casual-dresses', 'https://picsum.photos/seed/casualdress/600/400', 'Everyday comfortable and stylish casual dresses'],
    ['Formal Dresses', 'formal-dresses', 'https://picsum.photos/seed/formaldress/600/400', 'Elegant formal dresses for office and meetings'],
    ['Party Wear', 'party-wear', 'https://picsum.photos/seed/partydress/600/400', 'Stunning party wear to steal the show'],
    ['Wedding Dresses', 'wedding-dresses', 'https://picsum.photos/seed/weddingdress/600/400', 'Beautiful wedding and bridal dresses'],
    ['Summer Dresses', 'summer-dresses', 'https://picsum.photos/seed/summerdress/600/400', 'Light and breezy summer collection'],
    ['Winter Dresses', 'winter-dresses', 'https://picsum.photos/seed/winterdress/600/400', 'Warm and cozy winter dress collection'],
];
 $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, image, description) VALUES (?, ?, ?, ?)");
foreach($categories as $cat) { $stmt->execute($cat); }

// Insert Products (Thoda data agar nahi ho to)
 $products = [
    ['Floral Cotton Casual Dress', 1, 2499, 3999, 'Beautiful floral print cotton dress perfect for daily wear.', 'https://picsum.photos/seed/floral-cotton/600/800', 'https://picsum.photos/seed/floral-cotton2/600/800', 1, 30, 'S,M,L,XL', 'Blue,Pink'],
    ['Black Office Sheath Dress', 2, 4999, 7500, 'Sophisticated black sheath dress ideal for office.', 'https://picsum.photos/seed/black-sheath/600/800', '', 1, 15, 'S,M,L', 'Black'],
    ['Sequin Party Dress', 3, 7999, 12000, 'Dazzling sequin party dress.', 'https://picsum.photos/seed/sequin-party/600/800', '', 1, 8, 'S,M', 'Gold,Silver'],
];
 $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, category_id, price, original_price, description, image, image2, featured, stock, sizes, colors) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach($products as $p) { $stmt->execute($p); }

echo "<h2 style='text-align:center;padding:50px;font-family:system-ui;color:#16a34a;'>✅ Database Setup Complete! <br>Users Table Created. Orders Updated. <br><a href='login.php' style='color:#0ea5e9;'>Go to Login →</a></h2>";
?>