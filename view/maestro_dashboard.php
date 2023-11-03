<?php
session_start();

require_once "../handle_db/logout.php";
require_once "../config/database.php";

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
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
<button onclick="logout()">Cerrar Sesión</button>
            <script>
    function logout() {
        window.location.href = "login.php";
    }
</script>
    <p>Soy maestro jaja que risa</p>
</body>
</html>