<?php
session_start();
include '../includes/conexion.php';

// Verificar login
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: inicio-sesion.php");
    exit;
}

// Carrito y pago
$carrito = $_SESSION['carrito'] ?? [];
$pago = $_SESSION['pago'] ?? null;

if (empty($carrito)) {
    die("No hay productos en el carrito para confirmar la compra.");
}

if (!$pago) {
    die("No se encontró información de pago.");
}

// Calcular total
$totalCarrito = 0;
foreach ($carrito as $item) {
    $totalCarrito += $item['precio'] * $item['cantidad'];
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Insertar venta
    $stmtVenta = $conexion->prepare("INSERT INTO ventas (id_usuario, total, tipo_pago, fecha) VALUES (?, ?, ?, NOW())");
    $stmtVenta->bind_param("ids", $_SESSION['id_usuario'], $totalCarrito, $pago['tipo_pago']);
    $stmtVenta->execute();
    $id_venta = $conexion->insert_id;
    $stmtVenta->close();

    // Insertar detalle de ventas
    $stmtDetalle = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    foreach ($carrito as $item) {
        if (!isset($item['id'])) {
            throw new Exception("Falta id en el carrito para el item: " . json_encode($item));
        }
        $stmtDetalle->bind_param("iiid", $id_venta, $item['id'], $item['cantidad'], $item['precio']);
        $stmtDetalle->execute();

        // Actualizar stock y vendidos
        $update = $conexion->prepare("UPDATE producto SET stock = stock - ?, vendidos = vendidos + ? WHERE id_producto = ?");
        $update->bind_param("iii", $item['cantidad'], $item['cantidad'], $item['id']);
        $update->execute();
        $update->close();
    }
    $stmtDetalle->close();

    // Insertar datos de tarjeta si corresponde
    if ($pago['tipo_pago'] === 'tarjeta') {
        $stmtTarjeta = $conexion->prepare("
            INSERT INTO pago_tarjeta 
            (id_venta, tarjeta, mes, anio, codigo, nombre_titular, apellido_titular, dni, marca_tarjeta, cuotas) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtTarjeta->bind_param(
            "issssssssi",
            $id_venta,
            $pago['tarjeta'],
            $pago['mes'],
            $pago['anio'],
            $pago['codigo'],
            $pago['nombre_titular'],
            $pago['apellido_titular'],
            $pago['dni'],
            $pago['marca_tarjeta'],
            $pago['cuotas']
        );
        $stmtTarjeta->execute();
        $stmtTarjeta->close();
    }

    // Confirmar transacción
    $conexion->commit();

    // Limpiar carrito y pago
    unset($_SESSION['carrito']);
    unset($_SESSION['pago']);

    // Mostrar confirmación
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Compra Confirmada</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-success'>
            <h1>Compra realizada con éxito</h1>
            <p>Gracias por tu compra. Tu número de pedido es <strong>#{$id_venta}</strong>.</p>
            <a href='../index.php' class='btn btn-primary'>Volver al inicio</a>
        </div>
    </body>
    </html>";

} catch (Exception $e) {
    $conexion->rollback();
    die("Error al procesar la compra: " . $e->getMessage());
}
?>
