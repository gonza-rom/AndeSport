<?php
//solicitar el archivo de conexion a la base de datos
include '../includes/conexion.php';

//Enviar datos a la BD 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Datos del formulario
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $dni = $_POST['dni'];
  $fecha_nacimiento = $_POST['fecha_nacimiento'];
  $genero = $_POST['genero'];
  $provincia = $_POST['provincia'];
  $departamento = $_POST['departamento'];
  $localidad = $_POST['localidad'];
  $municipio = $_POST['municipio'];
  $calle = $_POST['calle'];
  $altura = $_POST['altura'];
  $latitud = $_POST['latitud'];
  $longitud = $_POST['longitud'];

  $foto_dni_frente = $_FILES['foto_dni_frente']['name'];
  $foto_dni_frente_tmp = $_FILES['foto_dni_frente']['tmp_name'];
  $foto_dni_frente_ruta = "../img_persona/" . uniqid() . "_" . basename($foto_dni_frente);

  $foto_dni_dorso = $_FILES['foto_dni_dorso']['name'];
  $foto_dni_dorso_tmp = $_FILES['foto_dni_dorso']['tmp_name'];
  $foto_dni_dorso_ruta = "../img_persona/" . uniqid() . "_" . basename($foto_dni_dorso);

  // Mover imagenes a la carpeta
  move_uploaded_file($foto_dni_frente_tmp, $foto_dni_frente_ruta);
  move_uploaded_file($foto_dni_dorso_tmp, $foto_dni_dorso_ruta);

  // Insertar en persona
  $sql = "INSERT INTO persona (nombre, apellido, dni, fecha_nacimiento, genero, provincia, departamento, localidad, municipio, calle, altura, latitud, longitud, foto_dni_frente, foto_dni_dorso)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("sssssssssssssss", $nombre, $apellido, $dni, $fecha_nacimiento, $genero, $provincia, $departamento, $localidad, $municipio, $calle, $altura, $latitud, $longitud, $foto_dni_frente_ruta, $foto_dni_dorso_ruta);

  if ($stmt->execute()) {
    $id_persona = $conexion->insert_id;
    // Redirigir al formulario de usuario pasando el id_persona
    header("Location: registro-usuario.php?id_persona=$id_persona");
    exit();
  } else {
    echo "Error al registrar persona: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agregar Persona - Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
  <header class="position-relative py-3 px-6">
    <span class="align-items-center">Registro de Persona</span>
    <a href="./inicio-sesion.php" class="btn btn-light text-dark btn-sm position-absolute start-0 top-50 translate-middle-y ms-3">
      Volver
    </a>
  </header>

  <div class="form-wrapper">
    <h2 class="mb-4">Datos Personales</h2>
    <form id="formAgregarPersona" class="formulario-estilo formulario-ancho" method="POST" action="registro-persona.php" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="nombre" class="form-label">Nombre(s)</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
          <div class="invalid-feedback">El nombre solo puede contener letras y debe tener al menos 3 caracteres.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="apellido" class="form-label">Apellido(s)</label>
          <input type="text" class="form-control" id="apellido" name="apellido" required>
          <div class="invalid-feedback">El apellido solo puede contener letras y debe tener al menos 3 caracteres.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="dni" class="form-label">DNI</label>
          <input type="number" class="form-control" id="dni" name="dni" required>
          <div class="invalid-feedback">Ingrese un DNI válido entre 1000000 y 99999999</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
          <input type="date" class="form-control" id="fechaNacimiento" name="fecha_nacimiento" required>
          <div class="invalid-feedback">Ingrese una fecha válida. Debe ser mayor de 13 años.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="genero" class="form-label">Género</label>
          <select class="form-control" id="genero" name="genero" required>
            <option value="" disabled selected>Seleccione una opción</option>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
            <option value="No-binario">No Binario</option>
            <option value="Otro">Otro</option>
            <option value="Prefiero-no-decirlo">Prefiero no decirlo</option>
          </select>
          <div class="invalid-feedback">Seleccione un género.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="provinciaUsuario" class="form-label">Provincia</label>
          <?php
          // Obtener provincias desde la BD
          $consultaProvincias = $conexion->query("SELECT codprov, nomprov FROM provincias ORDER BY nomprov ASC");
          ?>

          <select class="form-control" id="provinciaUsuario" name="provincia" required>
            <option value="">Seleccione una provincia</option>
            <?php while ($prov = $consultaProvincias->fetch_assoc()): ?>
              <option value="<?= $prov['codprov'] ?>"><?= $prov['nomprov'] ?></option>
            <?php endwhile; ?>
          </select>
          <div class="invalid-feedback">Debe seleccionar una provincia.</div>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Departamento</label>
          <select class="form-control" id="departamento" name="departamento" required>
            <option value="">Seleccione un departamento</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Localidad</label>
          <select class="form-control" id="localidad" name="localidad" required>
            <option value="">Seleccione una localidad</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Municipio</label>
          <select class="form-control" id="municipio" name="municipio" required>
            <label class="form-label">Municipio</label>
            <option value="">Seleccione un municipio</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="calle" class="form-label">Calle</label>
          <input type="text" class="form-control" id="calle" name="calle" required>
        </div>

        <div class="col-md-6 mb-3">
          <label for="altura" class="form-label">Altura</label>
          <input type="number" class="form-control" id="altura" name="altura" required>
        </div>

        <div class="col-md-12 mb-3">
          <label for="coordenadas" class="form-label">Ubicación geográfica</label>
          <div id="map" style="height: 300px; border-radius: 10px; border: 1px solid #ccc;"></div>
          <input type="hidden" id="latitud" name="latitud">
          <input type="hidden" id="longitud" name="longitud">
          <div class="form-text">Seleccioná un punto en el mapa para guardar la ubicación.</div>
          <div class="mb-3">
            <div id="map-error" class="invalid-feedback d-block" style="display: none;">
              Por favor, seleccioná una ubicación en el mapa.
            </div>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="fotoDniFrente" class="form-label">Imagen del DNI (Frente)</label>
          <input type="file" class="form-control" id="fotoDniFrente" name="foto_dni_frente" accept="image/*" required>
          <div class="invalid-feedback">Debe subir la foto del frente del DNI.</div>

          <div class="mt-2">
            <img id="previewFrente" src="" alt="Vista previa frente DNI" class="img-fluid rounded shadow-sm" style="max-height: 200px; display: none;">
            <button type="button" id="btnEliminarFrente" class="btn btn-outline-danger btn-sm mt-2" style="display: none;">
              <i class="bi bi-x-circle"></i> Eliminar imagen
            </button>
          </div>
        </div>

        <div class="col-md-6 mb-3">
          <label for="fotoDniDorso" class="form-label">Foto del DNI (Dorso)</label>
          <input type="file" class="form-control" id="fotoDniDorso" name="foto_dni_dorso" accept="image/*" required>
          <div class="invalid-feedback">Debe subir la foto del dorso del DNI.</div>

          <div class="mt-2">
            <img id="previewDorso" src="" alt="Vista previa dorso DNI" class="img-fluid rounded shadow-sm" style="max-height: 200px; display: none;">
            <button type="button" id="btnEliminarDorso" class="btn btn-outline-danger btn-sm mt-2" style="display: none;">
              <i class="bi bi-x-circle"></i> Eliminar imagen
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar</button>
    </form>

  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const provincia = document.getElementById("provinciaUsuario");
      const departamento = document.getElementById("departamento");
      const localidad = document.getElementById("localidad");
      const municipio = document.getElementById("municipio");

      provincia.addEventListener("change", () => {
        const codprov = provincia.value;
        if (codprov) {
          fetch(`obtener_opciones.php?tipo=departamentos&codprov=${codprov}`)
            .then(res => res.json())
            .then(data => {
              departamento.innerHTML = '<option value="">Seleccione un departamento</option>';
              data.forEach(dep => {
                departamento.innerHTML += `<option value="${dep.coddpto}">${dep.nomdpto}</option>`;
              });
              localidad.innerHTML = '<option value="">Seleccione una localidad</option>';
              municipio.innerHTML = '<option value="">Seleccione un municipio</option>';
            });
        }
      });

      departamento.addEventListener("change", () => {
        const coddpto = departamento.value;
        if (coddpto) {
          fetch(`obtener_opciones.php?tipo=localidades&coddpto=${coddpto}`)
            .then(res => res.json())
            .then(data => {
              localidad.innerHTML = '<option value="">Seleccione una localidad</option>';
              data.forEach(loc => {
                localidad.innerHTML += `<option value="${loc.codloc}">${loc.nomloc}</option>`;
              });
            });

          fetch(`obtener_opciones.php?tipo=municipios&coddpto=${coddpto}`)
            .then(res => res.json())
            .then(data => {
              municipio.innerHTML = '<option value="">Seleccione un municipio</option>';
              data.forEach(mun => {
                municipio.innerHTML += `<option value="${mun.codmun}">${mun.nommun}</option>`;
              });
            });
        }
      });
    });

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

    const camposPersona = {
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
      provinciaUsuario: {
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
      municipio: {
        validar: (v) => limpiarTexto(v).length >= 2,
        mensaje: "Ingrese un municpio válida."
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

    // Asignar eventos input/change a los campos
    Object.keys(camposPersona).forEach(id => {
      const input = document.getElementById(id);

      if (!input) return; //si no existe, saltea

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
        if (!input) return;
        const valor = input.type === "file" ? input.files[0] : input.value;
        const esValido = camposPersona[id].validar(valor);
        validarCampo(input, esValido, camposPersona[id].mensaje);
        if (!esValido) valido = false;
      });

      // Validar latitud y longitud
      const lat = document.getElementById('latitud').value;
      const lng = document.getElementById('longitud').value;
      const mapError = document.getElementById('map-error');

      if (!lat || !lng) {
        mapError.style.display = 'block';
        valido = false;
      }

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

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Iniciar el mapa
    const map = L.map('map').setView([-34.6037, -58.3816], 13); // Coordenadas por defecto (Buenos Aires)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marcador;

    // Detectar ubicación del usuario
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        map.setView([lat, lng], 15);

        // Agregar marcador inicial 
        marcador = L.marker([lat, lng]).addTo(map);
        document.getElementById('latitud').value = lat;
        document.getElementById('longitud').value = lng;
      }, function(error) {
        console.warn("No se pudo obtener la ubicación del usuario:", error.message);
      });
    } else {
      console.warn("Geolocalización no soportada.");
    }

    //Clic para seleccionar nueva ubicacion
    map.on('click', function(e) {
      const {
        lat,
        lng
      } = e.latlng;

      // Guardar en los campos ocultos
      document.getElementById('latitud').value = lat.toFixed(6);
      document.getElementById('longitud').value = lng.toFixed(6);

      //Ocultar mensaje de error si hace click
      const errorDiv = document.getElementById('map-error');
      if (errorDiv) {
        errorDiv.style.display = 'none';
        errorDiv.classList.remove('d-block');
      }

      // Colocar marcador
      if (marcador) {
        marcador.setLatLng(e.latlng);
      } else {
        marcador = L.marker(e.latlng).addTo(map);
      }
    });
  </script>

  <script>
    // Función para escalar/redimensionar una imagen a un tamaño fijo con compresión JPEG
    function escalarImagen(file, width, height, calidad, callback) {
      const reader = new FileReader(); // Lector para leer el archivo como DataURL (base64)

      reader.onload = function(e) {
        const img = new Image(); // Crear una nueva imagen

        img.onload = function() {
          // Crear un lienzo (canvas) donde se dibujará la imagen escalada
          const canvas = document.createElement("canvas");
          canvas.width = width;
          canvas.height = height;

          const ctx = canvas.getContext("2d"); // Obtener el contexto 2D para dibujar
          ctx.drawImage(img, 0, 0, width, height); // Dibujar y redimensionar la imagen al canvas

          // Convertir el canvas a un blob JPEG con la calidad indicada
          canvas.toBlob(
            function(blob) {
              // Crear un nuevo archivo (File) a partir del blob resultante
              const archivoFinal = new File([blob], file.name, {
                type: "image/jpeg"
              });

              // Ejecutar el callback pasando el archivo escalado y la URL para vista previa
              callback(archivoFinal, URL.createObjectURL(blob));
            },
            "image/jpeg", // Tipo de imagen final
            calidad // Calidad de compresión (de 0 a 1)
          );
        };

        img.src = e.target.result; // Cargar la imagen con el contenido leído
      };

      reader.readAsDataURL(file); // Leer el archivo como DataURL (base64)
    }

    // ✅ NUEVA función para limpiar input, preview y clases de validación
    function limpiarImagen(idInput, idPreview, idBtnEliminar) {
      const input = document.getElementById(idInput);
      const preview = document.getElementById(idPreview);
      const btnEliminar = document.getElementById(idBtnEliminar);

      // Limpiar input file
      input.value = "";
      // Ocultar y limpiar preview
      preview.src = "";
      preview.style.display = "none";
      // Ocultar botón eliminar
      btnEliminar.style.display = "none";
      // Quitar validaciones de Bootstrap
      input.classList.remove("is-valid", "is-invalid");
    }

    // Función para manejar el cambio de archivo de un input file, escalarlo y mostrar vista previa
    function manejarImagen(idInput, idPreview, idBtnEliminar) {
      const input = document.getElementById(idInput); // Input de tipo file
      const preview = document.getElementById(idPreview); // Imagen para vista previa
      const btnEliminar = document.getElementById(idBtnEliminar);

      // Botón eliminar: limpiar imagen al hacer clic
      btnEliminar.addEventListener("click", function() {
        limpiarImagen(idInput, idPreview, idBtnEliminar);
      });

      input.addEventListener("change", function(event) {
        const file = event.target.files[0]; // Obtener el archivo seleccionado

        // Si no hay archivo o no es imagen, limpiar la vista previa
        if (!file || !file.type.startsWith("image/")) {
          limpiarImagen(idInput, idPreview, idBtnEliminar);
          //preview.src = "";
          //preview.style.display = "none";
          return;
        }

        // Escalar la imagen a 250x250 con calidad 0.9
        escalarImagen(file, 250, 250, 0.9, (imagenEscalada, urlPreview) => {
          // Mostrar la vista previa con la imagen escalada
          preview.src = urlPreview;
          preview.style.display = "block";
          btnEliminar.style.display = "inline-block"; //Mostrar boton eliminar

          // Reemplazar el archivo original en el input por la versión escalada
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(imagenEscalada);
          input.files = dataTransfer.files;

          // Verificar el tamaño final de la imagen escalada y mostrar advertencia si supera los 450 KB
          const kb = imagenEscalada.size / 1024;
          if (kb > 450) {
            alert("Advertencia: la imagen redimensionada supera los 450 KB.");
          }
        });
      });
    }

    // Llamar la función para manejar el frente y dorso del DNI
    document.addEventListener("DOMContentLoaded", () => {
      manejarImagen("fotoDniFrente", "previewFrente", "btnEliminarFrente");
      manejarImagen("fotoDniDorso", "previewDorso", "btnEliminarDorso");
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
          title: '¡Persona registrada correctamente!',
          text: 'Ahora completá el registro de usuario.',
          icon: 'success',
          confirmButtonText: 'Continuar'
        });
      });
    </script>
  <?php endif; ?>

</body>

</html>