<?php
function formatPrice($price) {
    return 'Rs. ' . number_format($price, 0);
}

function getDiscountPercent($price, $original_price) {
    if ($original_price > $price) {
        return round((($original_price - $price) / $original_price) * 100);
    }
    return 0;
}

function getCartCount() {
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

function addToCart($product_id) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCartQty($product_id, $qty) {
    if ($qty < 1) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}
?>