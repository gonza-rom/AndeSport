<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/carrito.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    <!-- Header con navegación -->
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

    <!-- Contenido del Carrito -->
    <main class="container my-5">
        <h1 class="text-center mb-4">Carrito de Compras</h1>
        <div class="row">
            <!-- Lista de Productos -->
            <div class="col-md-8">
                <div class="list-group">
                    <!-- Producto Individual en el Carrito -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <img src="../img/calzado1.jpg" alt="Producto 1" class="img-thumbnail me-3" style="width: 80px;">
                        <div class="flex-grow-1">
                            <h5 class="mb-0">Producto 1</h5>
                            <p class="text-muted">Descripción breve del producto</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary me-2" >-</button>
                            <span class="me-2">1</span>
                            <button class="btn btn-outline-secondary me-3" >+</button>
                            <span class="fw-bold me-3">$120</span>
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Añadir más productos según sea necesario -->
                    <!-- Producto Individual en el Carrito -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <img src="../img/cocina2.jpg" alt="Producto 1" class="img-thumbnail me-3" style="width: 80px;">
                        <div class="flex-grow-1">
                            <h5 class="mb-0">Producto 1</h5>
                            <p class="text-muted">Descripción breve del producto</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary me-2" >-</button>
                            <span class="me-2">1</span>
                            <button class="btn btn-outline-secondary me-3" >+</button>
                            <span class="fw-bold me-3">$120</span>
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Producto Individual en el Carrito -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <img src="../img/carpa2.jpg" alt="Producto 1" class="img-thumbnail me-3" style="width: 80px;">
                        <div class="flex-grow-1">
                            <h5 class="mb-0">Producto 1</h5>
                            <p class="text-muted">Descripción breve del producto</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary me-2" >-</button>
                            <span class="me-2">1</span>
                            <button class="btn btn-outline-secondary me-3" >+</button>
                            <span class="fw-bold me-3">$120</span>
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen del Carrito -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resumen de la Compra</h5>
                        <p class="card-text">Subtotal: <span id="subtotal">$360</span></p>
                        <p class="card-text">Envío: <span id="envio">$20</span></p>
                        <hr>
                        <h4>Total: <span id="total">$380</span></h4>
                        <button class="btn btn-secondary w-100 mt-3" >Vaciar Carrito</button>
                        <button class="btn btn-warning w-100 mt-3" onclick="window.location.href='pago.php'">Proceder al pago</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
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

    <!-- Bootstrap JS y Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>   
</body>
</html>
