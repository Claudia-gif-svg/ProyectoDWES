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
    $archivo = __DIR__ . '/../logs/app.log'; 
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




function crearRegistro($conexion, $tipo, $datos) {
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
        return "Error: No tienes permisos para realizar esta acción.";
    }

    try {
        if ($tipo === 'proyecto') {
            $sql = "INSERT INTO proyectos (nombre, descripcion) VALUES (:nom, :desc)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':nom' => $datos['nombre_proyecto'], ':desc' => $datos['descripcion']]);
            return "Proyecto creado con éxito.";
        } elseif ($tipo === 'usuario') {
            $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (:nom, :corr, :pass, :rol)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':nom' => $datos['nombre'],
                ':corr' => $datos['correo'],
                ':pass' => $datos['contrasena'],
                ':rol' => $datos['rol']
            ]);
            
            $enlaceMail = "<br><a href='mailto:{$datos['correo']}?subject=Nueva Cuenta&body=Hola {$datos['nombre']}, tu cuenta ha sido creada.' class='btn-correo'>Enviar correo de aviso</a>";
            return "Usuario creado con éxito. " . $enlaceMail;
        }
    } catch (PDOException $e) {
        return "Error al crear: " . $e->getMessage();
    }
}

function modificarRegistro($conexion, $tipo, $datos) {
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
        return "Error: Acción no autorizada.";
    }

    try {
        if ($tipo === 'proyecto') {
            $id = $datos['id_proyecto'];
            $stmt = $conexion->prepare("SELECT nombre, descripcion FROM proyectos WHERE id_proyecto = ?");
            $stmt->execute([$id]);
            $actual = $stmt->fetch(PDO::FETCH_ASSOC);

            $nom = !empty($datos['nombre_proyecto']) ? $datos['nombre_proyecto'] : $actual['nombre'];
            $desc = !empty($datos['descripcion']) ? $datos['descripcion'] : $actual['descripcion'];

            $sql = "UPDATE proyectos SET nombre = :nom, descripcion = :desc WHERE id_proyecto = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':nom' => $nom, ':desc' => $desc, ':id' => $id]);
            return "Proyecto modificado correctamente.";

        } elseif ($tipo === 'usuario') {
            $correo = $datos['correo'];
            $stmt = $conexion->prepare("SELECT nombre, rol FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $actual = $stmt->fetch(PDO::FETCH_ASSOC);

            $nom = !empty($datos['nombre']) ? $datos['nombre'] : $actual['nombre'];
            $rol = !empty($datos['rol']) ? $datos['rol'] : $actual['rol'];

            $sql = "UPDATE usuarios SET nombre = :nom, rol = :rol WHERE correo = :corr";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':nom' => $nom, ':rol' => $rol, ':corr' => $correo]);
            
            $enlaceMail = "<br><a href='mailto:{$correo}?subject=Perfil Actualizado&body=Hola {$nom}, tu perfil ha sido modificado.' class='btn-correo'>Enviar correo de aviso</a>";
            return "Usuario modificado correctamente. " . $enlaceMail;
        }
    } catch (PDOException $e) {
        return "Error al modificar: " . $e->getMessage();
    }
}

function eliminarRegistro($conexion, $tipo, $id) {
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
        return "Error: No tienes permiso para eliminar.";
    }

    try {
        if ($tipo === 'proyecto') {
            $sql = "DELETE FROM proyectos WHERE id_proyecto = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return "Proyecto eliminado correctamente.";
        } elseif ($tipo === 'usuario') {
            $sql = "DELETE FROM usuarios WHERE correo = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            
            return "Usuario eliminado correctamente. " ;
        }
    } catch (PDOException $e) {
        return "Error al eliminar: " . $e->getMessage();
    }
}

?>
