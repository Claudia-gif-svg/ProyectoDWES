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
        $mensaje = crearRegistro($conexion, 'proyecto', $_POST);
    } elseif ($accion_post === 'modificar') {
        $mensaje = modificarRegistro($conexion, 'proyecto', $_POST);
    } elseif ($accion_post === 'eliminar') {
        $mensaje = eliminarRegistro($conexion, 'proyecto', $_POST['id_proyecto']);
    }
}

$accion = $_GET['accion'] ?? '';

$stmt = $conexion->query("SELECT id_proyecto, nombre FROM proyectos");
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <i class="fa-solid fa-folder-plus"></i> Crear
        </a>
        <a href="?accion=modificar" class="menu__button menu__modificar <?= $accion === 'modificar' ? 'active' : '' ?>">
            <i class="fa-solid fa-pen-to-square"></i> Modificar
        </a>
        <a href="?accion=eliminar" class="menu__button menu__eliminar <?= $accion === 'eliminar' ? 'active' : '' ?>">
            <i class="fa-solid fa-trash"></i> Eliminar
        </a>
    </nav>

    ---

    <?php if ($accion === 'crear'): ?>
        <h3 class="panel__subtitle">Crear Proyecto</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form form__proyecto">
            <input type="hidden" name="accion" value="crear">

            <label for="nombre_proyecto">Nombre:</label>
            <input type="text" name="nombre_proyecto" class="form__input" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" class="form__input" rows="4"></textarea>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-plus"></i> Guardar Proyecto
            </button>
        </form>

    <?php elseif ($accion === 'modificar'): ?>
        <h3 class="panel__subtitle">Modificar Proyecto</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form form__proyecto">
            <input type="hidden" name="accion" value="modificar">

            <label for="id_proyecto">Selecciona Proyecto:</label>
            <select name="id_proyecto" class="form__input" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($proyectos as $p): ?>
                    <option value="<?= $p['id_proyecto'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="nombre_proyecto">Nuevo Nombre (opcional):</label>
            <input type="text" name="nombre_proyecto" class="form__input">

            <label for="descripcion">Nueva Descripción (opcional):</label>
            <textarea name="descripcion" class="form__input" rows="4"></textarea>

            <button type="submit" class="form__button">
                <i class="fa-solid fa-floppy-disk"></i> Actualizar Proyecto
            </button>
        </form>

    <?php elseif ($accion === 'eliminar'): ?>
        <h3 class="panel__subtitle">Eliminar Proyecto</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="form form__proyecto">
            <input type="hidden" name="accion" value="eliminar">

            <label for="id_proyecto">Selecciona Proyecto a eliminar:</label>
            <select name="id_proyecto" class="form__input" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($proyectos as $p): ?>
                    <option value="<?= $p['id_proyecto'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="form__button btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este proyecto?')">
                <i class="fa-solid fa-trash-can"></i> Eliminar Definitivamente
            </button>
        </form>
    <?php endif; ?>

    <a href="admin.php" class="form__button volver__button">
        <i class="fa-solid fa-arrow-left"></i> Volver al Panel
    </a>

    <hr>

    <h3 class="panel__subtitle">Listado de Proyectos Actuales</h3>
    <?php imprimirProyectos($conexion); ?>

</div>
</body>
</html>
