<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$products = [];

$query = "SELECT * FROM products ORDER BY product_id DESC";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $products[] = $row;

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product / Service Management - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8z+qLQSh77L1xY8f0zY4nY9g5f1tYg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                Product / Service Management
            </h1>

            <a href="add.php" class="add-btn">
                Add Product / Service
            </a>

        </div>

        <div class="table-container">

            <div class="search-box">

                <input
                    type="text"
                    id="searchProduct"
                    placeholder="Search products or services..."
                    onkeyup="searchTable('searchProduct', 'productsTable')"
                >

            </div>

            <table id="productsTable">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Date Created</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($products) > 0) : ?>

                        <?php foreach ($products as $product) : ?>

                            <tr>

                                <td>
                                    <?php echo $product['product_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($product['category']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($product['type']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($product['price']); ?>
                                </td>

                                <td>
                                    <?php echo $product['stock_quantity']; ?>
                                </td>

                                <td>

                                    <?php if ($product['status'] == 'active') : ?>

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
                                    <?php echo date("M d, Y", strtotime($product['created_at'])); ?>
                                </td>

                                <td class="action-buttons">

                                    <a
                                        href="edit.php?id=<?php echo $product['product_id']; ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="delete.php?id=<?php echo $product['product_id']; ?>"
                                        class="delete-btn"
                                        onclick="return confirmDelete()"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="9" class="no-data">
                                No products or services found.
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