<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$customers = [];

$query = "SELECT * FROM customers ORDER BY customer_id DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - FINTRACK</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8z+qLQSh77L1xY8f0zY4nY9g5f1tYg==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>

<div class="main-container">

    <?php include '../includes/sidebar.php'; ?>

    <div class="content">

        <?php include '../includes/header.php'; ?>

        <div class="page-header">
            <h1 class="page-title">Customer Management</h1>

            <a href="add.php" class="add-btn">
                Add Customer
            </a>
        </div>

        <div class="table-container">

            <div class="search-box">
                <input
                    type="text"
                    id="searchCustomer"
                    placeholder="Search customers..."
                    onkeyup="searchTable('searchCustomer', 'customersTable')"
                >
            </div>

            <table id="customersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (count($customers) > 0) : ?>

                        <?php foreach ($customers as $customer) : ?>

                            <tr>
                                <td><?php echo $customer['customer_id']; ?></td>

                                <td>
                                    <?php echo htmlspecialchars($customer['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($customer['contact_number']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($customer['email']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($customer['address']); ?>
                                </td>

                                <td>
                                    <?php echo date("M d, Y", strtotime($customer['created_at'])); ?>
                                </td>

                                <td class="action-buttons">
                                    <a 
                                        href="edit.php?id=<?php echo $customer['customer_id']; ?>" 
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>

                                    <a 
                                        href="delete.php?id=<?php echo $customer['customer_id']; ?>"
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
                            <td colspan="7" class="no-data">
                                No customers found.
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