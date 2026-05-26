<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

$totalCustomers = 0;
$totalProducts = 0;
$totalPurchases = 0;
$totalExpenses = 0;
$totalSales = 0;

/* TOTAL CUSTOMERS */
$queryCustomers = "SELECT COUNT(*) AS total FROM customers";
$resultCustomers = mysqli_query($conn, $queryCustomers);

if ($resultCustomers) {
    $row = mysqli_fetch_assoc($resultCustomers);
    $totalCustomers = $row['total'];
}

/* TOTAL PRODUCTS */
$queryProducts = "SELECT COUNT(*) AS total FROM products";
$resultProducts = mysqli_query($conn, $queryProducts);

if ($resultProducts) {
    $row = mysqli_fetch_assoc($resultProducts);
    $totalProducts = $row['total'];
}

/* TOTAL PURCHASES */
$queryPurchases = "SELECT COUNT(*) AS total FROM purchases";
$resultPurchases = mysqli_query($conn, $queryPurchases);

if ($resultPurchases) {
    $row = mysqli_fetch_assoc($resultPurchases);
    $totalPurchases = $row['total'];
}

/* TOTAL EXPENSES */
$queryExpenses = "SELECT SUM(amount) AS total FROM expenses";
$resultExpenses = mysqli_query($conn, $queryExpenses);

if ($resultExpenses) {
    $row = mysqli_fetch_assoc($resultExpenses);
    $totalExpenses = $row['total'] ?? 0;
}

/* TOTAL SALES */
$querySales = "SELECT SUM(total_amount) AS total FROM purchases";
$resultSales = mysqli_query($conn, $querySales);

if ($resultSales) {
    $row = mysqli_fetch_assoc($resultSales);
    $totalSales = $row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - FINTRACK</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

<div class="main-container">

    <?php include 'includes/sidebar.php'; ?>

    <div class="content">

        <?php include 'includes/header.php'; ?>

        <div class="dashboard">

            <h1 class="page-title">
                Dashboard
            </h1>

            <div class="cards">

                <div class="card">

                    <h3>Total Customers</h3>

                    <p>
                        <?php echo $totalCustomers; ?>
                    </p>

                </div>

                <div class="card">

                    <h3>Total Products</h3>

                    <p>
                        <?php echo $totalProducts; ?>
                    </p>

                </div>

                <div class="card">

                    <h3>Total Transactions</h3>

                    <p>
                        <?php echo $totalPurchases; ?>
                    </p>

                </div>

                <div class="card">

                    <h3>Total Sales</h3>

                    <p>
                        ₱<?php echo number_format($totalSales, 2); ?>
                    </p>

                </div>

                <div class="card">

                    <h3>Total Expenses</h3>

                    <p>
                        ₱<?php echo number_format($totalExpenses, 2); ?>
                    </p>

                </div>

            </div>

            <div class="welcome-box">

                <h2>
                    Welcome,
                    <?php echo $_SESSION['full_name']; ?>
                </h2>

                <p>
                    You are logged in as
                    <strong>
                        <?php echo ucfirst($_SESSION['role']); ?>
                    </strong>
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>