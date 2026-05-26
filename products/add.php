<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $product_name = cleanInput($_POST['product_name']);
    $category = cleanInput($_POST['category']);
    $description = cleanInput($_POST['description']);
    $type = cleanInput($_POST['type']);
    $price = cleanInput($_POST['price']);
    $stock_quantity = cleanInput($_POST['stock_quantity']);
    $status = cleanInput($_POST['status']);

    if (
        empty($product_name) ||
        empty($type) ||
        empty($price) ||
        empty($status)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $query = "INSERT INTO products (
                    product_name,
                    category,
                    description,
                    type,
                    price,
                    stock_quantity,
                    status
                  )
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssdiss",
            $product_name,
            $category,
            $description,
            $type,
            $price,
            $stock_quantity,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {

            $success = "Product / Service added successfully.";

        } else {

            $error = "Something went wrong.";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Product / Service - FINTRACK</title>

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
                    Add Product / Service
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

                    <label>Product / Service Name</label>

                    <input
                        type="text"
                        name="product_name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category"
                    >

                </div>

                <div class="form-group">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="4"
                    ></textarea>

                </div>

                <div class="form-group">

                    <label>Type</label>

                    <select name="type" required>

                        <option value="">
                            Select Type
                        </option>

                        <option value="product">
                            Product
                        </option>

                        <option value="service">
                            Service
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="price"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Stock Quantity</label>

                    <input
                        type="number"
                        name="stock_quantity"
                        value="0"
                    >

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
                    Save Product / Service
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>