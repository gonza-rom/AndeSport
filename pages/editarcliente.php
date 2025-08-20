<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido</title>
    <link rel="stylesheet" href="../styles/editar.css">
</head>
<body>
    <div class="edit-product-page">
        <h1>Editar Cliente</h1>

        <!-- Formulario de edición de producto -->
        <form class="edit-product-form" action="/PagEditarStock" method="post">
            <label for="nombre">Nombre del cliente:</label>
            <input type="text" id="nombre" name="nombre" value="Nombre del Cliente" required>

            <a href="../pages/administrador.php"><button type="button" class="boton">Guardar Cambios</button></a>
            <button type="button" onclick="window.history.back()">Cancelar</button>
        </form>
    </div>
</body>
</html>