<?php

// CONEXIÓN A LA BD

$host = "localhost";
$usuario = "root";
$password = "";
$bd = "AppFichajes";

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$bd;charset=utf8mb4",
        $usuario,
        $password
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}


// LOGIN

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST["correo"];
    $contrasena = $_POST["password"];

    $stmt = $conexion->prepare(
        "SELECT * FROM usuarios WHERE correo = :correo"
    );
    $stmt->bindParam(":correo", $correo);
    $stmt->execute();

    $usuarioBD = $stmt->fetch(PDO::FETCH_ASSOC);

    // 👉 Para prácticas SIN hash cambia por: $contrasena === $usuarioBD["contrasena"]
    if ($usuarioBD && password_verify($contrasena, $usuarioBD["contrasena"])) {

        session_start();
        $_SESSION["correo"] = $usuarioBD["correo"];
        $_SESSION["nombre"] = $usuarioBD["nombre"];
        $_SESSION["rol"] = $usuarioBD["rol"];

        if ($usuarioBD["rol"] === "administrador") {
            header("Location: admin.php");
        } else {
            header("Location: usuario.php");
        }
        exit;
    } else {
        $mensaje = "❌ Correo o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aplicación de Fichajes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

<header class="header">
    <h1>📌 Aplicación de Fichajes</h1>
    <p>Control de horas trabajadas</p>
</header>

<main>
    <section class="login">
        <h2 class="login__title">Iniciar sesión</h2>

        <?php if ($mensaje): ?>
            <p class="login__message"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST" class="login__form">
            <label class="login__label">Correo:</label>
            <input type="email" name="correo" class="login__input" required>

            <label class="login__label">Contraseña:</label>
            <input type="password" name="password" class="login__input" required>

            <button type="submit" class="login__button">Entrar</button>
        </form>
    </section>
</main>

<footer class="footer">
    <p>© 2026 - Aplicación de Fichajes</p>
</footer>

</body>
</html>
