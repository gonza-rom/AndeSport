<?php
session_start(); // Iniciar sesion
include '../includes/conexion.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];

    //Buscar el usuario en la base de datos
    $sql = "SELECT * FROM usuario where email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows === 1){
        $usuario = $resultado->fetch_assoc();

        //Verificamos contraseña
        if(password_verify($contraseña, $usuario['contraseña'])){
            //Guardar datos en sesion
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            //Redigirimos segun el rol
            if($usuario['rol'] == 'admin'){
                header("Location: administrador.php");
            }elseif($usuario['rol'] == 'cliente'){
                header("Location: ../index.php");
            }elseif($usuario['rol'] == 'gerente'){
                header("Location: gerente.php");
            }elseif($usuario['rol'] == 'repartidor'){
                header("Location: pedidos.php");
            }elseif($usuario['rol'] == 'stock'){
                header("Location: controlstock.php");
            }else{
                header("Location: index.php");
            }
            exit();
        }else{
            echo "<script>alert('Contraseña incorrecta'); window.location.href='inicio-sesion.php';</script>";
        }
    }else{
        echo "<script>alert('Usuario no encontrado'); window.location.href='inicio-sesion.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Inicio de Sesión - AndeSport</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/inicio-sesion.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h1 class="logo">AndeSport</h1>
            <h4 style="color: var(--primary-color);">Inicio de Sesión</h4>
        </div>
        <form method="POST" action="inicio-sesion.php">
            <!-- Correo Electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required>
            </div>
            <!-- Contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="contraseña" placeholder="Contraseña" required>
            </div>
            <!-- Recordarme -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <!-- Botón de Inicio de Sesión -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
            <!-- Botón de Registrarse -->
             <br>
            <div class="d-grid">
               <a href="registro-persona.php" class="btn btn-primary">Registrarme</a>
            </div>
            <br>
            <!-- Enlace de Recuperación de Contraseña -->
            <div class="text-center mt-3">
                <a href="olvide-contraseña.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="text-center mt-3">
                <a href="../index.php" class="forgot-password">Volver al inicio</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
