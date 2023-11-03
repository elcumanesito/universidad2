<?php
session_start();
require_once "../config/database.php";
require_once "../handle_db/logout.php";
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
// Obtener nombre y apellido del alumno
$alumno_id = $_SESSION['usuario_id'];
$query_obtener_alumno = "SELECT nombre, apellido FROM alumnos WHERE id = :alumnoId";
$statement_obtener_alumno = $pdo->prepare($query_obtener_alumno);
$statement_obtener_alumno->bindParam(':alumnoId', $alumno_id);
$statement_obtener_alumno->execute();
$alumno = $statement_obtener_alumno->fetch(PDO::FETCH_ASSOC);
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['accion']) && $_GET['accion'] == 'calificaciones') {
    header("Location: alumno_calificaciones.php");
    exit();
}
if (isset($_GET['accion']) && $_GET['accion'] == 'clases') {
    header("Location: alumno_clases.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/dist/output.css">
</head>
<body>
<div>
    <div>
        <div id="logo">

        </div>
        <div>
            <h5>Alumno</h5>
            <h4><?= $alumno['nombre'] . ' ' . $alumno['apellido'] ?></h4>

        </div>
        <div>
            <h4>Menu Alumnos</h4>
            <p><a href="?accion=calificaciones">Ver calificaciones</a></p>
            <p><a href="?accion=clases">Administra tus clases</a></p>
        </div>
    </div>
    <div>
        <div>
            <button onclick="logout()">Cerrar Sesión</button>
        </div>
        <div>
            <h2>Dashboard</h2>
            <p>Bienvenido, selecciona la accion que quieras realizar en las pestañas del menu de la izquierda</p>
        </div>
    
    </div>
</div>



</body>
<script>function logout() {window.location.href = "login.php"; }</script>
</html>