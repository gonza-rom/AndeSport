<?php
//Iniciamos sesion
session_start();

//solicitar el archivo de conexion a la BD
include '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sobre Nosotros | AndeSport</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>

    <!-- Header con el menú de navegación -->
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
                    <li><a href="contacto.php">Contacto</a></li>

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

    <!-- Sección de Sobre Nosotros -->
    <section id="about" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Sobre Nosotros</h2>
            <p class="text-center mb-5">Somos una empresa apasionada por la aventura y la naturaleza, dedicada a ofrecer los mejores productos para tus expediciones al aire libre.</p>

            <div class="row">
                <!-- Historia de la empresa -->
                <div class="col-lg-6 mb-4">
                    <h3>Nuestra Historia</h3>
                    <p>AndeSport nació con la visión de ser una tienda especializada en productos de alta calidad para actividades al aire libre. Desde nuestra fundación, nos hemos esforzado por ofrecer productos seleccionados cuidadosamente para nuestros clientes aventureros. Nos apasiona el deporte, la aventura y la exploración, y esa pasión se refleja en cada uno de nuestros productos.</p>
                </div>

                <!-- Valores de la empresa -->
                <div class="col-lg-6 mb-4">
                    <h3>Nuestros Valores</h3>
                    <ul class="list-unstyled">
                        <li><strong>Calidad:</strong> Cada producto que ofrecemos está diseñado para durar y brindar una experiencia óptima en cada aventura.</li>
                        <li><strong>Compromiso:</strong> Estamos comprometidos con nuestros clientes y su satisfacción es nuestra prioridad.</li>
                        <li><strong>Responsabilidad:</strong> Nos preocupamos por el medio ambiente y promovemos el uso responsable de los recursos naturales.</li>
                    </ul>
                </div>
            </div>

            <!-- Equipo y filosofía de trabajo -->
            <div class="row mt-5">
                <div class="col-lg-6 mb-4">
                    <h3>Nuestro Equipo</h3>
                    <p>Contamos con un equipo profesional y apasionado, dedicado a atenderte y guiarte en la elección del equipo perfecto para tus aventuras. Nuestros colaboradores son expertos en deportes al aire libre, trekking, escalada y actividades de campamento.</p>
                </div>

                <div class="col-lg-6 mb-4">
                    <h3>Nuestra Filosofía</h3>
                    <p>En AndeSport creemos que cada experiencia al aire libre debe ser única y significativa. Nos esforzamos por hacer de tu viaje algo inolvidable con productos confiables y de calidad que te acompañen en cada paso.</p>
                </div>
            </div>
        </div>
    </section>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>