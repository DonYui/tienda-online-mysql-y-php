<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['usuario_id'])) {
        include 'conexion.php';

        $usuario_id = $_SESSION['usuario_id'];
        $producto_id = $_POST['producto_id'];
        $comentario = $_POST['comentario'];

        // Insertar el comentario en la base de datos
        $query = "INSERT INTO comentarios (producto_id, usuario_id, comentario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $producto_id, $usuario_id, $comentario);

        if ($stmt->execute()) {
            header("Location: detalle_producto.php?id=$producto_id");
            exit;
        } else {
            echo "Error al agregar el comentario";
        }
    } else {
        echo "Debes iniciar sesión para comentar.";
    }
} else {
    header("Location: index.php");
    exit;
}
