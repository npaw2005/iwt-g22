<?php
session_start();
$pageTitle = 'Functionalities - Scholarship Management System';
require_once '../includes/header.php';
?>
    <div class="container">
        <div class="dashboard-header">
            <h1>System Functionalities</h1>
        </div>

        <div class="content-page">
            <h2>About This System</h2>
            <p>The Scholarship Management System allows students to apply for scholarships online. The Registrar manages available scholarships and reviews applications. The Admin manages user accounts.</p>

            <h2>User Roles</h2>
            <table>
                <caption>Summary of user roles and access</caption>
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Access</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Student</strong></td>
                        <td>Register, login, browse available scholarships, apply for a scholarship, view application status.</td>
                    </tr>
                    <tr>
                        <td><strong>Registrar</strong></td>
                        <td>Login, add or remove scholarships, view all student applications, approve or reject applications.</td>
                    </tr>
                    <tr>
                        <td><strong>Admin</strong></td>
                        <td>Login, manage all user accounts (add, edit, delete), view application summary.</td>
                    </tr>
                </tbody>
            </table>

            <h2>Application Process</h2>
            <ol>
                <li>Register for an account or log in as a student.</li>
                <li>Browse the available scholarships listed on the Home page.</li>
                <li>Click the <em>Apply</em> button next to the scholarship you want.</li>
                <li>Fill in the application form with your personal and academic details.</li>
                <li>Check the testimonial declaration checkbox and submit.</li>
                <li>Track the status of your application (<em>Pending</em> / <em>Approved</em> / <em>Rejected</em>) on your Home page.</li>
            </ol>
        </div>
    </div>
<?php require_once '../includes/footer.php'; ?>
