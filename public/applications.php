<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'registrar') {
    header("Location: home.php");
    exit();
}

$actionDone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $app_id = $_POST['app_id'];

    if ($app_id && in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE scholarships SET isApproved = ? WHERE id = ?");
        if ($stmt->execute([$status, $app_id])) {
            $actionDone = $status;
        }
    }
}

$stmt = $conn->query("SELECT s.*, u.username, u.email FROM scholarships s INNER JOIN users u ON s.user_id = u.id ORDER BY s.id DESC");
$applications = $stmt->fetchAll();

$pageTitle = 'Manage Applications - Scholarship Management System';
require_once '../includes/header.php';
?>
    <?php if ($actionDone): ?>
    <script type="text/javascript">
        alert("Application has been <?php echo $actionDone; ?>.");
    </script>
    <?php endif; ?>

    <div class="container">
        <div class="dashboard-header">
            <h1>Scholarship Applications</h1>
        </div>

        <div class="table-container">
            <table>
                <caption><strong>All Submitted Scholarship Applications</strong></caption>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th><abbr title="Grade Point Average">GPA</abbr></th>
                        <th>Income (<abbr title="Sri Lankan Rupees">Rs.</abbr>)</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo $app['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($app['username']); ?></strong><br>
                            <small><?php echo htmlspecialchars($app['email']); ?></small><br>
                            <small><abbr title="National Identity Card">NIC</abbr>: <?php echo htmlspecialchars($app['nic']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($app['title']); ?></td>
                        <td><?php echo htmlspecialchars($app['category']); ?></td>
                        <td><?php echo htmlspecialchars($app['gpa']); ?></td>
                        <td><?php echo htmlspecialchars($app['parents_income']); ?></td>
                        <td><?php echo htmlspecialchars($app['purpose']); ?></td>
                        <td><?php echo ucfirst($app['isApproved']); ?></td>
                        <td>
                            <?php if ($app['isApproved'] === 'pending'): ?>
                                <form action="applications.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Approve this application?')">Approve</button>
                                </form>
                                <form action="applications.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this application?')">Reject</button>
                                </form>
                            <?php else: ?>
                                <em><?php echo ucfirst($app['isApproved']); ?></em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($applications)): ?>
                        <tr><td colspan="9">No applications submitted yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php require_once '../includes/footer.php'; ?>
