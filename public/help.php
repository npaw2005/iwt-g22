<?php
session_start();
$pageTitle = 'Help - Scholarship Management System';
require_once '../includes/header.php';
?>
    <div class="container">
        <div class="dashboard-header">
            <h1>Help &amp; User Guide</h1>
        </div>

        <div class="content-page">
            <h2>For Students</h2>
            <ul>
                <li><strong>Register:</strong> Click <em>Sign Up</em> on the login page and fill in your username, email, and password.</li>
                <li><strong>Login:</strong> Enter your username and password on the login page. Use <em>Guest View</em> to browse without an account.</li>
                <li><strong>Browse Scholarships:</strong> After logging in, the Home page shows all available scholarships with their names, descriptions, and deadlines.</li>
                <li><strong>Apply:</strong> Click the <em>Apply</em> button next to a scholarship. Fill in the application form with your <abbr title="Grade Point Average">GPA</abbr>, <abbr title="National Identity Card">NIC</abbr>, parents' details, permanent address, and contact number. Check the testimonial box and submit.</li>
                <li><strong>Track Status:</strong> Your submitted applications are listed on your Home page. Status shows as <em>Pending</em>, <em>Approved</em>, or <em>Rejected</em>.</li>
                <li><strong>Logout:</strong> Click Logout in the navigation bar to end your session.</li>
            </ul>

            <hr>

            <h2>For Registrars</h2>
            <ul>
                <li><strong>Login:</strong> Use your registrar credentials to log in.</li>
                <li><strong>Manage Scholarships:</strong> Click <em>Registrar Panel</em> in the navigation bar. Use the form at the top to add new scholarships with a name, description, and deadline. Use the Remove button to delete a scholarship.</li>
                <li><strong>Review Applications:</strong> Scroll down on the Registrar Panel to see all student applications.</li>
                <li><strong>Approve or Reject:</strong> Click the Approve or Reject button next to any pending application.</li>
            </ul>

            <hr>

            <h2>For Administrators</h2>
            <ul>
                <li><strong>Login:</strong> Use admin credentials.</li>
                <li><strong>Admin Panel:</strong> Click <em>Admin Panel</em> in the navigation bar to access user management.</li>
                <li><strong>Add User:</strong> Use the Add New User form to create Student, Registrar, or Admin accounts.</li>
                <li><strong>Edit User:</strong> Click Edit next to any user to update their details.</li>
                <li><strong>Delete User:</strong> Click Delete to remove a non-admin user.</li>
                <li><strong>Application Summary:</strong> The Scholarship Applications table shows a read-only overview of all submissions.</li>
            </ul>

            <address>
                For technical support, contact the system administrator.
            </address>
        </div>
    </div>
<?php require_once '../includes/footer.php'; ?>
