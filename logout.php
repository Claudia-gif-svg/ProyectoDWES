<?php
session_start();
require_once "./pages/funciones.php"; // incluir la función

if (isset($_SESSION["correo"])) {
    // Registrar log antes de destruir la sesión
    escribirLog("Usuario {$_SESSION['correo']} cerró sesión", "INFO");
}

// Vaciar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: index.php");
exit;
?>
