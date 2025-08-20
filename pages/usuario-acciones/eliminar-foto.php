<?php
session_start();
require_once '../../includes/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;

if ($id_usuario) {
    // Obtener la foto actual
    $query = "SELECT foto_usuario FROM usuario WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($fotoActual);
    $stmt->fetch();
    $stmt->close();

    // Eliminar archivo físico si existe y no es la imagen por defecto
    if ($fotoActual && $fotoActual !== 'img_usuario/default.jpg') {
        $rutaFoto = '../../' . $fotoActual;
        if (file_exists($rutaFoto)) {
            unlink($rutaFoto);
        }
    }

    // Reemplazar en base de datos con la imagen por defecto
    $query = "UPDATE usuario SET foto_usuario = 'img_usuario/default.jpg' WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
}

// Redirigir al perfil (ajustá según tu estructura)
header("Location: ../interfaz-cliente.php");
exit;
