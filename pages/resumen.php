<?php
session_start();
include '../includes/conexion.php';

// Verificar login
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: inicio-sesion.php");
    exit;
}

// Primero traemos los datos de la persona junto con el usuario
$sql = "SELECT p.nombre, p.apellido, p.dni, p.calle, p.altura, 
               p.provincia AS codprov, p.departamento AS coddpto, p.localidad AS codloc, p.municipio AS codmun,
               u.email, u.telefono
        FROM persona p
        INNER JOIN usuario u ON u.id_persona = p.id_persona
        WHERE u.id_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
$persona = $result->fetch_assoc();
$stmt->close();

// Luego, traemos los nombres legibles
$provincia = $conexion->query("SELECT nomprov FROM provincias WHERE codprov = ".$persona['codprov'])->fetch_assoc()['nomprov'] ?? '';
$departamento = $conexion->query("SELECT nomdpto FROM dpto WHERE coddpto = ".$persona['coddpto'])->fetch_assoc()['nomdpto'] ?? '';
$localidad = $conexion->query("SELECT nomloc FROM localidades WHERE codloc = ".$persona['codloc'])->fetch_assoc()['nomloc'] ?? '';
$municipio = $conexion->query("SELECT nommun FROM municipio WHERE codmun = ".$persona['codmun'])->fetch_assoc()['nommun'] ?? '';
// Datos de carrito (ejemplo)


$carrito = $_SESSION['carrito'] ?? [];
$totalCarrito = 0;

// Datos de pago
$pago = $_SESSION['pago'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h1>Resumen de tu compra</h1>

<!-- Datos personales -->
<div class="card mb-4">
    <div class="card-header">Datos del Cliente</div>
    <div class="card-body">
        <p><strong>Nombre:</strong> <?= $persona['nombre'] . " " . $persona['apellido'] ?></p>
        <p><strong>Email:</strong> <?= $persona['email'] ?></p>
        <p><strong>Teléfono:</strong> <?= $persona['telefono'] ?></p>
        <p><strong>Dirección:</strong> <?= $persona['calle'] . " " . $persona['altura'] ?></p>
        <p><strong>Provincia:</strong> <?= $provincia ?></p>
        <p><strong>Departamento:</strong> <?= $departamento ?></p>
        <p><strong>Localidad:</strong> <?= $localidad ?></p>
        <p><strong>Municipio:</strong> <?= $municipio ?></p>
    </div>
</div>

<!-- Carrito -->
<div class="card mb-4">
    <div class="card-header">Productos</div>
    <div class="card-body">
        <?php if ($carrito): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                    $totalCarrito += $subtotal;
                ?>
                <tr>
                    <td><?= $item['nombre'] ?></td>
                    <td><?= $item['cantidad'] ?></td>
                    <td>$<?= number_format($item['precio'], 2, ',', '.') ?></td>
                    <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="fw-bold">Total: $<?= number_format($totalCarrito, 2, ',', '.') ?></p>
        <?php else: ?>
            <p>No hay productos en el carrito.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Pago -->
<div class="card mb-4">
    <div class="card-header">Forma de Pago</div>
    <div class="card-body">
        <?php if ($pago): ?>
            <p><strong>Tipo:</strong> <?= ucfirst($pago['tipo_pago']) ?></p>
            <?php if ($pago['tipo_pago'] === 'tarjeta'): ?>
                <p><strong>Tarjeta:</strong> <?= $pago['marca_tarjeta'] ?></p>
                <p><strong>Titular:</strong> <?= $pago['nombre_titular'] . " " . $pago['apellido_titular'] ?></p>
                <p><strong>DNI:</strong> <?= $pago['dni'] ?></p>
                <p><strong>Cuotas:</strong> <?= $pago['cuotas'] ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p>No se cargó información de pago.</p>
        <?php endif; ?>
    </div>
</div>

<form action="confirmar_compra.php" method="POST">
    <input type="hidden" name="total" value="<?= $totalCarrito ?>">
    <button type="submit" class="btn btn-success">Confirmar Compra</button>
</form>
</body>
</html>