<?php
session_start();


if (!isset($_SESSION["correo"])) {
    header("Location: index.php");
    exit;
}

require_once "../basedatos/conexionbd.php";


$proyectos = [];
if (!isset($_SESSION['inicio_jornada'])) {
    $stmt = $conexion->query("SELECT id_proyecto, nombre FROM proyectos");
    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$mensaje = "";
$fichaje_activo = isset($_SESSION['inicio_jornada']);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'])) {
    
    if ($_POST['accion'] === 'iniciar' && isset($_POST['id_proyecto'])) {
      
        $_SESSION['proyecto_id'] = $_POST['id_proyecto'];
        
       
        $stmtNom = $conexion->prepare("SELECT nombre FROM proyectos WHERE id_proyecto = ?");
        $stmtNom->execute([$_POST['id_proyecto']]);
        $_SESSION['proyecto_nombre'] = $stmtNom->fetchColumn();
        
        $_SESSION['inicio_jornada'] = time();
        $fichaje_activo = true;
        $mensaje = "✅ Jornada iniciada en: " . $_SESSION['proyecto_nombre'];
    } 
    
    elseif ($_POST['accion'] === 'parar') {
        $segundos = time() - $_SESSION['inicio_jornada'];
        $horas = round($segundos / 3600, 2);
        
      
        if ($horas <= 0) $horas = 0.01; 

        $sql = "INSERT INTO fichajes (correo_usuario, id_proyecto, horas, fecha) VALUES (?, ?, ?, CURDATE())";
        $ins = $conexion->prepare($sql);
        $ins->execute([$_SESSION["correo"], $_SESSION['proyecto_id'], $horas]);

        $mensaje = "🛑 Guardado: " . $horas . "h en " . $_SESSION['proyecto_nombre'];
        
      
        unset($_SESSION['inicio_jornada'], $_SESSION['proyecto_id'], $_SESSION['proyecto_nombre']);
        $fichaje_activo = false;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario - Fichajes</title>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<div class="panel">
    <h2>Hola, <?= htmlspecialchars($_SESSION["nombre"]) ?> 👋</h2>
    
    <?php if ($mensaje): ?>
        <p style="color: #155724; background: #d4edda; padding: 10px; border-radius: 5px;"><?= $mensaje ?></p>
    <?php endif; ?>

    <form method="POST">
        <?php if (!$fichaje_activo): ?>
            <label for="id_proyecto">¿En qué proyecto vas a trabajar?</label>
            <select name="id_proyecto" id="id_proyecto" class="select-proyecto" required>
                <option value="">-- Selecciona proyecto --</option>
                <?php foreach ($proyectos as $p): ?>
                    <option value="<?= $p['id_proyecto'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="accion" value="iniciar" class="btn btn-iniciar">
                ▶️ Empezar a trabajar
            </button>
        <?php else: ?>
            <div class="status-box">
                <p>🚀 <strong>Trabajando en:</strong><br><?= htmlspecialchars($_SESSION['proyecto_nombre']) ?></p>
                <small>Desde las: <?= date("H:i", $_SESSION['inicio_jornada']) ?></small>
            </div>
            
            <button type="submit" name="accion" value="parar" class="btn btn-parar">
                ⏹️ Terminar y Guardar
            </button>
        <?php endif; ?>
    </form>

    <a href="logout.php" class="logout">Cerrar sesión</a>
</div>

</body>
</html>
