<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$transactions = [];

$customer_id = "";

/*
    LOAD CUSTOMERS
*/

$customers = [];

$customerQuery = "SELECT * FROM customers ORDER BY customer_name ASC";

$customerResult = mysqli_query($conn, $customerQuery);

if ($customerResult) {

    while ($row = mysqli_fetch_assoc($customerResult)) {

        $customers[] = $row;

    }

}

/*
    TRANSACTION QUERY
*/

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
";

/*
    FILTER BY CUSTOMER
*/

if (isset($_GET['filter'])) {

    $customer_id = intval($_GET['customer_id']);

    if (!empty($customer_id)) {

        $query .= " WHERE purchases.customer_id = '$customer_id'";

    }

}

$query .= " ORDER BY purchases.purchase_id DESC";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $transactions[] = $row;

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Transactions Report - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                Customer Transactions Report
            </h1>

            <button class="print-btn" onclick="window.print()">
                Print Report
            </button>

        </div>

        <form method="GET" class="filter-form">

            <div class="filter-grid">

                <div class="form-group">

                    <label>Select Customer</label>

                    <select name="customer_id">

                        <option value="">
                            All Customers
                        </option>

                        <?php foreach ($customers as $customer) : ?>

                            <option
                                value="<?php echo $customer['customer_id']; ?>"
                                <?php echo ($customer_id == $customer['customer_id']) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($customer['customer_name']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>&nbsp;</label>

                    <button
                        type="submit"
                        name="filter"
                        class="save-btn"
                    >
                        Filter Report
                    </button>

                </div>

            </div>

        </form>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Purchase Date</th>
                        <th>Processed By</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($transactions) > 0) : ?>

                        <?php foreach ($transactions as $transaction) : ?>

                            <tr>

                                <td>
                                    #<?php echo $transaction['purchase_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($transaction['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($transaction['total_amount']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($transaction['amount_paid']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($transaction['balance_amount']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($transaction['payment_status']); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($transaction['purchase_date'])); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($transaction['full_name']); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="8" class="no-data">
                                No transaction records found.
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