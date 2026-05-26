<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

$purchases = [];

/*
    LOAD UNPAID / PARTIAL PURCHASES
*/

$query = "
    SELECT
        purchases.*,
        customers.customer_name
    FROM purchases
    INNER JOIN customers
        ON purchases.customer_id = customers.customer_id
    WHERE purchases.payment_status != 'paid'
    ORDER BY purchases.purchase_id DESC
";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $purchases[] = $row;

    }

}

/*
    SAVE PAYMENT
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $purchase_id = intval($_POST['purchase_id']);
    $amount_paid = floatval($_POST['amount_paid']);
    $payment_method = cleanInput($_POST['payment_method']);
    $payment_date = $_POST['payment_date'];
    $remarks = cleanInput($_POST['remarks']);

    if (
        empty($purchase_id) ||
        empty($amount_paid) ||
        empty($payment_method) ||
        empty($payment_date)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        /*
            GET PURCHASE
        */

        $purchaseQuery = "SELECT * FROM purchases 
                          WHERE purchase_id = ? 
                          LIMIT 1";

        $stmt = mysqli_prepare($conn, $purchaseQuery);

        mysqli_stmt_bind_param($stmt, "i", $purchase_id);

        mysqli_stmt_execute($stmt);

        $purchaseResult = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($purchaseResult) == 0) {

            $error = "Purchase transaction not found.";

        } else {

            $purchase = mysqli_fetch_assoc($purchaseResult);

            $newAmountPaid = $purchase['amount_paid'] + $amount_paid;

            $newBalance = $purchase['total_amount'] - $newAmountPaid;

            /*
                DETERMINE STATUS
            */

            if ($newBalance <= 0) {

                $paymentStatus = 'paid';
                $newBalance = 0;

            } elseif ($newAmountPaid > 0) {

                $paymentStatus = 'partial';

            } else {

                $paymentStatus = 'unpaid';

            }

            mysqli_begin_transaction($conn);

            try {

                /*
                    INSERT PAYMENT
                */

                $insertQuery = "INSERT INTO payments (
                                    purchase_id,
                                    user_id,
                                    amount_paid,
                                    payment_method,
                                    payment_date,
                                    remarks
                                )
                                VALUES (?, ?, ?, ?, ?, ?)";

                $insertStmt = mysqli_prepare($conn, $insertQuery);

                mysqli_stmt_bind_param(
                    $insertStmt,
                    "iidsss",
                    $purchase_id,
                    $_SESSION['user_id'],
                    $amount_paid,
                    $payment_method,
                    $payment_date,
                    $remarks
                );

                mysqli_stmt_execute($insertStmt);

                /*
                    UPDATE PURCHASE
                */

                $updateQuery = "UPDATE purchases
                                SET amount_paid = ?,
                                    balance_amount = ?,
                                    payment_status = ?
                                WHERE purchase_id = ?";

                $updateStmt = mysqli_prepare($conn, $updateQuery);

                mysqli_stmt_bind_param(
                    $updateStmt,
                    "ddsi",
                    $newAmountPaid,
                    $newBalance,
                    $paymentStatus,
                    $purchase_id
                );

                mysqli_stmt_execute($updateStmt);

                mysqli_commit($conn);

                $success = "Payment added successfully.";

            } catch (Exception $e) {

                mysqli_rollback($conn);

                $error = "Payment transaction failed.";

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Payment - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8z+qLQSh77L1xY8f0zY4nY9g5f1tYg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="form-container">

            <div class="page-header">

                <h1 class="page-title">
                    Add Payment
                </h1>

                <a href="index.php" class="back-btn">
                    Back
                </a>

            </div>

            <?php if (!empty($error)) : ?>

                <div class="error-message">
                    <?php echo $error; ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($success)) : ?>

                <div class="success-message">
                    <?php echo $success; ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label>Purchase Transaction</label>

                    <select name="purchase_id" required>

                        <option value="">
                            Select Transaction
                        </option>

                        <?php foreach ($purchases as $purchase) : ?>

                            <option value="<?php echo $purchase['purchase_id']; ?>">

                                Transaction #<?php echo $purchase['purchase_id']; ?>

                                -
                                <?php echo htmlspecialchars($purchase['customer_name']); ?>

                                -
                                Balance:
                                <?php echo formatCurrency($purchase['balance_amount']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Amount Paid</label>

                    <input
                        type="number"
                        step="0.01"
                        name="amount_paid"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Payment Method</label>

                    <select name="payment_method" required>

                        <option value="">
                            Select Payment Method
                        </option>

                        <option value="cash">
                            Cash
                        </option>

                        <option value="gcash">
                            GCash
                        </option>

                        <option value="bank_transfer">
                            Bank Transfer
                        </option>

                        <option value="others">
                            Others
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Payment Date</label>

                    <input
                        type="date"
                        name="payment_date"
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        rows="4"
                    ></textarea>

                </div>

                <button type="submit" class="save-btn">
                    Save Payment
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>