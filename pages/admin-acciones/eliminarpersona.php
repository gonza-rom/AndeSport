<?php
include '../../includes/conexion.php';

if (!isset($_GET['id_persona'])) {
    echo "ID no especificado.";
    exit;
}

$id_persona = $_GET['id_persona'];

// 1. Buscar el id_usuario relacionado a esta persona
$stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE id_persona = ?");
$stmt->bind_param("i", $id_persona);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_usuario = $row['id_usuario'];

    // 2. Eliminar registros en historial_stock que dependan de este usuario
    $stmt = $conexion->prepare("DELETE FROM historial_stock WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    // 3. Eliminar usuario relacionado
    $stmt = $conexion->prepare("DELETE FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
}

// 4. Finalmente eliminar la persona
$stmt = $conexion->prepare("DELETE FROM persona WHERE id_persona = ?");
$stmt->bind_param("i", $id_persona);

if ($stmt->execute()) {
    header("Location: ../administrador.php?mensaje=persona_eliminada");
    exit;
} else {
    echo "Error al eliminar la persona: " . $conexion->error;
}
?>