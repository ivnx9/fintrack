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
        customers.address,
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
        products.product_name
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

    <title>Receipt - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        body {
            background: #fff;
            font-family: Arial, sans-serif;
        }

        .receipt-container {
            width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 40px;
            border: 1px solid #ccc;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .receipt-header h1 {
            margin-bottom: 10px;
        }

        .receipt-info {
            margin-bottom: 25px;
        }

        .receipt-info table {
            width: 100%;
        }

        .receipt-info td {
            padding: 5px 0;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .receipt-table th,
        .receipt-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        .receipt-summary {
            width: 300px;
            margin-left: auto;
        }

        .receipt-summary table {
            width: 100%;
        }

        .receipt-summary td {
            padding: 6px 0;
        }

        .print-btn {
            margin-top: 30px;
            padding: 10px 20px;
            border: none;
            background: #111;
            color: #fff;
            cursor: pointer;
        }

        @media print {

            .print-btn {
                display: none;
            }

        }

    </style>

</head>
<body>

<div class="receipt-container">

    <div class="receipt-header">

        <h1>FINTRACK</h1>

        <p>
            Customer Purchase Record and Financial Reporting System
        </p>

        <h2>Official Receipt</h2>

    </div>

    <div class="receipt-info">

        <table>

            <tr>

                <td>
                    <strong>Receipt No:</strong>
                    #<?php echo $purchase['purchase_id']; ?>
                </td>

                <td align="right">

                    <strong>Date:</strong>

                    <?php echo date("F d, Y", strtotime($purchase['purchase_date'])); ?>

                </td>

            </tr>

            <tr>

                <td>

                    <strong>Customer:</strong>

                    <?php echo htmlspecialchars($purchase['customer_name']); ?>

                </td>

                <td align="right">

                    <strong>Processed By:</strong>

                    <?php echo htmlspecialchars($purchase['full_name']); ?>

                </td>

            </tr>

            <tr>

                <td colspan="2">

                    <strong>Address:</strong>

                    <?php echo htmlspecialchars($purchase['address']); ?>

                </td>

            </tr>

            <tr>

                <td colspan="2">

                    <strong>Contact Number:</strong>

                    <?php echo htmlspecialchars($purchase['contact_number']); ?>

                </td>

            </tr>

        </table>

    </div>

    <table class="receipt-table">

        <thead>

            <tr>

                <th>Product / Service</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($items as $item) : ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?>
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

        </tbody>

    </table>

    <div class="receipt-summary">

        <table>

            <tr>

                <td>
                    <strong>Total Amount</strong>
                </td>

                <td align="right">

                    <?php echo formatCurrency($purchase['total_amount']); ?>

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Amount Paid</strong>
                </td>

                <td align="right">

                    <?php echo formatCurrency($purchase['amount_paid']); ?>

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Balance</strong>
                </td>

                <td align="right">

                    <?php echo formatCurrency($purchase['balance_amount']); ?>

                </td>

            </tr>

            <tr>

                <td>
                    <strong>Status</strong>
                </td>

                <td align="right">

                    <?php echo ucfirst($purchase['payment_status']); ?>

                </td>

            </tr>

        </table>

    </div>

    <button class="print-btn" onclick="window.print()">
        Print Receipt
    </button>

</div>

</body>
</html>