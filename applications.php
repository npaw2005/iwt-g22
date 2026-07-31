<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'registrar') {
    header("Location: home.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $app_id = isset($_POST['app_id']) ? $_POST['app_id'] : null;

    if ($app_id && in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE scholarships SET isApproved = ? WHERE id = ?");
        if ($stmt->execute([$status, $app_id])) {
            $message = "Application has been successfully " . $status . ".";
        }
    }
}

// Fetch all applications along with user info
$stmt = $conn->query("
    SELECT s.*, u.username, u.email 
    FROM scholarships s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.id DESC
");
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - Scholarship Management System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Scholarship System</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="functionalities.php">Functionalities</a></li>
            <li><a href="applications.php">Manage Applications</a></li>
            <li><a href="help.php">Help</a></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="dashboard-header" style="padding: 1.5rem;">
            <h1 style="margin-bottom: 0;">Scholarship Applications Review</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom: 1rem;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Metrics</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?php echo $app['id']; ?></td>
                        <td><?php echo htmlspecialchars($app['username']); ?><br><small><?php echo htmlspecialchars($app['email']); ?></small><br><small>NIC: <?php echo htmlspecialchars(isset($app['nic']) ? $app['nic'] : 'N/A'); ?></small><br><small>Tel: <?php echo htmlspecialchars(isset($app['contact_numbers']) ? $app['contact_numbers'] : 'N/A'); ?></small></td>
                        <td><?php echo htmlspecialchars($app['title']); ?></td>
                        <td><?php echo htmlspecialchars(isset($app['category']) ? $app['category'] : 'N/A'); ?></td>
                        <td>
                            Income: $<?php echo htmlspecialchars(isset($app['parents_income']) ? $app['parents_income'] : '0'); ?><br>
                            Occ: <?php echo htmlspecialchars(isset($app['parents_occupation']) ? $app['parents_occupation'] : 'N/A'); ?><br>
                            GPA: <?php echo htmlspecialchars(isset($app['gpa']) ? $app['gpa'] : 'N/A'); ?>
                        </td>
                        <td style="max-width: 250px; overflow-wrap: break-word;" title="Purpose: <?php echo htmlspecialchars(isset($app['purpose']) ? $app['purpose'] : 'N/A'); ?> | Desc: <?php echo htmlspecialchars($app['description']); ?> | Address: <?php echo htmlspecialchars(isset($app['permanent_address']) ? $app['permanent_address'] : 'N/A'); ?> | Testimonial: <?php echo empty($app['testimonial_checked']) ? 'No' : 'Yes'; ?>">
                            <strong>Purpose:</strong> <?php echo htmlspecialchars(isset($app['purpose']) ? $app['purpose'] : 'N/A'); ?><br>
                            <strong>Desc:</strong> <?php echo htmlspecialchars($app['description']); ?><br>
                            <strong>Testimonial:</strong> <?php echo empty($app['testimonial_checked']) ? 'No' : 'Yes'; ?>
                        </td>
                        <td>
                            <?php echo ucfirst($app['isApproved']); ?>
                        </td>
                        <td style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <?php if ($app['isApproved'] === 'pending'): ?>
                                <form action="applications.php" method="POST" style="margin:0; display:inline-block;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="btn" style="background:#6B2226; color:white; padding:0.3rem 0.6rem; font-size:0.8rem; white-space:nowrap; border:none; cursor:pointer;">Approve</button>
                                </form>
                                <form action="applications.php" method="POST" style="margin:0; display:inline-block;">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="btn btn-danger" style="padding:0.3rem 0.6rem; font-size:0.8rem; white-space:nowrap; border:none; cursor:pointer;">Reject</button>
                                </form>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($applications)): ?>
                        <tr><td colspan="7">No applications have been submitted yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
