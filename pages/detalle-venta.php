    <?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: inicio-sesion.php");
    exit();
}

$id_venta = $_GET['id_venta'] ?? null;
if (!$id_venta) die("ID de venta no válido.");

// Verificar que la venta pertenece al usuario
$sql = "SELECT * FROM ventas WHERE id_venta = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_venta, $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();
$stmt->close();

if (!$venta) die("Venta no encontrada o no autorizada.");

// Traer detalle de productos
$sql_detalle = "SELECT dv.cantidad, dv.precio_unitario, p.nombre 
                FROM detalle_ventas dv
                INNER JOIN producto p ON dv.id_producto = p.id_producto
                WHERE dv.id_venta = ?";
$stmt = $conexion->prepare($sql_detalle);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result_detalle = $stmt->get_result();
$productos = $result_detalle->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Venta #<?= $id_venta ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h1>Detalle de Venta #<?= $id_venta ?></h1>
<p><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($venta['fecha'])) ?></p>
<p><strong>Total:</strong> $<?= number_format($venta['total'], 2, ',', '.') ?></p>
<p><strong>Tipo de pago:</strong> <?= ucfirst($venta['tipo_pago']) ?></p>

<div class="card mt-4">
    <div class="card-header">Productos</div>
    <div class="card-body">
        <?php if ($productos): ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): 
                        $subtotal = $p['cantidad'] * $p['precio_unitario'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><?= $p['cantidad'] ?></td>
                            <td>$<?= number_format($p['precio_unitario'], 2, ',', '.') ?></td>
                            <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay productos en esta venta.</p>
        <?php endif; ?>
    </div>
</div>

<a href="interfaz-cliente.php" class="btn btn-primary mt-3">Volver a mis pedidos</a>

</body>
</html>
