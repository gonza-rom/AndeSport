<?php
include '../../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_persona'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $fecha = $_POST['fecha_nacimiento'];
    $provincia = $_POST['provincia'];
    $departamento = $_POST['departamento'];
    $localidad = $_POST['localidad'];
    $municipio = $_POST['municipio'];
    $calle = $_POST['calle'];
    $altura = $_POST['altura'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];

    // Primero obtengo las rutas actuales para las fotos, para no perderlas si no se cargan nuevas
    $stmtFoto = $conexion->prepare("SELECT foto_dni_frente, foto_dni_dorso FROM persona WHERE id_persona = ?");
    $stmtFoto->bind_param("i", $id);
    $stmtFoto->execute();
    $resFoto = $stmtFoto->get_result();
    $personaFotos = $resFoto->fetch_assoc();

    $foto_frente = $personaFotos['foto_dni_frente'];
    $foto_dorso = $personaFotos['foto_dni_dorso'];

    // Si se subió nueva foto de frente
    if (!empty($_FILES['foto_dni_frente']['name'])) {
        $nombreArchivoFrente = uniqid() . "_" . basename($_FILES["foto_dni_frente"]["name"]);
        $rutaDestinoFrente = "../../uploads/" . $nombreArchivoFrente;

        if (move_uploaded_file($_FILES["foto_dni_frente"]["tmp_name"], $rutaDestinoFrente)) {
            $foto_frente = "uploads/" . $nombreArchivoFrente;
        }
    }

    // Si se subió nueva foto de dorso
    if (!empty($_FILES['foto_dni_dorso']['name'])) {
        $nombreArchivoDorso = uniqid() . "_" . basename($_FILES["foto_dni_dorso"]["name"]);
        $rutaDestinoDorso = "../../uploads/" . $nombreArchivoDorso;

        if (move_uploaded_file($_FILES["foto_dni_dorso"]["tmp_name"], $rutaDestinoDorso)) {
            $foto_dorso = "uploads/" . $nombreArchivoDorso;
        }
    }

    // Preparar la consulta con todos los campos
    $stmt = $conexion->prepare("UPDATE persona SET nombre=?, apellido=?, dni=?, fecha_nacimiento=?, provincia=?, departamento=?, localidad=?, municipio=?, calle=?, altura=?, latitud=?, longitud=?, foto_dni_frente=?, foto_dni_dorso=? WHERE id_persona=?");

    $stmt->bind_param(
        "sssssssssssddsi",
        $nombre,
        $apellido,
        $dni,
        $fecha,
        $provincia,
        $departamento,
        $localidad,
        $municipio,
        $calle,
        $altura,
        $latitud,
        $longitud,
        $foto_frente,
        $foto_dorso,
        $id
    );

    if ($stmt->execute()) {
        header("Location: ../administrador.php?mensaje=actualizado");
        exit;
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}
?>
