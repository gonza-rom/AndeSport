<?php
//solicitar el archivo de conexion a la base de datos
include '../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_persona = $_POST['id_persona'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $contraseña = $_POST['contraseña'];
    $rol = 'cliente';

    $foto_usuario_ruta = ''; //esta ruta se guarda en BD

    if (!empty($_FILES['foto_usuario']['name'])) {
        //Imagen subida desde archivo
        $foto_usuario = $_FILES['foto_usuario']['name'];
        $foto_usuario_tmp = $_FILES['foto_usuario']['tmp_name'];

        //Ruta fisica(servidor) y ruta web(navegador)
        $nombre_archivo = uniqid() . "_" . basename($foto_usuario);
        $ruta_fisica = "../img_usuario/" . $nombre_archivo; //guardamos el archivo
        $foto_usuario_ruta = "img_usuario/" . $nombre_archivo; //guardamos en la base y mostramos en el fronted

        if (!move_uploaded_file($foto_usuario_tmp, $ruta_fisica)) {
            echo "Error al subir la imagen desde archivo.";
            exit();
        }
    } elseif (!empty($_POST['foto_base64'])) {
        //Imagen tomada de la camara
        $base64 = $_POST['foto_base64'];
        $img_data = explode(',', $base64);

        if (count($img_data) == 2) {
            $foto_binaria = base64_decode($img_data[1]);

            $nombre_archivo = uniqid() . '.png';
            $ruta_fisica = '../img_usuario/' . $nombre_archivo;
            $foto_usuario_ruta = 'img_usuario/' . $nombre_archivo;

            if (!file_put_contents($ruta_fisica, $foto_binaria)) {
                echo "Error al guardar la imagen tomada con la cámara.";
                exit();
            }
        } else {
            echo "Formato de imagen inválido.";
            exit();
        }
    } else {
        echo "Debe cargar una imagen o tomar una foto.";
        exit();
    }

    $hash = password_hash($contraseña, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (nombre, email, telefono, contraseña, rol, foto_usuario, id_persona)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssi", $nombre, $email, $telefono, $hash, $rol, $foto_usuario_ruta, $id_persona);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['registro_exitoso'] = true;
        header("Location: inicio-sesion.php");
        //echo "<script>alert('Usuario registrado correctamente'); window.location.href='inicio-sesion.php';</script>";
        exit();
    } else {
        echo "Error al crear usuario: " . $stmt->error;
    }
}

$id_persona = isset($_GET['id_persona']) ? $_GET['id_persona'] : '';

$persona = null;
if (!empty($id_persona)) {
    $stmt = $conexion->prepare("SELECT * FROM persona WHERE id_persona = ?");
    $stmt->bind_param("i", $id_persona);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado && $resultado->num_rows > 0) {
        $persona = $resultado->fetch_assoc();
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #fff6f1;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #ff6f3c;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 100px;
        }

        .formulario-estilo {
            background: linear-gradient(to bottom right, #ffffff, #fff4ec);
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(255, 111, 60, 0.2);
            max-width: 500px;
            width: 100%;
            border-left: 5px solid #ff6f3c;
            border-right: 5px solid #ff6f3c;
            animation: fadeIn 0.5s ease-in-out;
        }


        .formulario-ancho {
            max-width: 900px;
            /* más ancho que el contenedor default de Bootstrap */
            margin: 0 auto;
            /* centra el formulario */
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .formulario-ancho {
                padding: 10px;
            }
        }


        h2.mb-4 {
            text-align: center;
            color: #ff6f3c;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <header>Registro de Nuevo Usuario</header>

    <?php if ($persona): ?>
        <div style="display: flex; justify-content: center; align-items: center; height: 45vh;">
            <div class="card mb-3" style="max-width: 400px; font-size: 0.9rem;">
                <div class="card-header bg-success text-white py-2 px-3" style="font-weight: 600; font-size: 1rem;">
                    Datos ingresados previamente
                </div>
                <div class="card-body py-2 px-3">
                    <div class="row g-2">
                        <div class="col-6"><strong>Nombre:</strong><br><?php echo htmlspecialchars($persona['nombre']); ?></div>
                        <div class="col-6"><strong>Apellido:</strong><br><?php echo htmlspecialchars($persona['apellido']); ?></div>
                        <div class="col-6"><strong>DNI:</strong><br><?php echo htmlspecialchars($persona['dni']); ?></div>
                        <div class="col-6"><strong>Fecha Nac.:</strong><br><?php echo htmlspecialchars($persona['fecha_nacimiento']); ?></div>
                        <div class="col-12"><strong>Dirección:</strong><br><?php echo htmlspecialchars($persona['calle'] . ' ' . $persona['altura']); ?></div>
                        <div class="col-6"><strong>Provincia:</strong><br><?php echo htmlspecialchars($persona['provincia']); ?></div>
                        <div class="col-6"><strong>Departamento:</strong><br><?php echo htmlspecialchars($persona['departamento']); ?></div>
                        <div class="col-12"><strong>Localidad:</strong><br><?php echo htmlspecialchars($persona['localidad']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-wrapper">
        <h2 class="mb-4">Datos de Usuario</h2>
        <form id="formAgregarUsuario" class="formulario-estilo formulario-ancho" method="POST" action="registro-usuario.php" enctype="multipart/form-data">
            <input type="hidden" name="id_persona" value="<?php echo $id_persona; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
                    <input type="text" class="form-control" id="nombreUsuario" name="nombre" required>
                    <div class="invalid-feedback">No debe tener espacios al principio o final, ni múltiples espacios
                        entre palabras.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
                </div>


                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" required>
                    <div class="invalid-feedback">Ingrese un teléfono válido.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                    <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="confirmarContraseña" class="form-label">Confirmar contraseña</label>
                    <input type="password" class="form-control" id="confirmarContraseña" required>
                    <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Foto de usuario</label>

                    <!-- OPCIÓN 1: SUBIR ARCHIVO -->
                    <input type="file" class="form-control mb-2" id="fotoUsuario" name="foto_usuario" accept="image/*">

                    <!-- OPCIÓN 2: SACAR FOTO -->
                    <video id="camera" autoplay muted playsinline class="w-100 mb-2 rounded shadow-sm" style="max-height: 250px; display: none;"></video>
                    <p id="mensaje-camara" class="text-danger" style="display: none;">
                        ⚠️ No se pudo acceder a la cámara. Autorizala o subí una imagen manualmente.
                    </p>
                    <canvas id="photo" style="display: none;"></canvas>
                    <input type="hidden" name="foto_base64" id="foto_base64">

                    <div class="d-flex gap-2" id="bloque-camara" style="display: none;">
                        <button type="button" class="btn btn-primary btn-sm" id="capture-btn">📸 Tomar foto</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="cancelarFoto" style="display: none;">❌ Cancelar</button>
                        <small class="text-muted">Podés subir una imagen o tomar una foto en tiempo real.</small>
                    </div>

                </div>


                <div id="previewContainer" class="mt-2" style="display: none;">
                    <img id="previewImage" src="" alt="Vista previa" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                    <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="btnEliminarImagen">Eliminar Imagen</button>
                </div>
            </div>

            <div class="col-12"><button type="submit" class="btn btn-success w-100">Guardar</button></div>
        </form>

    </div>

    <script>
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

        const camposPersona = {
            nombreUsuario: {
                validar: (v) => /^[A-Za-zÁÉÍÓÚáéíóúÑñ]{3,12}$/.test(v),
                mensaje: "Se permite únicamente letras. No debe contener espacios. Debe tener 3 caracteres como mínimo y 12 como máximo."
            },
            email: {
                validar: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
                mensaje: "Ingrese un correo electrónico válido."
            },
            telefono: {
                validar: (v) => /^\d{7,15}$/.test(v),
                mensaje: "Ingrese un teléfono válido, entre 7 y 15 dígitos."
            },
            contraseña: {
                validar: (v) => /^[A-Za-z0-9]{6,}$/.test(v) &&
                    /[A-Z]/.test(v) && // al menos una mayúscula
                    /[a-z]/.test(v) && // al menos una minúscula
                    /\d/.test(v), // al menos un número
                mensaje: "La contraseña debe tener al menos 6 caracteres, una mayúscula, una minúscula, un número y no contener símbolos."
            },
            confirmarContraseña: {
                validar: (v) => v === document.getElementById("contraseña").value,
                mensaje: "Las contraseñas no coinciden."
            },
            fotoUsuario: {
                validar: (f) => f instanceof File && f.size > 0 && ['image/jpeg', 'image/png'].includes(f.type),
                mensaje: "Debe subir una imagen JPG o PNG del usuario."
            },
        };


        // Asignar eventos input/change a los campos
        Object.keys(camposPersona).forEach(id => {
            const input = document.getElementById(id);
            const tipoArchivo = input.type === "file";

            input.addEventListener(tipoArchivo ? "change" : "input", () => {
                const valor = tipoArchivo ? input.files[0] : input.value;
                validarCampo(input, camposPersona[id].validar(valor), camposPersona[id].mensaje);
            });
        });

        document.getElementById("formAgregarPersona").addEventListener("submit", function(e) {
            e.preventDefault();
            let valido = true;

            Object.keys(camposPersona).forEach(id => {
                const input = document.getElementById(id);
                const valor = input.type === "file" ? input.files[0] : input.value;
                const esValido = camposPersona[id].validar(valor);
                validarCampo(input, esValido, camposPersona[id].mensaje);
                if (!esValido) valido = false;
            });

            if (valido) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Formulario válido!',
                    text: 'Datos de persona enviados correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    this.submit(); //Enviar formulario cuando se cierra el popup
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Errores en el formulario',
                    text: 'Por favor, corrige los errores antes de enviar.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    </script>


    <script>
        //<!--- Para sacar foto con camara --->
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('photo');
            const inputBase64 = document.getElementById('foto_base64');
            const captureBtn = document.getElementById('capture-btn');
            const cancelarBtn = document.getElementById('cancelarFoto');
            const context = canvas.getContext('2d');
            const inputArchivo = document.getElementById('fotoUsuario');
            const mensajeCamara = document.getElementById('mensaje-camara');

            let stream;

            // Paso 1: Preguntamos al usuario
            const pedirPermiso = confirm("¿Deseás activar la cámara para tomar una foto?");
            if (!pedirPermiso) {
                alert("No se activó la cámara. Podés subir una foto desde tu dispositivo.");
                mensajeCamara.style.display = 'block';
                return;
            }

            // Paso 2: Verificamos si el navegador ya tiene el permiso bloqueado
            if (navigator.permissions) {
                navigator.permissions.query({
                    name: 'camera'
                }).then((permiso) => {
                    if (permiso.state === 'denied') {
                        alert("La cámara está bloqueada en el navegador. Activala desde la configuración del sitio.");
                        document.getElementById('bloque-camara').style.display = 'none';
                        mensajeCamara.style.display = 'block';
                    } else {
                        iniciarCamara(); // Intentamos activarla
                    }
                }).catch(() => {
                    iniciarCamara(); // Si no se puede verificar permisos, lo intentamos igual
                });
            } else {
                iniciarCamara(); // Navegadores sin support para Permissions API
            }

            function iniciarCamara() {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then((s) => {
                        stream = s;
                        const videoTrack = stream.getVideoTracks()[0];
                        if (videoTrack && videoTrack.readyState === "live") {
                            video.srcObject = stream;
                            document.getElementById('bloque-camara').style.display = 'block'; //mostrar
                            video.style.display = 'block';
                            mensajeCamara.style.display = 'none';
                        } else {
                            throw new Error("La cámara no está activa");
                        }
                    })
                    .catch((error) => {
                        console.warn("No se pudo acceder a la cámara:", error);
                        document.getElementById('mensaje-camara').style.display = 'block';

                        //ocultamos el boton para sacar foto y texto
                        document.getElementById('bloque-camara').style.display = 'none';

                        alert("No se pudo acceder a la cámara. Podés subir una imagen desde tu dispositivo.");
                        mensajeCamara.style.display = 'block';
                        video.style.display = 'none';
                    });
            }

            // Botón para tomar foto
            captureBtn.addEventListener('click', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const base64Image = canvas.toDataURL('image/png');
                inputBase64.value = base64Image;

                // Ocultar video, mostrar imagen
                video.style.display = 'none';
                canvas.style.display = 'block';
                captureBtn.style.display = 'none';
                cancelarBtn.style.display = 'inline';

                inputArchivo.disabled = true;

                // Detener cámara
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });

            // Botón cancelar foto
            cancelarBtn.addEventListener('click', () => {
                inputBase64.value = '';
                canvas.style.display = 'none';
                video.style.display = 'block';
                captureBtn.style.display = 'inline';
                cancelarBtn.style.display = 'none';
                inputArchivo.disabled = false;

                iniciarCamara(); // Vuelve a pedir cámara
            });
        });


        //<!--- Para subir imagen --->

        const fotoInput = document.getElementById('fotoUsuario');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const btnEliminarImagen = document.getElementById('btnEliminarImagen');

        fotoInput.addEventListener('change', async function() {
            const file = this.files[0];

            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
                if (!validTypes.includes(file.type)) {
                    alert('Solo se permiten imágenes JPG, PNG o SVG.');
                    this.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                if (file.type === 'image/svg+xml') {
                    if (file.size > 450 * 1024) {
                        alert('La imagen SVG supera los 450 KB. Por favor, elige una más liviana.');
                        this.value = '';
                        previewContainer.style.display = 'none';
                        return;
                    }

                    // Mostrar vista previa sin redimensionar
                    previewImage.src = URL.createObjectURL(resized);
                    previewContainer.style.display = 'block';
                    document.getElementById('capture-btn').disabled = true;

                    // No redimensionar SVG, pero actualizar el input (opcional)
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    this.files = dataTransfer.files;
                    return;
                }

                // Si es JPG o PNG → redimensionar
                const resized = await redimensionarImagen(file, 250, 250);

                if (resized.size > 450 * 1024) {
                    alert('La imagen redimensionada supera los 450 KB. Por favor, elige una más liviana.');
                    this.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }

                // Mostrar vista previa redimensionada
                previewImage.src = URL.createObjectURL(resized);
                previewContainer.style.display = 'block';

                //Bloquear cámara y botones 
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                video.style.display = 'none';
                canvas.style.display = 'none';
                captureBtn.style.display = 'none';
                cancelarBtn.style.display = 'none';

                // Reemplazar el archivo original con el redimensionado
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(resized);
                this.files = dataTransfer.files;
            } else {
                previewContainer.style.display = 'none';
            }
        });

        btnEliminarImagen.addEventListener('click', () => {
            fotoInput.value = '';
            previewImage.src = '';
            previewContainer.style.display = 'none';
            fotoInput.classList.remove('is-valid', 'is-invalid');

            //Volver a mostrar botones 
            captureBtn.style.display = 'inline';
            video.style.display = 'block';

            //Reiniciar camara
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then((s) => {
                    stream = s;
                    video.srcObject = stream;
                })
                .catch((error) => {
                    console.error("No se pudo reactivar la cámara: ", error);
                });
        });

        // Función para redimensionar imagen usando canvas (solo JPG o PNG)
        async function redimensionarImagen(file, width, height) {
            return new Promise((resolve, reject) => {
                if (file.type === 'image/svg+xml') {
                    // No redimensionar SVG
                    resolve(file);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob(function(blob) {
                            const newFile = new File([blob], file.name, {
                                type: file.type
                            });
                            resolve(newFile);
                        }, file.type, 0.9); // calidad 90%
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
</body>

</html>