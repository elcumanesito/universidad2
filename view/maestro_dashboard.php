<?php
session_start();
require_once "../handle_db/logout.php";
require_once "../config/database.php";

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}


// Inicializar variables de maestro
$maestroNombre = "Administrador";

// Verificar si el usuario es un maestro y obtener su nombre
if ($_SESSION['user_role'] == 2) {
    $user_id = $_SESSION['user_id'];

    // Prepara la consulta para obtener la información del maestro
    $query_obtener_maestro = "SELECT maestros.nombre 
                              FROM maestros 
                              JOIN usuarios ON maestros.usuario_id = usuarios.id
                              WHERE usuarios.id = :userId";
    $statement_obtener_maestro = $pdo->prepare($query_obtener_maestro);
    $statement_obtener_maestro->bindParam(':userId', $user_id);

    // Manejo de errores más detallado
    if (!$statement_obtener_maestro->execute()) {
        print_r($statement_obtener_maestro->errorInfo());
    }

    // Verificar si la consulta devolvió resultados
    if ($statement_obtener_maestro->rowCount() > 0) {
        $maestro = $statement_obtener_maestro->fetch(PDO::FETCH_ASSOC);

        $maestroNombre = $maestro['nombre'];
    } else {
        echo "No se encontraron resultados para el maestro con ID: $user_id";
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'alumnos') {
    header("Location: maestro_alumnos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h5 class="mb-2">Maestro</h5>
        <h5 class="mb-2"><?php echo $maestroNombre ?></h5>
    </div>
    <div class="p-4">
        <h5 class="mb-4 flex justify-center ">MENU MAESTROS</h5>
        <p class="mb-5"><a href="?accion=alumnos">Alumnos</a></p>
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
            <p>Bienvenido, selecciona la acción que quieras realizar en las pestañas del menú de la izquierda</p>
        </div>
    </div>
        </div>
    </div>
</body>
</html>
