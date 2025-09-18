<?php
session_start();
include '../includes/conexion.php';

// Verificar si se recibió un producto
if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    // Consultar el producto en la BD
    $sql = "SELECT id_producto, nombre, precio, img_producto 
            FROM producto 
            WHERE id_producto = $id_producto LIMIT 1";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();

        // Si no existe el carrito, lo creamos
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Si el producto ya está en el carrito, sumamos cantidad
        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_producto'] == $id_producto) {
                $item['cantidad']++;
                $encontrado = true;
                break;
            }
        }

        // Si no estaba en el carrito, lo agregamos
        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id_producto' => $producto['id_producto'],
                'nombre'      => $producto['nombre'],
                'precio'      => $producto['precio'],
                'cantidad'    => 1,
                'imagen'      => $producto['img_producto']
            ];
        }
    }
}

// Redirigir al carrito
header("Location: ver-carrito.php");
exit;
?>
