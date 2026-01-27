<?php
session_start();

// Solo administradores
if (!isset($_SESSION["correo"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">
    <h1 class="header__title">Bienvenido al Panel de Administrador</h1>
</div>

<div class="panel">

    <!-- Botones principales -->
    <nav class="panel__nav">
        <a href="admin_usuarios.php" class="menu__button menu__usuarios">
            <i class="fa-solid fa-users"></i> Usuarios
        </a>
        <a href="admin_proyectos.php" class="menu__button menu__proyectos">
            <i class="fa-solid fa-folder"></i> Proyectos
        </a>
    </nav>

    <p class="panel__info">Selecciona una opción para gestionar Usuarios o Proyectos.</p>

</div>

</body>
</html>
