<?php
// Incluye el archivo de conexión a la base de datos
include_once 'conexion.php';

// Consulta SQL para obtener productos
$query = "SELECT * FROM productos";
$result = $conn->query($query);

// Verifica si hay resultados
if ($result->num_rows > 0) {
    $productos = [];

    // Obtén los datos de cada producto y agrégalos al array
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    // Establece la cabecera para devolver JSON
    header('Content-Type: application/json');
    echo json_encode($productos);
} else {
    echo json_encode([]);
}

// Cierra la conexión a la base de datos
$conn->close();
?>
