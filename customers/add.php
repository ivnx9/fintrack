<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_name = cleanInput($_POST['customer_name']);
    $contact_number = cleanInput($_POST['contact_number']);
    $email = cleanInput($_POST['email']);
    $address = cleanInput($_POST['address']);

    if (empty($customer_name)) {

        $error = "Customer name is required.";

    } else {

        $query = "INSERT INTO customers (
                    customer_name,
                    contact_number,
                    email,
                    address
                  )
                  VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $customer_name,
            $contact_number,
            $email,
            $address
        );

        if (mysqli_stmt_execute($stmt)) {

            $success = "Customer added successfully.";

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

    <title>Add Customer - FINTRACK</title>

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
                    Add Customer
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

                    <label>Customer Name</label>

                    <input
                        type="text"
                        name="customer_name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Contact Number</label>

                    <input
                        type="text"
                        name="contact_number"
                    >

                </div>

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                    >

                </div>

                <div class="form-group">

                    <label>Address</label>

                    <textarea
                        name="address"
                        rows="4"
                    ></textarea>

                </div>

                <button type="submit" class="save-btn">
                    Save Customer
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

</body>
</html>