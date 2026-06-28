<?php
require_once 'database.php';
unset($_SESSION['admin_logged_in']); unset($_SESSION['admin_id']);
header("Location: admin_login.php"); exit;