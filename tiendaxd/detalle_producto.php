<?php
// Verifica si el usuario está logueado para permitirle comentar
if (isset($_SESSION['user_id'])):
?>
    <h3>Deja un comentario:</h3>
    <form action="agregar_comentario.php" method="POST">
        <textarea name="comentario" rows="5" required></textarea>
        <input type="hidden" name="producto_id" value="<?= $producto['id']; ?>">
        <button type="submit">Enviar comentario</button>
    </form>
<?php else: ?>
    <p>Debes <a href="usuarios/login.php">iniciar sesión</a> para dejar un comentario.</p>
<?php endif; ?>

<!-- Mostrar los comentarios -->
<h3>Comentarios:</h3>
<?php
// Consulta para obtener los comentarios del producto
$comentariosQuery = "SELECT c.comentario, c.fecha, u.username 
                     FROM comentarios c 
                     JOIN usuarios u ON c.usuario_id = u.id 
                     WHERE c.producto_id = ? 
                     ORDER BY c.fecha DESC";
$stmt = $conn->prepare($comentariosQuery);
$stmt->bind_param("i", $producto['id']);
$stmt->execute();
$comentariosResult = $stmt->get_result();

if ($comentariosResult->num_rows > 0):
    while ($comentario = $comentariosResult->fetch_assoc()):
?>
        <div class="comentario">
            <p><strong><?= $comentario['username']; ?></strong> (<?= $comentario['fecha']; ?>)</p>
            <p><?= $comentario['comentario']; ?></p>
        </div>
<?php
    endwhile;
else:
?>
    <p>No hay comentarios para este producto.</p>
<?php endif; ?>
