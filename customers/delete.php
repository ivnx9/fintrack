<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$customer_id = intval($_GET['id']);

/*
    Check if customer exists
*/

$query = "SELECT * FROM customers WHERE customer_id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $customer_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

/*
    Prevent deleting customer if already used in purchases
*/

$checkQuery = "SELECT * FROM purchases WHERE customer_id = ? LIMIT 1";

$checkStmt = mysqli_prepare($conn, $checkQuery);

mysqli_stmt_bind_param($checkStmt, "i", $customer_id);

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {

    echo "This customer cannot be deleted because it is already used in purchase records.";
    echo "<br><br>";
    echo "<a href='index.php'>Go Back</a>";
    exit();

}

/*
    Delete customer
*/

$deleteQuery = "DELETE FROM customers WHERE customer_id = ?";

$deleteStmt = mysqli_prepare($conn, $deleteQuery);

mysqli_stmt_bind_param($deleteStmt, "i", $customer_id);

if (mysqli_stmt_execute($deleteStmt)) {

    redirect('index.php');

} else {

    echo "Failed to delete customer.";

}
?>