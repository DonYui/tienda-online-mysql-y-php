let carrito = [];

// Función para cargar productos desde el servidor
async function cargarProductos() {
    try {
        const response = await fetch('http://localhost/tiendaxd/api/productos.php');
        const productos = await response.json();
        mostrarProductos(productos);
    } catch (error) {
        console.error('Error al cargar los productos:', error);
    }
}

// Función para mostrar productos en el HTML
function mostrarProductos(productos) {
    const gridProductos = document.getElementById('grid-productos');
    gridProductos.innerHTML = ''; // Limpiar productos existentes

    productos.forEach(producto => {
        const divProducto = document.createElement('div');
        divProducto.classList.add('producto');
        divProducto.setAttribute('data-id', producto.id);
        divProducto.setAttribute('data-sku', producto.sku);
        divProducto.setAttribute('data-nombre', producto.nombre);
        divProducto.setAttribute('data-precio', producto.precio);
        divProducto.setAttribute('data-peso', producto.peso);
        divProducto.setAttribute('data-descripcion', producto.descripcion);
        divProducto.setAttribute('data-imagen', producto.imagen);
        divProducto.setAttribute('data-categoria', producto.categoria);
        divProducto.setAttribute('data-fecha', producto.fecha);
        divProducto.setAttribute('data-stock', producto.stock);

        divProducto.innerHTML = `
            <img src="${producto.imagen}" alt="${producto.nombre}">
            <h3>${producto.nombre}</h3>
            <p>$${producto.precio.toFixed(2)}</p>
            <button onclick="agregarAlCarrito(${producto.id}, '${producto.nombre}', ${producto.precio})">Agregar al carrito</button>
            <button onclick="mostrarDetalles(this)">Ver detalles</button>
        `;
        gridProductos.appendChild(divProducto);
    });
}

// Función para mostrar los detalles del producto en un modal
function mostrarDetalles(element) {
    const producto = element.closest('.producto');
    const id = producto.getAttribute('data-id');
    const sku = producto.getAttribute('data-sku');
    const nombre = producto.getAttribute('data-nombre');
    const precio = producto.getAttribute('data-precio');
    const peso = producto.getAttribute('data-peso');
    const descripcion = producto.getAttribute('data-descripcion');
    const imagen = producto.getAttribute('data-imagen');
    const categoria = producto.getAttribute('data-categoria');
    const fecha = producto.getAttribute('data-fecha');
    const stock = producto.getAttribute('data-stock');

    // Rellenar los datos en el modal
    document.getElementById('id').textContent = id;
    document.getElementById('sku').textContent = sku;
    document.getElementById('nombre').textContent = nombre;
    document.getElementById('precio').textContent = precio;
    document.getElementById('peso').textContent = peso;
    document.getElementById('descripcion').textContent = descripcion;
    document.getElementById('imagen').src = imagen;
    document.getElementById('categoria').textContent = categoria;
    document.getElementById('fecha').textContent = fecha;
    document.getElementById('stock').textContent = stock;
    
    
    // Mostrar el modal
    document.getElementById('modal').style.display = 'block';
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('modal').style.display = 'none';
}

// Cerrar el modal cuando se haga clic fuera de él
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Función para filtrar productos por categoría
function filtrarProductos(categoria) {
    const productos = document.querySelectorAll('.producto');

    productos.forEach(producto => {
        const productoCategoria = producto.getAttribute('data-categoria');
        
        // Mostrar todos los productos si la categoría es "todos"
        if (categoria === 'todos' || productoCategoria === categoria) {
            producto.style.display = 'block';
        } else {
            producto.style.display = 'none';
        }
    });
}

// Función para agregar productos al carrito
function agregarAlCarrito(id, nombre, precio) {
    const productoExistente = carrito.find(producto => producto.id === id);
    
    if (productoExistente) {
        productoExistente.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1 });
    }

    actualizarCarrito();
}

// Función para actualizar el carrito
function actualizarCarrito() {
    const listaCarrito = document.getElementById('listaCarrito');
    const cantidadCarrito = document.getElementById('cantidadCarrito');
    const totalCarrito = document.getElementById('totalCarrito');
    
    listaCarrito.innerHTML = '';  // Limpiar lista del carrito
    let total = 0;
    let cantidadTotal = 0;

    carrito.forEach((producto, index) => {
        const item = document.createElement('div');
        item.classList.add('carrito-item');
        
        // Información del producto en el carrito
        item.innerHTML = `
            ${producto.nombre} x${producto.cantidad} - $${(producto.precio * producto.cantidad).toFixed(2)}
            <button onclick="aumentarCantidad(${index})">+</button>
            <button onclick="disminuirCantidad(${index})">-</button>
            <button onclick="eliminarDelCarrito(${index})">Eliminar</button>
        `;
        listaCarrito.appendChild(item);

        total += producto.precio * producto.cantidad;
        cantidadTotal += producto.cantidad;
    });

    totalCarrito.textContent = total.toFixed(2);
    cantidadCarrito.textContent = cantidadTotal;
}

// Función para aumentar la cantidad de un producto en el carrito
function aumentarCantidad(index) {
    carrito[index].cantidad += 1;
    actualizarCarrito();
}

// Función para disminuir la cantidad de un producto en el carrito
function disminuirCantidad(index) {
    if (carrito[index].cantidad > 1) {
        carrito[index].cantidad -= 1;
    } else {
        eliminarDelCarrito(index);  // Si la cantidad es 1, se elimina el producto
    }
    actualizarCarrito();
}

// Función para eliminar un producto del carrito
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

// Función para mostrar el modal del carrito
document.getElementById('abrirCarrito').addEventListener('click', function() {
    document.getElementById('carritoModal').style.display = 'block';
});

// Función para cerrar el carrito
function cerrarCarrito() {
    document.getElementById('carritoModal').style.display = 'none';
}

// Función para vaciar el carrito
function vaciarCarrito() {
    carrito = [];
    actualizarCarrito();
    cerrarCarrito();
}

function procesarPago() {
    // Simulación del proceso de pago
    fetch('procesar_pago.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            carrito: obtenerCarrito(), // Función que retorna los productos del carrito
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Pago realizado con éxito. Revisa tu correo para la factura.");
            vaciarCarrito();
        } else {
            alert("Hubo un problema con el pago: " + data.message);
        }
    })
    .catch(error => console.error('Error al procesar el pago:', error));
}

