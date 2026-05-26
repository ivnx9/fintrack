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
$user = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$user_id = intval($_GET['id']);

$query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    redirect('index.php');
}

$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $full_name = cleanInput($_POST['full_name']);
    $username = cleanInput($_POST['username']);
    $password = cleanInput($_POST['password']);
    $role = cleanInput($_POST['role']);
    $status = cleanInput($_POST['status']);

    if (
        empty($full_name) ||
        empty($username) ||
        empty($role) ||
        empty($status)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $checkQuery = "SELECT * FROM users 
                       WHERE username = ? 
                       AND user_id != ? 
                       LIMIT 1";

        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "si", $username, $user_id);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {

            $error = "Username already exists.";

        } else {

            if (!empty($password)) {

                $updateQuery = "UPDATE users 
                                SET full_name = ?, 
                                    username = ?, 
                                    password = ?, 
                                    role = ?, 
                                    status = ? 
                                WHERE user_id = ?";

                $updateStmt = mysqli_prepare($conn, $updateQuery);

                mysqli_stmt_bind_param(
                    $updateStmt,
                    "sssssi",
                    $full_name,
                    $username,
                    $password,
                    $role,
                    $status,
                    $user_id
                );

            } else {

                $updateQuery = "UPDATE users 
                                SET full_name = ?, 
                                    username = ?, 
                                    role = ?, 
                                    status = ? 
                                WHERE user_id = ?";

                $updateStmt = mysqli_prepare($conn, $updateQuery);

                mysqli_stmt_bind_param(
                    $updateStmt,
                    "ssssi",
                    $full_name,
                    $username,
                    $role,
                    $status,
                    $user_id
                );

            }

            if (mysqli_stmt_execute($updateStmt)) {

                $success = "User updated successfully.";

                $query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

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

    <title>Edit User - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="form-container">

            <div class="page-header">

                <h1 class="page-title">
                    Edit User
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
                        value="<?php echo htmlspecialchars($user['full_name']); ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Username</label>

                    <input
                        type="text"
                        name="username"
                        value="<?php echo htmlspecialchars($user['username']); ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>New Password</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Leave blank to keep current password"
                    >

                </div>

                <div class="form-group">

                    <label>Role</label>

                    <select name="role" required>

                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>
                            Admin
                        </option>

                        <option value="staff" <?php echo ($user['role'] == 'staff') ? 'selected' : ''; ?>>
                            Staff
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select name="status" required>

                        <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>
                            Active
                        </option>

                        <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>
                            Inactive
                        </option>

                    </select>

                </div>

                <button type="submit" class="save-btn">
                    Update User
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>