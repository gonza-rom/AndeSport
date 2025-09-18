<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 1rem;
        }
        nav ul li {
            padding: 10px 20px;
            border-radius: 20px;
            background: #f1f1f1;
            cursor: pointer;
        }
        nav ul li.active {
            background: #ffc107;
            font-weight: bold;
        }
        .qty-btn {
            border: none;
            padding: 0.3rem 0.6rem;
            font-size: 1rem;
            cursor: pointer;
        }
    </style>
</head>
<body class="container mt-5">

<!-- Nav de pasos -->
<nav class="d-flex justify-content-center mb-4">
    <ul class="d-flex m-0">
        <li id="carrito" class="active"><p class="m-0">Carrito</p></li>
        <li id="datos"><p class="m-0">Datos</p></li>
        <li id="entrega"><p class="m-0">Entrega</p></li>
        <li id="pago"><p class="m-0">Pago</p></li>
        <li id="resumen"><p class="m-0">Resumen</p></li>
    </ul>
</nav>

<h1 class="mb-4">Tu Carrito</h1>

<?php if (!empty($_SESSION['carrito'])): ?>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>Producto</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $total = 0;
        foreach ($_SESSION['carrito'] as $index => $item): 
            $subtotal = $item['precio'] * $item['cantidad'];
            $total += $subtotal;
        ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td><img src="<?php echo htmlspecialchars($item['imagen']); ?>" width="50"></td>
                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                <td>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <form method="post" action="update-carrito.php" class="d-flex align-items-center">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="action" value="decrease" class="btn btn-sm btn-warning">-</button>
                            <span class="mx-2"><?php echo $item['cantidad']; ?></span>
                            <button type="submit" name="action" value="increase" class="btn btn-sm btn-primary">+</button>
                        </form>
                    </div>
                </td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
                <td><a href="eliminar-carrito.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Eliminar</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <h3>Total: $<?php echo number_format($total, 2); ?></h3>
        <div>
            <a href="productos.php" class="btn btn-secondary">← Volver a productos</a>
            <a href="datos.php" class="btn btn-success">Continuar →</a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">No tienes productos en el carrito.</div>
    <a href="productos.php" class="btn btn-primary">Ir a productos</a>
<?php endif; ?>

</body>
</html>
