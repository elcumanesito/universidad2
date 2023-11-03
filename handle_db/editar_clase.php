<?php
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_materia_id"], $_POST["editar_materia_nombre"])) {
    $id = $_POST["editar_materia_id"];
    $nuevoNombre = $_POST["editar_materia_nombre"];

    // Actualizar el nombre de la materia en la tabla de clases
    $query_actualizar_clase = "UPDATE clases SET nombre = :nuevoNombre WHERE id = :id";
    $statement_actualizar_clase = $pdo->prepare($query_actualizar_clase);
    $statement_actualizar_clase->bindParam(":nuevoNombre", $nuevoNombre);
    $statement_actualizar_clase->bindParam(":id", $id);
    $statement_actualizar_clase->execute();

    // También puedes necesitar actualizar el nombre en la tabla de maestros si es necesario
    // ...

    // Redireccionar o realizar otras acciones después de la edición
    header("Location: ../view/clases_crud.php");
    exit();
}
?>
