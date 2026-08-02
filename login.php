<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>System Login</h2>
        <?php if ($error): ?>
        <script type="text/javascript">alert("<?php echo addslashes($error); ?>");</script>
        <?php endif; ?>
        <form action="login.php" method="POST" onsubmit="return checkLogin()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <input type="hidden" id="loginErrorHolder" value="">
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="signup.php" class="btn">Sign Up</a>
            <a href="home.php" class="btn">Guest View</a>
        </form>
    </div>

    <script type="text/javascript">
        function checkLogin() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;

            if (username == '') {
                alert('Please enter your username.');
                return false;
            }
            if (password == '') {
                alert('Please enter your password.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
