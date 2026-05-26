<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../dashboard.php');
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$user_id = intval($_GET['id']);

/*
    Prevent deleting your own account
*/

if ($user_id == $_SESSION['user_id']) {

    redirect('index.php');

}

/*
    Check if user exists
*/

$query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

/*
    Delete user
*/

$deleteQuery = "DELETE FROM users WHERE user_id = ?";

$deleteStmt = mysqli_prepare($conn, $deleteQuery);

mysqli_stmt_bind_param($deleteStmt, "i", $user_id);

if (mysqli_stmt_execute($deleteStmt)) {

    redirect('index.php');

} else {

    echo "Failed to delete user.";

}
?>