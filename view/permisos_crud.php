<?php
session_start();
require_once "../handle_db/logout.php";
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

// Función para obtener la contraseña por rol
function obtenerContrasenaPorRol($rol_id) {
    // Define las contraseñas asociadas a cada rol
    $contrasenas_por_rol = [
        1 => 'admin',
        2 => 'maestro',
        3 => 'alumno',
    ];

    // Devuelve la contraseña asociada al rol o una contraseña predeterminada si no se encuentra
    return $contrasenas_por_rol[$rol_id] ?? 'default';
}

// Función para obtener el correo por usuario_id
function obtenerCorreoPorUsuarioId($usuario_id, $pdo) {
    // Ejemplo básico:
    $query_obtener_correo = "SELECT correo FROM usuarios WHERE id = :usuario_id";
    $statement_obtener_correo = $pdo->prepare($query_obtener_correo);
    $statement_obtener_correo->bindParam(":usuario_id", $usuario_id);
    $statement_obtener_correo->execute();
    $resultado = $statement_obtener_correo->fetch(PDO::FETCH_ASSOC);

    // Devolver el correo obtenido o un valor predeterminado si no se encuentra
    return $resultado['correo'] ?? 'correo_de_prueba@example.com';
}

// Leer usuarios
$query_leer_usuarios = "SELECT usuarios.id, usuarios.correo, roles.nombre as nombre_rol FROM usuarios JOIN roles ON usuarios.rol_id = roles.id";
$statement_leer_usuarios = $pdo->prepare($query_leer_usuarios);
$statement_leer_usuarios->execute();
$usuarios = $statement_leer_usuarios->fetchAll(PDO::FETCH_ASSOC);


// Obtener roles desde la base de datos
$query_roles = "SELECT id, nombre FROM roles";
$statement_roles = $pdo->prepare($query_roles);
$statement_roles->execute();
$roles = $statement_roles->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_rol"])) {
    $usuario_id = $_POST["usuario_id"];
    $nuevo_rol_id = $_POST["nuevo_rol"];

    // Actualizar el rol y la contraseña en la base de datos
$query_actualizar_rol_contrasena = "UPDATE usuarios SET rol_id = :nuevo_rol, contrasena = :contrasena_nueva WHERE id = :usuario_id";
$statement_actualizar_rol_contrasena = $pdo->prepare($query_actualizar_rol_contrasena);
$contrasena_nueva = obtenerContrasenaPorRol($nuevo_rol_id); // Almacena el valor en una variable antes de pasarlo por referencia
$statement_actualizar_rol_contrasena->bindParam(":nuevo_rol", $nuevo_rol_id);
$statement_actualizar_rol_contrasena->bindParam(":contrasena_nueva", $contrasena_nueva);
$statement_actualizar_rol_contrasena->bindParam(":usuario_id", $usuario_id);
$statement_actualizar_rol_contrasena->execute();


    // Ahora, también insertar o eliminar en las tablas de maestros y alumnos según el nuevo rol
    if ($nuevo_rol_id == 2) {  // Rol de Maestro
        // Eliminar el usuario de la tabla de alumnos si existe
        $query_eliminar_alumno = "DELETE FROM alumnos WHERE usuario_id = :usuario_id";
        $statement_eliminar_alumno = $pdo->prepare($query_eliminar_alumno);
        $statement_eliminar_alumno->bindParam(":usuario_id", $usuario_id);
        $statement_eliminar_alumno->execute();
    
        // Verificar si el usuario ya es maestro
        $query_maestro_existente = "SELECT COUNT(*) as count FROM maestros WHERE usuario_id = :usuario_id";
        $statement_maestro_existente = $pdo->prepare($query_maestro_existente);
        $statement_maestro_existente->bindParam(":usuario_id", $usuario_id);
        $statement_maestro_existente->execute();
        $maestro_existente = $statement_maestro_existente->fetch(PDO::FETCH_ASSOC);
    
        if ($maestro_existente['count'] == 0) {
            // Si no es maestro, insertar en la tabla de maestros
            $query_insertar_maestro = "INSERT INTO maestros (usuario_id, correo) VALUES (:usuario_id, :correo)";
            $statement_insertar_maestro = $pdo->prepare($query_insertar_maestro);
    
            // Obtener el correo por el usuario_id
            $correo_temp = obtenerCorreoPorUsuarioId($usuario_id, $pdo);
    
            // Crear variables temporales para los valores
            $usuario_id_temp = $usuario_id;
    
            $statement_insertar_maestro->bindParam(":usuario_id", $usuario_id_temp);
            $statement_insertar_maestro->bindParam(":correo", $correo_temp);
            $statement_insertar_maestro->execute();
        }
    } elseif ($nuevo_rol_id == 3) {  // Rol de Alumno
        // Eliminar el usuario de la tabla de maestros si existe
        $query_eliminar_maestro = "DELETE FROM maestros WHERE usuario_id = :usuario_id";
        $statement_eliminar_maestro = $pdo->prepare($query_eliminar_maestro);
        $statement_eliminar_maestro->bindParam(":usuario_id", $usuario_id);
        $statement_eliminar_maestro->execute();
    
        // Verificar si el usuario ya es alumno
        $query_alumno_existente = "SELECT COUNT(*) as count FROM alumnos WHERE usuario_id = :usuario_id";
        $statement_alumno_existente = $pdo->prepare($query_alumno_existente);
        $statement_alumno_existente->bindParam(":usuario_id", $usuario_id);
        $statement_alumno_existente->execute();
        $alumno_existente = $statement_alumno_existente->fetch(PDO::FETCH_ASSOC);
    
        if ($alumno_existente['count'] == 0) {
            // Si no es alumno, insertar en la tabla de alumnos
            $query_insertar_alumno = "INSERT INTO alumnos (usuario_id, correo) VALUES (:usuario_id, :correo)";
            $statement_insertar_alumno = $pdo->prepare($query_insertar_alumno);
    
            // Obtener el correo por el usuario_id
            $correo_temp = obtenerCorreoPorUsuarioId($usuario_id, $pdo);
    
            // Crear variables temporales para los valores
            $usuario_id_temp = $usuario_id;
    
            $statement_insertar_alumno->bindParam(":usuario_id", $usuario_id_temp);
            $statement_insertar_alumno->bindParam(":correo", $correo_temp);
            $statement_insertar_alumno->execute();
        }
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
          </div>
        <table id="tablaUsuario">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Email / Usuario</th>
                    <th>Permiso</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario) : ?>
                    <tr>
                        <td><?= $usuario["id"] ?></td>
                        <td><?= $usuario["correo"] ?></td>
                        <td><?= $usuario["nombre_rol"]?></td>
                        <td>
                        <button onclick="abrirModal(<?= $usuario['id'] ?>, '<?= $usuario['nombre_rol'] ?>')">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
         <!-- Modal para editar el rol -->
    <div id="modal" style="display: none;">
        <form method="POST" action="">
            <input type="hidden" name="usuario_id" id="usuario_id">
            <label for="nuevo_rol">Nuevo Rol:</label>
            <select name="nuevo_rol" id="nuevo_rol">
                <?php foreach ($roles as $rol) : ?>
                    <option value="<?= $rol['id'] ?>"><?= $rol['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="editar_rol">Guardar</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>
        </form>
    </div>

        </div>
            </div>
        </div>
</div>
    <script>
        function abrirModal(usuarioId, rolActual) {
            // Establecer los valores actuales en el modal
            document.getElementById('usuario_id').value = usuarioId;
            document.getElementById('nuevo_rol').value = rolActual;

            // Mostrar el modal
            document.getElementById('modal').style.display = 'block';
        }
        function cerrarModal() {
            // Ocultar el modal
            document.getElementById('modal').style.display = 'none';
        }
    </script>

</body>
</html>
