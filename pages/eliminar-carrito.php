<?php
session_start();

if (isset($_GET['index'])) {
    $index = intval($_GET['index']);
    if (isset($_SESSION['carrito'][$index])) {
        unset($_SESSION['carrito'][$index]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar
    }
}

header("Location: ver-carrito.php");
exit;
?>