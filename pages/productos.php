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
                            <li><a class="saludo" href="interfaz-cliente.php">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a></li>
                        <?php elseif ($_SESSION['rol'] === 'admin'): ?>
                            <li><a class="saludo" href="administrador.php">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a></li>
                        <?php elseif ($_SESSION['rol'] === 'gerente'): ?>
                            <li><a class="saludo" href="gerente.php">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a></li>
                        <?php elseif ($_SESSION['rol'] === 'repartidor'): ?>
                            <li><a class="saludo" href="pedidos.php">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a></li>
                        <?php elseif ($_SESSION['rol'] === 'stock'): ?>
                            <li><a class="saludo" href="controlstock.php">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></a></li>
                        <?php else: ?>
                            <li><span class="saludo">Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span></li>
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
    
    <main class="container mt-5">
        <h1 class="text-center mb-4">Catálogo de Productos</h1>

        <!-- Barra de búsqueda -->
        <div class="d-flex justify-content-center mb-4">
            <input type="text" id="search-bar" class="form-control w-50" placeholder="Buscar producto por nombre...">
        </div>
         <!-- Carrito flotante -->
        <div class="cart-icon position-fixed top-0 end-0 m-4" style="cursor:pointer; z-index:1050;">
            <i class="fas fa-shopping-cart fa-2x"></i>
            <div class="cart-count" id="cart-count">0</div>
        </div>               
        <div class="d-flex">
            <!-- Filtros -->
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

            <!-- Lista de productos -->
            <section class="product-list d-flex flex-wrap gap-3">
                <?php
                $categoriaFiltro = '';
                if (isset($_GET['category']) && $_GET['category'] !== '') {
                    $categoriaFiltro = strtolower(trim($_GET['category']));
                }

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
                        $precio = $row['precio']; // valor crudo
                        $categoria = htmlspecialchars(strtolower($row['nombre_categoria']));
                        $img = !empty($row['img_producto']) ? htmlspecialchars($row['img_producto']) : 'default.png';
                ?>
                    <div class="product-card" 
                        data-id="<?= $id ?>" 
                        data-nombre="<?= $nombre ?>" 
                        data-precio="<?= $precio ?>" 
                        data-img="../<?= $img ?>">
                        <img src="../<?= $img ?>" alt="<?= $nombre ?>" style="max-width:150px; height:150px; object-fit:cover;">
                        <h3><?= $nombre ?></h3>
                        <p>$<?= number_format($precio, 2, '.', ',') ?></p>
                        <button class="btn btn-success add-to-cart">Añadir al carrito</button>
                        <a href="detalle-producto.php?id=<?= $id ?>" class="btn btn-info">Ver detalle</a>
                    </div>
                <?php
                    endwhile;
                else:
                    echo "<p>No hay productos disponibles.</p>";
                endif;
                ?>
            </section>
        </div>
    </main>
                
    <!-- Modal del carrito -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Carrito de compras</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cart-items">
                    <p>Tu carrito está vacío</p>
                </div>
                <div class="mt-3 text-end">
                    <h5>Total: $<span id="cart-total">0</span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-success" id="btn-finalizar">Finalizar compra</button>
            </div>
        </div>
    </div>
</div>


    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; AndeSport</p>
            <p class="mb-0">Te aseguramos CALIDAD en cada producto, embalaje seguro, y una excelente experiencia de compra.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let carrito = [];
    const cartCount = document.getElementById("cart-count");
    const cartItems = document.getElementById("cart-items");
    const cartTotal = document.getElementById("cart-total");

    // Añadir al carrito
    document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", () => {
            const card = btn.closest(".product-card");
            const id = card.dataset.id;
            const existente = carrito.find(p => p.id === id);

            if (existente) {
                existente.cantidad++;
            } else {
                carrito.push({
                    id: id,
                    nombre: card.dataset.nombre,
                    precio: parseFloat(card.dataset.precio),
                    img: card.dataset.img,
                    cantidad: 1
                });
            }
            actualizarCarrito();
        });
    });

    // Actualizar carrito
    function actualizarCarrito() {
        cartCount.textContent = carrito.reduce((sum, p) => sum + p.cantidad, 0);

        if (carrito.length === 0) {
            cartItems.innerHTML = "<p>Tu carrito está vacío</p>";
            cartTotal.textContent = "0";
            return;
        }

        cartItems.innerHTML = carrito.map((p, i) =>
            `<div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                    <img src="${p.img}" style="width:40px; height:40px; object-fit:cover;"> 
                    ${p.nombre} - $${p.precio}
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-warning me-2" onclick="cambiarCantidad(${i}, -1)">-</button>
                    <span>${p.cantidad}</span>
                    <button class="btn btn-sm btn-primary ms-2" onclick="cambiarCantidad(${i}, 1)">+</button>
                </div>
                <strong>$${(p.precio * p.cantidad).toFixed(2)}</strong>
            </div>`
        ).join("");

        const total = carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0);
        cartTotal.textContent = total.toFixed(2);
    }

    // Cambiar cantidad
    function cambiarCantidad(index, delta) {
        carrito[index].cantidad += delta;
        if (carrito[index].cantidad <= 0) {
            carrito.splice(index, 1);
        }
        actualizarCarrito();
    }

    // Abrir modal
    document.querySelector(".cart-icon").addEventListener("click", () => {
        const modal = new bootstrap.Modal(document.getElementById("cartModal"));
        modal.show();
    });

    document.getElementById("btn-finalizar").addEventListener("click", () => {
    fetch("guardar-carrito.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(carrito)
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            window.location.href = "ver-carrito.php"; // redirige al carrito en PHP
        } else {
            alert("Error al guardar el carrito");
        }
    })
    .catch(err => console.error(err));
});
</script>
</body>
</html>