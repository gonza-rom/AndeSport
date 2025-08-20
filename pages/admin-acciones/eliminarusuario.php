<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio-sesion.php");
    exit();
}

include '../../includes/conexion.php';

if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    if ($id_usuario == $_SESSION['id_usuario']) {
        echo "<script>alert('No puedes eliminarte a ti mismo.'); window.location.href='../administrador.php';</script>";
        exit();
    }

    $sql = "DELETE FROM usuario WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario eliminado permanentemente.'); window.location.href='../administrador.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar.'); window.location.href='../administrador.php';</script>";
    }

    $stmt->close();
} else {
    header("Location: ../administrador.php");
}
?>