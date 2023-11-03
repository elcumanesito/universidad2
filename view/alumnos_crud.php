<?php
session_start();
require_once "../config/database.php";
require_once "../handle_db/logout.php";
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['accion']) && $_GET['accion'] == 'maestros') {
    header("Location: maestros_crud.php");
    exit();
}

if (isset($_GET['accion']) && $_GET['accion'] == 'alumnos') {
    header("Location: alumnos_crud.php");
    exit();
}
if (isset($_GET['accion']) && $_GET['accion'] == 'permisos') {
    header("Location: permisos_crud.php");
    exit();
}
if (isset($_GET['accion']) && $_GET['accion'] == 'clases') {
    header("Location: clases_crud.php");
    exit();
}

// Leer Alumnos
$query_leer_alumnos = "SELECT * FROM alumnos";
$statement_leer_alumnos = $pdo->prepare($query_leer_alumnos);
$statement_leer_alumnos->execute();
$alumnos = $statement_leer_alumnos->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_alumno"])) {
    $usuario_id = $_POST["eliminar_alumno"];

    // Eliminar de la tabla de alumnos
    $query_eliminar_alumno = "DELETE FROM alumnos WHERE usuario_id = :usuario_id";
    $statement_eliminar_alumno = $pdo->prepare($query_eliminar_alumno);
    $statement_eliminar_alumno->bindParam(":usuario_id", $usuario_id);

    // Eliminar las clases asociadas del alumno
    $query_eliminar_clases = "DELETE FROM alumnos_clases WHERE alumno_id = :alumno_id";
    $statement_eliminar_clases = $pdo->prepare($query_eliminar_clases);
    $statement_eliminar_clases->bindParam(":alumno_id", $usuario_id);

    try {
        $pdo->beginTransaction();

        // Ejecutar la eliminación de clases
        $statement_eliminar_clases->execute();

        // Ejecutar la eliminación de alumno
        $statement_eliminar_alumno->execute();

        $pdo->commit();

        // Redirigir o mostrar un mensaje de éxito
        header("Location: alumnos_crud.php");
        exit();
    } catch (PDOException $e) {
        // Si hay algún error, hacer rollback y manejar el error según sea necesario
        $pdo->rollBack();
        echo "Error al eliminar al alumno: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_alumno"])) {
    $usuario_id_editar = $_POST["usuario_id_editar"];
    $nuevo_dni = $_POST["nuevo_dni"];
    $nuevo_nombre = $_POST["nuevo_nombre"];
    $nuevo_apellido = $_POST["nuevo_apellido"];
    $nueva_direccion = $_POST["nueva_direccion"];
    $nueva_fecha_nacimiento = $_POST["nueva_fecha_nacimiento"];

    // Consultar la información actual del alumno
    $query_obtener_alumno = "SELECT * FROM alumnos WHERE usuario_id = :usuario_id";
    $statement_obtener_alumno = $pdo->prepare($query_obtener_alumno);
    $statement_obtener_alumno->bindParam(":usuario_id", $usuario_id_editar);
    $statement_obtener_alumno->execute();
    $alumno_actual = $statement_obtener_alumno->fetch(PDO::FETCH_ASSOC);

    // Actualizar los campos modificados
    $alumno_actualizado = [
        'dni' => $nuevo_dni ?: $alumno_actual['dni'],
        'nombre' => $nuevo_nombre ?: $alumno_actual['nombre'],
        'apellido' => $nuevo_apellido ?: $alumno_actual['apellido'],
        'direccion' => $nueva_direccion ?: $alumno_actual['direccion'],
        'fecha_nacimiento' => $nueva_fecha_nacimiento ?: $alumno_actual['fecha_nacimiento'],
    ];

    // Preparar la consulta de actualización
    $query_actualizar_alumno = "UPDATE alumnos SET
        dni = :dni,
        nombre = :nombre,
        apellido = :apellido,
        direccion = :direccion,
        fecha_nacimiento = :fecha_nacimiento
        WHERE usuario_id = :usuario_id";

    $statement_actualizar_alumno = $pdo->prepare($query_actualizar_alumno);
    $statement_actualizar_alumno->bindParam(":dni", $alumno_actualizado['dni']);
    $statement_actualizar_alumno->bindParam(":nombre", $alumno_actualizado['nombre']);
    $statement_actualizar_alumno->bindParam(":apellido", $alumno_actualizado['apellido']);
    $statement_actualizar_alumno->bindParam(":direccion", $alumno_actualizado['direccion']);
    $statement_actualizar_alumno->bindParam(":fecha_nacimiento", $alumno_actualizado['fecha_nacimiento']);
    $statement_actualizar_alumno->bindParam(":usuario_id", $usuario_id_editar);

    // Ejecutar la consulta de actualización
    if ($statement_actualizar_alumno->execute()) {
        // La actualización fue exitosa
        // Redirigir o mostrar un mensaje de éxito
        header("Location: alumnos_crud.php");
        exit();
    } else {
        // La actualización falló, manejar el error según sea necesario
        echo "Error al actualizar el alumno.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CRUD Alumnos</title>
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
        <h5 class="mb-2">admin</h5>
        <h5 class="mb-2">Administrador</h5>
    </div>
    <div class="p-4">
        <h5 class="mb-4 flex justify-center ">MENU ADMINISTRACION</h5>
        <p class="mb-5"><a href="?accion=permisos">Permisos</a></p>
        <p class="mb-5"><a href="?accion=maestros">Maestros</a></p>
        <p class="mb-5"><a href="?accion=alumnos">Alumnos</a></p>
        <p class="mb-5"><a href="?accion=clases">Clases</a></p>
    </div>
</div>
        <div class="2 ">
            <div class="3 h-[69px] flex flex-row-reverse content-center items-center pr-8">
             <button class= "bg-[#fff5d2] w-[120px] h-[35px]  "onclick="logout()">Cerrar Sesión</button>
            <script>function logout() {window.location.href = "login.php";}</script>
            </div>
            <div class="4 bg-[#f5f6fa] w-[1080px] h-[555px] flex justify-center items-center">
        <div class="5 bg-white p-8 rounded-md">

        <div class="flex flex-row-reverse content-center items-center  pb-8">
        <button class= "bg-[#fff5d2] w-[150px] h-[35px]" onclick="abrirModal()">Agregar Alumno</button>
          </div>

        <table id="tablaAlumnos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DNI</th>
                    <th>Correo Electrónico</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Dirección</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno) : ?>
                    <tr>
                        <td><?= $alumno["id"] ?></td>
                        <td><?= $alumno["dni"] ?></td>
                        <td><?= $alumno["correo"] ?></td>
                        <td><?= $alumno["nombre"] ?></td>
                        <td><?= $alumno["apellido"] ?></td>
                        <td><?= $alumno["direccion"] ?></td>
                        <td><?= $alumno["fecha_nacimiento"] ?></td>
                        <td>
                            <button onclick="eliminarAlumno(<?= $alumno['usuario_id'] ?>)">Eliminar</button>
                        </td>
                        <td>
                             <button onclick="editarAlumno(<?= $alumno['usuario_id'] ?>)">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="modal" style="display: none;">
        <form id="formAgregarAlumno" action="/handle_db/insert_alumno.php" method="POST">
        <label>DNI: <input type="text" name="dni"></label><br>
        <label>Correo Electrónico: <input type="text" name="correo"></label><br>
        <label>Nombre: <input type="text" name="nombre"></label><br>
        <label>Apellido: <input type="text" name="apellido"></label><br>
        <label>Dirección: <input type="text" name="direccion"></label><br>
        <label>Fecha de Nacimiento: <input type="text" name="fecha_nacimiento"></label><br>
        <button type="submit">Guardar</button>
        <button type="button" onclick="cerrarModal()">Cancelar</button>
    </form>
</div>
<div id="modal2" style="display: none;">
            <form method="POST" action="">
                <input type="hidden" name="usuario_id_editar" id="usuario_id_editar">
                <label>DNI: <input type="text" name="nuevo_dni"></label><br>
                <label>Nombre: <input type="text" name="nuevo_nombre"></label><br>
                <label>Apellido: <input type="text" name="nuevo_apellido"></label><br>
                <label>Dirección: <input type="text" name="nueva_direccion"></label><br>
                <label>Fecha de Nacimiento: <input type="text" name="nueva_fecha_nacimiento"></label><br>
                <button type="submit" name="editar_alumno">Guardar</button>
                <button type="button" onclick="cerrarModal2()">Cancelar</button>
            </form>
        </div>
        </div>
    </div>
        </div>
    </div>

        <script>
           function abrirModal() {
                document.getElementById('modal').style.display = 'block';
            }

            function cerrarModal() {
                document.getElementById('modal').style.display = 'none';
            }

            function editarAlumno(usuarioId) {
                console.log("Editar alumno. Usuario ID:", usuarioId);
                // Establecer los valores actuales en el modal
                document.getElementById('usuario_id_editar').value = usuarioId;

                // Mostrar el modal de edición
                document.getElementById('modal2').style.display = 'block';
            }

            function cerrarModal2() {
                document.getElementById('modal2').style.display = 'none';
            }


            // Función para eliminar alumno
            function eliminarAlumno(alumnoId) {
                if (confirm("¿Estás seguro de que quieres eliminar este alumno?")) {
                    // Crear un formulario dinámico
                    var form = document.createElement("form");
                    form.method = "POST";
                    
                    // Agregar un input oculto con el valor del ID del alumno
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "eliminar_alumno";
                    input.value = alumnoId;
                    form.appendChild(input);

                    // Adjuntar el formulario al cuerpo del documento
                    document.body.appendChild(form);

                    // Enviar el formulario
                    form.submit();
                }
            }
        </script>
</body>
</html>


