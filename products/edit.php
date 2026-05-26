<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";
$product = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php');
}

$product_id = intval($_GET['id']);

$query = "SELECT * FROM products WHERE product_id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $product_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

$product = mysqli_fetch_assoc($result);

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

        $updateQuery = "UPDATE products 
                        SET product_name = ?, 
                            category = ?, 
                            description = ?, 
                            type = ?, 
                            price = ?, 
                            stock_quantity = ?, 
                            status = ?
                        WHERE product_id = ?";

        $updateStmt = mysqli_prepare($conn, $updateQuery);

        mysqli_stmt_bind_param(
            $updateStmt,
            "ssssdisi",
            $product_name,
            $category,
            $description,
            $type,
            $price,
            $stock_quantity,
            $status,
            $product_id
        );

        if (mysqli_stmt_execute($updateStmt)) {

            $success = "Product / Service updated successfully.";

            $query = "SELECT * FROM products WHERE product_id = ? LIMIT 1";

            $stmt = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param($stmt, "i", $product_id);

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            $product = mysqli_fetch_assoc($result);

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

    <title>Edit Product / Service - FINTRACK</title>

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
                    Edit Product / Service
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
                        value="<?php echo htmlspecialchars($product['product_name']); ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category"
                        value="<?php echo htmlspecialchars($product['category']); ?>"
                    >

                </div>

                <div class="form-group">

                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="4"
                    ><?php echo htmlspecialchars($product['description']); ?></textarea>

                </div>

                <div class="form-group">

                    <label>Type</label>

                    <select name="type" required>

                        <option value="product"
                            <?php echo ($product['type'] == 'product') ? 'selected' : ''; ?>>
                            Product
                        </option>

                        <option value="service"
                            <?php echo ($product['type'] == 'service') ? 'selected' : ''; ?>>
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
                        value="<?php echo $product['price']; ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Stock Quantity</label>

                    <input
                        type="number"
                        name="stock_quantity"
                        value="<?php echo $product['stock_quantity']; ?>"
                    >

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select name="status" required>

                        <option value="active"
                            <?php echo ($product['status'] == 'active') ? 'selected' : ''; ?>>
                            Active
                        </option>

                        <option value="inactive"
                            <?php echo ($product['status'] == 'inactive') ? 'selected' : ''; ?>>
                            Inactive
                        </option>

                    </select>

                </div>

                <button type="submit" class="save-btn">
                    Update Product / Service
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>