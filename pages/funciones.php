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
?>
