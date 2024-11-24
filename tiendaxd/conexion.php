<?php
$host = 'localhost';
$db = 'tienda_coleccionables';
$user = 'root'; // Cambia si es necesario
$pass = ''; // Cambia si tienes contraseña

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
