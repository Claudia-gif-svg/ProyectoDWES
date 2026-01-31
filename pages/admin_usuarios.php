<?php
session_start();

if (!isset($_SESSION["correo"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

require_once "../basedatos/conexionbd.php";
require_once "funciones.php";

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion_post = $_POST['accion'] ?? '';
    
    if ($accion_post === 'crear') {

        $mensaje = crearRegistro($conexion, 'usuario', $_POST);
    } elseif ($accion_post === 'modificar') {
        $mensaje = modificarRegistro($conexion, 'usuario', $_POST);
    } elseif ($accion_post === 'eliminar') {
        $mensaje = eliminarRegistro($conexion, 'usuario', $_POST['correo']);
    }
}

$accion = $_GET['accion'] ?? '';

$stmt = $conexion->query("SELECT correo, nombre FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <?php if ($mensaje): ?>
        <?php 
            $clase = "alerta--exito"; 
            if (str_contains($mensaje, 'Error')) {
                $clase = "alerta--error";
            }
        ?>
        <div class="alerta <?php echo $clase; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <nav class="panel__nav">
        <a href="?accion=crear" class="menu__button menu__crear <?= $accion === 'crear' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-plus"></i> Crear
        </a>
        <a href="?accion=modificar" class="menu__button menu__modificar <?= $accion === 'modificar' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-pen"></i> Modificar
        </a>
        <a href="?accion=eliminar" class="menu__button menu__eliminar <?= $accion === 'eliminar' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-minus"></i> Eliminar
        </a>
    </nav>

    ---

    <?php if ($accion === 'crear'): ?>
        <h3 class="panel__subtitle">Crear Usuario</h3>
        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" class="form form__usuario">
            <input type="hidden" name="accion" value="crear">

            <label>Nombre:</label>
            <input type="text" name="nombre" class="form__input" required>

            <label>Correo:</label>
            <input type="email" name="correo" class="form__input" required>

            <label>Contraseña:</label>
            <input type="password" name="contrasena" class="form__input" required>

            <label>Rol:</label>
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
        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" class="form form__usuario">
            <input type="hidden" name="accion" value="modificar">

            <label>Selecciona Usuario:</label>
            <select name="correo" class="form__input" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['correo'] ?>"><?= htmlspecialchars($u['nombre']) ?> (<?= $u['correo'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <label>Nuevo Nombre (opcional):</label>
            <input type="text" name="nombre" class="form__input">

            <label>Nuevo Rol:</label>
            <select name="rol" class="form__input">
                <option value="">-- Mantener actual --</option>
                <option value="usuario">Usuario</option>
                <option value="administrador">Administrador</option>
            </select>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-user-pen"></i> Actualizar Usuario
            </button>
        </form>

    <?php elseif ($accion === 'eliminar'): ?>
        <h3 class="panel__subtitle">Eliminar Usuario</h3>
        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>" class="form form__usuario">
            <input type="hidden" name="accion" value="eliminar">

            <label>Selecciona Usuario:</label>
            <select name="correo" class="form__input" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['correo'] ?>"><?= htmlspecialchars($u['nombre']) ?> (<?= $u['correo'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="form__button btn-danger" onclick="return confirm('¿Borrar este usuario?')">
                <i class="fa-solid fa-user-minus"></i> Eliminar Definitivamente
            </button>
        </form>
    <?php endif; ?>

    <a href="admin.php" class="form__button volver__button">
        <i class="fa-solid fa-arrow-left"></i> Volver al Panel
    </a>

    <hr>
    <h3 class="panel__subtitle">Usuarios Actuales</h3>
    <?php imprimirUsuarios($conexion); ?>

</div>
</body>
</html>
