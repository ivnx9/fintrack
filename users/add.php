<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {

    redirect('../dashboard.php');

}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $full_name = cleanInput($_POST['full_name']);
    $username = cleanInput($_POST['username']);
    $password = cleanInput($_POST['password']);
    $role = cleanInput($_POST['role']);
    $status = cleanInput($_POST['status']);

    if (
        empty($full_name) ||
        empty($username) ||
        empty($password) ||
        empty($role) ||
        empty($status)
    ) {

        $error = "Please fill in all fields.";

    } else {

        // Check username

        $checkQuery = "SELECT * FROM users WHERE username = ? LIMIT 1";

        $checkStmt = mysqli_prepare($conn, $checkQuery);

        mysqli_stmt_bind_param($checkStmt, "s", $username);

        mysqli_stmt_execute($checkStmt);

        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {

            $error = "Username already exists.";

        } else {

            // TEMPORARY plain password
            // later papalitan natin ng password_hash()

            $query = "INSERT INTO users (
                        full_name,
                        username,
                        password,
                        role,
                        status
                      )
                      VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $full_name,
                $username,
                $password,
                $role,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {

                $success = "User added successfully.";

            } else {

                $error = "Something went wrong.";

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add User - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8z+qLQSh77L1xY8f0zY4nY9g5f1tYg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="form-container">

            <div class="page-header">

                <h1 class="page-title">
                    Add User
                </h1>

                <a href="index.php" class="back-btn">
                    Back
                </a>

            </div>

            <?php if (!empty($error)) : ?>

                <div class="error-message">
                    <?php echo $error; ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($success)) : ?>

                <div class="success-message">
                    <?php echo $success; ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label>Full Name</label>

                    <input
                        type="text"
                        name="full_name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Username</label>

                    <input
                        type="text"
                        name="username"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Role</label>

                    <select name="role" required>

                        <option value="">
                            Select Role
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                        <option value="staff">
                            Staff
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select name="status" required>

                        <option value="">
                            Select Status
                        </option>

                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>

                <button type="submit" class="save-btn">
                    Save User
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>