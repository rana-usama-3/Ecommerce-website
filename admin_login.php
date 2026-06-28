<?php
require_once 'database.php';

if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_index.php");
    exit;
}

 $error = '';
 $success = '';

// --- LOGIN LOGIC ---
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_email'] = $admin['email'];

        // --- LOGIN LOG SAVE KAREIN ---
        $ip = $_SERVER['REMOTE_ADDR']; // User IP
        $user_agent = $_SERVER['HTTP_USER_AGENT']; // Browser Info
        $log_stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, ip_address, user_agent) VALUES (?, ?, ?)");
        $log_stmt->execute([$admin['id'], $ip, $user_agent]);

        header("Location: admin_index.php");
        exit;
    } else {
        $error = 'Invalid email or password!';
    }
}

// --- FORGOT PASSWORD LOGIC ---
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $email = trim($_POST['forgot_email']);
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if($admin) {
        $success = "A password reset link has been sent to <strong>$email</strong>. (Check Inbox/Spam)";
    } else {
        $error = "Email not found in admin records.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Admin Login - DressVibe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body.admin-login-body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #18181b 0%, #27272a 100%); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .admin-login-box { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); width: 100%; max-width: 420px; }
        .admin-login-box h2 { text-align: center; margin-bottom: 24px; color: #18181b; font-family: 'Playfair Display', serif; font-size: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
        .form-group input { width: 100%; padding: 12px; border: 1.5px solid #e4e4e7; border-radius: 8px; box-sizing: border-box; outline: none; }
        .form-group input:focus { border-color: #e11d48; }
        .admin-btn-primary { width: 100%; background: #e11d48; color: white; padding: 14px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .admin-btn-primary:hover { background: #be123c; }
        .toggle-link { margin-top: 16px; font-size: 13px; text-align: center; }
        .toggle-link a { color: #e11d48; text-decoration: none; font-weight: 600; }
        .error-msg { color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; text-align: center; }
        .success-msg { color: #16a34a; background: #f0fdf4; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; text-align: center; }
    </style>
</head>
<body class="admin-login-body">
    <div class="admin-login-box">
        <h2><i class="fas fa-gem" style="color:#e11d48;"></i> Admin Panel</h2>
        
        <?php if($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?><div class="success-msg"><?php echo $success; ?></div><?php endif; ?>

        <!-- Login Form -->
        <form method="post" id="loginForm">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="admin@dressvibe.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" name="login" class="admin-btn-primary">Login to Dashboard</button>
            
            <div class="toggle-link">
                <a href="#" onclick="toggleForgot()">Forgot Password?</a>
            </div>
        </form>

        <!-- Forgot Password Form -->
        <form method="post" id="forgotForm" style="display:none;">
            <p style="font-size:13px; color:#71717a; text-align:center; margin-bottom:15px;">Enter your admin email to reset password.</p>
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="forgot_email" required placeholder="admin@dressvibe.com">
            </div>
            <button type="submit" name="forgot_password" class="admin-btn-primary" style="background:#18181b;">Send Reset Link</button>
            <div class="toggle-link">
                <a href="#" onclick="toggleForgot()">Back to Login</a>
            </div>
        </form>
    </div>
    <script>function toggleForgot(){var a=document.getElementById('loginForm'),b=document.getElementById('forgotForm');'none'===a.style.display?(a.style.display='block',b.style.display='none'):(a.style.display='none',b.style.display='block');}</script>
</body>
</html>