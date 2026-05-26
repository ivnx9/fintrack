<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$receivables = [];

$totalReceivables = 0;

$query = "
    SELECT
        purchases.*,
        customers.customer_name,
        customers.contact_number,
        users.full_name
    FROM purchases
    INNER JOIN customers
        ON purchases.customer_id = customers.customer_id
    INNER JOIN users
        ON purchases.user_id = users.user_id
    WHERE purchases.payment_status != 'paid'
    ORDER BY purchases.purchase_id DESC
";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $receivables[] = $row;

        $totalReceivables += $row['balance_amount'];

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Receivables Report - FINTRACK</title>

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
                Receivables Report
            </h1>

            <button class="print-btn" onclick="window.print()">
                Print Report
            </button>

        </div>

        <div class="report-summary">

            <div class="report-card">

                <h3>Total Receivables</h3>

                <p>
                    <?php echo formatCurrency($totalReceivables); ?>
                </p>

            </div>

        </div>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Contact Number</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Purchase Date</th>
                        <th>Processed By</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($receivables) > 0) : ?>

                        <?php foreach ($receivables as $row) : ?>

                            <tr>

                                <td>
                                    #<?php echo $row['purchase_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['contact_number']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($row['total_amount']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($row['amount_paid']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($row['balance_amount']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($row['payment_status']); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($row['purchase_date'])); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['full_name']); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="9" class="no-data">
                                No receivable records found.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>