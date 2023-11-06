<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inscribir"]) && isset($_POST["clase_id"])) {
    $alumno_id = $_SESSION['user_id'];
    $clase_id = $_POST["clase_id"];

    // Lógica de inscripción a la clase
    require_once "../config/database.php";  // Asegúrate de la ruta correcta

    // Verificar que el alumno existe
    $query_verificar_alumno = "SELECT id FROM alumnos WHERE usuario_id = :alumno_id";

    $statement_verificar_alumno = $pdo->prepare($query_verificar_alumno);
    $statement_verificar_alumno->bindParam(":alumno_id", $alumno_id);
    $statement_verificar_alumno->execute();

    if (!$statement_verificar_alumno->fetch()) {
        // El alumno no existe
        echo "Error: El alumno no existe.";
        exit();
    }

    try {
        $query = "INSERT INTO alumnos_clases (alumno_id, clase_id) VALUES (:alumno_id, :clase_id)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(":alumno_id", $alumno_id);
        $statement->bindParam(":clase_id", $clase_id);

        if ($statement->execute()) {
            // Éxito al inscribirse
            header("Location: alumno_clases.php");
            exit();
        } else {
            // Error al inscribirse
            echo "Error al inscribirse en la clase.";
        }
    } catch (PDOException $e) {
        echo "Error de PDO: " . $e->getMessage();
    }
} else {
    // Acceso no autorizado
    header("Location: login.php");
    exit();
}

?>
