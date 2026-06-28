    </main>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-inner">
                <div>
                    <h3>Subscribe to Our Newsletter</h3>
                    <p>Get 10% off on your first order + exclusive deals!</p>
                </div>
                <form class="newsletter-form" onsubmit="event.preventDefault(); this.innerHTML='<p style=color:white;font-weight:600>✅ Subscribed Successfully!</p>'">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-icon"><i class="fas fa-gem"></i></span>
                        <span class="logo-text">DressVibe</span>
                    </div>
                    <p>Your one-stop destination for trendy and elegant dresses. We bring you the latest fashion at affordable prices.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <a href="index.php">Home</a>
                    <a href="categories.php">Categories</a>
                    <a href="products.php">Shop All</a>
                    <a href="cart.php">Cart</a>
                </div>
                <div class="footer-col">
                    <h4>Categories</h4>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name LIMIT 6");
                    while($cat = $stmt->fetch()) {
                        echo '<a href="products.php?cat=' . $cat['slug'] . '">' . htmlspecialchars($cat['name']) . '</a>';
                    }
                    ?>
                </div>
                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Gulberg III, Lahore, Pakistan</p>
                    <p><i class="fas fa-phone"></i> +92 325 4357477</p>
                    <p><i class="fas fa-envelope"></i> egoenergetic@gmail.com</p>
                    <p><i class="fas fa-clock"></i> Mon-Sat: 10AM - 9PM</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 DressVibe. All rights reserved.</p>
                <div class="payment-icons">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>