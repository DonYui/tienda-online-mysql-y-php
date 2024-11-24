<?php
session_start();
include 'conexion.php'; // Asegúrate de que esta ruta sea correcta

// Consultar categorías
$categoriaQuery = "SELECT * FROM categorias"; 
$categoriaResult = $conn->query($categoriaQuery);

// Obtener el término de búsqueda
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Modificar la consulta de productos para filtrar por nombre
$productoQuery = "SELECT p.*, c.nombre AS categoria_nombre 
                  FROM productos p 
                  JOIN categorias c ON p.id_categoria = c.id";

// Si hay un término de búsqueda, agregar la condición WHERE
if ($buscar != '') {
    $productoQuery .= " WHERE p.nombre LIKE '%" . $conn->real_escape_string($buscar) . "%'";
}

$productoResult = $conn->query($productoQuery);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tienda de objetos coleccionables">
    <title>Tienda de Coleccionables</title>
    <link rel="stylesheet" href="style.css">

    <style>

        /* Estilos para el modal */
        .modal {
            display: none; /* Inicialmente oculto */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7); /* Fondo oscuro con opacidad */
            padding-top: 60px;
            transition: opacity 0.3s ease;
        }

        /* Contenido del modal */
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Animación de entrada del modal */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Estilos para el cierre del modal */
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #ff5733; /* Color llamativo para el botón de cierre */
            text-decoration: none;
        }

        /* Título del modal */
        .modal-content h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Imagen del producto */
        #imagen {
            width: 100%;
            max-width: 400px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Estilo de las secciones de detalles */
        .modal-content p {
            font-size: 16px;
            margin: 8px 0;
            line-height: 1.5;
            color: #555;
        }

        .modal-content strong {
            color: #333;
        }

        /* Estilo del formulario de comentarios */
        #formComentario {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #formComentario textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            resize: vertical;
            margin-bottom: 15px;
            transition: border-color 0.3s;
        }

        /* Cambio de color del borde en focus */
        #formComentario textarea:focus {
            border-color: #ff5733;
            outline: none;
        }

        /* Estilo del botón de envío de comentario */
        #formComentario button {
            background-color: #c93a05; /* Verde atractivo */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #formComentario button:hover {
            background-color: #218838;
        }

        /* Estilo para el fondo oscuro del modal */
        .modal-open {
            display: block;
            opacity: 1;
            pointer-events: all;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">
                <img src="images/logo.png" alt="Logo de la tienda"/>
            </a>
            <h1>Wandering Merchant</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#productos">Productos</a></li>
                <li><a href="#categorias">Categorías</a></li>
                <li><a href="#contacto">Contacto</a></li>
                <li><a href="#nosotros">Sobre Nosotros</a></li>
                
                <!-- Mostrar los enlaces según si el usuario ha iniciado sesión -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li><a href="usuarios/perfil.php">Perfil</a></li>
                    <li><a href="usuarios/logout.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="usuarios/login.php">Iniciar Sesión</a></li>
                    <li><a href="usuarios/register.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- Buscador -->
        <div class="buscador-container">
            <form method="GET" action="index.php" class="buscador">
                <input type="text" name="buscar" id="buscar" placeholder="Buscar productos..." value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>" class="buscador-input">
                <button type="submit" class="buscar-btn">🔍</button>
            </form>
        </div>

        <div class="carrito">
            <a href="#" id="abrirCarrito">🛒 Carrito (<span id="cantidadCarrito">0</span>)</a>
        </div>
    </header>

    <div id="carritoModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarCarrito()">&times;</span>
            <h2>Tu carrito</h2>
            <div id="listaCarrito"></div>
            <p><strong>Total: $</strong><span id="totalCarrito">0</span></p>
            <button onclick="vaciarCarrito()">Vaciar Carrito</button>
            <button onclick="mostrarInterfazPago()">Pagar</button>
        </div>
    </div>

    <section id="inicio" class="hero">
        <h2>Bienvenido a la tienda de objetos coleccionables</h2>
        <p>Explora nuestra colección única de artículos raros y exclusivos</p>
    </section>

    <!-- Sección de categorías -->
    <section id="categorias" class="categorias">
        <h2>Categorías de Productos</h2>
        <ul>
            <li><a href="#" data-categoria="todos" onclick="filtrarProductos('todos')">Todos</a></li>
            <?php while($categoria = $categoriaResult->fetch_assoc()): ?>
                <li><a href="#" data-categoria="<?= $categoria['nombre']; ?>" onclick="filtrarProductos('<?= $categoria['nombre']; ?>')"><?= $categoria['nombre']; ?></a></li>
            <?php endwhile; ?>
        </ul>
    </section>

    <section id="productos" class="productos">
        <h2>Nuestros Productos</h2>
        
        <!-- Si no se encuentra ningún producto -->
        <?php if ($productoResult->num_rows > 0): ?>
            <div class="grid-productos">
                <?php while($producto = $productoResult->fetch_assoc()): ?>
                    <div class="producto" data-id="<?= $producto['id']; ?>" data-sku="<?= $producto['sku']; ?>" data-nombre="<?= $producto['nombre']; ?>" data-precio="<?= $producto['precio']; ?>" data-peso="<?= $producto['peso']; ?>" data-descripcion="<?= $producto['descripcion']; ?>" data-imagen="images/<?= $producto['imagen']; ?>" data-categoria="<?= $producto['categoria_nombre']; ?>" data-fecha="<?= $producto['fecha']; ?>" data-stock="<?= $producto['stock']; ?>">
                        <img src="images/<?= $producto['imagen']; ?>" alt="<?= $producto['nombre']; ?>">
                        <h3><?= $producto['nombre']; ?></h3>
                        <p>$<?= number_format($producto['precio'], 2); ?></p>
                        <button onclick="agregarAlCarrito(<?= $producto['id']; ?>, '<?= $producto['nombre']; ?>', <?= $producto['precio']; ?>)">Agregar al carrito</button>
                        <button onclick="mostrarDetalles(this)">Ver detalles</button>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No se encontraron productos que coincidan con tu búsqueda.</p>
        <?php endif; ?>
    </section>

    <!-- Modal para mostrar los detalles del producto -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2 id="nombre"></h2>
            <img id="imagen" src="" alt="Imagen del producto">
            <p><strong>ID:</strong> <span id="id"></span></p>
            <p><strong>SKU:</strong> <span id="sku"></span></p>
            <p><strong>Precio:</strong> $<span id="precio"></span></p>
            <p><strong>Peso:</strong> <span id="peso"></span> kg</p>
            <p><strong>Descripción:</strong> <span id="descripcion"></span></p>
            <p><strong>Categoría:</strong> <span id="categoria"></span></p>
            <p><strong>Fecha de creación:</strong> <span id="fecha"></span></p>
            <p><strong>Stock:</strong> <span id="stock"></span></p>
            <h3>Dejar un comentario al vendedor</h3>
            <form id="formComentario" action="agregar_comentario.php" method="POST">
                <input type="hidden" name="producto_id" id="producto_id" value="">
                <textarea name="comentario" placeholder="Escribe tu comentario..." required></textarea><br>
                <button type="submit">Enviar Comentario</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Wandering Merchant. Todos los derechos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
