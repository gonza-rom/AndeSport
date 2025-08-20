<?php
//Iniciamos sesion
session_start();

//solicitar el archivo de conexion a la BD
include '../includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="../styles/productos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
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
                    <li><a href="sobrenosotros.php">Sobre Nosotros</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>

                <div class="nav-sesion">
                    <?php if (isset($_SESSION['nombre'])): ?>
                        <a href="logout.php">Cerrar sesión</a>
                    <?php else: ?>
                        <a href="inicio-sesion.php">Iniciar Sesión</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Controles de paginación -->
    <main class="container mt-5">
        <h1 class="text-center mb-4">Catálogo de Productos</h1>
        <nav aria-label="Paginación de productos" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled" id="prev-page">
                    <a class="page-link text-danger" href="#" tabindex="-1">Anterior</a>
                </li>
                <!-- Los números de página serán generados dinámicamente -->
                <li class="page-item" id="next-page">
                    <a class="page-link text-danger" href="#">Siguiente</a>
                </li>
            </ul>
        </nav>
        <div class="d-flex justify-content-center">
            <input type="text" id="search-bar" class="form-control w-50" placeholder="Buscar producto por nombre...">
        </div>

        <div class="d-flex justify-content-center mt-4">
            <!-- Filtros a la izquierda -->
            <form method="GET" class="filters bg-white p-4 rounded me-4" style="width: 200px;">
                <h2>Filtros</h2>

                <div class="mb-3">
                    <label for="category">Categoría</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">Todos</option>
                        <option value="Ropa" <?= (isset($_GET['category']) && $_GET['category'] == 'Ropa') ? 'selected' : '' ?>>Ropa</option>
                        <option value="Accesorios" <?= (isset($_GET['category']) && $_GET['category'] == 'Accesorios') ? 'selected' : '' ?>>Accesorios</option>
                        <option value="Calzado" <?= (isset($_GET['category']) && $_GET['category'] == 'Calzado') ? 'selected' : '' ?>>Calzado</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning w-100">Aplicar Filtros</button>
            </form>

        <!-- Productos a la derecha -->
        <section class="product-list">
            <!-- Aquí irán las tarjetas de los productos -->
            <?php
            // procesar filtro
            $categoriaFiltro = '';
            if (isset($_GET['category']) && $_GET['category'] !== '') {
                $categoriaFiltro = strtolower(trim($_GET['category']));
            }

            // armar la consulta
            $sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.img_producto, c.nombre_categoria
                    FROM producto p
                    JOIN categorias c ON p.id_categoria = c.id_categoria";

            if ($categoriaFiltro !== '') {
                $categoriaFiltroSQL = $conexion->real_escape_string($categoriaFiltro);
                $sql .= " WHERE LOWER(c.nombre_categoria) = '$categoriaFiltroSQL'";
            }

            $resultado = $conexion->query($sql);

            if ($resultado && $resultado->num_rows > 0):
                while ($row = $resultado->fetch_assoc()):
                    $id = $row['id_producto'];
                    $nombre = htmlspecialchars($row['nombre']);
                    $descripcion = htmlspecialchars($row['descripcion']);
                    $precio = number_format($row['precio'], 2);
                    $categoria = htmlspecialchars(strtolower($row['nombre_categoria']));
                    $img = !empty($row['img_producto']) ? htmlspecialchars($row['img_producto']) : 'default.png';
            ?>
                <div class="product-card" data-category="<?php echo $categoria; ?>">
                    <a href="detalle-producto.php?id=<?php echo $id; ?>">
                        <img src="../<?php echo $img; ?>" alt="<?php echo $nombre; ?>">
                        <h3><?php echo $nombre; ?></h3>
                        <p>$<?php echo $precio; ?></p>
                        <button>Añadir al carrito</button>
                    </a>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <p class="text-center">No hay productos disponibles.</p>
            <?php
            endif;
            ?>
            <!-- Añadir más productos según sea necesario -->
        </section>
    </main>

    <!-- Footer -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const priceInput = document.getElementById('price');
        const priceValue = document.getElementById('price-value');

        priceInput.addEventListener('input', () => {
            priceValue.textContent = priceInput.value;
        });

        document.addEventListener('DOMContentLoaded', () => {
    const products = Array.from(document.querySelectorAll('.product-card'));
    const productsPerPage = 6; // Número de productos por página
    let currentPage = 1;
    const totalPages = Math.ceil(products.length / productsPerPage);

    const paginationContainer = document.querySelector('.pagination');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');

    // Función para generar los números de página
    const generatePagination = () => {
        // Eliminar todos los números de página actuales
        const pageNumbers = paginationContainer.querySelectorAll('.page-item.number');
        pageNumbers.forEach(page => page.remove());

        // Generar nuevos números de página
        for (let i = 1; i <= totalPages; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item number ${i === currentPage ? 'active' : ''}`;
            pageItem.innerHTML = <a class="page-link text-danger" href="#">${i}</a>;
            pageItem.addEventListener('click', () => {
                currentPage = i;
                updatePagination();
            });
            paginationContainer.insertBefore(pageItem, nextPageBtn);
        }
    };

    // Función para actualizar la paginación
    const updatePagination = () => {
        // Mostrar solo los productos de la página actual
        products.forEach((product, index) => {
            const startIndex = (currentPage - 1) * productsPerPage;
            const endIndex = startIndex + productsPerPage;

            product.style.display = index >= startIndex && index < endIndex ? 'block' : 'none';
        });

        // Actualizar botones "Anterior" y "Siguiente"
        prevPageBtn.classList.toggle('disabled', currentPage === 1);
        nextPageBtn.classList.toggle('disabled', currentPage === totalPages);

        // Actualizar la clase activa de los números de página
        generatePagination();
    };

    // Eventos para los botones "Anterior" y "Siguiente"
    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    });

    nextPageBtn.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
        }
    });

    // Inicializar la paginación
    generatePagination();
    updatePagination();
});


        // Elementos HTML
        const categoryFilter = document.getElementById('category');
        const applyFiltersButton = document.getElementById('apply-filters');
        const productCards = document.querySelectorAll('.product-card');

        // Evento para aplicar los filtros
        applyFiltersButton.addEventListener('click', () => {
            const selectedCategory = categoryFilter.value; // Obtiene la categoría seleccionada
            
            // Recorre todas las tarjetas de productos
            productCards.forEach(card => {
                const productCategory = card.getAttribute('data-category'); // Obtiene la categoría de cada tarjeta
                
                // Muestra u oculta la tarjeta según la categoría seleccionada
                if (selectedCategory === 'todos' || productCategory === selectedCategory) {
                    card.style.display = 'block'; // Muestra el producto
                } else {
                    card.style.display = 'none'; // Oculta el producto
                }
            });
        });
    </script>
</body>
</html>