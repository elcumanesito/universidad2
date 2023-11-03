<?php
session_start();
require_once "../config/database.php";
require_once "../handle_db/logout.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

/////////// Obtener el ID del alumno desde la sesión
$alumno_id = $_SESSION['user_id'];

// //////Preparar y ejecutar la consulta para obtener el nombre y apellido del alumno
$query_obtener_alumno = "SELECT nombre, apellido FROM alumnos WHERE id = :alumnoId";

try {
    $statement_obtener_alumno = $pdo->prepare($query_obtener_alumno);
    $statement_obtener_alumno->bindParam(':alumnoId', $alumno_id);
    $statement_obtener_alumno->execute();

    ////////////////// Verificar si se encontraron resultados
    $alumno = $statement_obtener_alumno->fetch(PDO::FETCH_ASSOC);

    if ($alumno) {
        ///////////////////acceder a $alumno['nombre'] y $alumno['apellido']
    } else {
        echo "No se encontraron resultados para el alumno con ID: $alumno_id";
    }
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
}

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
        <div id="logo"></div>
        <div>
            <h5>Alumno</h5>
            <h4> <?php
           
            if (isset($alumno['nombre'], $alumno['apellido'])) {
                echo "<h4>{$alumno['nombre']} {$alumno['apellido']}</h4>";
            }
            ?></h4>

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

    <div>
        <h4>Clases Disponibles</h4>
        <ul>
            <?php
            ////////////////// Obtener clases disponibles
            $query_leer_clases = "SELECT id, nombre FROM clases";
            $statement_leer_clases = $pdo->prepare($query_leer_clases);
            $statement_leer_clases->execute();
            $clases_disponibles = $statement_leer_clases->fetchAll(PDO::FETCH_ASSOC);

            foreach ($clases_disponibles as $clase) :
            ?>
                <li>
                    <?= $clase['nombre'] ?>
                    <form method="post" action="../handle_db/inscribir_clase.php">
                        <input type="hidden" name="clase_id" value="<?= $clase['id'] ?>">
                        <button type="submit" name="inscribir">Inscribirse</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <h4>Clases Inscritas</h4>
        <ul>
            <?php
            //////////////////// Obtener clases inscritas
            $query_clases_inscritas = "SELECT c.id, c.nombre FROM clases c
                                       INNER JOIN alumnos_clases ac ON c.id = ac.clase_id
                                       WHERE ac.alumno_id = :alumnoId";
            $statement_clases_inscritas = $pdo->prepare($query_clases_inscritas);
            $statement_clases_inscritas->bindParam(':alumnoId', $_SESSION['usuario_id']);
            $statement_clases_inscritas->execute();
            $clases_inscritas = $statement_clases_inscritas->fetchAll(PDO::FETCH_ASSOC);

            foreach ($clases_inscritas as $clase) :
            ?>
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
</div>

<script>
    function logout() {
        window.location.href = "login.php";
    }
</script>

</body>
</html>