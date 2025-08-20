<?php
session_start();
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin', 'stock'])) {
    header("Location: ../index.php");
    exit();
}

include '../../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);

    $stmt = $conexion->prepare("SELECT * FROM producto WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST['id_producto'];
    $stock_ajustado = intval($_POST['ajuste']);
    $tipo_movimiento = $_POST['tipo'] === 'add' ? 'ingreso' : 'egreso';
    $motivo = $_POST['motivo'];
    $id_usuario = $_SESSION['id_usuario'];

    $stmt = $conexion->prepare("SELECT stock FROM producto WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();

    $nuevo_stock = $tipo_movimiento === 'ingreso'
        ? $producto['stock'] + $stock_ajustado
        : $producto['stock'] - $stock_ajustado;

    if ($nuevo_stock < 0) {
        echo "<script>alert('No se puede dejar el stock en negativo'); window.location.href='../controlstock.php';</script>";
        exit();
    }

    $stmt = $conexion->prepare("UPDATE producto SET stock = ? WHERE id_producto = ?");
    $stmt->bind_param("ii", $nuevo_stock, $id_producto);
    $stmt->execute();
    $stmt->close();

    // Insertar historial
    $stmt = $conexion->prepare("INSERT INTO historial_stock (id_producto, id_usuario, cantidad, tipo_movimiento, motivo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $id_producto, $id_usuario, $stock_ajustado, $tipo_movimiento, $motivo);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Stock actualizado y registrado en historial'); window.location.href='../controlstock.php';</script>";
    exit();
}
?>

<!-- HTML para formulario de edición -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container py-4">
    <h2>Editar Stock de: <?= htmlspecialchars($producto['nombre']) ?></h2>
    <form action="" method="POST">
        <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

        <div class="mb-3">
            <label class="form-label">Stock actual</label>
            <input type="number" class="form-control" value="<?= $producto['stock'] ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad a ajustar</label>
            <input type="number" name="ajuste" class="form-control" required min="1">
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de movimiento</label>
            <select name="tipo" class="form-select" required>
                <option value="add">Ingreso</option>
                <option value="remove">Egreso</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Motivo</label>
            <input type="text" name="motivo" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Stock</button>
        <a href="../controlstock.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>

</html>