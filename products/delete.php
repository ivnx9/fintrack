<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {

    redirect('index.php');

}

$product_id = intval($_GET['id']);

/*
    Check if product exists
*/

$query = "SELECT * FROM products WHERE product_id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $product_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

/*
    Prevent deleting product if already used in purchase items
*/

$checkQuery = "SELECT * FROM purchase_items 
               WHERE product_id = ? 
               LIMIT 1";

$checkStmt = mysqli_prepare($conn, $checkQuery);

mysqli_stmt_bind_param($checkStmt, "i", $product_id);

mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {

    echo "This product/service cannot be deleted because it is already used in purchase transactions.";
    echo "<br><br>";
    echo "<a href='index.php'>Go Back</a>";
    exit();

}

/*
    Delete product
*/

$deleteQuery = "DELETE FROM products WHERE product_id = ?";

$deleteStmt = mysqli_prepare($conn, $deleteQuery);

mysqli_stmt_bind_param($deleteStmt, "i", $product_id);

if (mysqli_stmt_execute($deleteStmt)) {

    redirect('index.php');

} else {

    echo "Failed to delete product/service.";

}
?>