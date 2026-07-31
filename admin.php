<?php
session_start();
require_once 'config/db.php';

// Server-side authorization block
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'registrar'])) {
    header("Location: home.php");
    exit();
}

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$editUser = null;

// Handle form submissions (Add / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = isset($_POST['action']) ? $_POST['action'] : '';
    if ($postAction === 'add') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        if($stmt->execute([$username, $password, $email, $role])) {
            $message = "User added successfully.";
        }
    } elseif ($postAction === 'edit') {
        $id = $_POST['id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        if (!empty($_POST['password'])) {
            $stmt = $conn->prepare("UPDATE users SET username=?, password=?, email=?, role=? WHERE id=?");
            $stmt->execute([$username, trim($_POST['password']), $email, $role, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
            $stmt->execute([$username, $email, $role, $id]);
        }
        $message = "User updated successfully.";
        $action = 'list'; // go back to list
    }
}

// Handle GET actions (Delete / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'delete') {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            // Prevent deleting self or admins
            $check = $conn->prepare("SELECT role FROM users WHERE id = ?");
            $check->execute([$id]);
            $u = $check->fetch();
            if ($u && $u['role'] !== 'admin') {
                $conn->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
                $message = "User deleted successfully.";
            } else {
                $message = "Cannot delete admin accounts.";
            }
        }
        $action = 'list';
    } elseif ($action === 'edit') {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $editUser = $stmt->fetch();
    } elseif (in_array($action, ['approve', 'reject'])) {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $status = $action === 'approve' ? 'approved' : 'rejected';
            $conn->prepare("UPDATE scholarships SET isApproved = ? WHERE id = ?")->execute([$status, $id]);
            $message = "Application " . $status . " successfully.";
        }
        $action = 'list';
    }
}

// Fetch metrics and users for 'list'
$users = [];
$stats = ['total_users' => 0, 'students' => 0, 'admins' => 0];

if ($action === 'list') {
    $stmt = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
    
    $stats['total_users'] = count($users);
    foreach ($users as $u) {
        if ($u['role'] === 'student') $stats['students']++;
        if (in_array($u['role'], ['admin', 'registrar'])) $stats['admins']++;
    }

    $stmtApps = $conn->query("SELECT s.*, u.username FROM scholarships s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC");
    $applications = $stmtApps->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Scholarship Management System</title>
    <link rel="stylesheet" href="css/style.css?v=2">
    <style>
        .form-group { margin-bottom: 1rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.5rem; }
        .btn-edit  { background: #6B2226; color: white; padding: 0.3rem 0.7rem; font-size: 0.85rem; }
        .btn-sm    { padding: 0.3rem 0.7rem; font-size: 0.85rem; }
        .status-pending  { color: #b8860b; }
        .status-approved { color: green; font-weight: bold; }
        .status-rejected { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Scholarship System</div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="functionalities.php">Functionalities</a></li>
            <li><a href="help.php">Help</a></li>
            <li><a href="admin.php">Admin Panel</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="dashboard-header" style="padding: 1.5rem;">
            <h1 style="margin-bottom: 0;">System Administration</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; margin-bottom: 1rem;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($action === 'edit' && $editUser): ?>
            <div class="content-page">
                <h2>Edit User Details (Select View)</h2>
                <form action="admin.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($editUser['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($editUser['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Password (leave blank to keep current)</label>
                        <input type="password" name="password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role">
                            <option value="student" <?php if($editUser['role']=='student') echo 'selected'; ?>>Student</option>
                            <option value="admin" <?php if($editUser['role']=='admin') echo 'selected'; ?>>Admin</option>
                            <option value="registrar" <?php if($editUser['role']=='registrar') echo 'selected'; ?>>Registrar</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="admin.php" class="btn btn-danger">Cancel</a>
                </form>
            </div>
        <?php else: ?>

            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Total Users</th>
                        <th>Registered Students</th>
                        <th>Admin / Registrar Accounts</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $stats['total_users']; ?></td>
                        <td><?php echo $stats['students']; ?></td>
                        <td><?php echo $stats['admins']; ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="table-container">
                <h2>User Management</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email'] ? $u['email'] : 'N/A'); ?></td>
                            <td><?php echo ucfirst($u['role']); ?></td>
                            <td>
                                <a href="admin.php?action=edit&id=<?php echo $u['id']; ?>" class="btn btn-edit">Edit</a>
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="admin.php?action=delete&id=<?php echo $u['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="content-page">
                <h2>Add New User</h2>
                <form action="admin.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role">
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                            <option value="registrar">Registrar</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
            
            <div class="table-container">
                <h2>Scholarship Applications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo $app['id']; ?></td>
                            <td><?php echo htmlspecialchars($app['username']); ?></td>
                            <td><?php echo htmlspecialchars($app['title']); ?></td>
                            <td><?php echo htmlspecialchars(isset($app['category']) ? $app['category'] : 'N/A'); ?></td>
                            <td>
                                <?php if ($app['isApproved'] === 'approved'): ?>
                                    <span class="status-approved">Approved</span>
                                <?php elseif ($app['isApproved'] === 'rejected'): ?>
                                    <span class="status-rejected">Rejected</span>
                                <?php else: ?>
                                    <span class="status-pending">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
