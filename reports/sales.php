<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$reports = [];

$date_from = "";
$date_to = "";

$totalSales = 0;
$totalPaid = 0;
$totalBalance = 0;

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
    FILTER BY DATE
*/

if (isset($_GET['filter'])) {

    $date_from = $_GET['date_from'];
    $date_to = $_GET['date_to'];

    if (!empty($date_from) && !empty($date_to)) {

        $query .= " WHERE purchase_date 
                    BETWEEN '$date_from' 
                    AND '$date_to'";

    }

}

$query .= " ORDER BY purchase_id DESC";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $reports[] = $row;

        $totalSales += $row['total_amount'];
        $totalPaid += $row['amount_paid'];
        $totalBalance += $row['balance_amount'];

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Financial Reports - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .report-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.08);
        }

        .report-card h3 {
            margin-bottom: 10px;
            color: #555;
        }

        .report-card p {
            font-size: 28px;
            font-weight: bold;
        }

        .filter-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 0 5px rgba(0,0,0,0.08);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .print-btn {
            padding: 10px 20px;
            background: #111;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

    </style>

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">

            <h1 class="page-title">
                Financial Reports
            </h1>

            <button class="print-btn" onclick="window.print()">
                Print Report
            </button>

        </div>

        <form method="GET" class="filter-form">

            <div class="filter-grid">

                <div class="form-group">

                    <label>Date From</label>

                    <input
                        type="date"
                        name="date_from"
                        value="<?php echo $date_from; ?>"
                    >

                </div>

                <div class="form-group">

                    <label>Date To</label>

                    <input
                        type="date"
                        name="date_to"
                        value="<?php echo $date_to; ?>"
                    >

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

        <div class="report-summary">

            <div class="report-card">

                <h3>Total Sales</h3>

                <p>
                    <?php echo formatCurrency($totalSales); ?>
                </p>

            </div>

            <div class="report-card">

                <h3>Total Paid</h3>

                <p>
                    <?php echo formatCurrency($totalPaid); ?>
                </p>

            </div>

            <div class="report-card">

                <h3>Total Balance</h3>

                <p>
                    <?php echo formatCurrency($totalBalance); ?>
                </p>

            </div>

        </div>

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Processed By</th>
                        <th>Total Amount</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Purchase Date</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($reports) > 0) : ?>

                        <?php foreach ($reports as $report) : ?>

                            <tr>

                                <td>
                                    #<?php echo $report['purchase_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($report['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($report['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($report['total_amount']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($report['amount_paid']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($report['balance_amount']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst($report['payment_status']); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($report['purchase_date'])); ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="8" class="no-data">
                                No report data found.
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