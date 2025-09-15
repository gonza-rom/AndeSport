<?php
session_start();
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin', 'stock'])) {
    header("Location: ../index.php");
    exit();
}

include '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stock.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <header class="p-3 mb-3 border-bottom bg-dark">
        <div class="container d-flex align-items-center justify-content-between text-light">
            <h1 class="logo">AndeSport Stock</h1>
            <?php if (isset($_SESSION['nombre'])): ?>
                <span class="logo">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <?php endif; ?>
            <button class="btn btn-warning" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
        </div>
    </header>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus"></i> Agregar Producto
        </button>
    </div>

    <main class="container my-4">
        <h2 class="mb-4">Inventario</h2>
        <div class="table-responsive mb-4">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Descripción</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <?php
                    $sql = "SELECT p.id_producto, p.nombre, p.precio, p.stock, p.descripcion, c.nombre_categoria AS categoria
                            FROM producto p
                            JOIN categorias c ON p.id_categoria = c.id_categoria
                            WHERE p.estado = 'activo'";

                    $resultado = $conexion->query($sql);

                    if ($resultado->num_rows > 0) {
                        while ($fila = $resultado->fetch_assoc()) {
                            $stock_bajo = $fila['stock'] <= 5 ? '<span class="badge bg-danger">Bajo</span>' : '';
                            echo "<tr>
                                   <td>{$fila['id_producto']}</td>
                                   <td>{$fila['nombre']}</td>
                                   <td>{$fila['categoria']}</td>
                                   <td>$ {$fila['precio']}</td>
                                   <td>{$fila['descripcion']}</td>
                                   <td class='text-danger'>{$fila['stock']} $stock_bajo</td>
                                   <td>
                                     <a href='../pages/admin-acciones/editar-stock.php?id={$fila['id_producto']}' class='btn btn-warning btn-sm'>
                                       <i class='fas fa-box'></i> Editar Stock
                                     </a>
                                   </td>
                                 </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay productos cargados.</td></tr>";
                    }
                    ?>
                </tbody>

                </tbody>
            </table>
        </div>

        <h2 class="mb-4">Historial de Movimientos</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID Producto</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Fecha de Venta</th>
                        <th>Cantidad Vendida</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT h.id_producto, h.motivo, p.nombre, c.nombre_categoria AS categoria, h.fecha, h.cantidad
                            FROM historial_stock h
                            JOIN producto p ON h.id_producto = p.id_producto
                            JOIN categorias c ON p.id_categoria = c.id_categoria
                            ORDER BY h.fecha DESC
                            LIMIT 20";

                    $res = $conexion->query($sql);

                    if ($res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id_producto']}</td>
                                    <td>{$row['nombre']}</td>
                                    <td>{$row['categoria']}</td>
                                    <td>{$row['fecha']}</td>
                                    <td>{$row['cantidad']}</td>
                                    <td>{$row['motivo']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Sin movimientos registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para agregar producto -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../pages/admin-acciones/agregar-producto.php" method="POST">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="productName" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Categoría</label>
                            <select class="form-select" id="productCategory" name="id_categoria" required>
                                <option disabled selected>Seleccione una categoría</option>
                                <option value="1">Ropa</option>
                                <option value="2">Accesorios</option>
                                <option value="3">Calzado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="productPrice" name="precio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" id="productStock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" name="descripcion" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>