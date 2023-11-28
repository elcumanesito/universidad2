<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once "../config/database.php";
    $email = $_POST["email"];
    $password = $_POST["password"];
    $query = "SELECT * FROM usuarios WHERE correo = :email AND contrasena = :password";
    $statement = $pdo->prepare($query);
    $statement->bindParam("email", $email);
    $statement->bindParam("password", $password);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['logged_in'] = true;
        if ($user['rol_id'] == 3) {
            // Usuario con rol de alumno
            if (isset($user['id']) && !empty($user['id'])) {
                $_SESSION['user_id'] = $user['id'];
                // Obtener información adicional del alumno
                $query_alumno_info = "SELECT id, nombre, apellido FROM alumnos WHERE usuario_id = :usuario_id";
                $statement_alumno_info = $pdo->prepare($query_alumno_info);
                $statement_alumno_info->bindParam(":usuario_id", $_SESSION['user_id']);
                $statement_alumno_info->execute();
                $alumno_info = $statement_alumno_info->fetch(PDO::FETCH_ASSOC);
                $_SESSION['alumno_info'] = $alumno_info;
            } else {
                echo "Error: No hay valor de id para el alumno.";
                exit();
            }
        } elseif ($user['rol_id'] == 2) {
    // Usuario con rol de maestro
    $_SESSION['user_id'] = $user['id']; // Utiliza el 'id' de la tabla 'usuarios'

    // Obtener información adicional del maestro
    $query_maestro_info = "SELECT id, nombre FROM maestros WHERE id = :maestro_id";
    $statement_maestro_info = $pdo->prepare($query_maestro_info);
    $statement_maestro_info->bindParam(":maestro_id", $_SESSION['user_id']);

    try {
        $statement_maestro_info->execute();

        // Verificar si la consulta devolvió resultados
        if ($statement_maestro_info->rowCount() > 0) {
            $maestro_info = $statement_maestro_info->fetch(PDO::FETCH_ASSOC);
            $_SESSION['maestro_info'] = $maestro_info;
        } else {
            echo "No se encontraron resultados para el maestro con ID: " . $_SESSION['user_id'];
        }
    } catch (PDOException $e) {
        echo "Error en la ejecución de la consulta del maestro: " . $e->getMessage();
    }

} else {
    // Otros roles
    $_SESSION['user_id'] = $user['id'];
}

        $_SESSION['user_role'] = $user['rol_id'];

        switch ($user["rol_id"]) {
            case 1:
                header("Location: /view/admin_dashboard.php");
                break;
            case 2:
                header("Location: /view/maestro_dashboard.php");
                break;
            case 3:
                header("Location: /view/alumno_dashboard.php");
                break;
            default:
                // Maneja roles desconocidos
                break;
        }
    } else {
        // Credenciales inválidas, redirecciona a la página de inicio de sesión con un mensaje de error
        header("Location: login.php?error=1");
    }
} else {
    // Redirecciona a la página de inicio de sesión si se accede directamente sin una solicitud POST
    header("Location: login.php");
}

?>
