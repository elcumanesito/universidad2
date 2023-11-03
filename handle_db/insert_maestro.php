<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $direccion = $_POST["direccion"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $clase_asignada = $_POST["clase_asignada"];

    // Insertar en la tabla de usuarios
    $default_password = "maestro";
    $rol_id = 2;

    $query_usuario = "INSERT INTO usuarios (correo, contrasena, rol_id) 
                      VALUES (:correo, :contrasena, :rol_id)";
    
    $statement_usuario = $pdo->prepare($query_usuario);
    $statement_usuario->bindParam("correo", $correo);
    $statement_usuario->bindParam("contrasena", $default_password);
    $statement_usuario->bindParam("rol_id", $rol_id);
    
    $statement_usuario->execute();

    // Obtener el ID del usuario recién insertado
    $usuario_id = $pdo->lastInsertId();

    // Insertar en la tabla de maestros
    $query = "INSERT INTO maestros (usuario_id, nombre, correo, direccion, fecha_nacimiento, clase_asignada) 
              VALUES (:usuario_id, :nombre, :correo, :direccion, :fecha_nacimiento, :clase_asignada)";
    
    $statement = $pdo->prepare($query);
    $statement->bindParam("usuario_id", $usuario_id);
    $statement->bindParam("nombre", $nombre);
    $statement->bindParam("correo", $correo);
    $statement->bindParam("direccion", $direccion);
    $statement->bindParam("fecha_nacimiento", $fecha_nacimiento);
    $statement->bindParam("clase_asignada", $clase_asignada);
    
    
    $statement->execute();

    // Redireccionar a la página de administración de alumnos
    header("Location: ../view/maestros_crud.php");
} 
// else {
//     // Redireccionar si no es una solicitud POST
//     header("Location: ../login.php");
// }
?>
