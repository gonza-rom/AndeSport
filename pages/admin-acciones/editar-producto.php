<?php
session_start();
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin', 'stock'])) {
    header("Location: ../index.php");
    exit();
}

include '../../includes/conexion.php';

// Obtener el id del producto desde GET o POST (mejor GET en URL)
$id_producto = $_GET['id_producto'] ?? null;

if (!$id_producto) {
    die("Error: id de producto no especificado");
}

// Obtener datos enviados por POST
$nombre = $_POST['nombre'] ?? '';
$id_categoria = $_POST['id_categoria'] ?? '';
$precio = $_POST['precio'] ?? '';
$stock = $_POST['stock'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

// Validar datos mínimos aquí según necesites

// Procesar la imagen (si se subió una nueva)
if (isset($_FILES['img_producto']) && $_FILES['img_producto']['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = $_FILES['img_producto']['name'];
    $tmpNombre = $_FILES['img_producto']['tmp_name'];

    $rutaDestino = '../../img_producto/' . basename($nombreArchivo);

    // Mover archivo subido a la carpeta destino
    if (move_uploaded_file($tmpNombre, $rutaDestino)) {
        $rutaImagen = 'img_producto/' . basename($nombreArchivo);
    } else {
        $rutaImagen = null;
    }
} else {
    $rutaImagen = null; // No se subió imagen nueva
}

// Si no se subió imagen nueva, mantenemos la anterior
if (!$rutaImagen) {
    // Consultar la ruta actual de imagen para este producto
    $query = "SELECT img_producto FROM producto WHERE id_producto = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();

    // Enlazar variable para resultado
    $stmt->bind_result($img_producto);
    $stmt->fetch();

    $rutaImagen = $img_producto;

    $stmt->close();
}

// Ahora hacemos el UPDATE con todos los datos
$sqlUpdate = "UPDATE producto SET 
                nombre = ?, 
                id_categoria = ?, 
                precio = ?, 
                stock = ?, 
                descripcion = ?, 
                img_producto = ?
              WHERE id_producto = ?";

$stmt = $conexion->prepare($sqlUpdate);
$actualizo = $stmt->execute([$nombre, $id_categoria, $precio, $stock, $descripcion, $rutaImagen, $id_producto]);

if ($actualizo) {
    echo "<script>
            alert('Producto actualizado correctamente.');
            window.location.href='../administrador.php';
          </script>";
    exit();
} else {
    echo "<script>
            alert('Error al actualizar el producto: " . addslashes($stmt->error) . "');
            window.location.href='../administrador.php';
          </script>";
}
?>