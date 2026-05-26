<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {

    redirect('index.php');

}

$purchase_id = intval($_GET['id']);

$purchase = null;
$items = [];

/*
    LOAD PURCHASE DETAILS
*/

$query = "
    SELECT 
        purchases.*,
        customers.customer_name,
        customers.contact_number,
        customers.email,
        users.full_name
    FROM purchases
    INNER JOIN customers
        ON purchases.customer_id = customers.customer_id
    INNER JOIN users
        ON purchases.user_id = users.user_id
    WHERE purchases.purchase_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $purchase_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

$purchase = mysqli_fetch_assoc($result);

/*
    LOAD PURCHASE ITEMS
*/

$itemQuery = "
    SELECT
        purchase_items.*,
        products.product_name,
        products.type
    FROM purchase_items
    INNER JOIN products
        ON purchase_items.product_id = products.product_id
    WHERE purchase_items.purchase_id = ?
";

$itemStmt = mysqli_prepare($conn, $itemQuery);

mysqli_stmt_bind_param($itemStmt, "i", $purchase_id);

mysqli_stmt_execute($itemStmt);

$itemResult = mysqli_stmt_get_result($itemStmt);

while ($row = mysqli_fetch_assoc($itemResult)) {

    $items[] = $row;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Transaction - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                Transaction Details
            </h1>

            <a href="index.php" class="back-btn">
                Back
            </a>

        </div>

        <div class="details-container">

            <div class="details-card">

                <h2>Purchase Information</h2>

                <table class="details-table">

                    <tr>
                        <td><strong>Transaction ID</strong></td>
                        <td>#<?php echo $purchase['purchase_id']; ?></td>
                    </tr>

                    <tr>
                        <td><strong>Customer</strong></td>
                        <td><?php echo htmlspecialchars($purchase['customer_name']); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Contact Number</strong></td>
                        <td><?php echo htmlspecialchars($purchase['contact_number']); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo htmlspecialchars($purchase['email']); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Processed By</strong></td>
                        <td><?php echo htmlspecialchars($purchase['full_name']); ?></td>
                    </tr>

                    <tr>
                        <td><strong>Purchase Date</strong></td>
                        <td>
                            <?php echo date("F d, Y", strtotime($purchase['purchase_date'])); ?>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Payment Status</strong></td>
                        <td>
                            <?php echo ucfirst($purchase['payment_status']); ?>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Total Amount</strong></td>
                        <td>
                            <?php echo formatCurrency($purchase['total_amount']); ?>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Amount Paid</strong></td>
                        <td>
                            <?php echo formatCurrency($purchase['amount_paid']); ?>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Balance</strong></td>
                        <td>
                            <?php echo formatCurrency($purchase['balance_amount']); ?>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>Remarks</strong></td>
                        <td>
                            <?php echo nl2br(htmlspecialchars($purchase['remarks'])); ?>
                        </td>
                    </tr>

                </table>

            </div>

            <div class="details-card">

                <h2>Purchased Items</h2>

                <table>

                    <thead>

                        <tr>

                            <th>Product / Service</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($items) > 0) : ?>

                            <?php foreach ($items as $item) : ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                    </td>

                                    <td>
                                        <?php echo ucfirst($item['type']); ?>
                                    </td>

                                    <td>
                                        <?php echo $item['quantity']; ?>
                                    </td>

                                    <td>
                                        <?php echo formatCurrency($item['price']); ?>
                                    </td>

                                    <td>
                                        <?php echo formatCurrency($item['subtotal']); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else : ?>

                            <tr>

                                <td colspan="5" class="no-data">
                                    No items found.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>