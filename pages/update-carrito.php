<?php
session_start();

if (isset($_POST['index']) && isset($_POST['action'])) {
    $index = intval($_POST['index']);
    $action = $_POST['action'];

    if (isset($_SESSION['carrito'][$index])) {
        if ($action === 'increase') {
            $_SESSION['carrito'][$index]['cantidad']++;
        } elseif ($action === 'decrease') {
            $_SESSION['carrito'][$index]['cantidad']--;
            if ($_SESSION['carrito'][$index]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$index]);
            }
        }
    }
}

header("Location: ver-carrito.php");
exit;
