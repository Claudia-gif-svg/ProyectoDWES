<?php
session_start();

// Solo administradores
if (!isset($_SESSION["correo"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

require_once "../basedatos/conexionbd.php";
require_once "funciones.php";

$accion = $_GET['accion'] ?? 'crear';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">
    <h1 class="header__title">Administrar Usuarios</h1>
</div>

<div class="panel">

    <!-- Botones de acción -->
    <nav class="panel__nav">
        <a href="?accion=crear" class="menu__button menu__crear">
            <i class="fa-solid fa-user-plus"></i> Crear
        </a>
        <a href="?accion=modificar" class="menu__button menu__modificar">
            <i class="fa-solid fa-user-pen"></i> Modificar
        </a>
        <a href="?accion=eliminar" class="menu__button menu__eliminar">
            <i class="fa-solid fa-user-minus"></i> Eliminar
        </a>
    </nav>
<?php
// Cargar usuarios desde la base de datos
$stmt = $conexion->query("SELECT correo, nombre FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <?php if ($accion === 'crear'): ?>
        <h3 class="panel__subtitle">Crear Usuario</h3>
        <form method="POST" action="admin_acciones.php" class="form form__usuario">
            <input type="hidden" name="tipo" value="usuario">
            <input type="hidden" name="accion" value="crear">

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" class="form__input" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" class="form__input" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" class="form__input" required>

            <label for="rol">Rol:</label>
            <select name="rol" class="form__input" required>
                <option value="usuario">Usuario</option>
                <option value="administrador">Administrador</option>
            </select>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-user-plus"></i> Crear Usuario
            </button>
        </form>

    <?php elseif ($accion === 'modificar'): ?>
        <h3 class="panel__subtitle">Modificar Usuario</h3>
        <form method="POST" action="admin_acciones.php" class="form form__usuario">
            <input type="hidden" name="tipo" value="usuario">
            <input type="hidden" name="accion" value="modificar">

            <label for="correo">Selecciona Usuario:</label>
            <select name="correo" class="form__input" required>
                <?php
               
                foreach ($usuarios as $u) {
                    echo "<option value='{$u['correo']}'>{$u['nombre']} ({$u['correo']})</option>";
                }
                ?>
            </select>

            <label for="nombre">Nuevo Nombre:</label>
            <input type="text" name="nombre" class="form__input">

            <label for="rol">Nuevo Rol:</label>
            <select name="rol" class="form__input">
                <option value="">-- Mantener actual --</option>
                <option value="usuario">Usuario</option>
                <option value="administrador">Administrador</option>
            </select>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-user-pen"></i> Modificar Usuario
            </button>
        </form>

    <?php elseif ($accion === 'eliminar'): ?>
        <h3 class="panel__subtitle">Eliminar Usuario</h3>
        <form method="POST" action="admin_acciones.php" class="form form__usuario">
            <input type="hidden" name="tipo" value="usuario">
            <input type="hidden" name="accion" value="eliminar">

            <label for="correo">Selecciona Usuario:</label>
            <select name="correo" class="form__input" required>
                <?php
                foreach ($usuarios as $u) {
                    echo "<option value='{$u['correo']}'>{$u['nombre']} ({$u['correo']})</option>";
                }
                ?>
            </select>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-user-minus"></i> Eliminar Usuario
            </button>
        </form>
    <?php endif; ?>

    <!-- Tabla de usuarios -->
    <h3 class="panel__subtitle">Usuarios Actuales</h3>
    <?php imprimirUsuarios($conexion); ?>

</div>
</body>
</html>
