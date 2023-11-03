<?php
if ($row) {
    // Aquí se inicia la sesión y se configuran las variables de sesión.
    session_start();
    $_SESSION['id'] = $row['id'];
    $_SESSION['usuario_id'] = $row['usuario_id'];

}
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["darse_de_baja"])) {
    $alumno_id = $_SESSION['usuario_id'];
    $clase_id = $_POST["clase_id"];

    // Darse de baja de la clase
    $query_darse_de_baja = "DELETE FROM alumnos_clases WHERE alumno_id = :alumnoId AND clase_id = :claseId";
    $statement_darse_de_baja = $pdo->prepare($query_darse_de_baja);
    $statement_darse_de_baja->bindParam(':alumnoId', $alumno_id);
    $statement_darse_de_baja->bindParam(':claseId', $clase_id);
    $statement_darse_de_baja->execute();
}

header("Location: ../view/alumno_clases.php");
exit();
