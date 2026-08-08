<?php
session_start();
require_once '../config/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : 'Guest';
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';
$isRegistrar = $isLoggedIn && $_SESSION['role'] === 'registrar';
$isStudent = $isLoggedIn && $_SESSION['role'] === 'student';

$message = '';
$selectedScholarship = null;

// If student clicked Apply on a specific scholarship
if ($isStudent && isset($_GET['sid'])) {
    $stmt = $conn->prepare("SELECT * FROM scholarships WHERE id = ?");
    $stmt->execute([$_GET['sid']]);
    $selectedScholarship = $stmt->fetch();
}

// Handle form submission
if ($isStudent && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $scholarshipId = $_POST['scholarship_id'];
    $parentsIncome = $_POST['parents_income'];
    $parentsOccupation = trim($_POST['parents_occupation']);
    $purpose = trim($_POST['purpose']);
    $gpa = $_POST['gpa'];
    $permanentAddress = trim($_POST['permanent_address']);
    $nic = trim($_POST['nic']);
    $contactNumbers = trim($_POST['contact_numbers']);
    $description = trim($_POST['description']);
    $testimonialChecked = isset($_POST['testimonial_checked']) ? 1 : 0;

    // Check if already applied
    $check = $conn->prepare("SELECT COUNT(*) FROM student_scholarships WHERE student_id = ? AND scholarship_id = ?");
    $check->execute([$_SESSION['user_id'], $scholarshipId]);
    $alreadyApplied = $check->fetchColumn();

    if ($alreadyApplied > 0) {
        $message = 'You have already applied for this scholarship.';
    } else {
        $stmt = $conn->prepare("INSERT INTO student_scholarships (student_id, scholarship_id, parents_income, parents_occupation, purpose, gpa, permanent_address, nic, contact_numbers, description, testimonial_checked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $scholarshipId, $parentsIncome, $parentsOccupation, $purpose, $gpa, $permanentAddress, $nic, $contactNumbers, $description, $testimonialChecked])) {
            $message = 'Application submitted successfully.';
        } else {
            $message = 'Failed to submit application.';
        }
    }
}

// Fetch all scholarships for listing
$allScholarships = $conn->query("SELECT * FROM scholarships ORDER BY id ASC")->fetchAll();

// Fetch student's applications
$applications = [];
if ($isStudent) {
    $stmt = $conn->prepare("SELECT ss.*, s.name AS scholarship_name FROM student_scholarships ss INNER JOIN scholarships s ON ss.scholarship_id = s.id WHERE ss.student_id = ? ORDER BY ss.applied_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll();
}

$pageTitle = 'Home - Scholarship Management System';
require_once '../includes/header.php';
?>
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
                <?php if ($selectedScholarship): ?>
                    <!-- APPLICATION FORM for a specific scholarship -->
                    <div class="content-page">
                        <h2>Apply for: <?php echo htmlspecialchars($selectedScholarship['name']); ?></h2>
                        <p><?php echo htmlspecialchars($selectedScholarship['description']); ?></p>
                        <?php if ($selectedScholarship['deadline']): ?>
                            <p><strong>Deadline:</strong> <?php echo htmlspecialchars($selectedScholarship['deadline']); ?></p>
                        <?php endif; ?>
                        <form name="appForm" action="home.php" method="POST" onsubmit="return checkApplication()">
                            <input type="hidden" name="apply" value="1">
                            <input type="hidden" name="scholarship_id" value="<?php echo $selectedScholarship['id']; ?>">
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
                                <input type="number" step="0.01" min="0" max="4.0" id="appGpa" name="gpa">
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
                                <textarea name="description" id="appDesc" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="appTestimonial" name="testimonial_checked">
                                    I confirm that I have a testimonial from a Grama Niladhari or authorized personnel.
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                            <a href="home.php" class="btn">Back to Scholarships</a>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- LIST OF AVAILABLE SCHOLARSHIPS -->
                    <div class="content-page">
                        <h2>Available Scholarships</h2>
                        <?php if (empty($allScholarships)): ?>
                            <p>No scholarships are currently available.</p>
                        <?php else: ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Scholarship</th>
                                        <th>Description</th>
                                        <th>Deadline</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allScholarships as $sch): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($sch['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($sch['description']); ?></td>
                                        <td><?php echo htmlspecialchars($sch['deadline']); ?></td>
                                        <td><a href="home.php?sid=<?php echo $sch['id']; ?>" class="btn btn-primary">Apply</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- YOUR APPLICATIONS TABLE -->
                <div class="table-container">
                    <h2>Your Applications</h2>
                    <table>
                        <caption>Your submitted scholarship applications</caption>
                        <thead>
                            <tr>
                                <th>Scholarship</th>
                                <th>GPA</th>
                                <th>Status</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['scholarship_name']); ?></td>
                                <td><?php echo htmlspecialchars($app['gpa']); ?></td>
                                <td><em><?php echo ucfirst($app['status']); ?></em></td>
                                <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($applications) === 0): ?>
                            <tr><td colspan="4">No applications yet.</td></tr>
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
            <!-- GUEST VIEW -->
            <div class="content-page">
                <h2>Available Scholarships</h2>
                <?php if (empty($allScholarships)): ?>
                    <p>No scholarships are currently available. Please check back later.</p>
                <?php else: ?>
                    <p>We offer the following scholarship programmes for eligible students:</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Scholarship</th>
                                <th>Description</th>
                                <th>Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allScholarships as $sch): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($sch['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sch['description']); ?></td>
                                <td><?php echo htmlspecialchars($sch['deadline']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <br>
                <a href="login.php" class="btn btn-primary">Login to Apply</a>
                <a href="signup.php" class="btn">Register Now</a>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        function checkApplication() {
            var gpa = parseFloat(document.appForm.gpa.value);
            if (isNaN(gpa) || gpa < 0 || gpa > 4.0) {
                alert("GPA must be between 0.00 and 4.00.");
                return false;
            }

            if (document.appForm.nic.value == "") {
                alert("Please enter your NIC number.");
                return false;
            }

            if (document.appForm.testimonial_checked.checked == false) {
                alert("You must confirm the testimonial.");
                return false;
            }

            return true;
        }
    </script>
<?php require_once '../includes/footer.php'; ?>
