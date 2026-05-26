<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$users = [];

$query = "SELECT * FROM users ORDER BY user_id DESC";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $users[] = $row;

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Management - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                User Management
            </h1>

            <?php if (isAdmin()) : ?>

                <a href="add.php" class="add-btn">
                    Add User
                </a>

            <?php endif; ?>

        </div>

        <div class="table-container">

            <div class="search-box">

                <input
                    type="text"
                    id="searchUser"
                    placeholder="Search users..."
                    onkeyup="searchTable('searchUser', 'usersTable')"
                >

            </div>

            <table id="usersTable">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date Created</th>

                        <?php if (isAdmin()) : ?>

                            <th>Actions</th>

                        <?php endif; ?>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($users) > 0) : ?>

                        <?php foreach ($users as $user) : ?>

                            <tr>

                                <td>
                                    <?php echo $user['user_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($user['role']); ?>
                                </td>

                                <td>

                                    <?php if ($user['status'] == 'active') : ?>

                                        <span class="status-active">
                                            Active
                                        </span>

                                    <?php else : ?>

                                        <span class="status-inactive">
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($user['created_at'])); ?>
                                </td>

                                <?php if (isAdmin()) : ?>

                                    <td class="action-buttons">

                                        <a 
                                            href="edit.php?id=<?php echo $user['user_id']; ?>" 
                                            class="edit-btn"
                                        >
                                            Edit
                                        </a>

                                        <a 
                                            href="delete.php?id=<?php echo $user['user_id']; ?>"
                                            class="delete-btn"
                                            onclick="return confirmDelete()"
                                        >
                                            Delete
                                        </a>

                                    </td>

                                <?php endif; ?>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="7" class="no-data">
                                No users found.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

<script src="../assets/js/main.js"></script>

</body>
</html>