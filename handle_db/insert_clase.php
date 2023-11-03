<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $clase = $_POST["clase"];
 
    $query_clase = "INSERT INTO clases (nombre) 
                      VALUES (:clase)";
    
    $statement = $pdo->prepare($query_clase);
    $statement->bindParam("clase", $clase);
 
    
    $statement->execute();

    // Redireccionar a la página de administración de alumnos
    header("Location: ../view/clases_crud.php");
} 
// else {
//     // Redireccionar si no es una solicitud POST
//     header("Location: ../login.php");
// }
?>