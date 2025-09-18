<?php
session_start();

// Recibir datos enviados por fetch
$input = file_get_contents("php://input");
$carrito = json_decode($input, true);

if (is_array($carrito)) {
    $_SESSION['carrito'] = $carrito;

    echo json_encode(["ok" => true]);
} else {
    echo json_encode(["ok" => false]);
}


foreach ($carrito as &$p) {
    $p['imagen'] = $p['img']; // renombrar clave para que coincida
    unset($p['img']);
}
$_SESSION['carrito'] = $carrito;