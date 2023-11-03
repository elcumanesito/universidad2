<?php
session_start();
require_once "../config/database.php";

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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_clase"])) {
    $id = $_POST["eliminar_clase"];

    // Eliminar de la tabla de clases
    $query_eliminar_clase = "DELETE FROM clases WHERE id = :id";
    $statement_eliminar_clase = $pdo->prepare($query_eliminar_clase);
    $statement_eliminar_clase->bindParam("id", $id);
    $statement_eliminar_clase->execute();

}


$query_leer_clases = "SELECT clases.id, clases.nombre AS clase_nombre, maestros.nombre AS maestro_nombre
                      FROM clases
                      LEFT JOIN maestros ON clases.nombre = maestros.clase_asignada";
$statement_leer_clases = $pdo->prepare($query_leer_clases);
$statement_leer_clases->execute();
$clases = $statement_leer_clases->fetchAll(PDO::FETCH_ASSOC);

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
        <button class= "bg-[#fff5d2] w-[150px] h-[35px]" onclick="abrirModal()">Agregar Clase</button>
          </div>
          <table id="tablaClases">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Clase</th>
                    <th>Maestro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clases as $clase) : ?>
                    <tr>
                    <td><?= $clase["id"] ?></td>
                    <td><?= $clase["clase_nombre"] ?></td>
                    <td><?= $clase["maestro_nombre"] ?></td>
                        
                        <td>
                            <button onclick="eliminarClase(<?= $clase['id'] ?>)">Eliminar</button>
                        </td>
                        <td>
                        <button onclick="editarClase(<?= $clase['id'] ?>, '<?= $clase['clase_nombre'] ?>')">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="modal" style="display: none;">
        <form id="formAgregarClase" action="/handle_db/editar_clase.php" method="POST">
        <label>Nombre de la materia: <input type="text" name="clase"></label><br>
        <label>Maestros disponibles para la clase: <input type="text" name="maestro"></label><br>
        <button type="submit">Guardar</button>
        <button type="button" onclick="cerrarModal()">Cancelar</button>
    </form>
    </div>
    <div id="modalEditar" style="display: none;">
    <form id="formEditarClase" action="/handle_db/editar_clase.php" method="POST">
        <input type="hidden" id="editarMateriaId" name="editar_materia_id">
        <label>Nombre de la materia: <input type="text" id="editarMateriaNombre" name="editar_materia_nombre"></label><br>
        <button type="submit">Guardar cambios</button>
        <button type="button" onclick="cerrarModalEditar()">Cancelar</button>
    </form>
</div>
</div>

</div>

        </div>
</div>
           

        <!-- Tabla de Clases -->
       

<script>
            function abrirModal() {
                document.getElementById('modal').style.display = 'block';
            }

            function cerrarModal() {
                document.getElementById('modal').style.display = 'none';
            }

            // Función para eliminar clase
            function eliminarClase(claseId) {
                if (confirm("¿Estás seguro de que quieres eliminar esta clase?")) {
                    // Crear un formulario dinámico
                    var form = document.createElement("form");
                    form.method = "POST";
                    
                    // Agregar un input oculto con el valor del ID de la clase
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "eliminar_clase";
                    input.value = claseId;
                    form.appendChild(input);

                    // Adjuntar el formulario al cuerpo del documento
                    document.body.appendChild(form);

                    // Enviar el formulario
                    form.submit();
                }
            }
    function editarClase(claseId, nombreMateria) {
        // Llenar el formulario de edición con los datos actuales
        document.getElementById('editarMateriaId').value = claseId;
        document.getElementById('editarMateriaNombre').value = nombreMateria;

        // Mostrar el modal de edición
        document.getElementById('modalEditar').style.display = 'block';
    }

    function cerrarModalEditar() {
        document.getElementById('modalEditar').style.display = 'none';
    }

        </script>
</body>
</html>