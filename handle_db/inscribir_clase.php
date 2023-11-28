<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inscribir"]) && isset($_POST["clase_id"]) && isset($_POST["alumno_id"])) {
    $alumno_id = $_POST["alumno_id"];
    $clase_id = $_POST["clase_id"];

    require_once "../config/database.php";

    // Antes de la inserción, verifica que el alumno existe
    $query_verificar_alumno = "SELECT id FROM alumnos WHERE usuario_id = :alumno_id";
    $statement_verificar_alumno = $pdo->prepare($query_verificar_alumno);
    $statement_verificar_alumno->bindParam(":alumno_id", $alumno_id);
    $statement_verificar_alumno->execute();
    
    $alumno_info = $statement_verificar_alumno->fetch(PDO::FETCH_ASSOC);
    
    if (!$alumno_info) {
        // El alumno no existe
        echo "Error: El alumno no existe.";
        exit();
    }
    
    // Ahora, realiza la inserción en la tabla alumnos_clases
    try {
        $query = "INSERT INTO alumnos_clases (alumno_id, clase_id) VALUES (:alumno_id, :clase_id)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(":alumno_id", $alumno_info['id']);
        $statement->bindParam(":clase_id", $clase_id);
    
        if ($statement->execute()) {
            // Éxito al inscribirse
            header("Location: /view/alumno_clases.php");
            exit();
        } else {
            // Error al inscribirse
            echo "Error al inscribirse en la clase.";
        }
    
        // Resto del código para ejecutar la inserción
    } catch (PDOException $e) {
        echo "Error de PDO: " . $e->getMessage();
    }
    

}
?>

