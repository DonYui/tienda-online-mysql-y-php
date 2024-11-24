<?php
include 'conexion.php';

// Obtener todos los productos
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM productos";
    $result = $conn->query($sql);
    $productos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    echo json_encode($productos);
}

// Agregar un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = $_POST['sku'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $peso = $_POST['peso'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_POST['imagen'];
    $categoria = $_POST['categoria'];
    $fecha = $_POST['fecha'];
    $stock = $_POST['stock'];

    $sql = "INSERT INTO productos (sku, nombre, precio, peso, descripcion, imagen, categoria, fecha, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddssssis", $sku, $nombre, $precio, $peso, $descripcion, $imagen, $categoria, $fecha, $stock);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
}

// Aquí puedes agregar lógica para actualizar y eliminar productos de manera similar.
?>
