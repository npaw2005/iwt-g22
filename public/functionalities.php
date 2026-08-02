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
            <p>The <abbr title="Scholarship Management System">SMS</abbr> allows students to apply for scholarships online. Authorised staff can review and process those applications.</p>

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
                        <td>Register, login, submit scholarship applications, view application status.</td>
                    </tr>
                    <tr>
                        <td><strong>Registrar</strong></td>
                        <td>Login, view all submitted applications, approve or reject applications.</td>
                    </tr>
                    <tr>
                        <td><strong>Admin</strong></td>
                        <td>Login, manage all user accounts (add, edit, delete), view application summary.</td>
                    </tr>
                </tbody>
            </table>

            <h2>Scholarship Categories</h2>
            <dl>
                <dt><strong>Merit Based</strong></dt>
                <dd>For students with outstanding academic performance (<abbr title="Grade Point Average">GPA</abbr> 3.5 or above).</dd>
                <dt><strong>Need Based</strong></dt>
                <dd>For students from low-income households. Grama Niladhari testimonial required.</dd>
                <dt><strong>Sports</strong></dt>
                <dd>For students who have represented the university or national teams in sports.</dd>
            </dl>

            <h2>Application Process</h2>
            <ol>
                <li>Register for an account or log in as a student.</li>
                <li>Go to the Home page and fill in the scholarship application form.</li>
                <li>Select the appropriate scholarship category using the radio buttons.</li>
                <li>Fill in all required personal and academic details including <abbr title="Grade Point Average">GPA</abbr>, <abbr title="National Identity Card">NIC</abbr>, and contact information.</li>
                <li>Check the testimonial declaration checkbox and submit.</li>
                <li>Track the status of your application (<em>Pending</em> / <em>Approved</em> / <em>Rejected</em>) on your Home page.</li>
            </ol>
        </div>
    </div>
<?php require_once '../includes/footer.php'; ?>
