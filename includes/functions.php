<?php

function cleanInput($data)
{
    return htmlspecialchars(trim($data));
}

function formatCurrency($amount)
{
    return "₱" . number_format($amount, 2);
}

function redirect($location)
{
    header("Location: " . $location);
    exit();
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function getCurrentDate()
{
    return date("Y-m-d");
}

function getCurrentDateTime()
{
    return date("Y-m-d H:i:s");
}

function activeMenu($page)
{
    $currentPage = basename($_SERVER['PHP_SELF']);

    if ($currentPage == $page) {
        return "active";
    }

    return "";
}

?>