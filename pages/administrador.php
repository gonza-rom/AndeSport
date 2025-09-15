<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: inicio-sesion.php");
    exit();
}

include '../includes/conexion.php';

// Traer los usuarios
$sql = "SELECT id_usuario, nombre, email, rol, activo FROM usuario";
$resultado = $conexion->query($sql);

// Traer personas 
$sql_personas = "SELECT 
                    p.id_persona, 
                    p.nombre, 
                    p.apellido, 
                    p.dni, 
                    p.fecha_nacimiento, 
                    p.genero, 
                    p.provincia, 
                    p.departamento, 
                    p.localidad, 
                    p.municipio, 
                    p.calle, 
                    p.altura,
                    u.id_usuario AS tiene_usuario 
                FROM persona p
                LEFT JOIN usuario u ON p.id_persona = u.id_persona";
$resultado_personas = $conexion->query($sql_personas);

//Traer productos
$sql_productos = "SELECT id_producto, nombre, descripcion, precio, stock, id_categoria, estado, img_producto FROM producto";
$resultado_productos = $conexion->query($sql_productos);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Panel de Administración - AndeSport</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../styles/administrador.css">
</head>

<body>
    <!-- Encabezado -->
    <header class="p-3 mb-3 border-bottom">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo">AndeSport Admin</h1>
            <button class="btn btn-primary" onclick="window.location.href='logout.php'">Cerrar Sesión</button>
        </div>
    </header>

    <div class="container">
        <!-- Sección de navegación -->
        <ul class="nav nav-pills mb-3" id="adminTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="personas-tab" data-bs-toggle="pill" href="#personas" role="tab">Personas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="usuarios-tab" data-bs-toggle="pill" href="#usuarios" role="tab">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="productos-tab" data-bs-toggle="pill" href="#productos" role="tab">Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="ventas-tab" data-bs-toggle="pill" href="#ventas" role="tab">Ventas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="proveedores-tab" data-bs-toggle="pill" href="#proveedores" role="tab">Proveedores</a>
            </li>

            <!-- Botón que abre el pop-up -->
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExportar">
                Exportar datos
            </button>
        </ul>

        <!-- Contenido de las secciones -->
        <div class="tab-content" id="adminTabsContent">

            <!-- Sección de Ventas -->
            <div class="tab-pane fade" id="ventas" role="tabpanel">
                <h2 class="mb-3">Ventas</h2>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de datos -->
                        <tr>
                            <td>201</td>
                            <td>Mochila de Montaña</td>
                            <td>3</td>
                            <td>$450</td>
                            <td>2024-10-28</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Sección de Productos -->
            <div class="tab-pane fade" id="productos" role="tabpanel">
                <h2 class="mb-3">Productos</h2>
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Producto</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Imagen</th>
                            <th>ID Categoria</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_productos && $resultado_productos->num_rows > 0): ?>
                            <?php while ($producto = $resultado_productos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['id_producto']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                                    <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                                    <td>
                                        <div style="float: right; margin-left: 20px; text-align: center;">
                                            <img src="../<?php echo $producto['img_producto'] . '?v=' . time(); ?>"
                                                alt="Foto de perfil"
                                                style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($producto['id_categoria']); ?></td>
                                    <td>
                                        <button
                                            class="btn btn-primary btn-sm btn-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editProductModal"
                                            data-id="<?php echo $producto['id_producto']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>"
                                            data-categoria="<?php echo $producto['id_categoria']; ?>"
                                            data-precio="<?php echo $producto['precio']; ?>"
                                            data-stock="<?php echo $producto['stock']; ?>"
                                            data-descripcion="<?php echo htmlspecialchars($producto['descripcion'], ENT_QUOTES); ?>"
                                            data-img="<?php echo htmlspecialchars('../' . $producto['img_producto'], ENT_QUOTES); ?>">
                                            Editar
                                        </button>


                                        <?php if ($producto['estado'] == 'activo'): ?>
                                            <a href="../pages/admin-acciones/baja-producto.php?id_producto=<?php echo $producto['id_producto']; ?>"
                                                class="btn btn-warning btn-sm"
                                                onclick="return confirm('¿Dar de baja este producto?')">
                                                Dar de Baja
                                            </a>
                                        <?php else: ?>
                                            <a href="../pages/admin-acciones/alta-producto.php?id_producto=<?php echo $producto['id_producto']; ?>"
                                                class="btn btn-success btn-sm"
                                                onclick="return confirm('¿Dar de alta este producto?')">
                                                Dar de Alta
                                            </a>
                                        <?php endif; ?>

                                        <a href="../pages/admin-acciones/eliminar-producto.php?id_producto=<?php echo $producto['id_producto']; ?>"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                            <button class="btn btn-danger btn-sm">Eliminar</button>
                                        </a>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No hay productos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Seccion de Personas -->
            <div class="tab-pane fade" id="personas" role="tabpanel">
                <h2 class="mb-3">Personas Registradas</h2>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Persona</th>
                            <th>Nombre/s</th>
                            <th>Apellido/s</th>
                            <th>DNI</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Género</th>
                            <th>Provincia</th>
                            <th>Departamento</th>
                            <th>Localidad</th>
                            <th>Municipio</th>
                            <th>Calle</th>
                            <th>Altura</th>
                            <th>¿Tiene usuario?</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado_personas && $resultado_personas->num_rows > 0): ?>
                            <?php while ($persona = $resultado_personas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($persona['id_persona']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['dni']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['fecha_nacimiento']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['genero']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['provincia']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['departamento']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['localidad']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['municipio']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['calle']); ?></td>
                                    <td><?php echo htmlspecialchars($persona['altura']); ?></td>
                                    <td><?php echo $persona['tiene_usuario'] ? 'Sí' : 'No'; ?></td>
                                    <td>
                                        <a href="../pages/admin-acciones/editarpersona.php?id_persona=<?php echo $persona['id_persona']; ?>">
                                            <button class="btn btn-primary btn-sm">Editar</button>
                                        </a>
                                        <a href="../pages/admin-acciones/eliminarpersona.php?id_persona=<?php echo $persona['id_persona']; ?>" class="btn-eliminar">
                                            <button class="btn btn-danger btn-sm">Eliminar</button>
                                        </a>
                                        <?php if (!$persona['tiene_usuario']): ?>
                                            <a href="../pages/registro-usuario.php?id_persona=<?php echo $persona['id_persona']; ?>">
                                                <button class="btn btn-success btn-sm mt-1">Crear usuario</button>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No hay personas registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Nueva Sección de Proveedores -->
            <div class="tab-pane fade" id="proveedores" role="tabpanel">
                <h2 class="mb-3">Proveedores</h2>
                <a href="agregar-proveedor.php"><button class="btn btn-success mb-2">Agregar Proveedor</button></a>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Proveedor</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Outdoor Supplies</td>
                            <td>Juan Castro</td>
                            <td>+54 9 8765 4321</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Nike</td>
                            <td>Gonzalo Romero</td>
                            <td>+54 9 8765 6676</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Columbia</td>
                            <td>Tania Quiroga</td>
                            <td>+54 9 8765 4214</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Salomon</td>
                            <td>Anahi Barrionuevo</td>
                            <td>+54 9 8765 1231</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>501</td>
                            <td>THE NORTH FACE</td>
                            <td>Lucas Barrojo</td>
                            <td>+54 9 8765 1324</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Nueva Sección de Usuarios -->
            <div class="tab-pane fade" id="usuarios" role="tabpanel">
                <h2 class="mb-3">Usuarios</h2>
                <a href="nuevousuario.php"><button class="btn btn-success mb-2">Agregar Usuario</button></a>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultado->num_rows > 0) {
                            while ($row = $resultado->fetch_assoc()) {
                                $id = $row['id_usuario'];
                                $nombre = htmlspecialchars($row['nombre']);
                                $email = htmlspecialchars($row['email']);
                                $rol = htmlspecialchars($row['rol']);
                                $activo = $row['activo'];

                                echo "<tr" . ($activo ? "" : " class='table-secondary'") . ">";
                                echo "<td>$id</td>";
                                echo "<td>$nombre</td>";
                                echo "<td>$email</td>";
                                echo "<td>$rol</td>";
                                echo "<td>" . ($activo ? 'Activo' : 'Inactivo') . "</td>";
                                echo "<td>";

                                if ($id == $_SESSION['id_usuario']) {
                                    echo "<span class='text-muted'>No puedes modificarte a ti mismo</span>";
                                } else {
                                    // Botón Editar
                                    echo "<a href='../pages/admin-acciones/editarusuario.php?id=$id' class='btn btn-primary btn-sm'>Editar</a> ";
                                    // Botón de alta/baja
                                    if ($activo) {
                                        echo "<a href='../pages/admin-acciones/bajausuario.php?id=$id' class='btn btn-warning btn-sm' onclick=\"return confirm('¿Dar de baja este usuario?')\">Dar de Baja</a> ";
                                    } else {
                                        echo "<a href='../pages/admin-acciones/altausuario.php?id=$id' class='btn btn-success btn-sm' onclick=\"return confirm('¿Dar de alta este usuario?')\">Dar de Alta</a> ";
                                    }
                                    // Botón eliminar
                                    echo "<a href='../pages/admin-acciones/eliminarusuario.php?id=$id' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Eliminar permanentemente este usuario?')\">Eliminar</a>";
                                }

                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No hay usuarios registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para agregar producto -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../pages/admin-acciones/agregar-producto.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="productName" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Categoría</label>
                            <select class="form-select" id="productCategory" name="id_categoria" required>
                                <option disabled selected>Seleccione una categoría</option>
                                <option value="1">Ropa</option>
                                <option value="2">Accesorios</option>
                                <option value="3">Calzado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="productPrice" name="precio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" id="productStock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Imagen del producto (JPG o PNG)</label>
                            <input type="file" class="form-control" id="productImage" name="img_producto" accept=".jpg, .jpeg, .png" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para EDITAR producto -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarProducto" action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="id_producto" name="id_producto" value="">

                        <div class="mb-3">
                            <label for="productName" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="productNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Categoría</label>
                            <select class="form-select" id="productCategoria" name="id_categoria" required>
                                <option value="">Seleccione una categoría</option>
                                <option value="1">Ropa</option>
                                <option value="2">Accesorios</option>
                                <option value="3">Calzado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Precio</label>
                            <input type="number" class="form-control" id="productPrecio" name="precio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" id="stockProducto" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen actual</label><br>
                            <img id="previewImage" src="" alt="Imagen actual" style="max-width: 100%; max-height: 200px; margin-bottom: 10px; display:none;">
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Cambiar Imagen del producto (JPG o PNG)</label>
                            <input type="file" class="form-control" id="productImage" name="img_producto" accept=".jpg, .jpeg, .png">
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de Exportación -->
    <div class="modal fade" id="modalExportar" tabindex="-1" aria-labelledby="modalExportarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form-exportar" action="../pages/admin-acciones/exportar-datos.php" method="get" target="_blank">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalExportarLabel">Seleccionar atributos para exportar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div id="alertaCampos" class="alert alert-danger d-none" role="alert">
                                Debe seleccionar al menos un atributo para exportar.
                            </div>

                            <div class="col-md-6">
                                <label for="">Datos de Personas</label><br>
                                <label><input type="checkbox" name="campos[]" value="persona_nombre" checked> Nombre/s</label><br>
                                <label><input type="checkbox" name="campos[]" value="apellido" checked> Apellido/s</label><br>
                                <label><input type="checkbox" name="campos[]" value="dni"> DNI</label><br>
                                <label><input type="checkbox" name="campos[]" value="fecha_nacimiento"> Fecha de Nacimiento</label><br>
                                <label><input type="checkbox" name="campos[]" value="genero"> Género</label><br>
                                <label><input type="checkbox" name="campos[]" value="provincia"> Provincia</label><br>
                                <label><input type="checkbox" name="campos[]" value="departamento">Departamento</label><br>
                                <label><input type="checkbox" name="campos[]" value="localicad"> Localidad</label><br>
                                <label><input type="checkbox" name="campos[]" value="calle"> Calle</label><br>
                                <label><input type="checkbox" name="campos[]" value="altura"> Altura</label><br>
                            </div>

                            <div class="col-md-6">
                                <label for="">Datos de Usuarios</label><br>
                                <label><input type="checkbox" name="campos[]" value="usuario_nombre" checked> Usuario</label><br>
                                <label><input type="checkbox" name="campos[]" value="email" checked> Email</label><br>
                                <label><input type="checkbox" name="campos[]" value="foto_usuario"> Imagen</label><br>
                            </div>

                            <div class="col-md-6">
                                <br><label for="">Datos de Productos</label><br>
                                <label><input type="checkbox" name="campos[]" value="producto_nombre" checked> Nombre del Producto</label><br>
                                <label><input type="checkbox" name="campos[]" value="descripcion" checked> Descripcion</label><br>
                                <label><input type="checkbox" name="campos[]" value="precio"> Precio</label><br>
                                <label><input type="checkbox" name="campos[]" value="stock"> Stock</label><br>
                                <label><input type="checkbox" name="campos[]" value="categoria_nombre"> Categoria a la que pertenece</label><br>
                            </div>

                        </div>

                        <div class="mt-3">
                            <label for="formato">Formato:</label>
                            <select name="formato" id="formato" class="form-select w-auto d-inline">
                                <option value="pdf">PDF</option>
                                <option value="xls">XLS</option>
                                <option value="xlsx">XLSX</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Exportar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("#form-exportar");
            const alerta = document.querySelector("#alertaCampos");

            form.addEventListener("submit", function(e) {
                const checkboxes = form.querySelectorAll('input[name="campos[]"]');
                const alMenosUnoMarcado = Array.from(checkboxes).some(cb => cb.checked);

                if (!alMenosUnoMarcado) {
                    e.preventDefault();
                    alerta.classList.remove("d-none");
                } else {
                    alerta.classList.add("d-none"); // por si quedó visible de antes
                }
            });

            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const categoria = this.getAttribute('data-categoria');
                    const precio = this.getAttribute('data-precio');
                    const stock = this.getAttribute('data-stock');
                    const descripcion = this.getAttribute('data-descripcion');
                    const img = this.getAttribute('data-img');

                    // Mostrar modal con Bootstrap 5 (en caso de que no funcione data-bs-toggle)
                    const modalEl = document.getElementById('editProductModal');
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.show();

                    console.log(id, nombre, categoria, precio, stock, descripcion);

                    // Asignar valores a los inputs
                    document.getElementById('id_producto').value = id;
                    document.getElementById('productNombre').value = nombre;
                    document.getElementById('productCategoria').value = categoria;
                    document.getElementById('productPrecio').value = precio;
                    document.getElementById('stockProducto').value = stock;
                    document.getElementById('descripcion').value = descripcion;

                    // Mostrar preview imagen actual
                    const preview = document.getElementById('previewImage');
                    if (img) {
                        preview.src = img;
                        preview.style.display = 'block';
                    } else {
                        preview.src = '';
                        preview.style.display = 'none';
                    }

                    // Limpiar input file (no se puede rellenar por seguridad)
                    document.getElementById('productImage').value = '';

                    // Cambiar action del formulario
                    document.getElementById('formEditarProducto').action = "../pages/admin-acciones/editar-producto.php?id_producto=" + id;
                });
            });
        });
    </script>

    <!-- Persona actualizada con exito -->
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'actualizado'): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Los cambios se realizaron correctamente.',
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php endif; ?>
    <!-- Alert Usuario Actualizado  -->
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'usuario_actualizado'): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Usuario actualizado!',
                text: 'Los cambios del usuario se guardaron correctamente.',
                confirmButtonText: 'Aceptar'
            });
        </script>
        <!-- Seguro de eliminar -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.btn-confirmar').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        const url = btn.href;
                        const mensaje = btn.getAttribute('data-mensaje') || '¿Estás seguro?';

                        Swal.fire({
                            title: mensaje,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí',
                            cancelButtonText: 'Cancelar'
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.location.href = url;
                            }
                        });
                    });
                });
            });
        </script>
        <!-- Persona Eliminada -->
    <?php endif; ?>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'persona_eliminada'): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: 'La persona fue eliminada correctamente.',
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php endif; ?>


</body>

</html>