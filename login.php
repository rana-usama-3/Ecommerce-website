<?php
require_once 'database.php';
require_once 'functions.php';

// Agar pehle se login hai to index par bhejo
if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

 $error = '';
 $success = '';

// Login Logic
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: index.php");
        exit;
    } else {
        $error = 'Invalid email or password!';
    }
}

// Forgot Password Logic (Simple Simulation/Mail)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['forgot_email']);
    // Yahan real mail send karne ka logic hota hai
    // Abhi ke liye bas message show kar rahe hain
    $success = "If an account exists with $email, a password reset link has been sent.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - DressVibe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: 'Inter', sans-serif; }
        .login-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
        .login-card h2 { font-family: 'Playfair Display', serif; margin-bottom: 24px; color: #e11d48; }
        .form-group { margin-bottom: 16px; text-align: left; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #e4e4e7; border-radius: 8px; box-sizing: border-box; }
        .btn-primary { width: 100%; background: #e11d48; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .btn-primary:hover { background: #be123c; }
        .toggle-link { margin-top: 16px; font-size: 13px; }
        .toggle-link a { color: #e11d48; text-decoration: none; font-weight: 600; }
        .error-msg { color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
        .success-msg { color: #16a34a; background: #f0fdf4; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2><i class="fas fa-gem"></i> DressVibe</h2>
        
        <?php if($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?><div class="success-msg"><?php echo $success; ?></div><?php endif; ?>

        <!-- Login Form -->
        <form method="post" id="loginForm">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" name="login" class="btn-primary">Login</button>
            <div class="toggle-link">
                <a href="#" onclick="toggleForgot()">Forgot Password?</a>
            </div>
            <div class="toggle-link" style="margin-top: 10px;">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </form>

        <!-- Forgot Password Form (Hidden by default) -->
        <form method="post" id="forgotForm" style="display:none;">
            <p style="font-size:13px; color:#71717a; margin-bottom:15px;">Enter your email to receive a reset link.</p>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="forgot_email" required placeholder="Enter your email">
            </div>
            <button type="submit" name="forgot_password" class="btn-primary" style="background:#18181b;">Send Reset Link</button>
            <div class="toggle-link">
                <a href="#" onclick="toggleForgot()">Back to Login</a>
            </div>
        </form>
    </div>

    <script>
        function toggleForgot() {
            var loginForm = document.getElementById('loginForm');
            var forgotForm = document.getElementById('forgotForm');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                forgotForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                forgotForm.style.display = 'block';
            }
        }
    </script>
</body>
</html>