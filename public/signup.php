<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $password        = trim($_POST['password']);
    $fullName        = trim($_POST['full_name']);
    $nameInitials    = trim($_POST['name_with_initials']);
    $dob             = $_POST['dob'];
    $gender          = $_POST['gender'];

    if (empty($username) || empty($password) || empty($email) || empty($fullName) || empty($nameInitials) || empty($dob) || empty($gender)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username is already taken.';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, full_name, name_with_initials, dob, gender) VALUES (?, ?, ?, 'student', ?, ?, ?, ?)");
            if ($stmt->execute([$username, $password, $email, $fullName, $nameInitials, $dob, $gender])) {
                $success = 'Registration successful! You can now log in.';
            } else {
                $error = 'Database error occurred.';
            }
        }
    }
}

$pageTitle = 'Sign Up - Scholarship Management System';
require_once '../includes/header.php';
?>
    <div class="auth-container">
        <h2>Student Registration</h2>
        <?php if ($error): ?>
        <script type="text/javascript">alert("<?php echo addslashes($error); ?>");</script>
        <?php endif; ?>
        <?php if ($success): ?>
        <script type="text/javascript">alert("<?php echo addslashes($success); ?>");</script>
        <?php endif; ?>
        <form name="signupForm" id="signupForm" action="signup.php" method="POST" onsubmit="return checkForm()">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name">
            </div>
            <div class="form-group">
                <label for="name_with_initials">Name with Initials</label>
                <input type="text" id="name_with_initials" name="name_with_initials">
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">-- Select --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="login.php" class="btn">Back to Login</a>
        </form>
    </div>

    <script type="text/javascript">
        function checkForm() {
            var fullName   = document.signupForm.full_name.value;
            var initials   = document.signupForm.name_with_initials.value;
            var dob        = document.signupForm.dob.value;
            var gender     = document.signupForm.gender.value;
            var user       = document.signupForm.username.value;
            var email      = document.signupForm.email.value;
            var pass       = document.signupForm.password.value;
            var confirm    = document.signupForm.confirm_password.value;

            if (fullName == "") {
                alert("Full name is required.");
                return false;
            }
            if (initials == "") {
                alert("Name with initials is required.");
                return false;
            }
            if (dob == "") {
                alert("Date of birth is required.");
                return false;
            }
            if (gender == "") {
                alert("Please select a gender.");
                return false;
            }
            if (user == "") {
                alert("Username is required.");
                return false;
            }
            if (email == "") {
                alert("Valid email is required.");
                return false;
            }
            if (pass.length < 6) {
                alert("Password must be at least 6 characters.");
                return false;
            }
            if (pass != confirm || confirm == "") {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
<?php require_once '../includes/footer.php'; ?>
