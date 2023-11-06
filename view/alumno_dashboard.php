<?php
session_start();
require_once "../config/database.php";
require_once "../handle_db/logout.php";
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
// Obtener nombre y apellido del alumno
$alumno_id = $_SESSION['user_id'];
// Cambia la consulta para unir las tablas usuarios y alumnos
$query_obtener_alumno = "
    SELECT alumnos.nombre, alumnos.apellido
    FROM alumnos
    JOIN usuarios ON alumnos.usuario_id = usuarios.id
    WHERE alumnos.usuario_id = :alumnoId
";
$statement_obtener_alumno = $pdo->prepare($query_obtener_alumno);
$statement_obtener_alumno->bindParam(':alumnoId', $alumno_id);
$statement_obtener_alumno->execute();
$alumno = $statement_obtener_alumno->fetch(PDO::FETCH_ASSOC);
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
<div class="0 flex">
    <div class="1 w-[300px] h-screen bg-[#353a40] text-[#f5f6fa]">
    <div class="flex items-center space-x-2 p-4 border-b border-white">
        <img src="../assets/logo.jpg" alt="Logo" class="w-[50px] h-[50px] rounded-full transform scale-50">
        <h3>Universidad</h3>
    </div>
    <div class="p-4 border-b border-white">
    <h5>Alumno</h5>
            <h4><?php
if (is_array($alumno) && isset($alumno['nombre'], $alumno['apellido'])) {
    echo "<h4>{$alumno['nombre']} {$alumno['apellido']}</h4>";
} else {
    echo "<h4>Error al obtener información del alumno</h4>";
}
?>
    </div>
    <div class="p-4 border-b border-white">
              
            <p><a href="?accion=calificaciones">Ver calificaciones</a></p>
            <p><a href="?accion=clases">Administra tus clases</a></p>
    </div>
</div>
        <div class="2 ">
            <div class="3 h-[69px] flex flex-row-reverse content-center items-center pr-8">
             <button class= "bg-[#fff5d2] w-[120px] h-[35px]  "onclick="logout()">Cerrar Sesión</button>
            <script>function logout() {window.location.href = "login.php";}</script>
            </div>
            <div class="4 bg-[#f5f6fa] w-[1080px] h-[555px] flex justify-center items-center">
        <div class="5 bg-white p-8 rounded-md">
            <h1 class="text-center font-bold">Dashboard</h1>
            <p>Debido a que no pude recibir la informacion del Alumno que ingreso, no pude seguir avanzando con el programa :/ </p>
        </div>
    </div>
        </div>
    </div>
</body>
<script>function logout() {window.location.href = "login.php"; }</script>
</html>
