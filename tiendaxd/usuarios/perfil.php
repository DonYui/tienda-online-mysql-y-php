<?php
session_start();
include '../conexion.php'; // Ajusta la ruta de conexión si es necesario

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php'); // Redirigir al login si no está autenticado
    exit();
}

// Obtener los datos del usuario desde la base de datos
$usuarioId = $_SESSION['usuario_id'];
$query = "SELECT username, correo, foto_perfil FROM usuarios WHERE id = '$usuarioId'";
$result = $conn->query($query);
$usuario = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #e67e22;
        }
        .foto-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        p {
            color: #333;
        }
        .btn-cerrar-sesion {
            background-color: #e67e22;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn-cerrar-sesion:hover {
            background-color: #d35400;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Perfil de Usuario</h2>
        <!-- Mostrar foto de perfil -->
        <img src="../images/perfiles/<?php echo $usuario['foto_perfil']; ?>" alt="Foto de Perfil" class="foto-perfil">
        
        <!-- Mostrar nombre y correo -->
        <p><strong>Nombre:</strong> <?php echo $usuario['username']; ?></p>
        <p><strong>Correo:</strong> <?php echo $usuario['correo']; ?></p>
        
        <!-- Botón de Cerrar Sesión -->
        <form method="POST" action="logout.php">
            <button type="submit" class="btn-cerrar-sesion">Cerrar Sesión</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
