<?php
session_start();
include '../includes/conexion.php';

// Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: inicio-sesion.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Traer datos actuales
$sql = "SELECT p.id_persona, p.nombre, p.apellido, p.calle, p.altura,
        p.provincia AS codprov, p.departamento AS coddpto, p.localidad AS codloc, p.municipio AS codmun,
        p.latitud, p.longitud,
        prov.nomprov, dpto.nomdpto, loc.nomloc, mun.nommun
        FROM persona p
        LEFT JOIN provincias prov ON p.provincia = prov.codprov
        LEFT JOIN dpto dpto ON p.departamento = dpto.coddpto
        LEFT JOIN localidades loc ON p.localidad = loc.codloc
        LEFT JOIN municipio mun ON p.municipio = mun.codmun
        WHERE p.id_persona=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$persona = $result->fetch_assoc();
$stmt->close();

// Procesar actualización si envían formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calle = $_POST['calle'];
    $altura = $_POST['altura'];
    $provincia = $_POST['provincia'];
    $departamento = $_POST['departamento'];
    $localidad = $_POST['localidad'];
    $municipio = $_POST['municipio'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];

    $sql_update = "UPDATE persona 
                   SET calle=?, altura=?, provincia=?, departamento=?, localidad=?, municipio=?, latitud=?, longitud=? 
                   WHERE id_persona=?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param("ssiiiiddi", $calle, $altura, $provincia, $departamento, $localidad, $municipio, $latitud, $longitud, $id_usuario);
    $stmt->execute();
    $stmt->close();

    header("Location: pago.php");
    exit;
}

// Traer provincias para el select
$provincias = $conexion->query("SELECT codprov, nomprov FROM provincias ORDER BY nomprov ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>#map { height: 400px; }</style>
</head>
<body class="container mt-5">

<h3>Dirección de Entrega</h3>

<!-- Vista en solo lectura -->
<div id="direccion-actual" class="mb-4">
    <p><strong>Calle:</strong> <?= htmlspecialchars($persona['calle']) ?> <?= htmlspecialchars($persona['altura']) ?></p>
    <p><strong>Provincia:</strong> <?= htmlspecialchars($persona['nomprov']) ?></p>
    <p><strong>Departamento:</strong> <?= htmlspecialchars($persona['nomdpto']) ?></p>
    <p><strong>Localidad:</strong> <?= htmlspecialchars($persona['nomloc']) ?></p>
    <p><strong>Municipio:</strong> <?= htmlspecialchars($persona['nommun']) ?></p>
    <a href="ver-carrito.php" class="btn btn-success">Volver al carrito</a>
    <button type="button" id="btnModificar" class="btn btn-primary">Modificar dirección</button>
    <a href="pago.php" class="btn btn-success">Continuar con esta dirección</a>
</div>
  
<!-- Formulario oculto por defecto -->
<form method="POST" id="formDireccion" style="display:none;">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Calle</label>
            <input type="text" name="calle" class="form-control" value="<?= htmlspecialchars($persona['calle']) ?>" required>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Altura</label>
            <input type="text" name="altura" class="form-control" value="<?= htmlspecialchars($persona['altura']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Provincia</label>
            <select class="form-control" id="provincia" name="provincia" required>
                <option value="">Seleccione</option>
                <?php while ($prov = $provincias->fetch_assoc()): ?>
                    <option value="<?= $prov['codprov'] ?>" <?= ($prov['codprov']==$persona['codprov'])?'selected':'' ?>>
                        <?= $prov['nomprov'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Departamento</label>
            <select class="form-control" id="departamento" name="departamento" required></select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Localidad</label>
            <select class="form-control" id="localidad" name="localidad" required></select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Municipio</label>
            <select class="form-control" id="municipio" name="municipio" required></select>
        </div>
    </div>

    <input type="hidden" name="latitud" id="latitud" value="<?= $persona['latitud'] ?>">
    <input type="hidden" name="longitud" id="longitud" value="<?= $persona['longitud'] ?>">

    <div id="map" class="mb-3"></div>

    <button type="submit" class="btn btn-success">Guardar y continuar</button>
    <a href="datos.php" class="btn btn-secondary">Volver</a>
</form>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
// Mostrar formulario al hacer click en "Modificar dirección"
document.getElementById('btnModificar').addEventListener('click', function() {
    document.getElementById('direccion-actual').style.display = 'none';
    document.getElementById('formDireccion').style.display = 'block';
});

// Iniciar mapa
const lat = <?= $persona['latitud'] ?: '-31.416667' ?>;
const lng = <?= $persona['longitud'] ?: '-64.183333' ?>;

const map = L.map('map').setView([lat, lng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let marker = L.marker([lat, lng], {draggable:true}).addTo(map);
marker.on('dragend', function(e) {
    const pos = marker.getLatLng();
    document.getElementById('latitud').value = pos.lat;
    document.getElementById('longitud').value = pos.lng;
});

map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    document.getElementById('latitud').value = e.latlng.lat;
    document.getElementById('longitud').value = e.latlng.lng;
});
</script>
<script>
function mostrarFormulario() {
    document.getElementById("direccion-actual").style.display = "none";
    document.getElementById("form-direccion").style.display = "block";
}

document.addEventListener("DOMContentLoaded", function() {
  const provincia = document.getElementById("provincia");
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

          // Reiniciar localidades y municipios
          localidad.innerHTML = '<option value="">Seleccione una localidad</option>';
          municipio.innerHTML = '<option value="">Seleccione un municipio</option>';
        });
    }
  });

  departamento.addEventListener("change", () => {
    const coddpto = departamento.value;
    if (coddpto) {
      // Localidades
      fetch(`obtener_opciones.php?tipo=localidades&coddpto=${coddpto}`)
        .then(res => res.json())
        .then(data => {
          localidad.innerHTML = '<option value="">Seleccione una localidad</option>';
          data.forEach(loc => {
            localidad.innerHTML += `<option value="${loc.codloc}">${loc.nomloc}</option>`;
          });
        });

      // Municipios
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
</script>
</body>
</html>
