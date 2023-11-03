<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inscribir"])) {
    $alumno_id = $_SESSION['usuario_id'];
    $clase_id = $_POST["clase_id"];

    // Verificar si el alumno ya está inscrito en la clase
    $query_verificar = "SELECT id FROM alumnos_clases WHERE alumno_id = :alumnoId AND clase_id = :claseId";
    $statement_verificar = $pdo->prepare($query_verificar);
    $statement_verificar->bindParam(':alumnoId', $alumno_id);
    $statement_verificar->bindParam(':claseId', $clase_id);
    $statement_verificar->execute();
    $ya_inscrito = $statement_verificar->fetchColumn();

    if (!$ya_inscrito) {
        // Inscribir al alumno en la clase
        $query_inscribir = "INSERT INTO alumnos_clases (alumno_id, clase_id) VALUES (:alumnoId, :claseId)";
        $statement_inscribir = $pdo->prepare($query_inscribir);
        $statement_inscribir->bindParam(':alumnoId', $alumno_id);
        $statement_inscribir->bindParam(':claseId', $clase_id);

        // Intentemos ejecutar la consulta y, si hay un error, mostraremos el mensaje.
        if ($statement_inscribir->execute()) {
            echo "Inscripción exitosa.";
        } else {
            echo "Error al inscribir al alumno. Detalles: " . implode(" ", $statement_inscribir->errorInfo());
        }
    } else {
        echo "El alumno ya está inscrito en esta clase.";
    }
} else {
    echo "Solicitud no válida.";
}

header("Location: ../view/alumno_clases.php");
exit();
