<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['entrega'] = [
        'calle' => $_POST['calle'],
        'altura' => $_POST['altura'],
        'localidad' => $_POST['localidad'],
        'departamento' => $_POST['departamento'],
        'provincia' => $_POST['provincia']
    ];
}

header("Location: pago.php");
exit;
