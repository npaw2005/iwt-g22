<?php
$isLoggedIn = isset($_SESSION['user_id']);
$role = $isLoggedIn ? $_SESSION['role'] : '';
$isAdmin = $role === 'admin';
$isRegistrar = $role === 'registrar';
$isStudent = $role === 'student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Scholarship Management System'; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Scholarship System</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="functionalities.php">Functionalities</a></li>
            <?php if ($isRegistrar): ?>
                <li><a href="applications.php">Registrar Panel</a></li>
            <?php endif; ?>
            <li><a href="help.php">Help</a></li>
            <?php if ($isAdmin): ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php endif; ?>
            <?php if ($isLoggedIn): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
        <div class="navbar-clear"></div>
    </nav>
