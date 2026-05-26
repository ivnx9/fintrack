<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";
$expense = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {

    redirect('index.php');

}

$expense_id = intval($_GET['id']);

/*
    LOAD EXPENSE
*/

$query = "SELECT * FROM expenses 
          WHERE expense_id = ? 
          LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $expense_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    redirect('index.php');

}

$expense = mysqli_fetch_assoc($result);

/*
    UPDATE EXPENSE
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $expense_name = cleanInput($_POST['expense_name']);
    $category = cleanInput($_POST['category']);
    $amount = floatval($_POST['amount']);
    $payment_method = cleanInput($_POST['payment_method']);
    $expense_date = $_POST['expense_date'];
    $remarks = cleanInput($_POST['remarks']);

    if (
        empty($expense_name) ||
        empty($amount) ||
        empty($payment_method) ||
        empty($expense_date)
    ) {

        $error = "Please fill in all required fields.";

    } else {

        $updateQuery = "UPDATE expenses
                        SET expense_name = ?,
                            category = ?,
                            amount = ?,
                            payment_method = ?,
                            expense_date = ?,
                            remarks = ?
                        WHERE expense_id = ?";

        $updateStmt = mysqli_prepare($conn, $updateQuery);

        mysqli_stmt_bind_param(
            $updateStmt,
            "ssdsssi",
            $expense_name,
            $category,
            $amount,
            $payment_method,
            $expense_date,
            $remarks,
            $expense_id
        );

        if (mysqli_stmt_execute($updateStmt)) {

            $success = "Expense updated successfully.";

            /*
                RELOAD UPDATED DATA
            */

            $query = "SELECT * FROM expenses 
                      WHERE expense_id = ? 
                      LIMIT 1";

            $stmt = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param($stmt, "i", $expense_id);

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            $expense = mysqli_fetch_assoc($result);

        } else {

            $error = "Something went wrong.";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Expense - FINTRACK</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="form-container">

            <div class="page-header">

                <h1 class="page-title">
                    Edit Expense
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

                    <label>Expense Name</label>

                    <input
                        type="text"
                        name="expense_name"
                        value="<?php echo htmlspecialchars($expense['expense_name']); ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category"
                        value="<?php echo htmlspecialchars($expense['category']); ?>"
                    >

                </div>

                <div class="form-group">

                    <label>Amount</label>

                    <input
                        type="number"
                        step="0.01"
                        name="amount"
                        value="<?php echo $expense['amount']; ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Payment Method</label>

                    <select name="payment_method" required>

                        <option value="cash"
                            <?php echo ($expense['payment_method'] == 'cash') ? 'selected' : ''; ?>>
                            Cash
                        </option>

                        <option value="gcash"
                            <?php echo ($expense['payment_method'] == 'gcash') ? 'selected' : ''; ?>>
                            GCash
                        </option>

                        <option value="bank_transfer"
                            <?php echo ($expense['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>
                            Bank Transfer
                        </option>

                        <option value="others"
                            <?php echo ($expense['payment_method'] == 'others') ? 'selected' : ''; ?>>
                            Others
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Expense Date</label>

                    <input
                        type="date"
                        name="expense_date"
                        value="<?php echo $expense['expense_date']; ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        rows="4"
                    ><?php echo htmlspecialchars($expense['remarks']); ?></textarea>

                </div>

                <button type="submit" class="save-btn">
                    Update Expense
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>