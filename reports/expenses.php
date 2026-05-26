<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$reports = [];

$date_from = "";
$date_to = "";

$totalExpenses = 0;

$query = "
    SELECT
        expenses.*,
        users.full_name
    FROM expenses
    INNER JOIN users
        ON expenses.user_id = users.user_id
";

if (isset($_GET['filter'])) {

    $date_from = $_GET['date_from'];
    $date_to = $_GET['date_to'];

    if (!empty($date_from) && !empty($date_to)) {

        $query .= " WHERE expense_date 
                    BETWEEN '$date_from' 
                    AND '$date_to'";

    }

}

$query .= " ORDER BY expense_id DESC";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $reports[] = $row;
        $totalExpenses += $row['amount'];

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Reports - FINTRACK</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">
            <h1 class="page-title">Expense Reports</h1>

            <button class="print-btn" onclick="window.print()">
                Print Report
            </button>
        </div>

        <form method="GET" class="filter-form">

            <div class="filter-grid">

                <div class="form-group">
                    <label>Date From</label>
                    <input type="date" name="date_from" value="<?php echo $date_from; ?>">
                </div>

                <div class="form-group">
                    <label>Date To</label>
                    <input type="date" name="date_to" value="<?php echo $date_to; ?>">
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" name="filter" class="save-btn">
                        Filter Report
                    </button>
                </div>

            </div>

        </form>

        <div class="report-summary">

            <div class="report-card">
                <h3>Total Expenses</h3>
                <p><?php echo formatCurrency($totalExpenses); ?></p>
            </div>

        </div>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Expense ID</th>
                        <th>Expense Name</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Expense Date</th>
                        <th>Processed By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($reports) > 0) : ?>

                        <?php foreach ($reports as $report) : ?>

                            <tr>
                                <td>#<?php echo $report['expense_id']; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($report['expense_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($report['category']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($report['amount']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst(str_replace('_', ' ', $report['payment_method'])); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($report['expense_date'])); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($report['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($report['remarks']); ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="8" class="no-data">
                                No expense report data found.
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