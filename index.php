<?php
$host = "localhost";
$usuario = "root";
$password = "";
$bd = "AppFichajes";

try {
    // 1️⃣ Conexión al servidor MySQL
    $conexion = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $usuario,
        $password
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2️⃣ Crear base de datos
    $conexion->exec("
        CREATE DATABASE IF NOT EXISTS $bd
        CHARACTER SET utf8mb4
        COLLATE utf8mb4_general_ci
    ");

    // 3️⃣ Conectar a la base de datos
    $conexion = new PDO(
        "mysql:host=$host;dbname=$bd;charset=utf8mb4",
        $usuario,
        $password
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Base de datos lista<br>";

    // 4️⃣ Crear tabla usuarios usando try/catch
    try {
        $conexion->exec("
            CREATE TABLE usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                rol VARCHAR(255) NOT NULL
            )
        ");
        echo "Tabla usuarios creada<br>";

    } catch (PDOException $e) {
        // Código 42S01 = tabla ya existe
        if ($e->getCode() == "42S01") {
            echo "La tabla usuarios ya existe<br>";
        } else {
            throw $e; // otro error real
        }
    }

} catch (PDOException $e) {
    echo "Error grave: " . $e->getMessage();
}
