<?php
session_start();
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
    <title>Functionalities - Scholarship Management System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
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
            <h1>System Functionalities</h1>
        </div>

        <div class="content-page">
            <h2>What this system does</h2>
            <p>The Scholarship Management System allows students to apply for scholarships online and enables authorised staff to review and process those applications.</p>

            <h2>User Roles</h2>
            <table>
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Access</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Student</strong></td>
                        <td>Register, login, submit scholarship applications, view application status.</td>
                    </tr>
                    <tr>
                        <td><strong>Registrar</strong></td>
                        <td>Login, view all submitted applications, approve or reject applications.</td>
                    </tr>
                    <tr>
                        <td><strong>Admin</strong></td>
                        <td>Login, manage all user accounts (add, edit, delete), view application summary.</td>
                    </tr>
                </tbody>
            </table>

            <h2>Scholarship Categories</h2>
            <ul>
                <li><strong>Merit Based</strong> — For students with outstanding academic performance (GPA 3.5+).</li>
                <li><strong>Need Based</strong> — For students from low-income households. Testimonial required.</li>
                <li><strong>Sports</strong> — For students who have represented the university or national teams.</li>
            </ul>

            <h2>Application Process</h2>
            <ol>
                <li>Register for an account or log in as a student.</li>
                <li>Go to the Home page and fill in the scholarship application form.</li>
                <li>Select the appropriate scholarship category using the radio buttons.</li>
                <li>Fill in all required personal and academic details including GPA, NIC, and contact information.</li>
                <li>Check the testimonial declaration checkbox and submit.</li>
                <li>Track the status of your application (Pending / Approved / Rejected) on your Home dashboard.</li>
            </ol>
        </div>
    </div>
</body>
</html>
