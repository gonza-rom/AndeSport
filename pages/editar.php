<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../styles/editar.css">
</head>
<body>
    <div class="edit-product-page">
        <h1>Editar Producto</h1>

        <!-- Formulario de edición de producto -->
        <form class="edit-product-form" action="/PagEditarStock" method="post">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" value="Nombre del Producto" required>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" min="0" step="0.01" value="0.00" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required>Descripción del producto</textarea>

            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria" required>
                <option value="calzado">Calzado</option>
                <option value="carpas">Carpas</option>
                <option value="cocina">Cocina</option>
                <option value="mochilas">Mochilas</option>
            </select>

            <label for="imagen">Imagen del Producto:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">

            <a href="../pages/administrador.php"><button type="button" class="boton">Guardar Cambios</button></a>
            <button type="button" onclick="window.history.back()">Cancelar</button>
        </form>
    </div>
</body>
</html>
