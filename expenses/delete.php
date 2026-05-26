<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {

    redirect('index.php');

}

$expense_id = intval($_GET['id']);

/*
    CHECK IF EXPENSE EXISTS
*/

$query = "SELECT * FROM expenses 
          WHERE expense_id = ? 
          LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $expense_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

/*
    DELETE EXPENSE
*/

$deleteQuery = "DELETE FROM expenses 
                WHERE expense_id = ?";

$deleteStmt = mysqli_prepare($conn, $deleteQuery);

mysqli_stmt_bind_param($deleteStmt, "i", $expense_id);

if (mysqli_stmt_execute($deleteStmt)) {

    redirect('index.php');

} else {

    echo "Failed to delete expense.";

}
?>