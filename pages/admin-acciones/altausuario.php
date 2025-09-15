<?php
session_start();
include '../../includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../inicio-sesion.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conexion->prepare("UPDATE usuario SET activo = 1 WHERE id_usuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ../administrador.php");
    exit();
}
?>