<?php
include '../../includes/conexion.php';

if (!isset($_GET['id_persona'])) {
    echo "ID no especificado.";
    exit;
}

$id = $_GET['id_persona'];

$stmt = $conexion->prepare("SELECT * FROM persona WHERE id_persona = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Persona no encontrada.";
    exit;
}

$persona = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Persona</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Editar Persona</h2>
    <form action="guardar_edicion_persona.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_persona" value="<?= $persona['id_persona'] ?>">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($persona['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" name="apellido" value="<?= htmlspecialchars($persona['apellido']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">DNI</label>
            <input type="text" class="form-control" name="dni" value="<?= htmlspecialchars($persona['dni']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" name="fecha_nacimiento" value="<?= htmlspecialchars($persona['fecha_nacimiento']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Provincia</label>
            <input type="text" class="form-control" name="provincia" value="<?= htmlspecialchars($persona['provincia']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Departamento</label>
            <input type="text" class="form-control" name="departamento" value="<?= htmlspecialchars($persona['departamento']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Localidad</label>
            <input type="text" class="form-control" name="localidad" value="<?= htmlspecialchars($persona['localidad']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Municipio</label>
            <input type="text" class="form-control" name="municipio" value="<?= htmlspecialchars($persona['municipio']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Calle</label>
            <input type="text" class="form-control" name="calle" value="<?= htmlspecialchars($persona['calle']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Altura</label>
            <input type="text" class="form-control" name="altura" value="<?= htmlspecialchars($persona['altura']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Latitud</label>
            <input type="text" class="form-control" name="latitud" value="<?= htmlspecialchars($persona['latitud']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Longitud</label>
            <input type="text" class="form-control" name="longitud" value="<?= htmlspecialchars($persona['longitud']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Foto DNI Frente</label><br>
            <?php if (!empty($persona['foto_dni_frente'])): ?>
                <img src="../../<?= htmlspecialchars($persona['foto_dni_frente']) ?>" alt="DNI Frente" width="120" class="mb-2"><br>
            <?php endif; ?>
            <input type="file" class="form-control" name="foto_dni_frente">
        </div>

        <div class="mb-3">
            <label class="form-label">Foto DNI Dorso</label><br>
            <?php if (!empty($persona['foto_dni_dorso'])): ?>
                <img src="../../<?= htmlspecialchars($persona['foto_dni_dorso']) ?>" alt="DNI Dorso" width="120" class="mb-2"><br>
            <?php endif; ?>
            <input type="file" class="form-control" name="foto_dni_dorso">
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="../administrador.php" class="btn btn-secondary">Cancelar</a>
    </form>

</body>
</html>
