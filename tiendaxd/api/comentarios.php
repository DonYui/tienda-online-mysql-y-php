<?php
include 'conexion.php';

$producto_id = $_GET['producto_id'];

// Consulta para obtener los comentarios del producto
$query = "SELECT c.comentario, c.fecha, u.username 
          FROM comentarios c 
          JOIN usuarios u ON c.usuario_id = u.id 
          WHERE c.producto_id = ? 
          ORDER BY c.fecha DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$result = $stmt->get_result();

$comentarios = [];
while ($comentario = $result->fetch_assoc()) {
    $comentarios[] = $comentario;
}

echo json_encode($comentarios);
?>
