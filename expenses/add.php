<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

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

        $query = "INSERT INTO expenses (
                    user_id,
                    expense_name,
                    category,
                    amount,
                    expense_date,
                    payment_method,
                    remarks
                  )
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "issdsss",
            $_SESSION['user_id'],
            $expense_name,
            $category,
            $amount,
            $expense_date,
            $payment_method,
            $remarks
        );

        if (mysqli_stmt_execute($stmt)) {

            $success = "Expense added successfully.";

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

    <title>Add Expense - FINTRACK</title>

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
                    Add Expense
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
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Category</label>

                    <input
                        type="text"
                        name="category"
                    >

                </div>

                <div class="form-group">

                    <label>Amount</label>

                    <input
                        type="number"
                        step="0.01"
                        name="amount"
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

                    <label>Expense Date</label>

                    <input
                        type="date"
                        name="expense_date"
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
                    Save Expense
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>