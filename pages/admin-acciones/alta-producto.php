<?php
session_start();
include '../../includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../inicio-sesion.php");
    exit();
}

if (isset($_GET['id_producto'])) {
    $id = intval($_GET['id_producto']);
    $stmt = $conexion->prepare("UPDATE producto SET estado = 'activo' WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ../administrador.php");
    exit();
}
?>