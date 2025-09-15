<?php
session_start(); // Inicia o reanuda la sesión
session_unset(); // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirige al inicio de sesión
header("Location: inicio-sesion.php");
exit();
?>