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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password) || empty($email)) {
        $error = 'All fields are required.';
    } else {
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Student Registration</h2>
        <?php if ($error): ?>
        <script type="text/javascript">alert("<?php echo addslashes($error); ?>");</script>
        <?php endif; ?>
        <?php if ($success): ?>
        <script type="text/javascript">alert("<?php echo addslashes($success); ?>");</script>
        <?php endif; ?>
        <form id="signupForm" action="signup.php" method="POST" onsubmit="return checkForm()">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
                <small id="userError" style="color:red; display:none;">Username is required.</small>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
                <small id="emailError" style="color:red; display:none;">Valid email is required.</small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" onchange="checkPasswordMatch()">
                <small id="passError" style="color:red; display:none;">Password must be at least 6 characters.</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" onchange="checkPasswordMatch()">
                <small id="confirmError" style="color:red; display:none;">Passwords do not match.</small>
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="login.php" class="btn">Back to Login</a>
        </form>
    </div>

    <script type="text/javascript">
        function checkPasswordMatch() {
            var pass = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;
            if (confirm != '' && pass != confirm) {
                document.getElementById('confirmError').style.display = 'block';
            } else {
                document.getElementById('confirmError').style.display = 'none';
            }
        }

        function checkForm() {
            var valid = true;
            var user = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var pass = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (user == '') {
                document.getElementById('userError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('userError').style.display = 'none';
            }

            if (!emailRegex.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
            }

            if (pass.length < 6) {
                document.getElementById('passError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('passError').style.display = 'none';
            }

            if (pass != confirm || confirm == '') {
                document.getElementById('confirmError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('confirmError').style.display = 'none';
            }

            return valid;
        }
    </script>
</body>
</html>
