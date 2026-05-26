<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {

        $error = "Please fill in all fields.";

    } else {

        $query = "SELECT * FROM users 
                  WHERE username = ? 
                  AND status = 'active' 
                  LIMIT 1";

        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "s", $username);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {

            $user = mysqli_fetch_assoc($result);

            // TEMPORARY plain password check
            // later papalitan natin ng password_verify()

            if ($password == $user['password']) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();

            } else {

                $error = "Invalid username or password.";

            }

        } else {

            $error = "Invalid username or password.";

        }

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FINTRACK - Login</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8z+qLQSh77L1xY8f0zY4nY9g5f1tYg==" crossorigin="anonymous" referrerpolicy="no-referrer">

</head>
<body>

<div class="login-container">

    <div class="login-box">

        <h1>FINTRACK</h1>

        <p class="subtitle">
            Customer Purchase Record and Financial Reporting System
        </p>

        <?php if (!empty($error)) : ?>

            <div class="error-message">
                <?php echo $error; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label>Username</label>

                <input 
                    type="text" 
                    name="username" 
                    placeholder="Enter username"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input 
                    type="password" 
                    name="password" 
                    placeholder="Enter password"
                    required
                >

            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

    </div>

</div>

</body>
</html>