<?php
session_start();
include '../conexion.php'; // Ajusta la ruta de conexión si es necesario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Validar que el checkbox de términos y condiciones esté marcado
    if (!isset($_POST['terminos'])) {
        $error = "Debes aceptar los términos y condiciones para registrarte.";
    } else {
        // Procesar la imagen
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $fotoTmp = $_FILES['foto_perfil']['tmp_name'];
            $fotoNombre = $_FILES['foto_perfil']['name'];
            $fotoExt = pathinfo($fotoNombre, PATHINFO_EXTENSION);

            // Generar un nombre único para la imagen
            $fotoNombreNuevo = uniqid() . '.' . $fotoExt;

            // Directorio donde se guardarán las fotos
            $directorioDestino = '../images/perfiles/';  // Ajusta la ruta según sea necesario
            $rutaDestino = $directorioDestino . $fotoNombreNuevo;

            // Mover la imagen al directorio de destino
            if (move_uploaded_file($fotoTmp, $rutaDestino)) {
                // La foto se ha guardado correctamente

                // Encriptar la contraseña
                $contrasenaEncriptada = password_hash($contrasena, PASSWORD_DEFAULT);

                // Insertar el usuario en la base de datos
                $query = "INSERT INTO usuarios (username, correo, password, foto_perfil) 
                          VALUES ('$nombre', '$correo', '$contrasenaEncriptada', '$fotoNombreNuevo')";

                if ($conn->query($query)) {
                    $_SESSION['usuario_id'] = $conn->insert_id;  // Guardar el ID del nuevo usuario
                    $_SESSION['usuario_nombre'] = $nombre;
                    header('Location: ../index.php'); // Redirigir al inicio
                    exit();
                } else {
                    echo "Error al registrar el usuario: " . $conn->error;
                }
            } else {
                echo "Error al subir la imagen.";
            }
        } else {
            echo "No se ha seleccionado una foto válida.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
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
        }
        h2 {
            text-align: center;
            color: #e67e22;
        }
        label {
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #e67e22;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #d35400;
        }
        p {
            text-align: center;
            margin-top: 15px;
        }
        a {
            color: #e67e22;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro</h2>
        <form method="POST" action="register.php" enctype="multipart/form-data">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            <br>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            <br>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
            <br>

            <label for="foto_perfil">Foto de Perfil:</label>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" required>
            <br>

            <!-- Checkbox para aceptar términos y condiciones -->
            <label>
                <input type="checkbox" name="terminos" required>
                Acepto los <a href="terminosycondiciones.php" target="_blank">términos y condiciones</a>
            </label>
            <br>

            <button type="submit">Registrar</button>
        </form>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
