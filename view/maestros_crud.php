<?php
session_start();
require_once "../config/database.php";
require_once "../handle_db/logout.php";
// Verifica si el usuario ha iniciado sesión
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

// Leer Clases
$query_leer_clases = "SELECT * FROM clases";
$statement_leer_clases = $pdo->prepare($query_leer_clases);
$statement_leer_clases->execute();
$clases_disponibles = $statement_leer_clases->fetchAll(PDO::FETCH_ASSOC);

// Leer Maestro
$query_leer_maestros = "SELECT * FROM maestros";
$statement_leer_maestros = $pdo->prepare($query_leer_maestros);
$statement_leer_maestros->execute();
$maestros = $statement_leer_maestros->fetchAll(PDO::FETCH_ASSOC);


// Eliminar Alumno
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_maestro"])) {
    // var_dump($_POST["eliminar_maestro"]);
    $usuario_id = $_POST["eliminar_maestro"];

    // Eliminar de la tabla de maestros
    $query_eliminar_maestro = "DELETE FROM maestros WHERE usuario_id = :usuario_id";
    $statement_eliminar_maestro = $pdo->prepare($query_eliminar_maestro);
    $statement_eliminar_maestro->bindParam("usuario_id", $usuario_id, PDO::PARAM_STR);
    $statement_eliminar_maestro->execute();

    // Eliminar de la tabla de usuarios
    $query_eliminar_usuario = "DELETE FROM usuarios WHERE id = :usuario_id";
    $statement_eliminar_usuario = $pdo->prepare($query_eliminar_usuario);
    $statement_eliminar_usuario->bindParam("usuario_id", $usuario_id, PDO::PARAM_STR);
    $statement_eliminar_usuario->execute();

}

    // Manejar la edición del maestro
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_maestro"])) {
    $usuario_id_editar = $_POST["usuario_id_editar"];
    $nuevo_nombre = $_POST["nuevo_nombre"];
    $nueva_direccion = $_POST["nueva_direccion"];
    $nueva_fecha_nacimiento = $_POST["nueva_fecha_nacimiento"];
    $nueva_clase_asignada = $_POST["nueva_clase_asignada"];

    // Consultar la información actual del maestro
    $query_obtener_maestro = "SELECT * FROM maestros WHERE usuario_id = :usuario_id";
    $statement_obtener_maestro = $pdo->prepare($query_obtener_maestro);
    $statement_obtener_maestro->bindParam(":usuario_id", $usuario_id_editar);
    $statement_obtener_maestro->execute();
    $maestro_actual = $statement_obtener_maestro->fetch(PDO::FETCH_ASSOC);

    // Actualizar los campos modificados
    $maestro_actualizado = [
        'nombre' => $nuevo_nombre ?: $maestro_actual['nombre'],
        'direccion' => $nueva_direccion ?: $maestro_actual['direccion'],
        'fecha_nacimiento' => $nueva_fecha_nacimiento ?: $maestro_actual['fecha_nacimiento'],
        'clase_asignada' => $nueva_clase_asignada ?: $maestro_actual['clase_asignada'],
    ];

    // Preparar la consulta de actualización
    $query_actualizar_maestro = "UPDATE maestros SET
        nombre = :nombre,
        direccion = :direccion,
        fecha_nacimiento = :fecha_nacimiento,
        clase_asignada = :clase_asignada
        WHERE usuario_id = :usuario_id";

    $statement_actualizar_maestro = $pdo->prepare($query_actualizar_maestro);
    $statement_actualizar_maestro->bindParam(":nombre", $maestro_actualizado['nombre']);
    $statement_actualizar_maestro->bindParam(":direccion", $maestro_actualizado['direccion']);
    $statement_actualizar_maestro->bindParam(":fecha_nacimiento", $maestro_actualizado['fecha_nacimiento']);
    $statement_actualizar_maestro->bindParam(":clase_asignada", $maestro_actualizado['clase_asignada']);
    $statement_actualizar_maestro->bindParam(":usuario_id", $usuario_id_editar);

    // Ejecutar la consulta de actualización
    if ($statement_actualizar_maestro->execute()) {
        // La actualización fue exitosa
        // Redirigir o mostrar un mensaje de éxito
        header("Location: maestros_crud.php");
        exit();
    } else {
        // La actualización falló, manejar el error según sea necesario
        echo "Error al actualizar el maestro.";
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
        <button class= "bg-[#fff5d2] w-[150px] h-[35px]" onclick="abrirModal()">Agregar Maestro</button>
          </div>

        <div>
            <table id="tablaMaestros">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Direccion</th>
                    <th>Fec. de Nacimiento</th>
                    <th>Clase asignada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maestros as $maestro) : ?>
                    <tr>
                        <td><?= $maestro["id"] ?></td>
                        <td><?= $maestro["nombre"] ?></td>
                        <td><?= $maestro["correo"] ?></td>
                        <td><?= $maestro["direccion"] ?></td>
                        <td><?= $maestro["fecha_nacimiento"] ?></td>
                        <td><?= $maestro["clase_asignada"] ?></td>
                        <td>
                            <button onclick="eliminarMaestro(<?= $maestro['usuario_id'] ?>)">Eliminar</button>
                        </td>
                        <td> 
                        <button onclick="editarMaestro(<?= $maestro['usuario_id'] ?>)">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
            <div id="modal" style="display: none;">
        <form id="formAgregarMaestro" action="/handle_db/insert_maestro.php" method="POST">
        <label>Nombre: <input type="text" name="nombre"></label><br>
        <label>Email: <input type="text" name="correo"></label><br>
        <label>Direccion: <input type="text" name="direccion"></label><br>
        <label>Fec. de Nacimiento: <input type="text" name="fecha_nacimiento"></label><br>
        <label>Clase asignada:
                <select name="clase_asignada">
                    <?php foreach ($clases_disponibles as $clase) : ?>
                        <!-- Verificar si la clase ya está asignada a algún maestro -->
                        <?php
                            $query_maestro_clase = "SELECT * FROM maestros WHERE clase_asignada = :nombre";
                            $statement_maestro_clase = $pdo->prepare($query_maestro_clase);
                            $statement_maestro_clase->bindParam(":nombre", $clase["nombre"]);
                            $statement_maestro_clase->execute();
                            $maestro_asignado = $statement_maestro_clase->fetch(PDO::FETCH_ASSOC);
                        ?>

                        <!-- Mostrar la clase solo si no está asignada a ningún maestro -->
                        <?php if (!$maestro_asignado) : ?>
                            <option value="<?= $clase['nombre'] ?>"><?= $clase['nombre'] ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
        <button type="submit">Guardar</button>
        <button type="button" onclick="cerrarModal()">Cancelar</button>
    </form> 
</div>
<div id="modal2" style="display: none;">
            <form method="POST" action="">
                <input type="hidden" name="usuario_id_editar" id="usuario_id_editar">
                <label>Nombre: <input type="text" name="nuevo_nombre"></label><br>
                <label>Dirección: <input type="text" name="nueva_direccion"></label><br>
                <label>Fec. de Nacimiento: <input type="text" name="nueva_fecha_nacimiento"></label><br>
                <label>Clase asignada:
                    <select name="nueva_clase_asignada">
                        <?php foreach ($clases_disponibles as $clase) : ?>
                            <option value="<?= $clase['nombre'] ?>"><?= $clase['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="submit" name="editar_maestro">Guardar</button>
                <button type="button" onclick="cerrarModal2()">Cancelar</button>
            </form>
        </div>
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

        function editarMaestro(usuarioId) {
            console.log("Editar maestro. Usuario ID:", usuarioId);
            // Establecer los valores actuales en el modal
            document.getElementById('usuario_id_editar').value = usuarioId;

            // Mostrar el modal de edición
            document.getElementById('modal2').style.display = 'block';
        }

        function cerrarModal2() {
            document.getElementById('modal2').style.display = 'none';
        }
            // Función para eliminar maestro
            function eliminarMaestro(maestroId) {
                if (confirm("¿Estás seguro de que quieres eliminar este maestro?")) {
                    // Crear un formulario dinámico
                    var form = document.createElement("form");
                    form.method = "POST";
                    
                    // Agregar un input oculto con el valor del ID del maestro
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "eliminar_maestro";
                    input.value = maestroId;
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