<?php

$host = "localhost";
$db_name = "universidad";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    die("Error de conexión: " . $exception->getMessage());
}

return $pdo;
?>
