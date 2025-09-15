<?php
session_start();
include '../../includes/conexion.php';

// Asegurarse de que haya un usuario logueado con rol cliente
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] != 'cliente') {
    header("Location: ../inicio-sesion.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar que se haya enviado una imagen
if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === UPLOAD_ERR_OK) {
    $archivoTmp = $_FILES['nueva_foto']['tmp_name'];
    $nombreOriginal = $_FILES['nueva_foto']['name'];
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    //Validar extensiones permitidas
    $permitidas = ['jpg', 'png', 'jpeg'];
    if (!in_array($extension, $permitidas)) {
        echo "Formato no permitido. Solo se aceptan imágenes JPG, PNG o JPEG.";
        exit();
    }

    $nuevaRuta = 'img_usuario/' . $id_usuario . '.' . $extension;
    $rutaCompleta = __DIR__ . '/../../' . $nuevaRuta;

    // Crear carpeta si no existe
    if (!is_dir(__DIR__ . '/../../img_usuario')) {
        mkdir(__DIR__ . '/../../img_usuario', 0777, true);
    }

    // Mover archivo subido
    if (move_uploaded_file($archivoTmp, $rutaCompleta)) {
        // Guardar ruta en base de datos
        $stmt = $conexion->prepare("UPDATE usuario SET foto_usuario = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nuevaRuta, $id_usuario);
        $stmt->execute();

        // Redirigir al panel para ver el cambio
        header("Location: ../interfaz-cliente.php");
        exit();
    } else {
        echo "Error al guardar la nueva imagen.";
    }
} else {
    echo "No se seleccionó una imagen válida.";
}
?>
