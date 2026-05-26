<?php
session_start();

require_once '../includes/auth.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

$customers = [];
$products = [];

/*
    LOAD CUSTOMERS
*/

$customerQuery = "SELECT * FROM customers ORDER BY customer_name ASC";
$customerResult = mysqli_query($conn, $customerQuery);

if ($customerResult) {

    while ($row = mysqli_fetch_assoc($customerResult)) {

        $customers[] = $row;

    }

}

/*
    LOAD PRODUCTS
*/

$productQuery = "SELECT * FROM products 
                 WHERE status = 'active'
                 ORDER BY product_name ASC";

$productResult = mysqli_query($conn, $productQuery);

if ($productResult) {

    while ($row = mysqli_fetch_assoc($productResult)) {

        $products[] = $row;

    }

}

/*
    SAVE PURCHASE
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_id = intval($_POST['customer_id']);
    $purchase_date = $_POST['purchase_date'];
    $remarks = cleanInput($_POST['remarks']);

    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    if (
        empty($customer_id) ||
        empty($purchase_date)
    ) {

        $error = "Please fill in required fields.";

    } else {

        mysqli_begin_transaction($conn);

        try {

            $total_amount = 0;

            /*
                COMPUTE TOTAL
            */

            foreach ($product_ids as $index => $product_id) {

                $product_id = intval($product_id);

                $quantity = intval($quantities[$index]);

                $productQuery = "SELECT * FROM products 
                                 WHERE product_id = ? 
                                 LIMIT 1";

                $stmt = mysqli_prepare($conn, $productQuery);

                mysqli_stmt_bind_param($stmt, "i", $product_id);

                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);

                $product = mysqli_fetch_assoc($result);

                if ($product) {

                    $subtotal = $product['price'] * $quantity;

                    $total_amount += $subtotal;

                }

            }

            /*
                INSERT PURCHASE
            */

            $purchaseInsert = "INSERT INTO purchases (
                                customer_id,
                                user_id,
                                total_amount,
                                amount_paid,
                                balance_amount,
                                payment_status,
                                purchase_date,
                                remarks
                              )
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $amount_paid = 0;
            $balance_amount = $total_amount;
            $payment_status = 'unpaid';

            $stmt = mysqli_prepare($conn, $purchaseInsert);

            mysqli_stmt_bind_param(
                $stmt,
                "iidddsss",
                $customer_id,
                $_SESSION['user_id'],
                $total_amount,
                $amount_paid,
                $balance_amount,
                $payment_status,
                $purchase_date,
                $remarks
            );

            mysqli_stmt_execute($stmt);

            $purchase_id = mysqli_insert_id($conn);

            /*
                INSERT PURCHASE ITEMS
            */

            foreach ($product_ids as $index => $product_id) {

                $product_id = intval($product_id);

                $quantity = intval($quantities[$index]);

                $productQuery = "SELECT * FROM products 
                                 WHERE product_id = ? 
                                 LIMIT 1";

                $stmt = mysqli_prepare($conn, $productQuery);

                mysqli_stmt_bind_param($stmt, "i", $product_id);

                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);

                $product = mysqli_fetch_assoc($result);

                if ($product) {

                    $price = $product['price'];

                    $subtotal = $price * $quantity;

                    $itemInsert = "INSERT INTO purchase_items (
                                    purchase_id,
                                    product_id,
                                    quantity,
                                    price,
                                    subtotal
                                   )
                                   VALUES (?, ?, ?, ?, ?)";

                    $itemStmt = mysqli_prepare($conn, $itemInsert);

                    mysqli_stmt_bind_param(
                        $itemStmt,
                        "iiidd",
                        $purchase_id,
                        $product_id,
                        $quantity,
                        $price,
                        $subtotal
                    );

                    mysqli_stmt_execute($itemStmt);

                    /*
                        UPDATE STOCK
                    */

                    if ($product['type'] == 'product') {

                        $newStock = $product['stock_quantity'] - $quantity;

                        if ($newStock < 0) {
                            $newStock = 0;
                        }

                        $stockUpdate = "UPDATE products 
                                        SET stock_quantity = ? 
                                        WHERE product_id = ?";

                        $stockStmt = mysqli_prepare($conn, $stockUpdate);

                        mysqli_stmt_bind_param(
                            $stockStmt,
                            "ii",
                            $newStock,
                            $product_id
                        );

                        mysqli_stmt_execute($stockStmt);

                    }

                }

            }

            mysqli_commit($conn);

            $success = "Purchase transaction created successfully.";

        } catch (Exception $e) {

            mysqli_rollback($conn);

            $error = "Transaction failed.";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Purchase Transaction - FINTRACK</title>

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
                    Create Purchase Transaction
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

                    <label>Customer</label>

                    <select name="customer_id" required>

                        <option value="">
                            Select Customer
                        </option>

                        <?php foreach ($customers as $customer) : ?>

                            <option value="<?php echo $customer['customer_id']; ?>">

                                <?php echo htmlspecialchars($customer['customer_name']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Purchase Date</label>

                    <input
                        type="date"
                        name="purchase_date"
                        value="<?php echo date('Y-m-d'); ?>"
                        required
                    >

                </div>

                <hr>

                <h3>Purchase Items</h3>

                <div id="items-container">

                    <div class="purchase-item">

                        <div class="form-group">

                            <label>Product / Service</label>

                            <select name="product_id[]" required>

                                <option value="">
                                    Select Product / Service
                                </option>

                                <?php foreach ($products as $product) : ?>

                                    <option value="<?php echo $product['product_id']; ?>">

                                        <?php echo htmlspecialchars($product['product_name']); ?>
                                        -
                                        <?php echo formatCurrency($product['price']); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label>Quantity</label>

                            <input
                                type="number"
                                name="quantity[]"
                                min="1"
                                value="1"
                                required
                            >

                        </div>

                    </div>

                </div>

                <button
                    type="button"
                    class="secondary-btn"
                    onclick="addItem()"
                >
                    Add Another Item
                </button>

                <div class="form-group">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        rows="4"
                    ></textarea>

                </div>

                <button type="submit" class="save-btn">
                    Save Transaction
                </button>

            </form>

        </div>

        <?php include '../includes/footer.php'; ?>

    </div>

</div>

<script>

function addItem() {

    const container = document.getElementById('items-container');

    const html = `
        <div class="purchase-item">

            <hr>

            <div class="form-group">

                <label>Product / Service</label>

                <select name="product_id[]" required>

                    <option value="">
                        Select Product / Service
                    </option>

                    <?php foreach ($products as $product) : ?>

                        <option value="<?php echo $product['product_id']; ?>">

                            <?php echo htmlspecialchars($product['product_name']); ?>
                            -
                            <?php echo formatCurrency($product['price']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity[]"
                    min="1"
                    value="1"
                    required
                >

            </div>

        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

}

</script>

</body>
</html>