<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio-sesion.php");
    exit();
}

include '../../includes/conexion.php';

if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);

    $sql = "DELETE FROM producto WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);

    if ($stmt->execute()) {
        echo "<script>alert('Producto eliminado correctamente.'); window.location.href='../administrador.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar: " . $stmt->error . "'); window.location.href='../administrador.php';</script>";
    }

    $stmt->close();
    $conexion->close();
} else {
    header("Location: ../administrador.php");
}
?>