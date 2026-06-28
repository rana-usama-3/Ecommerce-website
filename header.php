<?php 
session_start(); // Ensure session is started
require_once 'database.php'; 
require_once 'functions.php'; 

// --- AUTHENTICATION GATE ---
// Agar user login nahi hai aur wo login/register page par nahi hai, to login page par bhejo
 $current_page = basename($_SERVER['PHP_SELF']);
 $allowed_pages = ['login.php', 'register.php'];

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    if (!in_array($current_page, $allowed_pages)) {
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'DressVibe'; ?> - DressVibe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container top-bar-inner">
            <span><i class="fas fa-truck"></i> Free Delivery on Orders Above Rs. 3,000</span>
            <span><i class="fas fa-phone"></i> +92 325 4357477</span>
            <span><i class="fas fa-envelope"></i> egoenergetic@gmail.com</span>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <span class="logo-icon"><i class="fas fa-gem"></i></span>
                <div>
                    <span class="logo-text">DressVibe</span>
                    <span class="logo-tagline">Elegance Redefined</span>
                </div>
            </a>

            <nav class="main-nav" id="mainNav">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
                <a href="categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">Categories</a>
                <a href="products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Shop</a>
                <a href="cart.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">Cart</a>
            </nav>

            <div class="header-actions">
                <!-- User Portal Icon (Naya) -->
                <a href="user_dashboard.php" class="cart-btn" title="My Account">
                    <i class="fas fa-user-circle"></i>
                </a>

                <!-- Admin Icon -->
                <a href="admin_index.php" class="cart-btn" title="Admin Panel" target="_blank">
                    <i class="fas fa-user-shield"></i>
                </a>

                <!-- Cart Icon -->
                <a href="cart.php" class="cart-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <?php $count = getCartCount(); if($count > 0): ?>
                        <span class="cart-badge"><?php echo $count; ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Logout Button (Naya) -->
                <a href="logout.php" class="cart-btn" title="Logout" style="font-size: 18px;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>

                <button class="mobile-menu-btn" onclick="document.getElementById('mainNav').classList.toggle('show')">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>
    <main>