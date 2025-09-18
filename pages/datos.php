<?php
session_start();

//solicitar el archivo de conexion a la BD
include '../includes/conexion.php';

// Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    // Guardar a dónde quería entrar antes de loguearse
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Datos del Cliente</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container mt-5">

    <nav class="d-flex justify-content-between flex-column flex-md-row mb-4">
        <ul class="d-flex m-0 list-unstyled gap-3">
            <li id="carrito"><p>Carrito</p></li>
            <li id="datos" class="active"><p><strong>Datos</strong></p></li>
            <li id="entrega"><p>Entrega</p></li>
            <li id="pago"><p>Pago</p></li>
            <li id="resumen"><p>Resumen</p></li>
        </ul>
    </nav>

    <div class="alert alert-warning">
        Debes iniciar sesión para continuar con tu compra.
    </div>

    <a href="inicio-sesion.php" class="btn btn-primary">Iniciar Sesión</a>
    <a href="registro-persona.php" class="btn btn-secondary">Registrarme</a>

    </body>
    </html>
    <?php
    exit;
}

// Si está logueado, buscar datos en la tabla persona
$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT 
            p.id_persona,
            p.nombre,
            p.apellido,
            p.dni,
            p.fecha_nacimiento,
            p.genero,
            p.calle,
            p.altura,
            prov.nomprov AS provincia,
            dpto.nomdpto AS departamento,
            mun.nommun AS municipio,
            loc.nomloc AS localidad
        FROM persona p
        LEFT JOIN provincias prov ON p.provincia = prov.codprov
        LEFT JOIN dpto dpto ON p.departamento = dpto.coddpto
        LEFT JOIN municipio mun ON p.municipio = mun.codmun
        LEFT JOIN localidades loc ON p.localidad = loc.codloc
        WHERE p.id_persona = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$persona = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos del Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<nav class="d-flex justify-content-between flex-column flex-md-row mb-4">
    <ul class="d-flex m-0 list-unstyled gap-3">
        <li id="carrito"><p>Carrito</p></li>
        <li id="datos" class="active"><p><strong>Datos</strong></p></li>
        <li id="entrega"><p>Entrega</p></li>
        <li id="pago"><p>Pago</p></li>
        <li id="resumen"><p>Resumen</p></li>
    </ul>
</nav>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-success text-white py-2 px-3" style="font-weight: 600; font-size: 1rem;">
        Datos ingresados previamente
    </div>
    <div class="card-body py-2 px-3">
        <?php if ($persona): ?>
            <div class="row g-2">
                <div class="col-6"><strong>Nombre:</strong><br><?php echo htmlspecialchars($persona['nombre']); ?></div>
                <div class="col-6"><strong>Apellido:</strong><br><?php echo htmlspecialchars($persona['apellido']); ?></div>
                <div class="col-6"><strong>DNI:</strong><br><?php echo htmlspecialchars($persona['dni']); ?></div>
                <div class="col-6"><strong>Fecha Nac.:</strong><br><?php echo htmlspecialchars($persona['fecha_nacimiento']); ?></div>
                <div class="col-12"><strong>Dirección:</strong><br><?php echo htmlspecialchars($persona['calle'] . ' ' . $persona['altura']); ?></div>
                <div class="col-6"><strong>Provincia:</strong><br><?php echo htmlspecialchars($persona['provincia']); ?></div>
                <div class="col-6"><strong>Departamento:</strong><br><?php echo htmlspecialchars($persona['departamento']); ?></div>
                <div class="col-6"><strong>Municipio:</strong><br><?php echo htmlspecialchars($persona['municipio']); ?></div>
                <div class="col-12"><strong>Localidad:</strong><br><?php echo htmlspecialchars($persona['localidad']); ?></div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No encontramos tus datos. Por favor completa tu registro.</div>
            <a href="registro-persona.php" class="btn btn-primary">Completar mis datos</a>
        <?php endif; ?>
    </div>
</div>

<a href="productos.php" class="btn btn-secondary">Volver a productos</a>
<a href="entrega.php" class="btn btn-primary">Continuar</a>

</body>
</html>
