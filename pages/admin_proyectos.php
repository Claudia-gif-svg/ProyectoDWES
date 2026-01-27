<?php
session_start();

if (!isset($_SESSION["correo"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}
$stmt = $conexion->query("SELECT id_proyecto, nombre FROM proyectos");
                $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "../basedatos/conexionbd.php";
require_once "funciones.php";

$accion = $_GET['accion'] ?? 'crear';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Proyectos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">
    <h1 class="header__title">Administrar Proyectos</h1>
</div>

<div class="panel">

    <!-- Botones de acción -->
    <nav class="panel__nav">
        <a href="?accion=crear" class="menu__button menu__crear">
            <i class="fa-solid fa-folder-plus"></i> Crear
        </a>
        <a href="?accion=modificar" class="menu__button menu__modificar">
            <i class="fa-solid fa-pen-to-square"></i> Modificar
        </a>
        <a href="?accion=eliminar" class="menu__button menu__eliminar">
            <i class="fa-solid fa-trash"></i> Eliminar
        </a>
    </nav>

    <?php if ($accion === 'crear'): ?>
        <h3 class="panel__subtitle">Crear Proyecto</h3>
        <form method="POST" action="admin_acciones.php" class="form form__proyecto">
            <input type="hidden" name="tipo" value="proyecto">
            <input type="hidden" name="accion" value="crear">

            <label for="nombre_proyecto">Nombre:</label>
            <input type="text" name="nombre_proyecto" class="form__input" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" class="form__input" rows="4"></textarea>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-folder-plus"></i> Crear Proyecto
            </button>
        </form>

    <?php elseif ($accion === 'modificar'): ?>
        <h3 class="panel__subtitle">Modificar Proyecto</h3>
        <form method="POST" action="admin_acciones.php" class="form form__proyecto">
            <input type="hidden" name="tipo" value="proyecto">
            <input type="hidden" name="accion" value="modificar">

            <label for="id_proyecto">Selecciona Proyecto:</label>
            <select name="id_proyecto" class="form__input" required>
                <?php
                
                foreach ($proyectos as $p) {
                    echo "<option value='{$p['id_proyecto']}'>{$p['nombre']}</option>";
                }
                ?>
            </select>

            <label for="nombre_proyecto">Nuevo Nombre:</label>
            <input type="text" name="nombre_proyecto" class="form__input">

            <label for="descripcion">Nueva Descripción:</label>
            <textarea name="descripcion" class="form__input" rows="4"></textarea>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-pen-to-square"></i> Modificar Proyecto
            </button>
        </form>

    <?php elseif ($accion === 'eliminar'): ?>
        <h3 class="panel__subtitle">Eliminar Proyecto</h3>
        <form method="POST" action="admin_acciones.php" class="form form__proyecto">
            <input type="hidden" name="tipo" value="proyecto">
            <input type="hidden" name="accion" value="eliminar">

            <label for="id_proyecto">Selecciona Proyecto:</label>
            <select name="id_proyecto" class="form__input" required>
                <?php
                foreach ($proyectos as $p) {
                    echo "<option value='{$p['id_proyecto']}'>{$p['nombre']}</option>";
                }
                ?>
            </select>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-trash"></i> Eliminar Proyecto
            </button>
        </form>
    <?php endif; ?>

    <!-- Tabla de proyectos -->
    <h3 class="panel__subtitle">Proyectos Actuales</h3>
    <?php imprimirProyectos($conexion); ?>

</div>
</body>
</html>
