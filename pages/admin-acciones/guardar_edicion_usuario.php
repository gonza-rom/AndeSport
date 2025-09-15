<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    // Si sube una nueva imagen
    $foto_ruta = null;
    if (!empty($_FILES['foto_usuario']['name'])) {
        $nombre_archivo = uniqid() . "_" . basename($_FILES["foto_usuario"]["name"]);
        $ruta_destino = "../../img_usuario/" . $nombre_archivo;

        if (move_uploaded_file($_FILES["foto_usuario"]["tmp_name"], $ruta_destino)) {
            $foto_ruta = "img_usuario/" . $nombre_archivo;
        }
    }

    if ($foto_ruta) {
        $stmt = $conexion->prepare(
            "UPDATE usuario 
             SET nombre=?, email=?, telefono=?, rol=?, foto_usuario=? 
             WHERE id_usuario=?"
        );
        $stmt->bind_param("sssssi", $nombre, $email, $telefono, $rol, $foto_ruta, $id);
    } else {
        $stmt = $conexion->prepare(
            "UPDATE usuario 
             SET nombre=?, email=?, telefono=?, rol=? 
             WHERE id_usuario=?"
        );
        $stmt->bind_param("ssssi", $nombre, $email, $telefono, $rol, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../administrador.php?mensaje=usuario_actualizado");
        exit;
    } else {
        echo "Error al actualizar el usuario: " . $conexion->error;
    }
}
?>