<?php
include '../../includes/conexion.php';

if (!isset($_GET['id'])) {
    echo "ID de usuario no especificado.";
    exit;
}

$id = $_GET['id'];

$stmt = $conexion->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit;
}

$usuario = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Editar Usuario</h2>
    <form action="guardar_edicion_usuario.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>">
        </div>

        <div class="mb-3">
    <label class="form-label">Rol</label>
    <select class="form-select" name="rol" required>
        <?php 
        $roles = ['cliente', 'admin', 'gerente', 'repartidor', 'stock'];
        $rolActual = !empty($usuario['rol']) ? $usuario['rol'] : 'cliente';
        foreach ($roles as $rol) {
            $selected = $rolActual === $rol ? 'selected' : '';
            echo "<option value='$rol' $selected>$rol</option>";
        }
        ?>
    </select>
</div>

        <div class="mb-3">
            <label class="form-label">Foto de Usuario</label><br>
            <?php if (!empty($usuario['foto_usuario'])): ?>
                <img src="../../<?= htmlspecialchars($usuario['foto_usuario']) ?>" alt="Foto" width="80" class="mb-2"><br>
            <?php endif; ?>
            <input type="file" class="form-control" name="foto_usuario">
        </div>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="../administrador.php" class="btn btn-secondary">Cancelar</a>
    </form>

</body>

</html>
