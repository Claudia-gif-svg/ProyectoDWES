<?php
session_start();
if (!isset($_SESSION["correo"])) {
    header("Location: ../index.php");
    exit;
}

require_once "../basedatos/conexionbd.php";
require_once "funciones.php";

$mensaje = "";
$fichaje_activo = isset($_COOKIE['inicio_jornada']);
$proyectos = $conexion->query("SELECT id_proyecto, nombre FROM proyectos")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'iniciar' && isset($_POST['id_proyecto'])) {
        $id_proyecto = $_POST['id_proyecto'];

        $proyecto = buscarProyecto($proyectos, $id_proyecto);

        iniciarCookiesFichaje($proyecto['id_proyecto'], $proyecto['nombre']);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } elseif ($_POST['accion'] === 'parar' && isset($_COOKIE['inicio_jornada'])) {
        $horas = calcularHorasTrabajadas($_COOKIE['inicio_jornada']);

        insertarFichaje($conexion, $_SESSION["correo"], $_COOKIE['proyecto_id'], $horas);
        $mensaje = '<i class="fa-solid fa-floppy-disk"></i> Guardado: ' . $horas . 'h en ' . $_COOKIE['proyecto_nombre'];
        resetearCookiesYFichaje($fichaje_activo);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario - Fichajes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <div class="panel">
        <h2>Hola, <?php echo htmlspecialchars($_SESSION["nombre"] ?? 'Usuario'); ?> </h2>

        <?php if ($mensaje): ?>
            <p class="mensaje" style="color: green; font-weight: bold;"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form method="POST">
            <?php if (!$fichaje_activo): ?>
                <label for="id_proyecto">¿En qué proyecto vas a trabajar?</label>
                <select name="id_proyecto" id="id_proyecto" class="select-proyecto" required>
                    <option value="">-- Selecciona proyecto --</option>
                    <?php foreach ($proyectos as $p): ?>
                        <option value="<?php echo $p['id_proyecto']; ?>">
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="accion" value="iniciar" class="btn btn-iniciar">
                    <i class="fa-solid fa-play"></i> Empezar a trabajar
                </button>
            <?php else: ?>
                <div class="status-box">
                    <p><i class="fa-solid fa-rocket"></i>Trabajando en:</strong><br><?php echo htmlspecialchars($_COOKIE['proyecto_nombre'] ?? 'Proyecto') ?></p>
                    <small>Desde las: <?php echo date("H:i", $_COOKIE['inicio_jornada']) ?></small>
                </div>

                <button type="submit" name="accion" value="parar" class="btn btn-parar">
                    <i class="fa-solid fa-stop"></i> Terminar y Guardar
                </button>
            <?php endif; ?>
        </form>

        <div style="margin-top: 20px;">
            <?php if (!$fichaje_activo): ?>
                <a href="../logout.php" class="logout">Cerrar sesión</a>
            <?php else: ?>
                <p class="logout-disabled" style="color: gray;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Termina tu fichaje antes de cerrar sesión
                </p>

            <?php endif; ?>
        </div>
    </div>

</body>

</html>
