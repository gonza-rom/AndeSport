<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceder al Pago - AndeSport</title>
    <link rel="stylesheet" href="../styles/carrito.css"> <!-- Asegúrate de que la ruta sea correcta -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<header>
    <div class="container d-flex justify-content-between align-items-center">
        <p class="logo">AndeSport</p>
        <nav>
            <a href="../index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="sobrenosotros.php">Sobre Nosotros</a>
            <a href="contacto.php">Contacto</a>
            <a href="inicio-sesion.php">Iniciar Sesión</a>
        </nav>
        
    </div>
</header>

<main class="container mt-4">
    <h1>Proceder al Pago</h1>

    <div class="row">
        <div class="col-md-8">
            <h2>Detalles de Envío</h2>
            <form>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección de Envío</label>
                    <input type="text" class="form-control" id="direccion" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" required>
                </div>
            </form>

            <h2>Método de Pago</h2>
            <form>
                <div class="mb-3">
                    <label for="tarjeta" class="form-label">Número de Tarjeta</label>
                    <input type="text" class="form-control" id="tarjeta" required>
                </div>
                <div class="mb-3">
                    <label for="fecha-expiracion" class="form-label">Fecha de Expiración</label>
                    <input type="text" class="form-control" id="fecha-expiracion" placeholder="MM/AA" required>
                </div>
                <div class="mb-3">
                    <label for="cvv" class="form-label">CVV</label>
                    <input type="text" class="form-control" id="cvv" required>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <h2>Resumen de la Compra</h2>
            <div class="list-group">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    Producto 1
                    <span>$120.00</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    Producto 2
                    <span>$120.00</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    Producto 1
                    <span>$120.00</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    Envio
                    <span>$20</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Total</strong>
                    <strong>$380.00</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-warning w-100 mt-3" onclick="window.location.href='pago-exitoso.php'">Proceder al pago</button>
    </div>
</main>
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-0">&copy; AndeSport</p>
        <p class="mb-0">Te aseguramos CALIDAD en cada producto, embalaje seguro, y una excelente experiencia de compra.</p>
        <ul class="list-inline mt-3">
            <li class="list-inline-item"><a href="#" class="text-white">Facebook</a></li>
            <li class="list-inline-item"><a href="#" class="text-white">Instagram</a></li>
            <li class="list-inline-item"><a href="#" class="text-white">Twitter</a></li>
        </ul>
    </div>
</footer>

</body>
</html>
