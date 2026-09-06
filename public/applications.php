<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'registrar') {
    header("Location: home.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add_scholarship') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $deadline = $_POST['deadline'];
        if ($name !== '') {
            $stmt = $conn->prepare("INSERT INTO scholarships (name, description, deadline) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $deadline]);
            $message = 'Scholarship added successfully.';
        }
    } elseif ($action === 'remove_scholarship') {
        $schId = $_POST['scholarship_id'];
        $stmt = $conn->prepare("DELETE FROM scholarships WHERE id = ?");
        $stmt->execute([$schId]);
        $message = 'Scholarship removed.';
    } elseif ($action === 'approve' || $action === 'reject') {
        $studentId = $_POST['student_id'];
        $scholarshipId = $_POST['scholarship_id'];
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE student_scholarships SET status = ? WHERE student_id = ? AND scholarship_id = ?");
        $stmt->execute([$status, $studentId, $scholarshipId]);
        $message = 'Application ' . $status . '.';
    }
}

$scholarships = $conn->query("SELECT * FROM scholarships ORDER BY id DESC")->fetchAll();

$stmt = $conn->query("SELECT ss.*, u.username, u.email, st.full_name, st.name_with_initials, st.dob, st.gender, st.parents_income, st.parents_occupation, st.gpa, st.permanent_address, st.nic, st.contact_numbers, s.name AS scholarship_name FROM student_scholarships ss INNER JOIN users u ON ss.student_id = u.id LEFT JOIN students st ON u.id = st.user_id INNER JOIN scholarships s ON ss.scholarship_id = s.id ORDER BY ss.applied_at DESC");
$applications = $stmt->fetchAll();

$viewApp = null;
if (isset($_GET['view_sid']) && isset($_GET['view_sch'])) {
    foreach ($applications as $app) {
        if ($app['student_id'] == $_GET['view_sid'] && $app['scholarship_id'] == $_GET['view_sch']) {
            $viewApp = $app;
            break;
        }
    }
}

$pageTitle = 'Manage Applications - Scholarship Management System';
require_once '../includes/header.php';
?>
    <?php if ($message): ?>
    <script type="text/javascript">alert("<?php echo addslashes($message); ?>");</script>
    <?php endif; ?>

    <div class="container">
        <div class="dashboard-header">
            <h1>Registrar Panel</h1>
        </div>

        <?php if ($viewApp): ?>
            <div class="content-page">
                <h2>Application Details</h2>
                <p><strong>Applicant:</strong> <?php echo htmlspecialchars($viewApp['username']); ?> (<?php echo htmlspecialchars($viewApp['full_name']); ?>)</p>
                <p><strong>Scholarship:</strong> <?php echo htmlspecialchars($viewApp['scholarship_name']); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($viewApp['status']); ?></p>
                <hr>
                <h3>Personal Information</h3>
                <ul>
                    <li><strong>DOB:</strong> <?php echo htmlspecialchars($viewApp['dob']); ?></li>
                    <li><strong>Gender:</strong> <?php echo htmlspecialchars($viewApp['gender']); ?></li>
                    <li><strong>NIC:</strong> <?php echo htmlspecialchars($viewApp['nic']); ?></li>
                    <li><strong>Email:</strong> <?php echo htmlspecialchars($viewApp['email']); ?></li>
                    <li><strong>Phone:</strong> <?php echo htmlspecialchars($viewApp['contact_numbers']); ?></li>
                    <li><strong>Address:</strong> <?php echo htmlspecialchars($viewApp['permanent_address']); ?></li>
                </ul>
                <hr>
                <h3>Academic & Financial Information</h3>
                <ul>
                    <li><strong>GPA:</strong> <?php echo htmlspecialchars($viewApp['gpa']); ?></li>
                    <li><strong>Parents' Income:</strong> Rs.<?php echo htmlspecialchars($viewApp['parents_income']); ?></li>
                    <li><strong>Parents' Occupation:</strong> <?php echo htmlspecialchars($viewApp['parents_occupation']); ?></li>
                    <li><strong>Purpose:</strong> <?php echo htmlspecialchars($viewApp['purpose']); ?></li>
                </ul>
                
                <div style="margin-top: 20px;">
                    <?php if ($viewApp['status'] === 'pending'): ?>
                        <form action="applications.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="student_id" value="<?php echo $viewApp['student_id']; ?>">
                            <input type="hidden" name="scholarship_id" value="<?php echo $viewApp['scholarship_id']; ?>">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Approve this application?')">Approve</button>
                        </form>
                        <form action="applications.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="student_id" value="<?php echo $viewApp['student_id']; ?>">
                            <input type="hidden" name="scholarship_id" value="<?php echo $viewApp['scholarship_id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this application?')">Reject</button>
                        </form>
                    <?php endif; ?>
                    <a href="applications.php" class="btn">Back to List</a>
                </div>
            </div>
        <?php else: ?>

            <div class="content-page">
                <h2>Add New Scholarship</h2>
                <form name="addScholarshipForm" action="applications.php" method="POST" onsubmit="return validateDeadline()">
                    <input type="hidden" name="action" value="add_scholarship">
                    <div class="form-group">
                        <label for="schName">Scholarship Name</label>
                        <input type="text" id="schName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="schDesc">Description</label>
                        <textarea id="schDesc" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="schDeadline">Application Deadline</label>
                        <input type="date" id="schDeadline" name="deadline">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Scholarship</button>
                </form>
            </div>

            <div class="table-container">
                <h2>Current Scholarships</h2>
                <table>
                    <caption>Scholarships available in the system</caption>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Deadline</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scholarships as $sch): ?>
                        <tr>
                            <td><?php echo $sch['id']; ?></td>
                            <td><?php echo htmlspecialchars($sch['name']); ?></td>
                            <td><?php echo htmlspecialchars($sch['description']); ?></td>
                            <td><?php echo htmlspecialchars($sch['deadline']); ?></td>
                            <td>
                                <form action="applications.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="remove_scholarship">
                                    <input type="hidden" name="scholarship_id" value="<?php echo $sch['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Remove this scholarship? All related applications will also be deleted.')">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($scholarships)): ?>
                        <tr><td colspan="5">No scholarships added yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2>Student Applications</h2>
                <table>
                    <caption>Click 'View' to see full details and approve/reject</caption>
                    <thead>
                        <tr>
                            <th>SID (Username)</th>
                            <th>Full Name</th>
                            <th>Scholarship</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($app['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['scholarship_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                            <td><strong><?php echo ucfirst($app['status']); ?></strong></td>
                            <td>
                                <a href="applications.php?view_sid=<?php echo $app['student_id']; ?>&view_sch=<?php echo $app['scholarship_id']; ?>" class="btn btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applications)): ?>
                            <tr><td colspan="6">No applications submitted yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        function validateDeadline() {
            var deadlineStr = document.addScholarshipForm.deadline.value;
            if (deadlineStr != "") {
                var parts = deadlineStr.split("-");
                var selectedDate = new Date(parts[0], parts[1] - 1, parts[2]);
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    alert("The deadline cannot be set in the past.");
                    return false;
                }
            }
            return true;
        }
    </script>
<?php require_once '../includes/footer.php'; ?>
