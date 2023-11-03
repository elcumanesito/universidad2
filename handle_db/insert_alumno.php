<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dni = $_POST["dni"];
    $correo = $_POST["correo"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $direccion = $_POST["direccion"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];

    // Insertar en la tabla de usuarios
    $default_password = "alumno";
    $rol_id = 3;

    $query_usuario = "INSERT INTO usuarios (correo, contrasena, rol_id) 
                      VALUES (:correo, :contrasena, :rol_id)";
    
    $statement_usuario = $pdo->prepare($query_usuario);
    $statement_usuario->bindParam("correo", $correo);
    $statement_usuario->bindParam("contrasena", $default_password);
    $statement_usuario->bindParam("rol_id", $rol_id);
    
    $statement_usuario->execute();

    // Obtener el ID del usuario recién insertado
    $usuario_id = $pdo->lastInsertId();

    // Insertar en la tabla de alumnos
    $query = "INSERT INTO alumnos (usuario_id, dni, correo, nombre, apellido, direccion, fecha_nacimiento) 
              VALUES (:usuario_id, :dni, :correo, :nombre, :apellido, :direccion, :fecha_nacimiento)";
    
    $statement = $pdo->prepare($query);
    $statement->bindParam("usuario_id", $usuario_id);
    $statement->bindParam("dni", $dni);
    $statement->bindParam("correo", $correo);
    $statement->bindParam("nombre", $nombre);
    $statement->bindParam("apellido", $apellido);
    $statement->bindParam("direccion", $direccion);
    $statement->bindParam("fecha_nacimiento", $fecha_nacimiento);
    
    
    $statement->execute();

    // Redireccionar a la página de administración de alumnos
    header("Location: ../view/alumnos_crud.php");
} 
// else {
//     // Redireccionar si no es una solicitud POST
//     header("Location: ../login.php");
// }
?>
