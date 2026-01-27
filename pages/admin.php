<?php
session_start();

// Solo administradores
if (!isset($_SESSION["correo"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

require_once "../basedatos/conexionbd.php";
require_once "funciones.php";

// Definimos qué formulario mostrar por defecto
$formulario = $_GET['form'] ?? 'usuarios';
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
<header class="header">
<h1 class="title">Bienvenido al Panel de Administrador</h1>

<!-- Menú para elegir formulario -->
<nav>
    <a href="?form=usuarios">Usuarios</a> 
    <a href="?form=proyectos">Proyectos</a>
</nav>
</header>
<?php if ($formulario === 'usuarios'): ?>

    <h3 class="login__title">Gestionar Usuarios</h3>
    <form method="POST" action="admin_acciones.php">
        <input type="hidden" name="tipo" value="usuario">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required>

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required>

        <label for="rol">Rol:</label>
        <select name="rol" id="rol" required>
            <option value="usuario">Usuario</option>
            <option value="administrador">Administrador</option>
        </select>

        <button type="submit">
            <i class="fa-solid fa-user-plus"></i> Crear Usuario
        </button>
    </form>

<?php elseif ($formulario === 'proyectos'): ?>
    <h3>Gestionar Proyectos</h3>
    <form method="POST" action="admin_acciones.php">
        <input type="hidden" name="tipo" value="proyecto">

        <label for="nombre_proyecto">Nombre del Proyecto:</label>
        <input type="text" id="nombre_proyecto" name="nombre_proyecto" required>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion"></textarea>

        <button type="submit">
            <i class="fa-solid fa-folder-plus"></i> Crear Proyecto
        </button>
    </form>
<?php endif; ?>

</body>
</html>
