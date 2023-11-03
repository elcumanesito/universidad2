<?php

// Función para cerrar sesión
function logout() {
    // Destruye todas las variables de sesión
    session_unset();
    // Destruye la sesión
    session_destroy();
    // Redirige a la página de inicio de sesión
    header("Location: ./view/login.php");
    exit();
}

?>
