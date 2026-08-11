<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'registrar') {
    header("Location: home.php");
    exit();
}

$message = '';

// Handle POST actions
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

// Fetch all scholarships
$scholarships = $conn->query("SELECT * FROM scholarships ORDER BY id DESC")->fetchAll();

// Fetch all student applications
$stmt = $conn->query("SELECT ss.*, u.username, u.email, st.full_name, st.name_with_initials, st.dob, st.gender, s.name AS scholarship_name FROM student_scholarships ss INNER JOIN users u ON ss.student_id = u.id LEFT JOIN students st ON u.id = st.user_id INNER JOIN scholarships s ON ss.scholarship_id = s.id ORDER BY ss.applied_at DESC");
$applications = $stmt->fetchAll();

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
                <caption>All submitted scholarship applications</caption>
                <thead>
                    <tr>
                        <th>Applicant Info</th>
                        <th>Application Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($app['username']); ?></strong> (<?php echo htmlspecialchars($app['gender']); ?>)<br>
                            <small><?php echo htmlspecialchars($app['full_name']); ?></small><br>
                            <small>DOB: <?php echo htmlspecialchars($app['dob']); ?> | NIC: <?php echo htmlspecialchars($app['nic']); ?></small><br>
                            <small>Email: <?php echo htmlspecialchars($app['email']); ?> | Phone: <?php echo htmlspecialchars($app['contact_numbers']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($app['scholarship_name']); ?></strong><br>
                            <small>GPA: <?php echo htmlspecialchars($app['gpa']); ?> | Income: Rs.<?php echo htmlspecialchars($app['parents_income']); ?></small><br>
                            <small>Purpose: <?php echo htmlspecialchars($app['purpose']); ?></small><br>
                            <small>Address: <?php echo htmlspecialchars($app['permanent_address']); ?></small>
                        </td>
                        <td><?php echo ucfirst($app['status']); ?></td>
                        <td>
                            <?php if ($app['status'] === 'pending'): ?>
                                <form action="applications.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="student_id" value="<?php echo $app['student_id']; ?>">
                                    <input type="hidden" name="scholarship_id" value="<?php echo $app['scholarship_id']; ?>">
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Approve this application?')">Approve</button>
                                </form>
                                <form action="applications.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="student_id" value="<?php echo $app['student_id']; ?>">
                                    <input type="hidden" name="scholarship_id" value="<?php echo $app['scholarship_id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this application?')">Reject</button>
                                </form>
                            <?php else: ?>
                                <em><?php echo ucfirst($app['status']); ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($applications)): ?>
                        <tr><td colspan="4">No applications submitted yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        function validateDeadline() {
            var deadlineStr = document.addScholarshipForm.deadline.value;
            if (deadlineStr != "") {
                var parts = deadlineStr.split("-");
                // Parse date parts to avoid UTC/local time zone issues
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
