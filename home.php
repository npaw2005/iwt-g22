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
    <div class="navbar-clear"></div>
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
            <script type="text/javascript">alert("<?php echo addslashes($message); ?>");</script>
            <?php endif; ?>

            <?php if ($isStudent): ?>
                <div class="content-page">
                    <h2>Apply for Scholarship</h2>
                    <form action="home.php" method="POST" onsubmit="return checkApplication()">
                        <input type="hidden" name="apply" value="1">
                        <div class="form-group">
                            <label>Scholarship Title</label>
                            <input type="text" id="appTitle" name="title">
                        </div>
                        <div class="form-group">
                            <label>Category</label><br>
                            <input type="radio" name="category" value="Merit Based" id="catMerit"> <label for="catMerit" style="display:inline; font-weight:normal;">Merit Based</label> &nbsp;
                            <input type="radio" name="category" value="Need Based" id="catNeed"> <label for="catNeed" style="display:inline; font-weight:normal;">Need Based</label> &nbsp;
                            <input type="radio" name="category" value="Sports" id="catSports"> <label for="catSports" style="display:inline; font-weight:normal;">Sports</label>
                            <br><small id="catError" style="color:red; display:none;">Please select a category.</small>
                        </div>
                        <div class="form-group">
                            <label>Parents' Annual Income (Rs.)</label>
                            <input type="number" step="0.01" id="appIncome" name="parents_income">
                        </div>
                        <div class="form-group">
                            <label>Parents' Occupation</label>
                            <input type="text" id="appOccupation" name="parents_occupation">
                        </div>
                        <div class="form-group">
                            <label>Purpose of Request</label>
                            <input type="text" id="appPurpose" name="purpose">
                        </div>
                        <div class="form-group">
                            <label>Current <abbr title="Grade Point Average">GPA</abbr> (0.00 - 4.00)</label>
                            <input type="number" step="0.01" min="0" max="4.0" id="appGpa" name="gpa" onchange="validateGpa()">
                            <small id="gpaError" style="color:red; display:none;">GPA must be between 0.00 and 4.00.</small>
                        </div>
                        <div class="form-group">
                            <label>Permanent Address</label>
                            <textarea name="permanent_address" id="appAddress" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label><abbr title="National Identity Card">NIC</abbr> Number</label>
                            <input type="text" id="appNic" name="nic">
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" id="appContact" name="contact_numbers">
                        </div>
                        <div class="form-group">
                            <label>Additional Description</label>
                            <textarea name="description" id="appDesc" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="appTestimonial" name="testimonial_checked">
                                I confirm that I have a testimonial from a Grama Niladhari or authorized personnel.
                            </label>
                            <br><small id="testimonialError" style="color:red; display:none;">You must confirm the testimonial.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </form>
                </div>

                <div class="table-container">
                    <h2>Your Applications</h2>
                    <table>
                        <caption>Your submitted scholarship applications</caption>
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
                                <td><em><?php echo ucfirst($app['isApproved']); ?></em></td>
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
                    <li><strong>Merit Based - Academic Excellence Award:</strong> For students with a <abbr title="Grade Point Average">GPA</abbr> of 3.5 or above.</li>
                    <li><strong>Need Based - Financial Assistance Grant:</strong> For students from low-income families. Requires a Grama Niladhari testimonial.</li>
                    <li><strong>Sports - Sports Achievement Scholarship:</strong> For students who have represented the university or national teams.</li>
                </ul>
                <a href="login.php" class="btn btn-primary">Login to Apply</a>
                <a href="signup.php" class="btn">Register Now</a>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        function validateGpa() {
            var gpa = parseFloat(document.getElementById('appGpa').value);
            var gpaError = document.getElementById('gpaError');
            if (isNaN(gpa) || gpa < 0 || gpa > 4.0) {
                gpaError.style.display = 'block';
            } else {
                gpaError.style.display = 'none';
            }
        }

        function checkApplication() {
            var valid = true;

            if (document.getElementById('appTitle').value == '') {
                alert('Please enter a scholarship title.');
                return false;
            }

            var category = document.querySelector('input[name="category"]:checked');
            if (category == null) {
                document.getElementById('catError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('catError').style.display = 'none';
            }

            var gpa = parseFloat(document.getElementById('appGpa').value);
            if (isNaN(gpa) || gpa < 0 || gpa > 4.0) {
                document.getElementById('gpaError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('gpaError').style.display = 'none';
            }

            if (document.getElementById('appNic').value == '') {
                alert('Please enter your NIC number.');
                return false;
            }

            if (!document.getElementById('appTestimonial').checked) {
                document.getElementById('testimonialError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('testimonialError').style.display = 'none';
            }

            return valid;
        }
    </script>
</body>
</html>
