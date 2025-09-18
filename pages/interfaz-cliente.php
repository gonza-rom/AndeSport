<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin', 'cliente'])) {
    header("Location: inicio-sesion.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['apellido'], $_POST['email'])) {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $genero = $_POST['genero'];
    $fecha_nacimiento = $_POST['fecha_nacimiento']; // Si se incluye en el formulario
    $provincia = $_POST['provincia'];
    $departamento = $_POST['departamento'];
    $localidad = $_POST['localidad'];
    $calle = $_POST['calle'];
    $altura = $_POST['altura'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];

    // 1. Primero actualizamos la tabla `persona`
    $sql_persona = "UPDATE persona 
                    INNER JOIN usuario ON usuario.id_persona = persona.id_persona
                    SET persona.nombre = ?, persona.apellido = ?, persona.genero = ?, persona.fecha_nacimiento = ?, persona.provincia = ?, persona.departamento = ?, persona.localidad = ?, persona.calle = ?, persona.altura = ?, persona.latitud = ?, persona.longitud = ?
                    WHERE usuario.id_usuario = ?";
    $stmt_persona = $conexion->prepare($sql_persona);
    $stmt_persona->bind_param("ssssssssddsi", $nombre, $apellido, $genero, $fecha_nacimiento, $provincia, $departamento, $localidad, $calle, $altura, $latitud, $longitud, $id_usuario);
    $stmt_persona->execute();

    // 2. Luego actualizamos la tabla `usuario`
    $sql_usuario = "UPDATE usuario SET email = ?, telefono = ? WHERE id_usuario = ?";
    $stmt_usuario = $conexion->prepare($sql_usuario);
    $stmt_usuario->bind_param("ssi", $email, $telefono, $id_usuario);
    $stmt_usuario->execute();

    // Opcional: redireccionar para evitar reenvío de formulario
    header("Location: interfaz-cliente.php?actualizado=1");
    exit();
}
// Traer datos del usuario y persona
$sql = "SELECT u.nombre AS nombre_usuario, u.email, u.telefono, u.foto_usuario, 
               p.nombre AS nombre_persona, p.apellido, p.dni, p.fecha_nacimiento, p.genero, p.provincia, p.departamento, p.localidad, p.calle, p.altura, p.latitud, p.longitud
        FROM usuario u 
        INNER JOIN persona p ON u.id_persona = p.id_persona
        WHERE u.id_usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos = $resultado->fetch_assoc();


// ---------------------------
// SECCIÓN MIS PEDIDOS
// ---------------------------

// Traer datos del usuario y persona
$sql = "SELECT u.nombre AS nombre_usuario, u.email, u.telefono, u.foto_usuario,
               p.nombre AS nombre_persona, p.apellido, p.dni, p.fecha_nacimiento, p.genero,
               p.provincia, p.departamento, p.localidad, p.calle, p.altura
        FROM usuario u
        INNER JOIN persona p ON u.id_persona = p.id_persona
        WHERE u.id_usuario = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos_usuario = $resultado->fetch_assoc();
$stmt->close();

// Traer pedidos del usuario
$sql_pedidos = "SELECT id_venta, total, tipo_pago, fecha FROM ventas WHERE id_usuario = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql_pedidos);
if (!$stmt) {
    die("Error en prepare pedidos: " . $conexion->error);
}
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado_pedidos = $stmt->get_result();
$pedidos = $resultado_pedidos->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        /* Estilos personalizados de AndeSport */
        :root {
            --primary-color: orange;
            --accent-color: #f7931e;
            --background-color: #f4f4f4;
            --text-color: #333333;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-color);
        }

        header {
            background-color: var(--primary-color);
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        nav a {
            color: white;
            margin-left: 1rem;
            text-decoration: none;
        }

        nav a:hover {
            color: var(--accent-color);
        }

        .card-title,
        .card-header h3 {
            color: var(--primary-color);
        }

        .btn-secondary {
            background-color: var(--accent-color);
            border: none;
        }

        .btn-secondary:hover {
            background-color: darkorange;
        }

        .pagination .page-link {
            color: var(--primary-color);
            /* Color naranja */
            text-decoration: none;
            /* Quitar subrayado */
        }

        .pagination .page-link:hover {
            color: darkorange;
            /* Color al pasar el mouse */
            background-color: transparent;
            /* Fondo transparente */
            border-color: transparent;
            /* Sin borde al hacer hover */
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['actualizado']) && $_GET['actualizado'] == 1): ?>
        <div class="alert alert-success text-center" role="alert">
            ¡Tus datos fueron actualizados correctamente!
        </div>
        <script>
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            }, 3000); // 4 segundos
        </script>

    <?php endif; ?>

    <!-- Barra de navegación de AndeSport -->
    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <p class="logo"><span> Hola, <?php echo htmlspecialchars($datos['nombre_persona'] . " " . $datos['apellido']); ?></span></p>
            <nav>
                <a href="../index.php">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="sobrenosotros.php">Sobre Nosotros</a>
                <a href="contacto.php">Contacto</a>
                <a href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
    </header>

    <!-- Contenido principal -->
    <div class="container my-5">
        <!-- Sección de Datos del Cliente -->
        <div class="card mb-4">
            <div class="card-body">

                <div style="float: right; margin-left: 20px; text-align: center;">
                    <img src="../<?php echo $datos['foto_usuario'] . '?v=' . time(); ?>"
                        alt="Foto de perfil"
                        style="width: 200px; height: 200px; object-fit: cover; border-radius: 40%; border: 2px solid #ccc;">

                    <form action="../pages/usuario-acciones/cambiar-foto.php" method="POST" enctype="multipart/form-data" style="margin-top: 10px;">
                        <input type="file" name="nueva_foto" id="fileInput" accept="image/*" style="display: none;" required>

                        <label for="fileInput" class="btn btn-primary btn-sm mt-2" style="cursor: pointer;">
                            Cambiar foto
                        </label>
                    </form>
                    <form action="../pages/usuario-acciones/eliminar-foto.php" method="POST" style="margin-top: 5px;">
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar foto</button>
                    </form>
                </div>

                <h3 class="card-title">Datos del Cliente</h3>
                <p><strong>Nombre:</strong> <?php echo $datos['nombre_persona'] . " " . $datos['apellido']; ?></p>
                <p><strong>D.N.I.:</strong> <?php echo $datos['dni']; ?></p>
                <p><strong>Fecha de nacimiento:</strong> <?php echo $datos['fecha_nacimiento']; ?></p>
                <p><strong>Género:</strong> <?php echo $datos['genero']; ?></p>
                <p><strong>Correo electrónico:</strong> <?php echo $datos['email']; ?></p>
                <p><strong>Teléfono: </strong><?php echo $datos['telefono']; ?></p>
                <p><strong>Dirección: </strong><?php echo $datos['calle'] . " " . $datos['altura'] . ", " . $datos['localidad'] . ", " . $datos['provincia']; ?></p>
                <p><strong>Provincia: </strong> <?php echo $datos['provincia']; ?></p>
                <p><strong>Departamento: </strong> <?php echo $datos['departamento']; ?></p>
                <p><strong>Localidad: </strong> <?php echo $datos['localidad']; ?></p>
                <p><strong>Calle: </strong> <?php echo $datos['calle']; ?></p>
                <p><strong>Altura: </strong> <?php echo $datos['altura']; ?></p>
                <p><strong>Ubicación geográfica: </strong></p>
                <div class="map-container" style="width: 100%; height: 300px; border: 1px solid #ddd;">
                    <iframe
                        src="https://maps.google.com/maps?q=<?php echo $datos['latitud']; ?>,<?php echo $datos['longitud']; ?>&hl=es&z=18&output=embed"
                        width="100%" height="300" frameborder="0" style="border:0;" allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
                <br>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editarModal">Editar
                    Datos</button>
            </div>
        </div>

        <!-- Formulario para editar datos del cliente -->
        <form id="formEditarCliente" method="POST" action="">

            <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editarModalLabel">Editar Datos del Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $datos['nombre_persona']; ?>">
                                <div class="invalid-feedback">El nombre solo puede contener letras y debe tener al menos 3 caracteres. No se permiten espacios en blanco.</div>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $datos['apellido']; ?>">
                                <div class="invalid-feedback">El apellido solo puede contener letras y debe tener al menos 3 caracteres.</div>
                            </div>
                            <div class="mb-3">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="number" class="form-control" id="dni" name="dni" value="<?php echo $datos['dni']; ?>">
                                <div class="invalid-feedback">Ingrese un DNI válido entre 1000000 y 99999999</div>
                            </div>
                            <div class="mb-3">
                                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fecha_nacimiento" value="<?php echo $datos['fecha_nacimiento']; ?>" required>
                                <div class="invalid-feedback">Ingrese una fecha válida. Debe ser mayor de 13 años.</div>
                            </div>
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <select class="form-control" id="genero" name="genero" required>
                                    <option value="" disabled <?php if ($datos['genero'] == '') echo 'selected'; ?>>Seleccione una opción</option>
                                    <option value="Masculino" <?php if ($datos['genero'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
                                    <option value="Femenino" <?php if ($datos['genero'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
                                    <option value="No-binario" <?php if ($datos['genero'] == 'No-binario') echo 'selected'; ?>>No Binario</option>
                                    <option value="Otro" <?php if ($datos['genero'] == 'Otro') echo 'selected'; ?>>Otro</option>
                                    <option value="Prefiero-no-decirlo" <?php if ($datos['genero'] == 'Prefiero-no-decirlo') echo 'selected'; ?>>Prefiero no decirlo</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un género.</div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $datos['email']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $datos['telefono']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="provincia" class="form-label">Provincia</label>
                                <input type="text" class="form-control" id="provincia" name="provincia" value="<?php echo $datos['provincia']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <input type="text" class="form-control" id="departamento" name="departamento" value="<?php echo $datos['departamento']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="localidad" class="form-label">Localidad</label>
                                <input type="text" class="form-control" id="localidad" name="localidad" value="<?php echo $datos['localidad']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="calle" class="form-label">Calle</label>
                                <input type="text" class="form-control" id="calle" name="calle" value="<?php echo $datos['calle']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="altura" class="form-label">Altura</label>
                                <input type="text" class="form-control" id="altura" name="altura" value="<?php echo $datos['altura']; ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="coordenadas" class="form-label">Ubicación geográfica</label>
                                <div id="map" style="height: 300px; border-radius: 10px; border: 1px solid #ccc;"></div>
                                <input type="hidden" id="latitud" name="latitud">
                                <input type="hidden" id="longitud" name="longitud">
                                <div class="form-text">Seleccioná un punto en el mapa para actualizar la ubicación.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <!-- Sección de Pedidos -->
        <div class="card mt-4">
        <div class="card-header">
            <h3>Mis Pedidos</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th># Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Tipo de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pedidos): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= $pedido['id_venta'] ?></td>
                                <td><?= date("d/m/Y H:i", strtotime($pedido['fecha'])) ?></td>
                                <td>$<?= number_format($pedido['total'], 2, ',', '.') ?></td>
                                <td><?= ucfirst($pedido['tipo_pago']) ?></td>
                                <td>
                                    <a href="detalle-venta.php?id_venta=<?= $pedido['id_venta'] ?>" class="btn btn-info btn-sm">Ver detalle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay pedidos realizados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; AndeSport</p>
            <p class="mb-0">Te aseguramos CALIDAD en cada producto, embalaje seguro, y una excelente experiencia de
                compra.</p>
            <ul class="list-inline mt-3">
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
            </ul>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        // Cuando se elige un archivo, automáticamente se envía el formulario
        document.getElementById("fileInput").addEventListener("change", function() {
            this.form.submit();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Coordenadas previas (las que vienen de la base de datos)
            var latitudGuardada = <?php echo json_encode($datos['latitud']); ?>;
            var longitudGuardada = <?php echo json_encode($datos['longitud']); ?>;

            // Coordenadas por defecto si no hay ninguna guardada
            var coordenadasIniciales = [latitudGuardada || -28.4696, longitudGuardada || -65.7795];

            // Inicializamos el mapa
            var mapa = L.map('map').setView(coordenadasIniciales, 16);

            // Capa base de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapa);

            // Agregamos un marcador en la ubicación inicial
            var marcador = L.marker(coordenadasIniciales, {
                draggable: true
            }).addTo(mapa);

            // Establecemos los valores iniciales en los campos ocultos
            document.getElementById('latitud').value = latitudGuardada;
            document.getElementById('longitud').value = longitudGuardada;

            // Cuando el marcador se mueva, actualizamos los inputs
            marcador.on('dragend', function(e) {
                var nuevaLat = marcador.getLatLng().lat.toFixed(7);
                var nuevaLng = marcador.getLatLng().lng.toFixed(7);
                document.getElementById('latitud').value = nuevaLat;
                document.getElementById('longitud').value = nuevaLng;
            });

            // También permitimos que el usuario haga clic para mover el marcador
            mapa.on('click', function(e) {
                marcador.setLatLng(e.latlng);
                document.getElementById('latitud').value = e.latlng.lat.toFixed(7);
                document.getElementById('longitud').value = e.latlng.lng.toFixed(7);
            });

            // 🔧 Arreglar el tamaño cuando se abre el modal
            const modal = document.getElementById('editarModal');
            modal.addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    mapa.invalidateSize();
                }, 100); // un pequeño delay ayuda a que se renderice bien
            });
        });
    </script>
    <script>
        function validarTextoSinEspaciosExtremosYUnSoloEspacioInterno(texto) {
            // No debe haber espacios al inicio o final
            if (texto.trim() !== texto) return false;

            // No debe haber más de un espacio seguido
            if (/\s{2,}/.test(texto)) return false;

            // Debe tener al menos una palabra con 2 o más letras
            if (!/^[a-zA-ZÁÉÍÓÚÑáéíóúñ]+(?: [a-zA-ZÁÉÍÓÚÑáéíóúñ]+)*$/.test(texto)) return false;

            return true;
        }

        function validarCampo(input, condicion, mensaje) {
            const feedback = input.nextElementSibling;
            if (condicion) {
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
                if (feedback && feedback.classList.contains("invalid-feedback")) {
                    feedback.textContent = "";
                }
            } else {
                input.classList.add("is-invalid");
                input.classList.remove("is-valid");
                if (feedback && feedback.classList.contains("invalid-feedback")) {
                    feedback.textContent = mensaje;
                }
            }
        }

        function limpiarTexto(texto) {
            return texto.trim().replace(/\s+/g, ' ');
        }

        const camposEditar = {
            nombre: {
                validar: (v) => /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{3,}$/.test(limpiarTexto(v)) && validarTextoSinEspaciosExtremosYUnSoloEspacioInterno(v),
                mensaje: "El nombre solo puede contener letras, debe tener al menos 3 caracteres y no debe tener espacios al principio o final, ni múltiples espacios entre palabras."
            },
            apellido: {
                validar: (v) => /^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]{2,}$/.test(limpiarTexto(v)) && validarTextoSinEspaciosExtremosYUnSoloEspacioInterno(v),
                mensaje: "El apellido solo puede contener letras, debe tener al menos 3 caracteres y no debe tener espacios al principio o final, ni múltiples espacios entre palabras."
            },
            dni: {
                validar: (v) => /^\d{7,8}$/.test(v) && parseInt(v) >= 1000000 && parseInt(v) <= 99999999,
                mensaje: "DNI válido entre 1000000 y 99999999."
            },
            fechaNacimiento: {
                validar: (v) => {
                    const fecha = new Date(v);
                    const hoy = new Date();
                    const edad = hoy.getFullYear() - fecha.getFullYear();
                    return v && fecha < hoy && edad >= 13 && edad <= 100;
                },
                mensaje: "Ingrese una fecha válida. Debe tener entre 13 y 100 años."
            },
            genero: {
                validar: (v) => v !== "",
                mensaje: "Seleccione un género."
            },
            fotoDniFrente: {
                validar: (f) => f instanceof File && f.size > 0 && ['image/jpeg', 'image/png', 'image/svg+xml'].includes(f.type),
                mensaje: "Debe subir una imagen JPG, PNG o SVG del frente del DNI."
            },
            fotoDniDorso: {
                validar: (f) => f instanceof File && f.size > 0 && ['image/jpeg', 'image/png', 'image/svg+xml'].includes(f.type),
                mensaje: "Debe subir una imagen JPG, PNG o SVG del dorso del DNI."
            },
            provincia: {
                validar: (v) => limpiarTexto(v).length >= 2,
                mensaje: "Ingrese una provincia válida."
            },
            departamento: {
                validar: (v) => limpiarTexto(v).length >= 2,
                mensaje: "Ingrese un departamento válido."
            },
            localidad: {
                validar: (v) => limpiarTexto(v).length >= 2,
                mensaje: "Ingrese una localidad válida."
            },
            altura: {
                validar: (v) => limpiarTexto(v).length >= 2,
                mensaje: "Ingrese una altura válida."
            },
            calle: {
                validar: (v) => limpiarTexto(v).length >= 2,
                mensaje: "Ingrese una calle válida."
            }
        };

        // Asignar validaciones en vivo
        Object.keys(camposEditar).forEach(id => {
            const input = document.getElementById(id);
            if (!input) return;
            input.addEventListener("input", () => {
                const valor = input.value;
                validarCampo(input, camposEditar[id].validar(valor), camposEditar[id].mensaje);
            });
        });

        //Validacion al enviar
        document.getElementById("formEditarCliente").addEventListener("submit", function(e) {
            e.preventDefault();
            let valido = true;

            Object.keys(camposEditar).forEach(id => {
                const input = document.getElementById(id);
                if (!input) return;
                const valor = input.value;
                const esValido = camposEditar[id].validar(valor);
                validarCampo(input, esValido, camposEditar[id].mensaje);
                if (!esValido) valido = false;
            });

            // Validar latitud y longitud
            const lat = document.getElementById('latitud').value;
            const lng = document.getElementById('longitud').value;

            if (!lat || !lng) {
                valido = false;
            }

            if (valido) {
                this.submit();
            } else {
                alert("Por favor, corrige los errores en el formulario.");
            }
        });
    </script>
</body>

</html>