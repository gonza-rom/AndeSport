<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: inicio-sesion.php");
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pago = $_POST['tipo_pago'];
    $tarjeta = $_POST['tarjeta'] ?? '';
    $mes = $_POST['mes'] ?? '';
    $anio = $_POST['anio'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $nombre_titular = trim($_POST['nombre_titular'] ?? '');
    $apellido_titular = trim($_POST['apellido_titular'] ?? '');
    $dni = $_POST['dni'] ?? '';
    $marca_tarjeta = $_POST['marca_tarjeta'] ?? '';
    $cuotas = $_POST['cuotas'] ?? '';

    // Validaciones
    if (!in_array($tipo_pago, ['tarjeta', 'efectivo'])) {
        $errores[] = "Seleccione un tipo de pago válido.";
    }

    if ($tipo_pago === 'tarjeta') {
        if (!preg_match('/^\d{16}$/', $tarjeta)) {
            $errores[] = "Número de tarjeta inválido. Debe tener 16 dígitos.";
        }
        if (!preg_match('/^(0[1-9]|1[0-2])$/', $mes)) {
            $errores[] = "Mes de vencimiento inválido.";
        }
        if (!preg_match('/^\d{2}$/', $anio)) {
            $errores[] = "Año de vencimiento inválido (dos dígitos).";
        }
        if (!preg_match('/^\d{3}$/', $codigo)) {
            $errores[] = "Código de seguridad inválido. Debe tener 3 dígitos.";
        }
        if (empty($nombre_titular) || empty($apellido_titular)) {
            $errores[] = "Debe ingresar nombre y apellido del titular.";
        }
        if (!preg_match('/^\d{7,9}$/', $dni)) {
            $errores[] = "DNI inválido. Solo números (7 a 9 dígitos).";
        }
        if (!in_array($marca_tarjeta, ['Visa', 'Mastercard'])) {
            $errores[] = "Debe seleccionar una tarjeta válida.";
        }
        if (!in_array($cuotas, ['3','6','9','12'])) {
            $errores[] = "Seleccione una cantidad de cuotas válida.";
        }
    }

    if (empty($errores)) {
        // Guardar en sesión
        $_SESSION['pago'] = [
            'tipo_pago' => $tipo_pago,
            'tarjeta' => $tarjeta,
            'mes' => $mes,
            'anio' => $anio,
            'codigo' => $codigo,
            'nombre_titular' => $nombre_titular,
            'apellido_titular' => $apellido_titular,
            'dni' => $dni,
            'marca_tarjeta' => $marca_tarjeta,
            'cuotas' => $cuotas
        ];
        header("Location: resumen.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<nav class="d-flex justify-content-between flex-column flex-md-row mb-4">
    <ul class="d-flex m-0 list-unstyled gap-3">
        <li id="carrito"><p>Carrito</p></li>
        <li id="datos"><p>Datos</p></li>
        <li id="entrega"><p>Entrega</p></li>
        <li id="pago" class="active"><p><strong>Pago</strong></p></li>
        <li id="resumen"><p>Resumen</p></li>
    </ul>
</nav>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">Tipo de Pago</div>
    <div class="card-body">
        <?php if ($errores): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tipo de pago</label>
                <select name="tipo_pago" class="form-select" required onchange="toggleTarjeta(this.value)">
                    <option value="">Seleccione</option>
                    <option value="efectivo" <?php if(!empty($_POST['tipo_pago']) && $_POST['tipo_pago']=='efectivo') echo 'selected'; ?>>Efectivo</option>
                    <option value="tarjeta" <?php if(!empty($_POST['tipo_pago']) && $_POST['tipo_pago']=='tarjeta') echo 'selected'; ?>>Tarjeta</option>
                </select>
            </div>

            <div id="tarjetaFields" style="display: <?php echo (!empty($_POST['tipo_pago']) && $_POST['tipo_pago']=='tarjeta') ? 'block' : 'none'; ?>;">
                <div class="mb-3">
                    <label class="form-label">Número de tarjeta</label>
                    <input type="text" name="tarjeta" class="form-control" maxlength="16" value="<?php echo $_POST['tarjeta'] ?? ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de vencimiento</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="mes" class="form-control" maxlength="2" placeholder="MM" style="max-width: 80px;" value="<?php echo $_POST['mes'] ?? ''; ?>">
                        <input type="text" name="anio" class="form-control" maxlength="2" placeholder="AA" style="max-width: 80px;" value="<?php echo $_POST['anio'] ?? ''; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Código de seguridad (CVV)</label>
                    <input type="password" name="codigo" class="form-control" maxlength="3" value="<?php echo $_POST['codigo'] ?? ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre del titular</label>
                    <input type="text" name="nombre_titular" class="form-control" value="<?php echo $_POST['nombre_titular'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellido del titular</label>
                    <input type="text" name="apellido_titular" class="form-control" value="<?php echo $_POST['apellido_titular'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">DNI del titular</label>
                    <input type="text" name="dni" class="form-control" maxlength="9" value="<?php echo $_POST['dni'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Marca de la tarjeta</label>
                    <select name="marca_tarjeta" class="form-select">
                        <option value="">Seleccione</option>
                        <option value="Visa" <?php if(!empty($_POST['marca_tarjeta']) && $_POST['marca_tarjeta']=='Visa') echo 'selected'; ?>>Visa Online</option>
                        <option value="Mastercard" <?php if(!empty($_POST['marca_tarjeta']) && $_POST['marca_tarjeta']=='Mastercard') echo 'selected'; ?>>Mastercard Online</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad de cuotas</label>
                    <select name="cuotas" class="form-select">
                        <option value="">Seleccione</option>
                        <?php foreach ([3,6,9,12] as $op): ?>
                            <option value="<?php echo $op; ?>" <?php if(!empty($_POST['cuotas']) && $_POST['cuotas']==$op) echo 'selected'; ?>><?php echo $op; ?> cuotas</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Continuar</button>
            <a href="entrega.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</div>

<script>
function toggleTarjeta(value) {
    document.getElementById('tarjetaFields').style.display = value === 'tarjeta' ? 'block' : 'none';
}
</script>

</body>
</html>
