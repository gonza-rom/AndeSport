<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Producto</title>
    <link rel="stylesheet" href="../styles/productos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:wght@400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <p class="logo">AndeSport</p>
        </div>
    </header>
    
    <main class="container mt-5">
        <button><a href="administrador.php">Volver al panel de Administrador</a></button>
        <h1 class="text-center mb-4">Nuevo Producto</h1>

        <form class="bg-white p-4 rounded" action="procesar_producto.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombreProducto" class="form-label">Nombre del Producto</label>
                <input type="text" id="nombreProducto" name="nombreProducto" class="form-control" placeholder="Nombre del producto" required>
            </div>

            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select id="categoria" name="categoria" class="form-select" required>
                    <option value="ropa">Ropa</option>
                    <option value="accesorios">Accesorios</option>
                    <option value="calzado">Calzado</option>
                    <option value="otros">Otros</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" id="precio" name="precio" class="form-control" placeholder="Precio en USD" min="1" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del Producto</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe el producto..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="imagenProducto" class="form-label">Subir Imagen</label>
                <input type="file" id="imagenProducto" name="imagenProducto" class="form-control" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" id="marca" name="marca" class="form-control" placeholder="Marca del producto" required>
            </div>

            <!-- Nueva sección para el punto de reposición -->
            <div class="mb-3">
                <label for="reposicion" class="form-label">Punto de Reposición</label>
                <div class="input-group">
                    <input type="text" id="reposicion" name="reposicion" class="form-control" placeholder="Selecciona un punto de reposición" readonly required>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#mapModal">
                        <i class="fas fa-map-marker-alt"></i> Seleccionar Ubicación
                    </button>
                </div>
                <!-- Mapa en miniatura -->
                <div id="mapaContainer" class="mt-3" style="display: none;">
                    <iframe id="mapIframe" width="300" height="200" frameborder="0" style="border:0;" allowfullscreen></iframe>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Subir Producto</button>
            </div>
        </form>
    </main>

    <!-- Modal para seleccionar el punto en el mapa -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">Seleccionar Punto de Reposición</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="mapModalIframe" width="100%" height="400" frameborder="0" style="border:0;" allowfullscreen></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="guardarUbicacion">Seleccionar Ubicación</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; AndeSport</p>
            <ul class="list-inline mt-3">
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
            </ul>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables para manejar el mapa
        const mapaModalIframe = document.getElementById('mapModalIframe');
        const mapaIframe = document.getElementById('mapIframe');
        const reposicionInput = document.getElementById('reposicion');
        const mapaContainer = document.getElementById('mapaContainer');
        const guardarUbicacionBtn = document.getElementById('guardarUbicacion');

        // Iniciar el mapa en el modal
        function initMap() {
            const defaultLocation = "Buenos Aires, Argentina"; // Ubicación por defecto
            mapaModalIframe.src = `https://www.google.com/maps/embed/v1/place?key=AIzaSyAA46K7kuviWb9mZseP7ulN6285zMUcEXM&q=${defaultLocation}`;
        }

        // Llamada al modal para inicializar el mapa
        $('#mapModal').on('show.bs.modal', function () {
            initMap();
        });

        // Acción al seleccionar una ubicación
        guardarUbicacionBtn.addEventListener('click', function() {
            const location = "Buenos Aires, Argentina"; // Ubicación seleccionada (se puede cambiar por coordenadas específicas)
            
            // Actualizar el campo de reposición y el mapa en miniatura
            reposicionInput.value = location;
            mapaIframe.src = `https://www.google.com/maps/embed/v1/place?key=AIzaSyAA46K7kuviWb9mZseP7ulN6285zMUcEXM&q=${location}`;
            mapaContainer.style.display = 'block';

            // Cerrar el modal
            $('#mapModal').modal('hide');
        });
    </script>
</body>
</html>
