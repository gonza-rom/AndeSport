<?php
session_start();
if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['admin', 'stock'])) {
    header("Location: ../index.php");
    exit();
}

include '../../includes/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'] ?? null;
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    $img_producto_ruta = '';

    if (!isset($_FILES['img_producto'])) {
        die("No se recibió el archivo img_producto");
    }

    if ($_FILES['img_producto']['error'] !== UPLOAD_ERR_OK) {
        die("Error en la subida del archivo. Código error: " . $_FILES['img_producto']['error']);
    }

    $img_producto = $_FILES['img_producto']['name'];
    $img_producto_tmp = $_FILES['img_producto']['tmp_name'];

    //Ruta fisica(servidor) y ruta web(navegador)
    $nombre_archivo = uniqid() . "_" . basename($img_producto);
    $ruta_fisica = "../../img_producto/" . $nombre_archivo; //guardamos el archivo
    $img_producto_ruta = "img_producto/" . $nombre_archivo; //guardamos en la base y mostramos en el fronted

    if (!move_uploaded_file($img_producto_tmp, $ruta_fisica)) {
        echo "Error al subir la imagen desde archivo.<br>";
        echo "Nombre archivo temporal: " . $img_producto_tmp . "<br>";
        echo "Destino: " . $ruta_fisica . "<br>";
        print_r($_FILES['img_producto']);
        exit();
    }

    // Validar campos básicos
    if (empty($nombre) || empty($id_categoria) || empty($precio) || empty($stock)) {
        die("Todos los campos obligatorios deben completarse.");
    }

    $sql = "INSERT INTO producto (nombre, descripcion, precio, stock, id_categoria, img_producto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdiis", $nombre, $descripcion, $precio, $stock, $id_categoria, $img_producto_ruta);

    if ($stmt->execute()) {
        $mensaje = 'Producto agregado exitosamente.';
    } else {
        $mensaje = 'Error al agregar el producto.';
    }

    // Redirigir según el rol
    if ($_SESSION['rol'] === 'admin') {
        $redirigir = '../administrador.php'; // Cambiá esto por la ruta a tu panel de admin
    } elseif ($_SESSION['rol'] === 'stock') {
        $redirigir = '../controlstock.php'; // Ya es correcto si este es el panel del rol "stock"
    } else {
        $redirigir = '../../index.php'; // Por si acaso
    }

    // Mostrar mensaje y redirigir
    echo "<script>
    alert('$mensaje');
    window.location.href = '$redirigir';
    </script>";

    $stmt->close();
    $conexion->close();
} else {
    header("Location: ../controlstock.php");
}
