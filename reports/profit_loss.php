<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$date_from = "";
$date_to = "";

$totalSales = 0;
$totalExpenses = 0;
$netProfit = 0;

/*
    SALES QUERY
*/

$salesQuery = "
    SELECT SUM(total_amount) AS total_sales
    FROM purchases
";

/*
    EXPENSE QUERY
*/

$expenseQuery = "
    SELECT SUM(amount) AS total_expenses
    FROM expenses
";

/*
    FILTER
*/

if (isset($_GET['filter'])) {

    $date_from = $_GET['date_from'];
    $date_to = $_GET['date_to'];

    if (!empty($date_from) && !empty($date_to)) {

        $salesQuery .= "
            WHERE purchase_date 
            BETWEEN '$date_from' 
            AND '$date_to'
        ";

        $expenseQuery .= "
            WHERE expense_date 
            BETWEEN '$date_from' 
            AND '$date_to'
        ";

    }

}

/*
    GET SALES
*/

$salesResult = mysqli_query($conn, $salesQuery);

if ($salesResult) {

    $salesData = mysqli_fetch_assoc($salesResult);

    $totalSales = $salesData['total_sales'] ?? 0;

}

/*
    GET EXPENSES
*/

$expenseResult = mysqli_query($conn, $expenseQuery);

if ($expenseResult) {

    $expenseData = mysqli_fetch_assoc($expenseResult);

    $totalExpenses = $expenseData['total_expenses'] ?? 0;

}

/*
    COMPUTE NET PROFIT
*/

$netProfit = $totalSales - $totalExpenses;

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profit and Loss Report - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8z+qLQSh77L1xY8f0zY4nY9g5f1tYg==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>

        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            font-size: 30px;
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
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
        }

        .print-btn {
            padding: 10px 20px;
            border: none;
            background: #111;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .profit {
            color: green;
        }

        .loss {
            color: red;
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
                Profit and Loss Report
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

                <h3>Total Expenses</h3>

                <p>
                    <?php echo formatCurrency($totalExpenses); ?>
                </p>

            </div>

            <div class="report-card">

                <h3>Net Profit / Loss</h3>

                <p class="<?php echo ($netProfit >= 0) ? 'profit' : 'loss'; ?>">

                    <?php echo formatCurrency($netProfit); ?>

                </p>

            </div>

        </div>

        <div class="table-container">

            <table>

                <tbody>

                    <tr>

                        <td>
                            <strong>Total Sales Revenue</strong>
                        </td>

                        <td>
                            <?php echo formatCurrency($totalSales); ?>
                        </td>

                    </tr>

                    <tr>

                        <td>
                            <strong>Total Expenses</strong>
                        </td>

                        <td>
                            <?php echo formatCurrency($totalExpenses); ?>
                        </td>

                    </tr>

                    <tr>

                        <td>
                            <strong>Net Profit / Loss</strong>
                        </td>

                        <td class="<?php echo ($netProfit >= 0) ? 'profit' : 'loss'; ?>">

                            <strong>
                                <?php echo formatCurrency($netProfit); ?>
                            </strong>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>