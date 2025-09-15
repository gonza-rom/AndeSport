<?php
include '../includes/conexion.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirigir si no se pasa un id válido
    header('Location: productos.php');
    exit();
}

$id = intval($_GET['id']);

// Consultar el producto
$sql = "SELECT p.*, c.nombre_categoria 
        FROM producto p
        JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.id_producto = $id AND p.estado = 'activo'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows === 0) {
    echo "<p>Producto no encontrado.</p>";
    exit();
}

$producto = $resultado->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto - AndeSport</title>
    <link rel="stylesheet" href="../styles/detalle-producto.css"> <!-- Asegúrate de vincular tu archivo CSS -->

    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <h1>AndeSport</h1>
        <nav>
            <a href="../index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="sobre-nosotros.php">Sobre Nosotros</a>
            <a href="contacto.php">Contacto</a>
            <a href="login.php">Iniciar Sesión</a>
        </nav>
    </header>

    <div class="ui-pdp-container__row ui-vip-grouped-header ui-vip-grouped-header__header-store">
        <div class="ui-pdp-breadcrumb">
            <a class="ui-pdp-breadcrumb__link" href="productos.php">Volver al listado</a>
            <nav aria-label="Breadcrumb">
                <ol class="andes-breadcrumb">
                    <li class="andes-breadcrumb__item">
                        <a class="andes-breadcrumb__link"
                            href="productos.php?category=<?php echo urlencode($producto['nombre_categoria']); ?>"
                            title="<?php echo htmlspecialchars($producto['nombre_categoria']); ?>">
                            <?php echo htmlspecialchars($producto['nombre_categoria']); ?>
                        </a>
                        <div class="andes-breadcrumb__chevron" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="6" height="8">
                                <path fill="none" stroke="#666" d="M1 0l4 4-4 4"></path>
                            </svg>
                        </div>
                    </li>
                    <li class="andes-breadcrumb__item">
                        <?php echo htmlspecialchars($producto['nombre']); ?>
                    </li>
                </ol>
            </nav>
        </div>


    </div>

    <main>
        <section class="producto-detalle">
            <div class="imagen-producto">
                <img src="../<?php echo htmlspecialchars($producto['img_producto']) ?: 'default.png'; ?>" alt="Imagen del Producto" />
            </div>
            <div class="info-producto">
                <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                <p class="descripcion"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                <ul class="caracteristicas">
                    <li>Marca: Marca del producto</li>
                    <li>Modelo: Modelo del producto</li>
                    <li>Tamaño: Talla o tamaño</li>
                    <li>Color: Color del producto</li>
                    <li>Material: Material del producto</li>
                </ul>
                <a href="carrito.php?agregar=<?php echo $producto['id_producto']; ?>">
                    <button class="btn-agregar-carrito">Añadir al carrito</button>
                </a>

            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 AndeSport. Todos los derechos reservados.</p>
    </footer>
</body>

</html>