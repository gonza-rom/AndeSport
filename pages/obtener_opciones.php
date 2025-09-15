<?php
include '../includes/conexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Siempre establecer cabecera JSON
header('Content-Type: application/json');

if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];

    if ($tipo === 'departamentos' && isset($_GET['codprov'])) {
        $codprov = $_GET['codprov'];
        $stmt = $conexion->prepare("SELECT coddpto, nomdpto FROM dpto WHERE codprov = ?");
        $stmt->bind_param("s", $codprov);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode($datos);
        exit;
    }

    if ($tipo === 'localidades' && isset($_GET['coddpto'])) {
        $coddpto = $_GET['coddpto'];
        $stmt = $conexion->prepare("SELECT codloc, nomloc FROM localidades WHERE coddpto = ?");
        $stmt->bind_param("s", $coddpto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode($datos);
    }

    if ($tipo === 'municipios' && isset($_GET['coddpto'])) {
        $coddpto = $_GET['coddpto'];
        $stmt = $conexion->prepare("SELECT codmun, nommun FROM municipio WHERE coddpto = ?");
        $stmt->bind_param("s", $coddpto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_all(MYSQLI_ASSOC);
        echo json_encode($datos);
    }
}
?>
