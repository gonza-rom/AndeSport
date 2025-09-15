<?php
//Iniciamos sesion
session_start();

//solicitar el archivo de conexion a la BD
include '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Contacto | AndeSport</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>

    <header>
        <div class="container">
            <p class="logo">AndeSport</p>

            <nav class="barra-navegacion">

                <ul class="menu">

                    <div class="nav-saludo">
                        <?php if (isset($_SESSION['nombre'])): ?>
                            <?php if ($_SESSION['rol'] === 'cliente'): ?>
                                <li><a class="saludo" href="interfaz-cliente.php">
                                        Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                    </a></li>
                            <?php elseif ($_SESSION['rol'] === 'admin'): ?>
                                <li><a class="saludo" href="administrador.php">
                                        Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                    </a></li>
                            <?php elseif ($_SESSION['rol'] === 'gerente'): ?>
                                <li><a class="saludo" href="gerente.php">
                                        Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                    </a></li>
                            <?php elseif ($_SESSION['rol'] === 'repartidor'): ?>
                                <li><a class="saludo" href="pedidos.php">
                                        Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                    </a></li>
                            <?php elseif ($_SESSION['rol'] === 'stock'): ?>
                                <li><a class="saludo" href="controlstock.php">
                                        Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                    </a></li>
                            <?php else: ?>
                                <li><span class="saludo">
                                        Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                    </span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="productos.php">Productos</a></li>
                    <li><a href="sobrenosotros.php">Sobre Nosotros</a></li>

                    <div class="nav-sesion">
                        <?php if (isset($_SESSION['nombre'])): ?>
                            <a href="logout.php">Cerrar sesión</a>
                        <?php else: ?>
                            <a href="inicio-sesion.php">Iniciar Sesión</a>
                        <?php endif; ?>
                    </div>

                </ul>
            </nav>
        </div>
    </header>

    <section id="contacto" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Contacto</h2>
            <p class="text-center mb-5">¿Tienes alguna pregunta? ¡Estamos aquí para ayudarte! Completa el formulario y nos pondremos en contacto contigo a la brevedad.</p>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <form action="enviar_contacto.php" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje:</label>
                            <textarea id="mensaje" name="mensaje" rows="5" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary w-100">Enviar Mensaje</button>
                    </form>
                </div>

                <div class="col-lg-6">
                    <h3>Información de Contacto</h3>
                    <ul class="list-unstyled">
                        <li><strong>Teléfono:</strong> +54 123 456 7890</li>
                        <li><strong>Email:</strong> contacto@andesport.com</li>
                        <li><strong>Dirección:</strong> Calle Falsa 123, Ciudad de Montaña</li>
                    </ul>
                    <h4 class="mt-4">Síguenos en redes sociales</h4>
                    <div>
                        <a href="#" class="btn btn-outline-secondary btn-sm me-2">Facebook</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm me-2">Instagram</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm">Twitter</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>