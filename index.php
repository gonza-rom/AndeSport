<?php
//Iniciamos sesion
session_start();

//solicitar el archivo de conexion a la BD
include 'includes/conexion.php';

// Consulta para obtener productos destacados (por ejemplo, los 4 más vendidos o destacados manualmente)
$sqlDestacados = "SELECT id_producto, nombre, precio, img_producto FROM producto 
                  WHERE estado = 'activo' 
                  ORDER BY vendidos DESC -- O podés usar otro criterio, como destacado = true
                  LIMIT 10";

$resultadoDestacados = $conexion->query($sqlDestacados);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>AndeSport</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <style>
        .saludo {
            font-weight: bold;
            color: black;
            text-decoration: none;
        }

        .saludo:hover {
            color: antiquewhite;
            text-decoration: none;
        }
    </style>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

</head>

<body>
    <header>
        <div class="container">
            <p class="logo">AndeSport</p>

            <nav class="barra-navegacion">
                <div class="nav-saludo">
                    <?php if (isset($_SESSION['nombre'])): ?>
                        <?php if ($_SESSION['rol'] === 'cliente'): ?>
                            <li><a class="saludo" href="pages/interfaz-cliente.php">
                                    Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                </a></li>
                        <?php elseif ($_SESSION['rol'] === 'admin'): ?>
                            <li><a class="saludo" href="pages/administrador.php">
                                    Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                </a></li>
                        <?php elseif ($_SESSION['rol'] === 'gerente'): ?>
                            <li><a class="saludo" href="pages/gerente.php">
                                    Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                </a></li>
                        <?php elseif ($_SESSION['rol'] === 'repartidor'): ?>
                            <li><a class="saludo" href="pages/pedidos.php">
                                    Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                </a></li>
                        <?php elseif ($_SESSION['rol'] === 'stock'): ?>
                            <li><a class="saludo" href="pages/controlstock.php">
                                    Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                </a></li>
                        <?php else: ?>
                            <li><span class="saludo">
                                    Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                                </span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <ul class="menu">
                    <li><a href="pages/productos.php">Productos</a></li>
                    <li><a href="pages/sobrenosotros.php">Sobre Nosotros</a></li>
                    <li><a href="pages/contacto.php">Contacto</a></li>
                </ul>


                <div class="nav-sesion">
                    <?php if (isset($_SESSION['nombre'])): ?>
                        <li><a href="pages/logout.php">Cerrar sesión</a></li>
                    <?php else: ?>
                        <li><a href="pages/inicio-sesion.php">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <section id="section1">
        <h1>Vendemos los productos más destacados de las principales marcas nacionales e importadas. <br> Todo lo
            necesario para completar tu
            equipo de montaña, trekking, escalada o campamento.</h1>
        <form action="">
            <button>COMPRÁ YA!</button>
        </form>
    </section>

    <section id="destacados" class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4 text-center">Productos Destacados</h2>
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php while ($producto = $resultadoDestacados->fetch_assoc()): ?>
                        <div class="swiper-slide">
                            <div class="producto-card">
                                <img src="<?php echo htmlspecialchars($producto['img_producto']) ?: 'default.png'; ?>" alt="Producto">
                                <h5><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                                <a href="pages/detalle-producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn-comprar">Comprar</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <!-- Botones de navegación -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>


    <section id="section2">
        <div class="container">
            <div class="img-container"></div>
            <h2>Somos <span class="color-acento">AndeSport</span></h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maiores in quidem harum fugiat officia, quisquam
                porro voluptatem asperiores corporis accusamus odit excepturi magni reprehenderit atque velit,
                blanditiis molestiae culpa nostrum?</p>
        </div>
    </section>

    <section id="nuestros-productos">
        <div class="container">
            <h2>Tenemos todo lo necesario para tu aventura!</h2>
            <div class="productos">
                <div class="carta">
                    <h3>Equipamiento Trekking</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Earum optio impedit quis pariatur
                        corporis
                        eum maiores, provident eligendi quisquam nulla magnam. Laudantium ipsam aut cupiditate ex quia,
                        voluptatem eum pariatur!</p>
                    <a href="pages/productos.php"> <button>Ver productos</button></a>
                </div>
                <div class="carta">
                    <h3>Equipamiento Camping</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Earum optio impedit quis pariatur
                        corporis
                        eum maiores, provident eligendi quisquam nulla magnam. Laudantium ipsam aut cupiditate ex quia,
                        voluptatem eum pariatur!</p>
                    <a href="pages/productos.php"> <button>Ver productos</button></a>
                </div>
                <div class="carta">
                    <h3>Equipamiento Cocina</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Earum optio impedit quis pariatur
                        corporis
                        eum maiores, provident eligendi quisquam nulla magnam. Lauda ntium ipsam aut cupiditate ex quia,
                        voluptatem eum pariatur!</p>
                    <a href="pages/productos.php"> <button>Ver productos</button></a>
                </div>
            </div>
        </div>
    </section>

    <section id="reputacion">
        <div class="container">
            <ul>
                <li>➡Te aseguramos CALIDAD en los productos de nuestra tienda.</li>
                <li>➡Dedicamos mucho al embalaje para que llegue tu producto seguro.</li>
                <li>➡Nos enfocamos en la EXPERIENCIA DEL CLIENTE para mejorar dia a día!</li>
                <li>➡Ofrecemos asesoria personalizada.</li>
            </ul>
        </div>
    </section>

    <section id="trabaja-con-nosotros">
        <h2>Quieres trabajar con nosotros?</h2>
        <a href="pages/cv.php"><button>Dejá tu CV aquí!</button></a>
    </section>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; AndeSport</p>
            <p class="mb-0">Te aseguramos CALIDAD en cada producto, embalaje seguro, y una excelente experiencia de compra.</p>
            <ul class="list-inline mt-3">
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
            </ul>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                576: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                992: {
                    slidesPerView: 4,
                },
            },
        });
    </script>

</body>

</html>