<?php
session_start();
require_once '../config/db.php';

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

$pageTitle = 'Login - Scholarship Management System';
require_once '../includes/header.php';
?>
    <div class="auth-container">
        <h2>System Login</h2>
        <?php if ($error): ?>
        <script type="text/javascript">alert("<?php echo addslashes($error); ?>");</script>
        <?php endif; ?>
        <form name="loginForm" action="login.php" method="POST" onsubmit="return checkLogin()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="signup.php" class="btn">Sign Up</a>
            <a href="home.php" class="btn">Guest View</a>
        </form>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #ecf0f1; text-align: center;">
            <a href="../GUIDE/" target="_blank" class="btn-guide">
                📖 Open Syllabus &amp; Viva Study Guide &rarr;
            </a>
            <p style="margin-top: 8px; margin-bottom: 0; font-size: 0.85em; color: #7f8c8d;">
                Complete guide for HTML, CSS, JS, PHP, SQL &amp; Viva preparation
            </p>
        </div>
    </div>

    <script type="text/javascript">
        function checkLogin() {
            var username = document.loginForm.username.value;
            var password = document.loginForm.password.value;

            if (username == "") {
                alert("Please enter your username.");
                return false;
            }
            if (password == "") {
                alert("Please enter your password.");
                return false;
            }
            return true;
        }
    </script>
<?php require_once '../includes/footer.php'; ?>
