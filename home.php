<?php
session_start();
require_once 'config/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : 'Guest';
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';
$isRegistrar = $isLoggedIn && $_SESSION['role'] === 'registrar';
$isStudent = $isLoggedIn && $_SESSION['role'] === 'student';

$message = '';

if ($isStudent && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $title = trim($_POST['title']);
    $category = $_POST['category'];
    $parents_income = $_POST['parents_income'];
    $parents_occupation = trim($_POST['parents_occupation']);
    $purpose = trim($_POST['purpose']);
    $gpa = $_POST['gpa'];
    $permanent_address = trim($_POST['permanent_address']);
    $nic = trim($_POST['nic']);
    $contact_numbers = trim($_POST['contact_numbers']);
    $description = trim($_POST['description']);
    $testimonial_checked = isset($_POST['testimonial_checked']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO scholarships (user_id, title, category, parents_income, parents_occupation, purpose, gpa, permanent_address, nic, contact_numbers, description, testimonial_checked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $title, $category, $parents_income, $parents_occupation, $purpose, $gpa, $permanent_address, $nic, $contact_numbers, $description, $testimonial_checked])) {
        $message = "Application submitted successfully.";
    } else {
        $message = "Failed to submit application.";
    }
}

$applications = [];
if ($isStudent) {
    $stmt = $conn->prepare("SELECT * FROM scholarships WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Scholarship Management System</title>
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
            <?php if ($isLoggedIn): ?>
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Role: <?php echo ucfirst($_SESSION['role']); ?></p>
            <?php else: ?>
                <h1>Welcome to the Scholarship Management System</h1>
                <p>Please login or register to apply for scholarships.</p>
            <?php endif; ?>
        </div>

        <?php if ($isLoggedIn): ?>
            <?php if ($message): ?>
                <div class="alert"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($isStudent): ?>
                <div class="content-page">
                    <h2>Apply for Scholarship</h2>
                    <form action="home.php" method="POST">
                        <input type="hidden" name="apply" value="1">
                        <div class="form-group">
                            <label>Scholarship Title</label>
                            <input type="text" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label><br>
                            <input type="radio" name="category" value="Merit Based" required> Merit Based &nbsp;
                            <input type="radio" name="category" value="Need Based"> Need Based &nbsp;
                            <input type="radio" name="category" value="Sports"> Sports
                        </div>
                        <div class="form-group">
                            <label>Parents' Annual Income (Rs.)</label>
                            <input type="number" step="0.01" name="parents_income" required>
                        </div>
                        <div class="form-group">
                            <label>Parents' Occupation</label>
                            <input type="text" name="parents_occupation" required>
                        </div>
                        <div class="form-group">
                            <label>Purpose of Request</label>
                            <input type="text" name="purpose" required>
                        </div>
                        <div class="form-group">
                            <label>Current GPA (0.00 - 4.00)</label>
                            <input type="number" step="0.01" min="0" max="4.0" name="gpa" required>
                        </div>
                        <div class="form-group">
                            <label>Permanent Address</label>
                            <textarea name="permanent_address" required rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>NIC Number</label>
                            <input type="text" name="nic" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_numbers" required>
                        </div>
                        <div class="form-group">
                            <label>Additional Description</label>
                            <textarea name="description" required rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="testimonial_checked" required>
                                I confirm that I have a testimonial from a Grama Niladhari or authorized personnel.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </form>
                </div>

                <div class="table-container">
                    <h2>Your Applications</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['title']); ?></td>
                                <td><?php echo htmlspecialchars($app['category']); ?></td>
                                <td><?php echo ucfirst($app['isApproved']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($applications) === 0): ?>
                            <tr><td colspan="3">No applications yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($isAdmin || $isRegistrar): ?>
                <div class="content-page">
                    <p>Use the navigation links above to access your panel.</p>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="content-page">
                <h2>Available Scholarships</h2>
                <p>We offer the following scholarship programmes for eligible students:</p>
                <ul class="scholarship-list">
                    <li><strong>Merit Based - Academic Excellence Award:</strong> For students with a GPA of 3.5 or above.</li>
                    <li><strong>Need Based - Financial Assistance Grant:</strong> For students from low-income families. Requires a Grama Niladhari testimonial.</li>
                    <li><strong>Sports - Sports Achievement Scholarship:</strong> For students who have represented the university or national teams.</li>
                </ul>
                <a href="login.php" class="btn btn-primary">Login to Apply</a>
                <a href="signup.php" class="btn">Register Now</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
