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
$query_obtener_alumno = "
    SELECT alumnos.nombre, alumnos.apellido, usuarios.id as usuario_id
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
// Manejar la inscripción y desinscripción de clases
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["inscribir"])) {
        // Inscribirse en una clase
        $clase_id = $_POST["clase_id"];
        inscribirseEnClase($pdo, $_SESSION['user_id'], $clase_id);
    } elseif (isset($_POST["darse_de_baja"])) {
        // Darse de baja de una clase
        $clase_id = $_POST["clase_id"];
        darseDeBajaDeClase($pdo, $alumno_id, $clase_id);
    }
}
// Función para inscribirse en una clase
function inscribirseEnClase($pdo, $alumno_id, $clase_id)
{
    $query_inscribirse = "INSERT INTO alumnos_clases (alumno_id, clase_id) VALUES (:alumno_id, :clase_id)";
$statement_inscribirse = $pdo->prepare($query_inscribirse);
$statement_inscribirse->bindParam(":alumno_id", $alumno_id, PDO::PARAM_INT);
$statement_inscribirse->bindParam(":clase_id", $clase_id, PDO::PARAM_INT);

if ($statement_inscribirse->execute()) {
    // Éxito
    header("Location: alumno_dashboard.php");
    exit();
} else {
    // Manejar el error según sea necesario
    echo "Error al inscribirse en la clase.";
}

}
// Función para darse de baja de una clase
function darseDeBajaDeClase($pdo, $alumno_id, $clase_id)
{
    $query_darse_de_baja = "DELETE FROM alumnos_clases WHERE alumno_id = :alumno_id AND clase_id = :clase_id";
    $statement_darse_de_baja = $pdo->prepare($query_darse_de_baja);
    $statement_darse_de_baja->bindParam(":alumno_id", $alumno_id);
    $statement_darse_de_baja->bindParam(":clase_id", $clase_id);

    if ($statement_darse_de_baja->execute()) {
        // Redirigir o mostrar un mensaje de éxito
        header("Location: alumno_dashboard.php");
        exit();
    } else {
        // Manejar el error según sea necesario
        echo "Error al darse de baja de la clase.";
    }
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
        <h5 class="mb-2">Alumno</h5>
        <h4 class="mb-2"> <?php        
           if (isset($alumno['nombre'], $alumno['apellido'])) {
               echo "<h4>{$alumno['nombre']} {$alumno['apellido']}</h4>";
           }
           ?></h4>
    </div>
    <div class="p-4">
        <h5 class="mb-4 flex justify-center ">MENU ALUMNOS</h5>
        <p class="mb-5"><a href="?accion=calificaciones">Ver calificaciones</a></p>
        <p class="mb-5"><a href="?accion=clases">Administra tus clases</a></p>
    </div>
</div>
        <div class="2 ">
            <div class="3 h-[69px] flex flex-row-reverse content-center items-center pr-8">
             <button class= "bg-[#fff5d2] w-[120px] h-[35px]  "onclick="logout()">Cerrar Sesión</button>
            <script>function logout() {window.location.href = "login.php";}</script>
            </div>
            <div class="4 bg-[#f5f6fa] w-[1080px] h-[555px] flex justify-center items-center">
                <div class="6 bg-white p-8 rounded-md">
        <h4 class ="font-bold text-lg">Clases Inscritas</h4>
        <ul>
            <?php
            function obtenerClasesInscritas($pdo, $alumno_id)
            {
                $query_clases_inscritas = "
                    SELECT c.id, c.nombre
                    FROM clases c
                    INNER JOIN alumnos_clases ac ON c.id = ac.clase_id
                    WHERE ac.alumno_id = :alumnoId
                ";
                $statement_clases_inscritas = $pdo->prepare($query_clases_inscritas);
                $statement_clases_inscritas->bindParam(':alumnoId', $alumno_id);
                $statement_clases_inscritas->execute();
                return $statement_clases_inscritas->fetchAll(PDO::FETCH_ASSOC);
                
                if (!$statement_clases_inscritas->execute()) {
                    echo "\nPDO::errorInfo():\n";
                    print_r($statement_clases_inscritas->errorInfo());
                }
            }
            $clases_inscritas = obtenerClasesInscritas($pdo, $alumno_id);
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                if (isset($_POST["inscribir"])) {
                    $clase_id = $_POST["clase_id"];
                    inscribirseEnClase($pdo, $_SESSION['user_id'], $clase_id);
                    $clases_inscritas = obtenerClasesInscritas($pdo, $alumno_id);
                } elseif (isset($_POST["darse_de_baja"])) {
                    $clase_id = $_POST["clase_id"];
                    darseDeBajaDeClase($pdo, $alumno_id, $clase_id);
                    $clases_inscritas = obtenerClasesInscritas($pdo, $alumno_id);
                }
            }
            foreach ($clases_inscritas as $clase) :
            ?>
     <div class="6 bg-white p-8 rounded-md">
    <h4 class="font-bold text-lg">Clases Inscritas</h4>
    <ul>
        <?php 
        var_dump($clases_inscritas);
        foreach ($clases_inscritas as $clase) : ?>
            <li>
                <?= $clase['nombre'] ?>
                <form method="post" action="../handle_db/darse_de_baja.php">
                    <input type="hidden" name="clase_id" value="<?= $clase['id'] ?>">
                    <button type="submit" name="darse_de_baja">Darse de Baja</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
            <?php endforeach; ?>
        </ul>
        </div>
        <div class="5 bg-white p-8 rounded-md ml-40 flex flex-col content-center items-center">
    <h4 class="font-bold text-lg">Clases Disponibles</h4>
    <ul>
        <?php $clases_disponibles = obtenerClasesDisponibles($pdo, $alumno_id);
        function obtenerClasesDisponibles($pdo, $alumno_id)
        {
            $query_clases_disponibles = "SELECT id, nombre FROM clases WHERE id NOT IN (SELECT clase_id FROM alumnos_clases WHERE alumno_id = :alumnoId)";
            $statement_clases_disponibles = $pdo->prepare($query_clases_disponibles);
            $statement_clases_disponibles->bindParam(':alumnoId', $alumno_id);
            $statement_clases_disponibles->execute();
            return $statement_clases_disponibles->fetchAll(PDO::FETCH_ASSOC);
        }
         ?>
        <?php foreach ($clases_disponibles as $clase) : ?>
            <?php
                $query_verificar_inscripcion = "SELECT COUNT(*) as count FROM alumnos_clases WHERE alumno_id = :alumno_id AND clase_id = :clase_id";
                $statement_verificar_inscripcion = $pdo->prepare($query_verificar_inscripcion);
                $statement_verificar_inscripcion->bindParam(':alumno_id', $alumno_id);
                $statement_verificar_inscripcion->bindParam(':clase_id', $clase['id']);
                $statement_verificar_inscripcion->execute();
                $inscripcion_existente = $statement_verificar_inscripcion->fetch(PDO::FETCH_ASSOC)['count'];
                if ($inscripcion_existente == 0) :
            ?>
                <li>
                    <?= $clase['nombre'] ?>
                    <form method="post" action="../handle_db/inscribir_clase.php">
                        <input type="hidden" name="alumno_id" value="<?= $alumno_id ?>">
                        <input type="hidden" name="clase_id" value="<?= $clase['id'] ?>">
                        <button type="submit" name="inscribir" class="text-xs text-green-600 flex flex-col content-center items-center">Inscribirse</button>
                    </form>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
    </div>
        </div>
    </div>
<script>
    function logout() {
        window.location.href = "login.php";
    }
</script>
</body>
</html>