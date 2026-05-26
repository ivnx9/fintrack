<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$payments = [];

$query = "
    SELECT
        payments.*,
        purchases.purchase_id,
        customers.customer_name,
        users.full_name
    FROM payments
    INNER JOIN purchases
        ON payments.purchase_id = purchases.purchase_id
    INNER JOIN customers
        ON purchases.customer_id = customers.customer_id
    INNER JOIN users
        ON payments.user_id = users.user_id
    ORDER BY payments.payment_id DESC
";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $payments[] = $row;

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment / Receivables - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                Payment / Receivables
            </h1>

            <a href="add.php" class="add-btn">
                Add Payment
            </a>

        </div>

        <div class="table-container">

            <div class="search-box">

                <input
                    type="text"
                    id="searchPayment"
                    placeholder="Search payments..."
                    onkeyup="searchTable('searchPayment', 'paymentsTable')"
                >

            </div>

            <table id="paymentsTable">

                <thead>

                    <tr>

                        <th>Payment ID</th>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th>Processed By</th>
                        <th>Remarks</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($payments) > 0) : ?>

                        <?php foreach ($payments as $payment) : ?>

                            <tr>

                                <td>
                                    <?php echo $payment['payment_id']; ?>
                                </td>

                                <td>
                                    #<?php echo $payment['purchase_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($payment['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($payment['amount_paid']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($payment['payment_date'])); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($payment['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($payment['remarks']); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="8" class="no-data">
                                No payment records found.
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