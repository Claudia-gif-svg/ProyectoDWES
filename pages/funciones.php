<?php

function resetearCookiesYFichaje(&$fichaje_activo) {
    $past = time() - 3600;
    setcookie('inicio_jornada', '', $past, "/");
    setcookie('proyecto_id', '', $past, "/");
    setcookie('proyecto_nombre', '', $past, "/");

    $fichaje_activo = false;
}

function insertarFichaje($conexion, $correo, $id_proy, $horas) {
    $sql = "INSERT INTO fichajes (correo_usuario, id_proyecto, horas, fecha) VALUES (?, ?, ?, CURDATE())";
    $conexion->prepare($sql)->execute([$correo, $id_proy, $horas]);
}
function buscarProyecto($lista, $id) {
    foreach ($lista as $p) {
        if ($p['id_proyecto'] == $id) return $p;
    }
    return null;
}
function iniciarCookiesFichaje($id_proyecto, $nombre_proyecto) {
    $future = time() + 86400; 
    setcookie('inicio_jornada', time(), $future, "/");
    setcookie('proyecto_id', $id_proyecto, $future, "/");
    setcookie('proyecto_nombre', $nombre_proyecto, $future, "/");
}


function calcularHorasTrabajadas($inicio) {
    $segundos = time() - $inicio;
    return max(0.01, round($segundos / 3600, 2));
}

function escribirLog($mensaje, $tipo = "INFO") {
    $archivo = __DIR__ . '/../logs/app.log'; // Ajusta ruta según ubicación del archivo
    $fecha = date('[Y-m-d H:i:s]');
    $texto = "$fecha [$tipo] $mensaje" . PHP_EOL;
    file_put_contents($archivo, $texto, FILE_APPEND);
}


function imprimirUsuarios($conexion) {
    $sql = "SELECT correo, nombre, rol FROM usuarios";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Usuarios</h2>";
    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>Correo</th>
                <th>Nombre</th>
                <th>Rol</th>
            </tr>";

    if (count($usuarios) > 0) {
        foreach ($usuarios as $fila) {
            echo "<tr>
                    <td>{$fila['correo']}</td>
                    <td>{$fila['nombre']}</td>
                    <td>{$fila['rol']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No hay usuarios</td></tr>";
    }

    echo "</table>";
}

function imprimirProyectos($conexion) {
    $sql = "SELECT id_proyecto, nombre, descripcion FROM proyectos";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Proyectos</h2>";
    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>";

    if (count($proyectos) > 0) {
        foreach ($proyectos as $fila) {
            echo "<tr>
                    <td>{$fila['id_proyecto']}</td>
                    <td>{$fila['nombre']}</td>
                    <td>{$fila['descripcion']}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No hay proyectos</td></tr>";
    }

    echo "</table>";
}

?>
