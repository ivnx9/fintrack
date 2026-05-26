<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$purchases = [];

$query = "
    SELECT 
        purchases.*,
        customers.customer_name,
        users.full_name
    FROM purchases
    INNER JOIN customers
        ON purchases.customer_id = customers.customer_id
    INNER JOIN users
        ON purchases.user_id = users.user_id
    ORDER BY purchases.purchase_id DESC
";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $purchases[] = $row;

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Purchase Transactions - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                Purchase Transactions
            </h1>

            <a href="create.php" class="add-btn">
                New Transaction
            </a>

        </div>

        <div class="table-container">

            <div class="search-box">

                <input
                    type="text"
                    id="searchPurchase"
                    placeholder="Search transactions..."
                    onkeyup="searchTable('searchPurchase', 'purchaseTable')"
                >

            </div>

            <table id="purchaseTable">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Customer</th>
                        <th>Processed By</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Purchase Date</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($purchases) > 0) : ?>

                        <?php foreach ($purchases as $purchase) : ?>

                            <tr>

                                <td>
                                    <?php echo $purchase['purchase_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($purchase['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($purchase['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($purchase['total_amount']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($purchase['amount_paid']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($purchase['balance_amount']); ?>
                                </td>

                                <td>

                                    <?php if ($purchase['payment_status'] == 'paid') : ?>

                                        <span class="status-active">
                                            Paid
                                        </span>

                                    <?php elseif ($purchase['payment_status'] == 'partial') : ?>

                                        <span class="status-warning">
                                            Partial
                                        </span>

                                    <?php else : ?>

                                        <span class="status-inactive">
                                            Unpaid
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($purchase['purchase_date'])); ?>
                                </td>

                                <td class="action-buttons">

                                    <a
                                        href="view.php?id=<?php echo $purchase['purchase_id']; ?>"
                                        class="view-btn"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="receipt.php?id=<?php echo $purchase['purchase_id']; ?>"
                                        class="print-btn"
                                    >
                                        Receipt
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="9" class="no-data">
                                No purchase transactions found.
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