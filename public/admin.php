<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit();
}

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$editUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'];

    if ($postAction === 'add') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $password, $email, $role])) {
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
        $action = 'list';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'delete') {
        $id = $_GET['id'];
        if ($id) {
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
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $editUser = $stmt->fetch();
    }
}

$users = [];
$applications = [];
$stats = ['total_users' => 0, 'students' => 0, 'staff' => 0];

if ($action === 'list') {
    $stmt = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();

    $stats['total_users'] = count($users);
    foreach ($users as $u) {
        if ($u['role'] === 'student') $stats['students']++;
        if (in_array($u['role'], ['admin', 'registrar'])) $stats['staff']++;
    }

    $stmt2 = $conn->query("SELECT ss.*, u.username, sch.name AS scholarship_name FROM student_scholarships ss INNER JOIN users u ON ss.student_id = u.id INNER JOIN scholarships sch ON ss.scholarship_id = sch.id ORDER BY ss.applied_at DESC");
    $applications = $stmt2->fetchAll();

    $stats['scholarships'] = $conn->query("SELECT COUNT(*) FROM scholarships")->fetchColumn();
    $stats['applications'] = count($applications);
}

$pageTitle = 'Admin Panel - Scholarship Management System';
require_once '../includes/header.php';
?>
    <div class="container">
        <div class="dashboard-header">
            <h1>Admin Panel</h1>
        </div>

        <?php if ($message): ?>
        <script type="text/javascript">alert("<?php echo addslashes($message); ?>");</script>
        <?php endif; ?>

        <?php if ($action === 'edit' && $editUser): ?>
            <div class="content-page">
                <h2>Edit User</h2>
                <form action="admin.php" method="POST" name="editUserForm" onsubmit="return checkEditUser()">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" id="editUsername" name="username" value="<?php echo htmlspecialchars($editUser['username']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="email" value="<?php echo htmlspecialchars($editUser['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Password <em>(leave blank to keep current)</em></label>
                        <input type="password" id="editPassword" name="password">
                    </div>
                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select id="editRole" name="role">
                            <option value="student" <?php if ($editUser['role'] === 'student') echo 'selected'; ?>>Student</option>
                            <option value="admin" <?php if ($editUser['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                            <option value="registrar" <?php if ($editUser['role'] === 'registrar') echo 'selected'; ?>>Registrar</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="admin.php" class="btn btn-danger">Cancel</a>
                </form>
            </div>

        <?php else: ?>
            <div class="table-container">
                <h2>System Report</h2>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Total Users</th>
                            <th>Students</th>
                            <th>Admin / Registrar</th>
                            <th>Scholarships</th>
                            <th>Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $stats['total_users']; ?></td>
                            <td><?php echo $stats['students']; ?></td>
                            <td><?php echo $stats['staff']; ?></td>
                            <td><?php echo $stats['scholarships']; ?></td>
                            <td><?php echo $stats['applications']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2>User Management</h2>
                <table>
                    <caption>Registered system users</caption>
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
                            <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['email'] ? $u['email'] : 'N/A'); ?></td>
                            <td><?php echo ucfirst($u['role']); ?></td>
                            <td>
                                <a href="admin.php?action=edit&id=<?php echo $u['id']; ?>" class="btn">Edit</a>
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="admin.php?action=delete&id=<?php echo $u['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="content-page">
                <h2>Add New User</h2>
                <form action="admin.php" method="POST" name="addUserForm" onsubmit="return checkAddUser()">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="newUsername">Username</label>
                        <input type="text" id="newUsername" name="username">
                    </div>
                    <div class="form-group">
                        <label for="newEmail">Email</label>
                        <input type="email" id="newEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">Password</label>
                        <input type="password" id="newPassword" name="password">
                    </div>
                    <div class="form-group">
                        <label for="newRole">Role</label>
                        <select id="newRole" name="role">
                            <option value="student">Student</option>
                            <option value="registrar">Registrar</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>

            <div class="table-container">
                <h2>Scholarship Applications <em>(Overview)</em></h2>
                <table>
                    <caption>Read-only summary of all applications</caption>
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Scholarship</th>
                            <th>GPA</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['username']); ?></td>
                            <td><?php echo htmlspecialchars($app['scholarship_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['gpa']); ?></td>
                            <td><?php echo ucfirst($app['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($applications)): ?>
                        <tr><td colspan="4">No applications yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        function checkAddUser() {
            var username = document.addUserForm.username.value;
            var email = document.addUserForm.email.value;
            var password = document.addUserForm.password.value;

            if (username == "") {
                alert("Please enter a username.");
                return false;
            }
            if (email == "") {
                alert("Please enter an email.");
                return false;
            }
            if (password == "") {
                alert("Please enter a password.");
                return false;
            }
            return true;
        }

        function checkEditUser() {
            var username = document.editUserForm.username.value;
            if (username == "") {
                alert("Username cannot be empty.");
                return false;
            }
            return true;
        }
    </script>
<?php require_once '../includes/footer.php'; ?>
