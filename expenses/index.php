<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$expenses = [];

$query = "
    SELECT
        expenses.*,
        users.full_name
    FROM expenses
    INNER JOIN users
        ON expenses.user_id = users.user_id
    ORDER BY expenses.expense_id DESC
";

$result = mysqli_query($conn, $query);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $expenses[] = $row;

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Expense Management - FINTRACK</title>

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
                Expense Management
            </h1>

            <a href="add.php" class="add-btn">
                Add Expense
            </a>

        </div>

        <div class="table-container">

            <div class="search-box">

                <input
                    type="text"
                    id="searchExpense"
                    placeholder="Search expenses..."
                    onkeyup="searchTable('searchExpense', 'expensesTable')"
                >

            </div>

            <table id="expensesTable">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Expense Name</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Expense Date</th>
                        <th>Processed By</th>
                        <th>Remarks</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (count($expenses) > 0) : ?>

                        <?php foreach ($expenses as $expense) : ?>

                            <tr>

                                <td>
                                    <?php echo $expense['expense_id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($expense['expense_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($expense['category']); ?>
                                </td>

                                <td>
                                    <?php echo formatCurrency($expense['amount']); ?>
                                </td>

                                <td>
                                    <?php echo ucfirst(str_replace('_', ' ', $expense['payment_method'])); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($expense['expense_date'])); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($expense['full_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($expense['remarks']); ?>
                                </td>

                                <td class="action-buttons">

                                    <a
                                        href="edit.php?id=<?php echo $expense['expense_id']; ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="delete.php?id=<?php echo $expense['expense_id']; ?>"
                                        class="delete-btn"
                                        onclick="return confirmDelete()"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="9" class="no-data">
                                No expense records found.
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