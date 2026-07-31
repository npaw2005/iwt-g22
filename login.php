<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: home.php");
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Scholarship Management System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
    <div class="auth-container">
        <h2>System Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom: 1rem;">Login</button>
            <div style="display: flex; gap: 1rem;">
                <a href="signup.php" class="btn" style="flex: 1; background: #6B2226; color: white;">Sign Up</a>
                <a href="home.php" class="btn" style="flex: 1; background: #8C5D63; color: white;">Guest View</a>
            </div>
        </form>
    </div>
</body>
</html>
