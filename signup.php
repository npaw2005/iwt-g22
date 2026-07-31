<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    // Server-side validation just in case client-side is bypassed
    if (empty($username) || empty($password) || empty($email)) {
        $error = 'All fields are required.';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username is already taken.';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'student')");
            if ($stmt->execute([$username, $password, $email])) {
                $success = 'Registration successful! You can now log in.';
            } else {
                $error = 'Database error occurred.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Scholarship Management System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
    <div class="auth-container">
        <h2>Student Registration</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom: 1rem;"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form id="signupForm" action="signup.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
                <small id="userError" style="color: red; display: none; margin-top: 5px;">Username is required.</small>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                <small id="emailError" style="color: red; display: none; margin-top: 5px;">Valid email is required.</small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <small id="passError" style="color: red; display: none; margin-top: 5px;">Password must be at least 6 characters.</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password">
                <small id="confirmError" style="color: red; display: none; margin-top: 5px;">Passwords do not match.</small>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom: 1rem;">Create Account</button>
            <div style="text-align: center;">
                <a href="login.php" style="color: var(--secondary-color); text-decoration: none; font-weight: 600;">Already have an account? Login</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            var valid = true;
            
            var user = document.getElementById('username').value.trim();
            var email = document.getElementById('email').value.trim();
            var pass = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;

            // Validate Username
            if (user === '') {
                document.getElementById('userError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('userError').style.display = 'none';
            }

            // Validate Email Regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
            }

            // Validate Password Length
            if (pass.length < 6) {
                document.getElementById('passError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('passError').style.display = 'none';
            }

            // Validate Confirm Password Match
            if (pass !== confirm || confirm === '') {
                document.getElementById('confirmError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('confirmError').style.display = 'none';
            }

            // Prevent going to server if validation fails
            if (!valid) {
                e.preventDefault(); 
            }
        });
    </script>
</body>
</html>
