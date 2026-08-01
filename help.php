<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$role = $isLoggedIn ? $_SESSION['role'] : '';
$isAdmin = $role === 'admin';
$isRegistrar = $role === 'registrar';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - Scholarship Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Scholarship System</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="functionalities.php">Functionalities</a></li>
            <?php if ($isRegistrar): ?>
                <li><a href="applications.php">Manage Applications</a></li>
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
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>Help &amp; User Guide</h1>
        </div>

        <div class="content-page">
            <h2>For Students</h2>
            <ul>
                <li><strong>Register:</strong> Click <em>Sign Up</em> on the login page and fill in your username, email, and password.</li>
                <li><strong>Login:</strong> Enter your username and password on the login page. Use <em>Guest View</em> to browse without an account.</li>
                <li><strong>Apply for a Scholarship:</strong> After logging in, go to the Home page and fill in the application form. Select a category, enter your GPA, NIC, parents' details, permanent address, and contact number. Check the testimonial box and submit.</li>
                <li><strong>Track Status:</strong> Your submitted applications are listed on your Home page. Status shows as Pending, Approved, or Rejected.</li>
                <li><strong>Logout:</strong> Click Logout in the navigation bar to end your session.</li>
            </ul>

            <hr>

            <h2>For Registrars</h2>
            <ul>
                <li><strong>Login:</strong> Use your registrar credentials to log in.</li>
                <li><strong>Review Applications:</strong> Click <em>Manage Applications</em> in the navigation bar to view all submitted applications.</li>
                <li><strong>Approve or Reject:</strong> Click the Approve or Reject button next to any pending application. The status updates immediately.</li>
            </ul>

            <hr>

            <h2>For Administrators</h2>
            <ul>
                <li><strong>Login:</strong> Use admin credentials.</li>
                <li><strong>Admin Panel:</strong> Click <em>Admin Panel</em> in the navigation bar to access user management.</li>
                <li><strong>Add User:</strong> Use the Add New User form to create Student, Registrar, or Admin accounts.</li>
                <li><strong>Edit User:</strong> Click Edit next to any user to update their details.</li>
                <li><strong>Delete User:</strong> Click Delete to remove a non-admin user.</li>
                <li><strong>Application Summary:</strong> The Scholarship Applications table shows a read-only overview of all submissions.</li>
            </ul>

            <hr>

            <h2>Default Accounts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Username</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Admin</td>
                        <td><code>admin</code></td>
                        <td><code>admin123</code></td>
                    </tr>
                    <tr>
                        <td>Registrar</td>
                        <td><code>registrar</code></td>
                        <td><code>registrar123</code></td>
                    </tr>
                    <tr>
                        <td>Student</td>
                        <td><code>ucsc</code></td>
                        <td><code>ucsc</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
