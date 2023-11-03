<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    require_once "../config/database.php";

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate user credentials (you might want to enhance this)
    $query = "SELECT * FROM usuarios WHERE correo = :email AND contrasena = :password";
    $statement = $pdo->prepare($query);
    $statement->bindParam("email", $email);
    $statement->bindParam("password", $password);
    $statement->execute();

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Inicia la sesión y guarda la información del usuario
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['rol_id'];

        // Redirecciona basado en el rol del usuario
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
