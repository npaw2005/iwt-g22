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
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $parents_income = isset($_POST['parents_income']) ? $_POST['parents_income'] : 0;
    $parents_occupation = trim($_POST['parents_occupation']);
    $purpose = trim($_POST['purpose']);
    $gpa = isset($_POST['gpa']) ? $_POST['gpa'] : 0;
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
            <?php if ($isLoggedIn): ?>
                <h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Your personalized dashboard for managing scholarships.</p>
            <?php else: ?>
                <h1>Welcome to the Scholarship System</h1>
                <p>Please explore our features or log in to apply and manage your scholarships.</p>
            <?php endif; ?>
        </div>
        
        <div class="content-page">
            <?php if ($isLoggedIn): ?>
                <?php if ($message): ?>
                    <div class="alert" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom: 1rem; padding: 1rem; border-radius: 5px;"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if ($isStudent): ?>
                    <div class="flex-container" style="display: flex; gap: 2rem; flex-wrap: wrap;">
                        <div class="flex-item" style="flex: 1; min-width: 300px;">
                            <h3>Apply for Scholarship</h3>
                            <form action="home.php" method="POST">
                                <input type="hidden" name="apply" value="1">
                                <div style="margin-bottom: 1rem;">
                                    <label>Scholarship Name / Title</label><br>
                                    <input type="text" name="title" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Category</label><br>
                                    <input type="radio" name="category" value="Merit Based" required> Merit Based
                                    <input type="radio" name="category" value="Need Based"> Need Based
                                    <input type="radio" name="category" value="Sports"> Sports
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Parents' Annual Income</label><br>
                                    <input type="number" step="0.01" name="parents_income" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Parents' Occupation</label><br>
                                    <input type="text" name="parents_occupation" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Purpose of Request</label><br>
                                    <input type="text" name="purpose" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Current GPA</label><br>
                                    <input type="number" step="0.01" min="0" max="4.0" name="gpa" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Permanent Address</label><br>
                                    <textarea name="permanent_address" required style="width: 100%; padding: 0.5rem;" rows="2"></textarea>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>National Identity Card (NIC) Number</label><br>
                                    <input type="text" name="nic" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Contact Numbers</label><br>
                                    <input type="text" name="contact_numbers" required style="width: 100%; padding: 0.5rem;">
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label>Additional Description / Justification</label><br>
                                    <textarea name="description" required style="width: 100%; padding: 0.5rem;" rows="4"></textarea>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: flex; gap: 0.5rem; align-items: start;">
                                        <input type="checkbox" name="testimonial_checked" required style="margin-top: 0.2rem;">
                                        <span>I confirm that I have attached a testimonial from a Grama Niladhari or authorized personnel.</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            </form>
                        </div>
                        
                        <div class="flex-item" style="flex: 1; min-width: 300px;">
                            <h3>Your Applications</h3>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f4f4f4; text-align: left;">
                                        <th style="padding: 0.5rem; border-bottom: 1px solid #ddd;">Title</th>
                                        <th style="padding: 0.5rem; border-bottom: 1px solid #ddd;">Category</th>
                                        <th style="padding: 0.5rem; border-bottom: 1px solid #ddd;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars($app['title']); ?></td>
                                        <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;"><?php echo htmlspecialchars(isset($app['category']) ? $app['category'] : 'N/A'); ?></td>
                                        <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;">
                                            <?php 
                                            if ($app['isApproved'] === 'pending') echo '<span style="color: #b8860b;">Pending</span>';
                                            elseif ($app['isApproved'] === 'approved') echo '<span style="color: green;">Approved</span>';
                                            else echo '<span style="color: #8B0000;">Rejected</span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (count($applications) === 0): ?>
                                    <tr><td colspan="3" style="padding: 0.5rem; text-align: center;">No applications yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br><br>
                <?php endif; ?>

                <p>From here, you can navigate through the system to explore available scholarships and update your profile.</p>
                <br>
                <a href="functionalities.php" class="btn btn-primary" style="width: auto;">Explore Functionalities</a>
                <a href="help.php" class="btn btn-primary" style="width: auto; background-color: #8C5D63;">Get Help</a>
            <?php else: ?>
                <h2>Available Scholarships</h2>
                <p>We offer several scholarship programmes for eligible students. Login or register to apply.</p>
                <ul class="scholarship-list">
                    <li><strong>Merit Based — Academic Excellence Award:</strong> For students with a GPA of 3.5 or above. Covers tuition fees for one academic year.</li>
                    <li><strong>Need Based — Financial Assistance Grant:</strong> For students from low-income families. Requires proof of household income and a Grama Niladhari testimonial.</li>
                    <li><strong>Sports — Sports Achievement Scholarship:</strong> For students who have represented the university or national teams in sports.</li>
                </ul>
                <a href="login.php" class="btn btn-primary" style="width: auto;">Login to Apply</a>
                <a href="signup.php" class="btn btn-primary" style="width: auto; background-color: #8C5D63; margin-left: 0.5rem;">Register Now</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
