<?php
session_start();
require_once "../config/database.php";
require_once "../handle_db/logout.php";
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
            <h4>Nombre_del_alumno</h4>
        </div>
        <div>
            <h4>Menu Alumnos</h4>
            <p><a href="?accion=calificaciones">Ver calificaciones</a></p>
            <p><a href="?accion=clases">Administra tus clases</a></p>
        </div>
    </div>
    <div>
    <button onclick="logout()">Cerrar Sesión</button>
    </div>
</div>
    
</body>
</html>